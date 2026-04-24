<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financial\StoreSchoolBankAccountRequest;
use App\Http\Requests\Financial\UpdateSchoolBankAccountRequest;
use App\Models\SchoolBankAccount;

class BankAccountController extends Controller
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

        $accounts = SchoolBankAccount::query()->latest()->paginate(20);

        return view('financial.bank-accounts.index', compact('accounts'));
    }

    public function store(StoreSchoolBankAccountRequest $request)
    {
        $this->authorizeFinanceUser();

        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $account = SchoolBankAccount::query()->create($data);

        if ((bool) ($data['is_default'] ?? false)) {
            $this->makeDefault($account);
        }

        return back()->with('success', 'Bank account added successfully.');
    }

    public function update(UpdateSchoolBankAccountRequest $request, SchoolBankAccount $bankAccount)
    {
        $this->authorizeFinanceUser();

        $data = $request->validated();
        $data['updated_by'] = auth()->id();
        $bankAccount->update($data);

        if ((bool) ($data['is_default'] ?? false)) {
            $this->makeDefault($bankAccount);
        }

        return back()->with('success', 'Bank account updated successfully.');
    }

    public function destroy(SchoolBankAccount $bankAccount)
    {
        $this->authorizeFinanceUser();

        $bankAccount->delete();

        return back()->with('success', 'Bank account deleted.');
    }

    public function setDefault(SchoolBankAccount $bankAccount)
    {
        $this->authorizeFinanceUser();

        $this->makeDefault($bankAccount);

        return back()->with('success', 'Default bank account updated.');
    }

    protected function makeDefault(SchoolBankAccount $bankAccount): void
    {
        SchoolBankAccount::query()
            ->where('school_id', (int) $bankAccount->school_id)
            ->where('id', '!=', (int) $bankAccount->id)
            ->update(['is_default' => false]);

        $bankAccount->update([
            'is_default' => true,
            'is_active' => true,
            'updated_by' => auth()->id(),
        ]);
    }
}
