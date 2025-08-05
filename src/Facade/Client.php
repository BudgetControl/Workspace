<?php
namespace Budgetcontrol\Workspace\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Class Client
 * 
 * @method \Budgetcontrol\Connector\Client\MailerClient mailer()
 * @method \Budgetcontrol\Connector\Client\PushNotificationClient pushNotification()
 *
 * This class is a facade for the Client class.
 * @see \Budgetcontrol\Connector\Factory\MicroserviceClient
 */

class Client extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'client';
    }
}