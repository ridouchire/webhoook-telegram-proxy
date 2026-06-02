<?php

namespace Ridouchire\WebhookTelegramProxy;

interface NotificationProvider
{
    /** @phpstan-ignore missingType.return */
    public function send(string $message);
}
