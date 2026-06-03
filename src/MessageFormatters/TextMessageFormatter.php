<?php

namespace Ridouchire\WebhookTelegramProxy\MessageFormatters;

use Ridouchire\WebhookTelegramProxy\EventType;
use Ridouchire\WebhookTelegramProxy\MessageFormatter;

class TextMessageFormatter implements MessageFormatter
{
    /**
     * @param array{"message": string} $content
     */
    public function format(array $content): string
    {
        return $content['message'];
    }

    public function getType(): EventType
    {
        return EventType::TEXT_MESSAGE;
    }
}
