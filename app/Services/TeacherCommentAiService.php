<?php

namespace App\Services;

use App\Models\StudentResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class TeacherCommentAiService
{
    public function generateForStudentResult(StudentResult $studentResult): string
    {
        $apiKey = trim((string) config('services.openai.api_key'));
        if ($apiKey === '') {
            throw new RuntimeException('OpenAI API key is not configured. Set OPENAI_API_KEY in your environment.');
        }

        $studentResult->loadMissing([
            'student',
            'schoolClass',
            'arm',
            'session',
            'term',
            'examType',
            'items.subject',
        ]);

        $baseUrl = rtrim((string) config('services.openai.base_url', 'https://api.openai.com/v1'), '/');
        $model = trim((string) config('services.openai.model', 'gpt-4o-mini'));
        $timeout = (int) config('services.openai.timeout', 30);

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout($timeout)
            ->retry(2, 300)
            ->post("{$baseUrl}/chat/completions", [
                'model' => $model,
                'temperature' => 0.6,
                'max_tokens' => 220,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an experienced school class teacher writing report-card comments. Be concise, warm, and practical.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $this->buildPrompt($studentResult),
                    ],
                ],
            ]);

        if (!$response->successful()) {
            Log::warning('OpenAI teacher comment generation failed', [
                'student_result_id' => $studentResult->id,
                'status' => $response->status(),
                'body' => Str::limit((string) $response->body(), 500),
            ]);

            throw new RuntimeException('OpenAI could not generate the teacher comment at the moment.');
        }

        $content = trim((string) data_get($response->json(), 'choices.0.message.content', ''));
        $content = $this->sanitizeComment($content);

        if ($content === '') {
            throw new RuntimeException('OpenAI returned an empty teacher comment.');
        }

        return $content;
    }

    protected function buildPrompt(StudentResult $studentResult): string
    {
        $subjectRows = $studentResult->items
            ->map(function ($item) {
                return [
                    'subject' => (string) ($item->subject?->name ?? 'Unknown'),
                    'total' => (float) ($item->total_score ?? 0),
                    'grade' => (string) ($item->grade ?? ''),
                    'remark' => (string) ($item->remark ?? ''),
                ];
            })
            ->sortByDesc('total')
            ->values();

        $topSubjects = $subjectRows->take(3)->pluck('subject')->filter()->values()->all();
        $weakSubjects = $subjectRows->sortBy('total')->take(2)->pluck('subject')->filter()->values()->all();
        $attendanceTotal = (int) ($studentResult->attendance_total ?? 0);
        $attendancePresent = (int) ($studentResult->attendance_present ?? 0);
        $attendanceRate = $attendanceTotal > 0
            ? round(($attendancePresent / $attendanceTotal) * 100, 1)
            : null;

        $performance = [
            'student_name' => (string) ($studentResult->student?->full_name ?? 'Student'),
            'class' => trim((string) (($studentResult->schoolClass?->name ?? '') . ($studentResult->arm?->name ? ' ' . $studentResult->arm?->name : ''))),
            'term' => (string) ($studentResult->term?->name ?? ''),
            'session' => (string) ($studentResult->session?->name ?? ''),
            'exam_type' => (string) ($studentResult->examType?->name ?? ''),
            'total_score' => (float) ($studentResult->total_score ?? 0),
            'average_score' => (float) ($studentResult->average_score ?? 0),
            'class_average' => $studentResult->class_average !== null ? (float) $studentResult->class_average : null,
            'class_position' => $studentResult->class_position !== null ? (int) $studentResult->class_position : null,
            'attendance' => [
                'present' => $attendancePresent,
                'total' => $attendanceTotal,
                'rate_percent' => $attendanceRate,
            ],
            'top_subjects' => $topSubjects,
            'focus_subjects' => $weakSubjects,
            'subject_breakdown' => $subjectRows->all(),
        ];

        $performanceJson = json_encode($performance, JSON_UNESCAPED_SLASHES);

        return <<<PROMPT
Write one class teacher report-card comment for this student.

Rules:
- 3 to 4 sentences only.
- Mention current performance level based on the data.
- Mention at least one strength.
- Mention one area to improve.
- End with clear follow-up advice for next term (study habit/action focus).
- Keep tone encouraging and professional.
- Do not use markdown, bullet points, or quotation marks.
- Do not mention AI, model, system, or prompt.

Student performance data:
{$performanceJson}
PROMPT;
    }

    protected function sanitizeComment(string $content): string
    {
        $clean = strip_tags($content);
        $clean = preg_replace('/\s+/u', ' ', (string) $clean);
        $clean = trim((string) $clean);
        $clean = trim($clean, "\"'`");

        return Str::limit($clean, 700, '');
    }
}
