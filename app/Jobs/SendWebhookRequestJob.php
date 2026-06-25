<?php

namespace App\Jobs;

use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class SendWebhookRequestJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public Webhook $webhook,
        public string $event,
        public array $payload,
    ) {}

    public function handle(): void
    {
        $response = Http::timeout(10)->post($this->webhook->url, [
            'event'   => $this->event,
            'payload' => $this->payload,
        ]);

        WebhookDelivery::create([
            'webhook_id'      => $this->webhook->id,
            'event'           => $this->event,
            'payload'         => $this->payload,
            'response_status' => $response->status(),
            'success'         => $response->successful(),
            'delivered_at'    => now(),
        ]);
    }
}
