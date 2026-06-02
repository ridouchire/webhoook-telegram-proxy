<?php

namespace Ridouchire\WebhookTelegramProxy;

use InvalidArgumentException;

enum EventType
{
    case GRAFANA_ALERT;
    case TEXT_MESSAGE;

    public static function fromString(string $value): self
    {
        return match ($value) {
            self::GRAFANA_ALERT->name => self::GRAFANA_ALERT,
            self::TEXT_MESSAGE->name  => self::TEXT_MESSAGE,
            default                   => throw new InvalidArgumentException()
        };
    }
}
