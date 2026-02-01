<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public function sendTemplate(
        string $to,
        string $template,
        array $params = []
    ): void {
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
