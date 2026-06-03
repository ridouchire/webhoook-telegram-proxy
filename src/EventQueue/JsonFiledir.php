<?php

namespace Ridouchire\WebhookTelegramProxy\EventQueue;

use RuntimeException;
use Ridouchire\WebhookTelegramProxy\Event;
use Ridouchire\WebhookTelegramProxy\EventQueue;
use Ridouchire\WebhookTelegramProxy\Exceptions\EventQueueCommitFailed;
use Ridouchire\WebhookTelegramProxy\Exceptions\EventQueuePublushFailed;
use Ridouchire\WebhookTelegramProxy\Exceptions\EventQueueReceiveFailed;

class JsonFiledir implements EventQueue
{
    public function __construct(
        private string $data_dir
    ) {
    }

    public function publish(Event $event): void
    {
        $res = file_put_contents(
            $this->data_dir . DIRECTORY_SEPARATOR . $event->id . '.json',
            json_encode($event, JSON_PRETTY_PRINT)
        );

        if ($res === false) {
            throw new EventQueuePublushFailed();
        }
    }

    /**
     * @return Event[]
     */
    public function consume(): array
    {
        $events = [];

        /** @phpstan-ignore foreach.nonIterable */
        foreach (glob($this->data_dir . DIRECTORY_SEPARATOR . '*.json') as $filename) {
            $data = file_get_contents($filename);

            if ($data === false) {
                throw new RuntimeException();
            }

            /**
             * @var array
             * @phpstan-ignore missingType.iterableValue
             */
            $event_data = json_decode($data, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException(json_last_error_msg());
            }

            $events[] = Event::fromArray($event_data);
        }

        return $events;
    }

    public function consumeOne(): ?Event
    {
        $files = glob($this->data_dir . DIRECTORY_SEPARATOR . '*.json');

        if ($files === false) {
            throw new EventQueueReceiveFailed();
        }

        if (sizeof($files) == 0) {
            return null;
        }

        $filename = current($files);
        $data     = file_get_contents($filename);

        if ($data === false) {
            throw new EventQueueReceiveFailed();
        }

        /**
         * @var array
         * @phpstan-ignore missingType.iterableValue
         */
        $event_data = json_decode($data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new EventQueueReceiveFailed(json_last_error_msg());
        }

        return Event::fromArray($event_data);
    }

    public function commit(Event $event): void
    {
        $res = unlink($this->data_dir . DIRECTORY_SEPARATOR . $event->id . '.json');

        if ($res === false) {
            throw new EventQueueCommitFailed();
        }
    }
}
