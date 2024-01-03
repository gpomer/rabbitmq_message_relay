$payload = [
    'action' => 'updateitem',
    'identifier' => $identifier
];

$useRabbitMq = env('USE_RABBITMQ', false);

if ($useRabbitMq) {
    $syncResult = MessageRouterHelper::rabbitMQRelay("cachebuster", $payload);
} else {
    $syncResult = MessageRouterHelper::webhookRelay("cachebuster", $payload);
}

return $syncResult