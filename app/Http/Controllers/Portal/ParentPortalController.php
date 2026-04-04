<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Result;
use App\Support\PublicPageContent;

class ParentPortalController extends Controller
{
    public function dashboard()
    {
        $parent = auth()->user()->parentProfile;
        $children = $parent->children()->with(['schoolClass', 'arm'])->get();

        $childrenData = $children->map(function ($child) {
            return [
                'student' => $child,
                'latest_results' => Result::where('student_id', $child->id)->where('is_approved', true)->latest()->take(5)->get(),
                'outstanding_fees' => Invoice::where('student_id', $child->id)->where('status', '!=', 'paid')->sum('balance'),
            ];
        });

        $publicPage = PublicPageContent::forSchool(auth()->user()->school);

        return view('portal.parent.dashboard', compact('parent', 'childrenData', 'publicPage'));
    }
}
