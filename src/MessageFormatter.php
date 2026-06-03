<?php

namespace Ridouchire\WebhookTelegramProxy;

use Ridouchire\WebhookTelegramProxy\EventType;

interface MessageFormatter
{
    /** @phpstan-ignore missingType.iterableValue */
    public function format(array $content): string;
    public function getType(): EventType;
}
