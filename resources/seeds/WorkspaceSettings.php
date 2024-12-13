<?php

declare(strict_types=1);

use Budgetcontrol\Library\Model\Currency;
use Budgetcontrol\Library\ValueObject\WorkspaceSetting;
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
        $workspace->data = WorkspaceSetting::create(Currency::find(1), 1);
        $workspace->save();
    }
}
