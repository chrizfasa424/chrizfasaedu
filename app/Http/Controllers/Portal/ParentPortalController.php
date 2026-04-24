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
        $data = $this->buildParentPageData();

        return view('portal.parent.dashboard', $data);
    }

    public function academicOverview()
    {
        $data = $this->buildParentPageData();

        return view('portal.parent.academic-overview', $data);
    }

    public function resultsGrades()
    {
        $data = $this->buildParentPageData();

        return view('portal.parent.results-grades', $data);
    }

    public function feesSummary()
    {
        $data = $this->buildParentPageData();

        return view('portal.parent.fees-summary', $data);
    }

    private function buildParentPageData(): array
    {
        $user = auth()->user();
        $parent = $user->parentProfile;
        $children = $parent ? $parent->children()->with(['schoolClass', 'arm'])->get() : collect();

        $childrenData = $children->map(function ($child) {
            return [
                'student' => $child,
                'latest_results' => Result::where('student_id', $child->id)
                    ->where('is_approved', true)
                    ->with('subject')
                    ->latest()
                    ->take(5)
                    ->get(),
                'outstanding_fees' => Invoice::where('student_id', $child->id)
                    ->where('status', '!=', 'paid')
                    ->sum('balance'),
            ];
        });

        $publicPage = PublicPageContent::forSchool($user->school);

        return compact('parent', 'childrenData', 'publicPage');
    }
}
