<?php

namespace Ridouchire\WebhookTelegramProxy;

class NotificationMaster
{
    public function __construct(
        /** @var NotificationProvider[] */
        private array $providers = []
    ) {
    }

    public function register(NotificationProvider $notification_provider): void
    {
        $this->providers[] = $notification_provider;
    }

    public function send(string $message): void
    {
        /** @var NotificationProvider $provider */
        foreach ($this->providers as $provider) {
            $provider->send($message);
        }
    }
}
