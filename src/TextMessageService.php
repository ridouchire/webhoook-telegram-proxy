<?php

namespace Ridouchire\WebhookTelegramProxy;

use Ridouchire\WebhookTelegramProxy\EventQueue;

class TextMessageService
{
    public function __construct(
        private EventQueue $event_queue
    ) {
    }

    public function handle(string $message): void
    {
        $this->event_queue->publish(Event::draft(
            EventType::TEXT_MESSAGE,
            ['message' => $message]
        ));
    }
}
