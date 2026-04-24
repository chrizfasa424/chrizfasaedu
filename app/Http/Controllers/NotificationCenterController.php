<?php

namespace App\Http\Controllers;

use App\Notifications\StudentAssignmentReviewedNotification;
use App\Notifications\StudentAssignmentSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;

class NotificationCenterController extends Controller
{
    public function index(Request $request)
    {
        $user = $this->currentUser();
        abort_unless($user, 403, 'Unauthorized.');

        $roleValue = (string) ($user->role?->value ?? $user->role ?? '');
        $canManageResultFeedbackInbox = ($user->isSuperAdmin() || $user->isSchoolAdmin() || $user->isTeacher())
            || in_array($roleValue, ['principal', 'vice_principal'], true);

        $quickLinks = [];

        if ($user->isStudent()) {
            $quickLinks[] = [
                'label' => 'Inbox Messages',
                'description' => 'Unread messages sent to you.',
                'count' => $user->unreadMessagesCount(),
                'route' => route('portal.messages.index'),
            ];
            $quickLinks[] = [
                'label' => 'Assignment Reviews',
                'description' => 'Teacher reviews on your assignments.',
                'count' => $user->unreadNotifications()
                    ->where('type', StudentAssignmentReviewedNotification::class)
                    ->count(),
                'route' => route('portal.assignments'),
            ];
            $quickLinks[] = [
                'label' => 'Result Feedback',
                'description' => 'Unread responses on your result queries.',
                'count' => $user->unreadResultFeedbackResponsesCount(),
                'route' => route('portal.results.feedback.index'),
            ];
        } elseif ($user->isParent()) {
            $quickLinks[] = [
                'label' => 'Inbox Messages',
                'description' => 'Unread messages from school.',
                'count' => $user->unreadMessagesCount(),
                'route' => route('portal.messages.index'),
            ];
        } else {
            $quickLinks[] = [
                'label' => 'Portal Replies',
                'description' => 'Unread replies from students and parents.',
                'count' => $user->unreadAdminRepliesCount(),
                'route' => route('messages.index'),
            ];
            $quickLinks[] = [
                'label' => 'Assignment Submissions',
                'description' => 'New assignment submissions to review.',
                'count' => $user->unreadNotifications()
                    ->where('type', StudentAssignmentSubmittedNotification::class)
                    ->count(),
                'route' => route('academic.assignments.index'),
            ];

            if ($canManageResultFeedbackInbox) {
                $quickLinks[] = [
                    'label' => 'Result Feedback',
                    'description' => 'Open result feedback/query items.',
                    'count' => $user->openResultFeedbackCount(),
                    'route' => route('examination.result-feedback.index'),
                ];
            }
        }

        $bellCount = (int) collect($quickLinks)->sum('count');

        $recentNotifications = $user->notifications()
            ->latest()
            ->limit(25)
            ->get()
            ->map(function (DatabaseNotification $notification) use ($user) {
                return [
                    'id' => (string) $notification->id,
                    'title' => $this->notificationTitle((string) $notification->type),
                    'message' => (string) data_get($notification->data, 'message', 'You have a new notification.'),
                    'target_route' => $this->resolveRoute((string) data_get($notification->data, 'route', ''), $user),
                    'is_unread' => is_null($notification->read_at),
                    'created_at' => $notification->created_at,
                ];
            });

        return view('notifications.index', [
            'quickLinks' => $quickLinks,
            'bellCount' => $bellCount,
            'recentNotifications' => $recentNotifications,
            'databaseUnreadCount' => $user->unreadNotifications()->count(),
        ]);
    }

    public function open(Request $request, string $notification)
    {
        $user = $this->currentUser();
        abort_unless($user, 403, 'Unauthorized.');

        $entry = $user->notifications()->where('id', $notification)->firstOrFail();

        if (is_null($entry->read_at)) {
            $entry->markAsRead();
        }

        $target = $this->resolveRoute((string) data_get($entry->data, 'route', ''), $user);

        return redirect()->to($target);
    }

    public function markAllRead(Request $request)
    {
        $user = $this->currentUser();
        abort_unless($user, 403, 'Unauthorized.');

        $user->unreadNotifications()->update(['read_at' => now()]);

        return redirect()
            ->route('notifications.index')
            ->with('success', 'All notifications marked as read.');
    }

    private function currentUser()
    {
        return auth('portal')->user() ?? auth()->user();
    }

    private function notificationTitle(string $type): string
    {
        return match ($type) {
            StudentAssignmentSubmittedNotification::class => 'Assignment Submission',
            StudentAssignmentReviewedNotification::class => 'Assignment Review',
            default => Str::headline(class_basename($type)),
        };
    }

    private function resolveRoute(string $route, $user): string
    {
        $route = trim($route);

        if ($route !== '') {
            if (str_starts_with($route, '/') && !str_starts_with($route, '//')) {
                return $route;
            }

            $parts = parse_url($route);
            $host = strtolower((string) ($parts['host'] ?? ''));
            $scheme = strtolower((string) ($parts['scheme'] ?? ''));
            $currentHost = strtolower((string) request()->getHost());

            if ($host !== '' && in_array($scheme, ['http', 'https'], true) && $host === $currentHost) {
                $path = (string) ($parts['path'] ?? '/');
                if (!str_starts_with($path, '/') || str_starts_with($path, '//')) {
                    $path = '/';
                }

                $query = isset($parts['query']) ? '?' . $parts['query'] : '';
                $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';

                return $path . $query . $fragment;
            }
        }

        return ($user->isStudent() || $user->isParent())
            ? route('portal.messages.index')
            : route('messages.index');
    }
}
