<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financial\StoreBursarySignatureRequest;
use App\Http\Requests\Financial\UpdateBursarySignatureRequest;
use App\Models\BursarySignature;
use Illuminate\Support\Facades\Storage;

class BursarySignatureController extends Controller
{
    protected function authorizeFinanceUser(): void
    {
        $user = auth()->user();

        abort_unless($user && in_array((string) ($user->role?->value ?? ''), [
            'super_admin',
            'school_admin',
            'principal',
            'vice_principal',
            'accountant',
        ], true), 403, 'Unauthorized access.');
    }

    public function index()
    {
        $this->authorizeFinanceUser();

        $signatures = BursarySignature::query()
            ->orderByRaw("CASE signature_role
                WHEN 'principal' THEN 1
                WHEN 'vice_principal' THEN 2
                WHEN 'bursar' THEN 3
                ELSE 4 END")
            ->orderByDesc('is_default')
            ->latest('id')
            ->paginate(20);

        $signatureRoles = BursarySignature::ROLE_OPTIONS;

        return view('financial.bursary-signatures.index', compact('signatures', 'signatureRoles'));
    }

    public function store(StoreBursarySignatureRequest $request)
    {
        $this->authorizeFinanceUser();

        $path = $request->file('signature')->store('bursary-signatures/' . auth()->user()->school_id);

        $signature = BursarySignature::query()->create([
            'name' => (string) $request->string('name'),
            'title' => $request->input('title'),
            'signature_role' => (string) $request->string('signature_role'),
            'signature_path' => $path,
            'is_active' => $request->boolean('is_active', true),
            'is_default' => $request->boolean('is_default', false),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        if ($signature->is_default) {
            $this->makeDefault($signature);
        }

        return back()->with('success', 'Signature added.');
    }

    public function update(UpdateBursarySignatureRequest $request, BursarySignature $bursarySignature)
    {
        $this->authorizeFinanceUser();

        $data = [
            'name' => (string) $request->string('name'),
            'title' => $request->input('title'),
            'signature_role' => (string) $request->string('signature_role'),
            'is_active' => $request->boolean('is_active', true),
            'is_default' => $request->boolean('is_default', false),
            'updated_by' => auth()->id(),
        ];

        if ($request->hasFile('signature')) {
            if ($bursarySignature->signature_path) {
                Storage::delete($bursarySignature->signature_path);
            }

            $data['signature_path'] = $request->file('signature')->store('bursary-signatures/' . auth()->user()->school_id);
        }

        $bursarySignature->update($data);

        if ($bursarySignature->is_default) {
            $this->makeDefault($bursarySignature);
        }

        return back()->with('success', 'Signature updated.');
    }

    public function destroy(BursarySignature $bursarySignature)
    {
        $this->authorizeFinanceUser();

        if ($bursarySignature->signature_path) {
            Storage::delete($bursarySignature->signature_path);
        }

        $bursarySignature->delete();

        return back()->with('success', 'Signature removed.');
    }

    public function setDefault(BursarySignature $bursarySignature)
    {
        $this->authorizeFinanceUser();

        $this->makeDefault($bursarySignature);

        return back()->with('success', 'Default signature updated for ' . $bursarySignature->signature_role_label . '.');
    }

    protected function makeDefault(BursarySignature $bursarySignature): void
    {
        BursarySignature::query()
            ->where('school_id', (int) $bursarySignature->school_id)
            ->where('signature_role', (string) $bursarySignature->signature_role)
            ->where('id', '!=', (int) $bursarySignature->id)
            ->update(['is_default' => false]);

        $bursarySignature->update([
            'is_default' => true,
            'is_active' => true,
            'updated_by' => auth()->id(),
        ]);
    }
}
