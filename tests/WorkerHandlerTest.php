<?php

use Monolog\Logger;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ridouchire\WebhookTelegramProxy\Event;
use Ridouchire\WebhookTelegramProxy\EventFormatter;
use Ridouchire\WebhookTelegramProxy\EventQueue;
use Ridouchire\WebhookTelegramProxy\EventType;
use Ridouchire\WebhookTelegramProxy\Exceptions\EventQueueCommitFailed;
use Ridouchire\WebhookTelegramProxy\Exceptions\EventQueueReceiveFailed;
use Ridouchire\WebhookTelegramProxy\Exceptions\FailedSendMessage;
use Ridouchire\WebhookTelegramProxy\NotificationMaster;
use Ridouchire\WebhookTelegramProxy\WorkerHandler;

class WorkerHandlerTest extends TestCase
{
    private MockObject|Logger $logger;
    private MockObject|EventQueue $event_queue;
    private MockObject|NotificationMaster $notification_master;
    private MockObject|EventFormatter $event_formatter;

    private WorkerHandler $worker_handler;

    public function setUp(): void
    {
        $this->logger = $this->createMock(Logger::class);

        $this->event_queue = $this->createMock(EventQueue::class);

        $this->notification_master = $this->createMock(NotificationMaster::class);

        $this->event_formatter = $this->createMock(EventFormatter::class);

        $this->worker_handler = new WorkerHandler(
            $this->event_queue,
            $this->logger,
            $this->notification_master,
            $this->event_formatter
        );
    }

    #[Test]
    public function testFailedReceiveEventFromQueue(): void
    {
        $this->event_queue
            ->expects($this->once())
            ->method('consumeOne')
            ->willThrowException(new EventQueueReceiveFailed());

        $this->logger
            ->expects($this->once())
            ->method('error');

        $this->event_formatter->expects($this->never())->method('format');
        $this->notification_master->expects($this->never())->method('send');

        $this->worker_handler->__invoke();
    }

    #[Test]
    public function testWhenEventNotFound(): void
    {
        $this->event_queue
            ->expects($this->once())
            ->method('consumeOne')
            ->willReturn(null);

        $this->logger
            ->expects($this->exactly(2))
            ->method('debug');

        $this->event_formatter->expects($this->never())->method('format');
        $this->notification_master->expects($this->never())->method('send');

        $this->worker_handler->__invoke();
    }

    #[Test]
    public function testFailedSendMessage(): void
    {
        $this->event_queue
            ->expects($this->once())
            ->method('consumeOne')
            ->willReturn(Event::draft(EventType::GRAFANA_ALERT));

        $this->logger
            ->expects($this->once())
            ->method('info');

        $this->notification_master
            ->expects($this->once())
            ->method('send')
            ->willThrowException(new FailedSendMessage());

        $this->event_formatter
            ->expects($this->once())
            ->method('format')
            ->willReturn('test');

        $this->worker_handler->__invoke();
    }

    #[Test]
    public function testFailedEventCommitToQueue(): void
    {
        $this->event_queue
            ->expects($this->once())
            ->method('consumeOne')
            ->willReturn(Event::draft(EventType::GRAFANA_ALERT));

        $this->event_queue
            ->expects($this->once())
            ->method('commit')
            ->willThrowException(new EventQueueCommitFailed());

        $this->logger
            ->expects($this->exactly(2))
            ->method('info');

        $this->logger
            ->expects($this->once())
            ->method('error');

        $this->notification_master
            ->expects($this->once())
            ->method('send');

        $this->event_formatter
            ->expects($this->once())
            ->method('format')
            ->willReturn('test');

        $this->worker_handler->__invoke();
    }

    #[Test]
    public function testSuccessfully(): void
    {
        $this->event_queue
            ->expects($this->once())
            ->method('consumeOne')
            ->willReturn(Event::draft(EventType::GRAFANA_ALERT));

        $this->event_queue
            ->expects($this->once())
            ->method('commit');

        $this->logger
            ->expects($this->exactly(2))
            ->method('info');

        $this->notification_master
            ->expects($this->once())
            ->method('send');

        $this->event_formatter
            ->expects($this->once())
            ->method('format')
            ->willReturn('test');

        $this->worker_handler->__invoke();
    }
}
