<?php

use Slim\Factory\AppFactory;
use Ridouchire\WebhookTelegramProxy\EventQueue\JsonFiledir;
use Ridouchire\WebhookTelegramProxy\Controllers\AlertReceiver;
use Ridouchire\WebhookTelegramProxy\Controllers\TextMessageReceiver;
use Ridouchire\WebhookTelegramProxy\GrafanaAlertService;
use Ridouchire\WebhookTelegramProxy\TextMessageService;

require_once __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

$app->add(new Tuupola\Middleware\HttpBasicAuthentication([
    'users' => [
        getenv('BASIC_AUTH_USERNAME') => getenv('BASIC_AUTH_PASSWORD')
    ]
]));

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$queue = new JsonFiledir(__DIR__ . DIRECTORY_SEPARATOR . 'queue');

$grafana_alerts_service = new GrafanaAlertService($queue);
$text_message_service   = new TextMessageService($queue);

$app->post('/webhooks/grafana', new AlertReceiver($grafana_alerts_service));
$app->post('/webhooks/text', new TextMessageReceiver($text_message_service));

$app->run();
