<?php

namespace Ridouchire\WebhookTelegramProxy\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ridouchire\WebhookTelegramProxy\TextMessageService;

final class TextMessageReceiver
{
    public function __construct(
        private TextMessageService $text_message_service
    ) {
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $params = (array) $request->getParsedBody();

        if (!isset($params['message'])) {
            $response = $response->withStatus(400);

            return $response;
        }

        /**
         * @phpstan-ignore argument.type
         */
        $this->text_message_service->handle($params['message']);

        return $response;
    }
}
