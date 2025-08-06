<?php

$clientConnector = new \Budgetcontrol\Connector\Factory\MicroserviceClient(
    [
        'mailer' => env('MAILER_SERVICE_URL', 'http://budgetcontrol-ms-notification'),
        'notification' => env('NOTIFICATION_SERVICE_URL', 'http://budgetcontrol-ms-notification'),
        ],
        env('SECRET_KEY')
);