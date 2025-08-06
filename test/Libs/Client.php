<?php
declare(strict_types=1);

namespace Budgetcontrol\Test\Libs;

use BudgetcontrolLibs\Mailer\View\ViewInterface;

class Client {

    private $log;
    private $data;

    public function mailer() {
        return $this;
    }

    public function sharedWorkspace(\Budgetcontrol\Connector\Entities\Payloads\Mailer\SharedWorkspace $data) {
        return true;
    }

    public function pushNotification() {
        return $this;
    }

    public function notificationMessageToUser(\Budgetcontrol\Connector\Entities\Payloads\Notification\PushNotification $data) {
        return true;
    }
}