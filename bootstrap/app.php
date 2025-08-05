<?php
// Autoload Composer dependencies

use \Illuminate\Support\Carbon as Date;
use Illuminate\Support\Facades\Facade;
use Monolog\Level;

require_once __DIR__ . '/../vendor/autoload.php';

// Set up your application configuration
// Initialize slim application
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Crea un'istanza del gestore del database (Capsule)
$capsule = new \Illuminate\Database\Capsule\Manager();

// Aggiungi la configurazione del database al Capsule
$connections = require_once __DIR__.'/../config/database.php';

$dbConnection = env('DB_CONNECTION');;
$capsule->addConnection($connections[$dbConnection]);

// Esegui il boot del Capsule
$capsule->bootEloquent();
$capsule->setAsGlobal();

// Set up the logger
require_once __DIR__ . '/../config/logger.php';

/** mail configuration */
require_once __DIR__ . '/../config/mail.php';

// Set up the client connector
require_once __DIR__ . '/../config/client-connector.php';

Facade::setFacadeApplication([
    'log' => $logger,
    'date' => new Date(),
    'mail' => $mail,
    'client' => $clientConnector
]);
