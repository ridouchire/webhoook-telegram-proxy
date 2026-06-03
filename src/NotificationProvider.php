<?php

namespace Ridouchire\WebhookTelegramProxy;

use Ridouchire\WebhookTelegramProxy\Exceptions\FailedSendMessage;

interface NotificationProvider
{
    /**
     * @throws FailedSendMessage
     */
    public function send(string $message): void;
}
