<?php

namespace Ridouchire\WebhookTelegramProxy\NotificationProviders;

use Monolog\Logger;
use Ridouchire\WebhookTelegramProxy\NotificationProvider;

class DummyProvider implements NotificationProvider
{
    public function __construct(
        private Logger $logger
    ) {
    }

    public function send(string $message): void
    {
        $this->logger->info(self::class . ': отправлено сообщение ' . $message);
    }
}
