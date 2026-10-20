<?php

declare(strict_types=1);

// A webhook endpoint as an integrator writes it, served by `php -S` in WebhookDeliveryTest: it
// verifies each delivery with Tawked\Webhook (every secret it holds, both schemes) and appends what
// it accepted to a file.

require __DIR__.'/../../vendor/autoload.php';

use Tawked\Exception\WebhookSignatureException;
use Tawked\Webhook;

$secrets = explode(',', (string) getenv('TAWKED_WEBHOOK_SECRETS'));
// Which of them this endpoint's receiver holds (the secrets choose the scheme): `?holds=tawked` its
// Tawked secret alone, `?holds=standard` its whsec_ secret alone, otherwise both.
$holds = $_GET['holds'] ?? 'both';
if ($holds !== 'both') {
    $secrets = array_values(array_filter($secrets, fn(string $s) => str_starts_with($s, 'whsec_') === ($holds === 'standard')));
}
$log = (string) getenv('TAWKED_RECEIVED_LOG');

try {
    $event = Webhook::constructEvent((string) file_get_contents('php://input'), getallheaders(), $secrets);
} catch (WebhookSignatureException $e) {
    http_response_code(400);
    file_put_contents($log, json_encode(['refused' => $e->reason(), 'message' => $e->getMessage()])."\n", FILE_APPEND);
    echo 'refused';

    return;
}

$typed = $event->typedData();
file_put_contents($log, json_encode([
    'event' => $event->event,
    'data' => $event->data,
    'test' => $event->test,
    'typed' => $typed === null ? null : $typed::class,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n", FILE_APPEND);
http_response_code(204);
