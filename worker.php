<?php

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use React\EventLoop\Loop;
use Ridouchire\WebhookTelegramProxy\EventFormatter;
use Ridouchire\WebhookTelegramProxy\EventQueue\JsonFiledir;
use Ridouchire\WebhookTelegramProxy\MessageFormatters\GrafanaAlertMessageFormatter;
use Ridouchire\WebhookTelegramProxy\MessageFormatters\TextMessageFormatter;
use Ridouchire\WebhookTelegramProxy\NotificationMaster;
use Ridouchire\WebhookTelegramProxy\NotificationProviders\TelegramProvider;
use Ridouchire\WebhookTelegramProxy\WorkerHandler;

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
$notification_master->register(new TelegramProvider($telegram_chat_id, $telegram_api_token));

$event_formatter = new EventFormatter();
$event_formatter->register(new TextMessageFormatter());
$event_formatter->register(new GrafanaAlertMessageFormatter());

$worker_handler = new WorkerHandler($queue, $logger, $notification_master, $event_formatter);

Loop::addPeriodicTimer(1, $worker_handler);
