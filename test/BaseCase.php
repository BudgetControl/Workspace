<?php
namespace Budgetcontrol\Test;

use Illuminate\Support\Facades\Facade;
use Budgetcontrol\Test\Libs\ClientMail;
use Budgetcontrol\Workspace\Controller\WorkspaceController;

class BaseCase extends \PHPUnit\Framework\TestCase
{
    protected $controller;

    public static function setUpBeforeClass(): void
    {
        // Configura il reporting degli errori prima di eseguire i test
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
    }

    protected function setup(): void
    {
        $this->controller = new WorkspaceController();
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
