<?php

namespace Ridouchire\WebhookTelegramProxy\NotificationProviders;

use Ridouchire\WebhookTelegramProxy\NotificationProvider;
use TelegramBot\Api\BotApi as TelegramBotApi;

class TelegramProvider implements NotificationProvider
{
    private TelegramBotApi $bot_api;

    public function __construct(
        private string $chat_id,
        string $bot_token,
    ) {
        $this->bot_api = new TelegramBotApi($bot_token);
        $this->bot_api->setCurlOption(CURLOPT_TIMEOUT, 10);
    }

    public function send(string $message): void
    {
        $this->bot_api->sendMessage($this->chat_id, $message);
    }
}
