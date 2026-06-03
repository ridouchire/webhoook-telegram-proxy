<?php

namespace Ridouchire\WebhookTelegramProxy;

use Ridouchire\WebhookTelegramProxy\Event;
use Ridouchire\WebhookTelegramProxy\MessageFormatter;
use RuntimeException;

class EventFormatter
{
    /**
     * @param array<string, MessageFormatter> $map
     */
    public function __construct(
        private array $map = []
    ) {
    }

    public function register(MessageFormatter $formatter): void
    {
        $this->map[$formatter->getType()->name] = $formatter;
    }

    public function format(Event $event): string
    {
        if (isset($this->map[$event->type->name])) {
            return $this->map[$event->type->name]->format($event->content);
        }

        throw new RuntimeException();
    }
}
