<?php

namespace Ridouchire\WebhookTelegramProxy\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ridouchire\WebhookTelegramProxy\GrafanaAlertService;

final class AlertReceiver
{
    public function __construct(
        private GrafanaAlertService $grafana_alert_service
    ) {
    }

    public function __invoke(Request $request, Response $response): Response
    {
        if ($request->getHeaderLine('Content-type') !== 'application/json') {
            $response = $response->withStatus(400);

            return $response;
        }

        $data = (string) $request->getBody();

        $payload = json_decode($data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $response = $response->withStatus(500);

            return $response;
        }

        /**
         * @phpstan-ignore argument.type
         */
        $this->grafana_alert_service->handle($payload);

        $response = $response->withStatus(200);

        return $response;
    }
}
