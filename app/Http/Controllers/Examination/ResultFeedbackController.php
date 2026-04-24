<?php

namespace App\Http\Controllers\Examination;

use App\Http\Controllers\Controller;
use App\Models\AcademicTerm;
use App\Models\ExamType;
use App\Models\StudentResultFeedback;
use Illuminate\Http\Request;

class ResultFeedbackController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = (int) $request->user()->school_id;

        $filters = $request->validate([
            'status' => ['nullable', 'in:open,in_review,resolved,closed'],
            'feedback_type' => ['nullable', 'in:feedback,query'],
            'term_id' => ['nullable', 'integer', 'exists:academic_terms,id'],
            'exam_type_id' => ['nullable', 'integer', 'exists:exam_types,id'],
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $query = StudentResultFeedback::query()
            ->with([
                'student:id,first_name,last_name,admission_number,registration_number,class_id,arm_id',
                'student.schoolClass:id,name,grade_level',
                'student.arm:id,name',
                'term:id,name,session_id',
                'term.session:id,name',
                'examType:id,name',
                'subject:id,name',
                'responder:id,first_name,last_name',
            ])
            ->where('school_id', $schoolId)
            ->when(!empty($filters['status']), fn ($q) => $q->where('status', (string) $filters['status']))
            ->when(!empty($filters['feedback_type']), fn ($q) => $q->where('feedback_type', (string) $filters['feedback_type']))
            ->when(!empty($filters['term_id']), fn ($q) => $q->where('term_id', (int) $filters['term_id']))
            ->when(!empty($filters['exam_type_id']), fn ($q) => $q->where('exam_type_id', (int) $filters['exam_type_id']))
            ->when(!empty($filters['q']), function ($q) use ($filters) {
                $needle = trim((string) $filters['q']);
                $q->where(function ($inner) use ($needle) {
                    $inner->where('title', 'like', '%' . $needle . '%')
                        ->orWhere('message', 'like', '%' . $needle . '%')
                        ->orWhereHas('student', function ($studentQuery) use ($needle) {
                            $studentQuery->where('first_name', 'like', '%' . $needle . '%')
                                ->orWhere('last_name', 'like', '%' . $needle . '%')
                                ->orWhere('admission_number', 'like', '%' . $needle . '%')
                                ->orWhere('registration_number', 'like', '%' . $needle . '%');
                        });
                });
            })
            ->orderByRaw("status = 'open' desc")
            ->orderByRaw("status = 'in_review' desc")
            ->latest();

        $feedbacks = $query->paginate(20)->withQueryString();

        $statusSummary = [
            'open' => StudentResultFeedback::query()->where('school_id', $schoolId)->where('status', 'open')->count(),
            'in_review' => StudentResultFeedback::query()->where('school_id', $schoolId)->where('status', 'in_review')->count(),
            'resolved' => StudentResultFeedback::query()->where('school_id', $schoolId)->where('status', 'resolved')->count(),
            'closed' => StudentResultFeedback::query()->where('school_id', $schoolId)->where('status', 'closed')->count(),
        ];

        $terms = AcademicTerm::query()
            ->where('school_id', $schoolId)
            ->with('session')
            ->orderByDesc('id')
            ->get();

        $examTypes = ExamType::query()
            ->where('school_id', $schoolId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('examination.result-feedback.index', [
            'feedbacks' => $feedbacks,
            'filters' => $filters,
            'statusSummary' => $statusSummary,
            'terms' => $terms,
            'examTypes' => $examTypes,
        ]);
    }

    public function update(Request $request, StudentResultFeedback $feedback)
    {
        abort_unless((int) $feedback->school_id === (int) $request->user()->school_id, 404);

        $validated = $request->validate([
            'status' => ['required', 'in:open,in_review,resolved,closed'],
            'admin_response' => ['nullable', 'string', 'max:3000'],
        ]);

        $responseText = $request->filled('admin_response')
            ? trim((string) $validated['admin_response'])
            : null;
        $existingResponse = trim((string) ($feedback->admin_response ?? ''));

        $payload = [
            'status' => (string) $validated['status'],
            'admin_response' => $responseText,
            'responded_by' => (int) $request->user()->id,
            'responded_at' => now(),
        ];

        if ($responseText !== null && $responseText !== '' && $responseText !== $existingResponse) {
            // New or updated admin response should appear as unread to the student.
            $payload['student_read_at'] = null;
        }

        $feedback->update($payload);

        return back()->with('success', 'Result feedback updated successfully.');
    }

    public function destroy(Request $request, StudentResultFeedback $feedback)
    {
        abort_unless((int) $feedback->school_id === (int) $request->user()->school_id, 404);

        $feedback->delete();

        return back()->with('success', 'Result feedback deleted successfully.');
    }
}
