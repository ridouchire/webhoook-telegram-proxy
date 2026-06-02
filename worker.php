<?php

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use React\EventLoop\Loop;
use Ridouchire\WebhookTelegramProxy\EventQueue\JsonFiledir;
use Ridouchire\WebhookTelegramProxy\EventType;
use Ridouchire\WebhookTelegramProxy\NotificationMaster;
use Ridouchire\WebhookTelegramProxy\NotificationProviders\TelegramProvider;

require_once __DIR__ . '/vendor/autoload.php';

$queue = new JsonFiledir(__DIR__ . DIRECTORY_SEPARATOR . 'queue');

/** @var string */
$telegram_api_token = getenv('TELEGRAM_API_TOKEN');

/** @var string */
$telegram_chat_id = getenv('TELEGRAM_CHAT_ID');

$logger = new Logger('ridouchire-webhook-worker');
$logger->pushHandler(new StreamHandler('php://stdout', Level::Debug));
$logger->info('Запуск');

$notification_master = new NotificationMaster();
$notification_master->attach(new TelegramProvider($telegram_chat_id, $telegram_api_token));

Loop::addPeriodicTimer(1, function ()  use ($queue, $logger, $notification_master) {
    $logger->debug('Пытаюсь получить событие из очереди');

    $event = $queue->consumeOne();

    if ($event === null) {
        $logger->debug('Нет события');

        return;
    }

    $logger->info("Обрабатываю событие #{$event->id}");

    switch($event->type) {
        case EventType::TEXT_MESSAGE:
            /**
             * @phpstan-ignore argument.type
             */
            $notification_master->send($event->content['message']);

            $queue->commit($event);
            $logger->debug('Фиксирую обработку события в очередь');

            break;
        case EventType::GRAFANA_ALERT:
            $message = "
Alert: {$event->content['name']}
Status: {$event->content['status']}

Summary: {$event->content['summary']}
";

            $notification_master->send($message);

            $queue->commit($event);
            $logger->debug('Фиксирую обработку события в очередь');

            break;
        default:
            $logger->error("Неизвестный тип события: {$event->type->name}");
            throw new RuntimeException();
    }
});
