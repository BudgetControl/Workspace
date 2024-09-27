<?php
namespace Budgetcontrol\Test;

use Budgetcontrol\Authentication\Facade\Crypt;
use Budgetcontrol\Test\Libs\Cache;
use Budgetcontrol\Test\Libs\ClientMail;
use Illuminate\Support\Facades\Facade;

class BaseCase extends \PHPUnit\Framework\TestCase
{

    public static function setUpBeforeClass(): void
    {
        // Configura il reporting degli errori prima di eseguire i test
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
    }

    protected function setup(): void
    {

        // Set up the Facade application
        Facade::setFacadeApplication([
            'log' => new \Monolog\Logger('test'),
            'date' => new \Illuminate\Support\Carbon(),
            'mail' => new ClientMail(),
            'crypt' => new \BudgetcontrolLibs\Crypt\Service\CryptableService(
                env('APP_KEY')
            )
        ]);

    }
    
}
