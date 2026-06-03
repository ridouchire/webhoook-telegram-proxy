<?php

namespace Ridouchire\WebhookTelegramProxy\MessageFormatters;

use Ridouchire\WebhookTelegramProxy\EventType;
use Ridouchire\WebhookTelegramProxy\MessageFormatter;

class GrafanaAlertMessageFormatter implements MessageFormatter
{
    /**
     * @param array{
     *     "name": string,
     *     "status": string,
     *     "summary": string,
     * } $content
     */
    public function format(array $content): string
    {
        $name    = $content['name'];
        $status  = $content['status'];
        $summary = $content['summary'];

        $message = "
Alert: {$name}
Status: {$status}

Summary: {$summary}
";

        return $message;
    }

    public function getType(): EventType
    {
        return EventType::GRAFANA_ALERT;
    }
}
