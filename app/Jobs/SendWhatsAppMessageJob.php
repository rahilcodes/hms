<?php

namespace App\Jobs;

use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $to;
    protected string $template;
    protected array $params;

    public function __construct(string $to, string $template, array $params = [])
    {
        $this->to = $to;
        $this->template = $template;
        $this->params = $params;
    }

    public function handle(WhatsAppService $service): void
    {
        $service->sendTemplate($this->to, $this->template, $this->params);
    }
}
