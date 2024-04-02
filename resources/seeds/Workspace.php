<?php

declare(strict_types=1);

use Budgetcontrol\Workspace\Domain\Model\User;
use Phinx\Seed\AbstractSeed;
use Budgetcontrol\Workspace\Domain\Model\Workspace as Model;

class Workspace extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        $workspace = new Model();
        $workspace->uuid = 'b1b3b3b3-3b3b-3b3b-3b3b-3b3b3b3b3b3b';
        $workspace->name = 'Workspace 1';
        $workspace->description = 'Workspace 1 description';
        $workspace->save();

        $user = User::find(1);
        $user->workspaces()->attach($workspace->id);

    }
}
