<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function send(string $to, string $message): bool
    {
        $driver = config('services.sms.driver', 'termii');

        return match($driver) {
            'termii' => $this->sendViaTermii($to, $message),
            'twilio' => $this->sendViaTwilio($to, $message),
            default => false,
        };
    }

    public function sendBulk(array $recipients, string $message): int
    {
        $sent = 0;
        foreach ($recipients as $phone) {
            if ($this->send($phone, $message)) $sent++;
        }
        return $sent;
    }

    protected function sendViaTermii(string $to, string $message): bool
    {
        try {
            $response = Http::post(config('services.termii.base_url') . '/api/sms/send', [
                'to' => $to,
                'from' => config('services.termii.sender_id'),
                'sms' => $message,
                'type' => 'plain',
                'channel' => 'generic',
                'api_key' => config('services.termii.api_key'),
            ]);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Termii SMS failed: ' . $e->getMessage());
            return false;
        }
    }

    protected function sendViaTwilio(string $to, string $message): bool
    {
        try {
            $response = Http::withBasicAuth(
                config('services.twilio.sid'),
                config('services.twilio.auth_token')
            )->asForm()->post(
                "https://api.twilio.com/2010-04-01/Accounts/" . config('services.twilio.sid') . "/Messages.json",
                [
                    'To' => $to,
                    'From' => config('services.twilio.from'),
                    'Body' => $message,
                ]
            );
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Twilio SMS failed: ' . $e->getMessage());
            return false;
        }
    }
}
