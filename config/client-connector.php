<?php

$clientConnector = new \Budgetcontrol\Connector\Factory\MicroserviceClient(
    $logger,
    [
        'mailer' => env('MAILER_SERVICE_URL', 'http://budgetcontrol-ms-notification'),
        'notification' => env('NOTIFICATION_SERVICE_URL', 'http://budgetcontrol-ms-notification'),
    ]
);