<?php

namespace Ridouchire\WebhookTelegramProxy\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Throwable;

class ErrorHandler
{
    /** @phpstan-ignore missingType.generics */
    public function __construct(
        private App $app
    ) {
    }

    public function __invoke(Request $request, Throwable $exception): Response
    {
        $response  = $this->app->getResponseFactory()->createResponse();

        $payload = [
            'type'  => get_class($exception),
            'error' => $exception->getMessage(),
            'file'  => $exception->getFile(),
            'line'  => $exception->getLine(),
            'trace' => explode(PHP_EOL, $exception->getTraceAsString())
        ];

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        if (json_last_error() !== JSON_ERROR_NONE || $json === false) {
            $response = $response->withStatus(500);

            return $response;
        }

        $response->withHeader('Content-type', 'application/json');
        $response->getBody()->write($json);

        return $response;
    }
}
