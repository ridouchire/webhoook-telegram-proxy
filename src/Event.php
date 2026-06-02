<?php

namespace Ridouchire\WebhookTelegramProxy;

use JsonSerializable;
use Ridouchire\WebhookTelegramProxy\EventType;

readonly class Event implements JsonSerializable
{
    /**
     * @phpstan-ignore missingType.iterableValue
     */
    private function __construct(
        public string $id,
        public EventType $type,
        public int $timestamp,
        public array $content = [],
    ) {
    }

    /**
     * @phpstan-ignore missingType.iterableValue
     */
    public static function draft(EventType $type, array $content = []): self
    {
        return new self(
            uniqid(),
            $type,
            time(),
            $content
        );
    }

    /**
     * @phpstan-ignore missingType.iterableValue
     */
    public static function fromArray(array $state): self
    {
        return new self(
            /**
             * @phpstan-ignore argument.type
             */
            $state['id'],
            /**
             * @phpstan-ignore argument.type
             */
            EventType::fromString($state['type']),
            /**
             * @phpstan-ignore argument.type
             */
            $state['timestamp'],
            /**
             * @phpstan-ignore argument.type
             */
            $state['content']
        );
    }

    /**
     * @phpstan-ignore missingType.iterableValue
     */
    public function jsonSerialize(): array
    {
        return [
            'id'        => $this->id,
            'type'      => $this->type->name,
            'timestamp' => $this->timestamp,
            'content'   => $this->content
        ];
    }
}
