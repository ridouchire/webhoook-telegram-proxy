<?php

namespace Ridouchire\WebhookTelegramProxy;

use SplObjectStorage;

class NotificationMaster
{
    /** @phpstan-ignore missingType.generics */
    private SplObjectStorage $providers;

    public function __construct()
    {
        $this->providers = new SplObjectStorage();
    }

    public function attach(NotificationProvider $notification_provider): void
    {
        $this->providers->attach($notification_provider);
    }

    public function send(string $message): void
    {
        /** @var NotificationProvider $provider */
        foreach ($this->providers as $provider) {
            $provider->send($message);
        }
    }
}
