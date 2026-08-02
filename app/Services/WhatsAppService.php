<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public static function configured(): bool
    {
        return (bool) (config('services.whatsapp.token') && config('services.whatsapp.phone_number_id'));
    }

    public function sendTemplate(
        string $to,
        string $template,
        array $params = []
    ): void {
        if (!self::configured()) {
            \Illuminate\Support\Facades\Log::info("WhatsApp not configured — skipped '{$template}' to {$to}", $params);
            return;
        }

        Http::withToken(config('services.whatsapp.token'))
            ->post(
                'https://graph.facebook.com/v18.0/' .
                config('services.whatsapp.phone_number_id') .
                '/messages',
                [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'template',
                    'template' => [
                        'name' => $template,
                        'language' => ['code' => 'en'],
                        'components' => [
                            [
                                'type' => 'body',
                                'parameters' => collect($params)->map(fn ($p) => [
                                    'type' => 'text',
                                    'text' => $p,
                                ])->toArray(),
                            ],
                        ],
                    ],
                ]
            );
    }
}
