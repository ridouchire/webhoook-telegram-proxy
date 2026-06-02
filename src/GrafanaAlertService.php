<?php

namespace Ridouchire\WebhookTelegramProxy;

use Ridouchire\WebhookTelegramProxy\Event;
use Ridouchire\WebhookTelegramProxy\EventQueue;
use Ridouchire\WebhookTelegramProxy\EventType;

class GrafanaAlertService
{
    public function __construct(
        private EventQueue $event_queue
    ) {
    }

    /** @phpstan-ignore missingType.iterableValue */
    public function handle(array $data): void
    {
        /** @phpstan-ignore foreach.nonIterable */
        foreach ($data['alerts'] as $alert_data) {

            /** @phpstan-ignore offsetAccess.nonOffsetAccessible */
            $status = $alert_data['status'];

            /** @phpstan-ignore offsetAccess.nonOffsetAccessible,offsetAccess.nonOffsetAccessible */
            $name = $alert_data['labels']['alertname'];

            /** @phpstan-ignore offsetAccess.nonOffsetAccessible,offsetAccess.nonOffsetAccessible */
            $summary = $alert_data['annotations']['summary'];

            $event = Event::draft(EventType::GRAFANA_ALERT, [
                'status'  => $status,
                'name'    => $name,
                'summary' => $summary
            ]);

            $this->event_queue->publish($event);
        }
    }
}
