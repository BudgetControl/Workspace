<?php

use Phinx\Seed\AbstractSeed;
use Budgetcontrol\Library\Model\User;
use Budgetcontrol\Library\Model\Workspace;
use Illuminate\Database\Capsule\Manager as DB;

class UserWorkspaceSeed extends AbstractSeed
{

    public function run(): void
    {
        \Budgetcontrol\Library\Model\User::create([
            'name' => 'Verdi',
            'email' => 'mario.verdi@email.it',
            'password' => 'password',
            'uuid' => '4373a9a3-a481-4d5a-b8fe-c2571be7efe3',
        ]);

        \Budgetcontrol\Library\Model\User::create([
            'name' => 'Rossi',
            'email' => 'mario.rossi@email.it',
            'password' => 'password',
            'uuid' => '4373a9a3-a481-4d5a-b8fe-c2571be7efe4',
        ]);

        \Budgetcontrol\Library\Model\User::create([
            'name' => 'Rossi',
            'email' => 'mario.verdi@email.it',
            'password' => 'password',
            'uuid' => '4373a9a3-a481-4d5a-b8fe-c2571be7efe5',
        ]);

    }
}
