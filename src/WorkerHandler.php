<?php

namespace Ridouchire\WebhookTelegramProxy;

use Monolog\Logger;
use Ridouchire\WebhookTelegramProxy\Exceptions\EventQueueCommitFailed;
use Ridouchire\WebhookTelegramProxy\Exceptions\EventQueueReceiveFailed;
use Ridouchire\WebhookTelegramProxy\Exceptions\FailedSendMessage;

class WorkerHandler
{
    public function __construct(
        private EventQueue $event_queue,
        private Logger $logger,
        private NotificationMaster $notification_master,
        private EventFormatter $event_formatter
    ) {
    }

    public function __invoke(): void
    {
        $this->logger->debug('Пытаюсь получить событие из очереди');

        try {
            $event = $this->event_queue->consumeOne();
        } catch (EventQueueReceiveFailed) {
            $this->logger->error('Ошибка получения события из очереди');

            return;
        }

        if ($event === null) {
            $this->logger->debug('Нет события');

            return;
        }

        $this->logger->info("Обрабатываю событие #{$event->id}, отправляю сообщение зарегистрированным провайдерам");

        try {
            $this->notification_master->send($this->event_formatter->format($event));
        } catch (FailedSendMessage $e) {
            $this->logger->error('Ошибка отправки сообщения: ' .  $e->getMessage());

            return;
        }


        $this->logger->info('Фиксирую обработку события в очередь');

        try {
            $this->event_queue->commit($event);
        } catch (EventQueueCommitFailed) {
            $this->logger->error('Ошибка фиксирования события в очереди');

            return;
        }
    }
}
