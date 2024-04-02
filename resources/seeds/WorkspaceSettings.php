<?php

declare(strict_types=1);

use Budgetcontrol\Workspace\Domain\Model\User;
use Phinx\Seed\AbstractSeed;
use Budgetcontrol\Workspace\Domain\Model\WorkspaceSettings as Model;

class WorkspaceSettings extends AbstractSeed
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
        $workspace->workspace_id = 1;
        $workspace->setting = 'app_configurations';
        $workspace->data = json_encode([
            "currency_id" => 1,
            "payment_type_id" => 1
        ]);
        $workspace->save();

        $user = User::find(1);
        $user->workspaces()->attach($workspace->id);

    }
}
