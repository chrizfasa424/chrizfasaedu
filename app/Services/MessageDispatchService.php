<?php

namespace App\Services;

use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\Student;
use App\Models\User;
use App\Support\RichTextSanitizer;
use Illuminate\Support\Facades\DB;

class MessageDispatchService
{
    /**
     * Create a message and fan-out recipient rows.
     */
    public function send(array $validated, User $sender): Message
    {
        $schoolId = $sender->school_id;

        $message = Message::create([
            'school_id' => $schoolId,
            'sender_id' => $sender->id,
            'audience'  => $validated['audience'],
            'class_id'  => $validated['class_id'] ?? null,
            'subject'   => trim(strip_tags((string) $validated['subject'])),
            'body'      => RichTextSanitizer::sanitize((string) $validated['body']),
        ]);

        $recipientIds = $this->resolveRecipients($validated['audience'], $validated['class_id'] ?? null, $schoolId);

        $rows = $recipientIds->unique()->map(fn ($uid) => [
            'message_id' => $message->id,
            'user_id'    => $uid,
            'read_at'    => null,
            'created_at' => now(),
            'updated_at' => now(),
        ])->values()->all();

        foreach (array_chunk($rows, 500) as $chunk) {
            MessageRecipient::insert($chunk);
        }

        return $message;
    }

    private function resolveRecipients(string $audience, ?int $classId, int $schoolId): \Illuminate\Support\Collection
    {
        return match ($audience) {
            'all_students' => User::where('school_id', $schoolId)
                ->where('role', 'student')
                ->pluck('id'),

            'all_parents' => User::where('school_id', $schoolId)
                ->where('role', 'parent')
                ->pluck('id'),

            'all_portal' => User::where('school_id', $schoolId)
                ->whereIn('role', ['student', 'parent'])
                ->pluck('id'),

            'class' => $this->resolveClassRecipients($classId, $schoolId),

            default => collect(),
        };
    }

    private function resolveClassRecipients(?int $classId, int $schoolId): \Illuminate\Support\Collection
    {
        if (!$classId) {
            return collect();
        }

        // Students in the class
        $studentUserIds = Student::where('class_id', $classId)
            ->where('school_id', $schoolId)
            ->whereNotNull('user_id')
            ->pluck('user_id');

        // Parents linked to those students (via parent_student pivot → parents table → users)
        $studentIds = Student::where('class_id', $classId)
            ->where('school_id', $schoolId)
            ->pluck('id');

        $parentUserIds = DB::table('parent_student')
            ->join('parents', 'parent_student.parent_id', '=', 'parents.id')
            ->whereIn('parent_student.student_id', $studentIds)
            ->whereNotNull('parents.user_id')
            ->pluck('parents.user_id');

        return $studentUserIds->merge($parentUserIds);
    }
}
