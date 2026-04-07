<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPasswordNotification extends ResetPassword
{
    /**
     * Build the reset URL.
     * Students/parents use /portal/reset-password, everyone else uses /admin-access/reset-password.
     */
    protected function resetUrl($notifiable): string
    {
        $isPortalUser = in_array($notifiable->role?->value ?? '', ['student', 'parent']);

        $routeName = $isPortalUser ? 'portal.password.reset' : 'admin.password.reset';

        return url(route($routeName, [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }

    public function toMail($notifiable): MailMessage
    {
        $isPortalUser = in_array($notifiable->role?->value ?? '', ['student', 'parent']);
        $loginRoute   = $isPortalUser ? route('portal.login') : route('login');
        $resetUrl     = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('Reset Your Password — ' . config('app.name'))
            ->greeting('Hello ' . ($notifiable->first_name ?? '') . ',')
            ->line('You requested a password reset for your ' . config('app.name') . ' account.')
            ->action('Reset Password', $resetUrl)
            ->line('This link expires in ' . config('auth.passwords.users.expire', 60) . ' minutes.')
            ->line('If you did not request this, no action is needed.')
            ->salutation('— ' . config('app.name') . ' Team');
    }
}
