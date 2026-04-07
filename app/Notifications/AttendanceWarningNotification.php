<?php

namespace App\Notifications;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttendanceWarningNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Student $student,
        public int     $absentCount,
        public string  $schoolName,
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Attendance Warning — {$this->student->full_name}")
            ->greeting("Dear {$notifiable->first_name},")
            ->line("This is an important notice from **{$this->schoolName}**.")
            ->line("**{$this->student->full_name}** has been absent **{$this->absentCount} times** this term.")
            ->line('Consistent attendance is critical for academic success. Please ensure the student attends school regularly.')
            ->line('If there are circumstances affecting attendance, please contact the school as soon as possible.')
            ->action('View Attendance', url('/portal'))
            ->salutation("Regards,\n{$this->schoolName}");
    }

    public function toArray($notifiable): array
    {
        return [
            'type'          => 'attendance_warning',
            'student_id'    => $this->student->id,
            'student_name'  => $this->student->full_name,
            'absent_count'  => $this->absentCount,
            'message'       => "{$this->student->full_name} has been absent {$this->absentCount} times this term.",
        ];
    }
}
