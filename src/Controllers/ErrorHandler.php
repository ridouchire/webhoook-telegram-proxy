<?php

namespace Ridouchire\WebhookTelegramProxy\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Throwable;

class ErrorHandler
{
    public function __construct(
        private App $app
    )
    {

            }

    public function __invoke(Request $request, Throwable $exception): Response
    {
        $payload = [
            'type'  => get_class($exception),
            'error' => $exception->getMessage(),
            'file'  => $exception->getFile(),
            'line'  => $exception->getLine(),
            'trace' => explode(PHP_EOL, $exception->getTraceAsString())
        ];

        $response  = $this->app->getResponseFactory()->createResponse();

        $response->getBody()->write(
            json_encode($payload, JSON_UNESCAPED_UNICODE)
        );

        return $response;
    }
}
