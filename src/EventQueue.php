<?php

namespace Ridouchire\WebhookTelegramProxy;

use Ridouchire\WebhookTelegramProxy\Event;

interface EventQueue
{
    public function publish(Event $event): void;

    /**
     * @return Event[]
     */
    public function consume(): array;
    public function consumeOne(): ?Event;

    public function commit(Event $event): void;
}
