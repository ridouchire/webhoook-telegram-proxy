<?php

namespace Ridouchire\WebhookTelegramProxy;

use Ridouchire\WebhookTelegramProxy\Event;
use Ridouchire\WebhookTelegramProxy\Exceptions\EventQueueCommitFailed;
use Ridouchire\WebhookTelegramProxy\Exceptions\EventQueuePublushFailed;
use Ridouchire\WebhookTelegramProxy\Exceptions\EventQueueReceiveFailed;

interface EventQueue
{
    /**
     * @throws EventQueuePublushFailed
     */
    public function publish(Event $event): void;

    /**
     * @throws EventQueueReceiveFailed
     *
     * @return Event[]
     */
    public function consume(): array;

    /**
     * @throws EventQueueReceiveFailed
     */
    public function consumeOne(): ?Event;

    /**
     * @throws EventQueueCommitFailed
     */
    public function commit(Event $event): void;
}
