<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AlterTablesAddWorkspaceIdColumns extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $this->table('accounts')
            ->changeColumn('workspace_id', 'biginteger', ['signed' => false])
            ->update();

        $this->table('budgets')
            ->changeColumn('workspace_id', 'biginteger', ['signed' => false])
            ->update();

        $this->table('entries')
            ->changeColumn('workspace_id', 'biginteger', ['signed' => false])
            ->update();

        $this->table('labels')
            ->changeColumn('workspace_id', 'biginteger', ['signed' => false])
            ->update();

        $this->table('models')
            ->changeColumn('workspace_id', 'biginteger', ['signed' => false])
            ->update();

        $this->table('payees')
            ->changeColumn('workspace_id', 'biginteger', ['signed' => false])
            ->update();

        $this->table('planned_entries')
            ->changeColumn('workspace_id', 'biginteger', ['signed' => false])
            ->update();

        $this->table('user_settings')
            ->changeColumn('workspace_id', 'biginteger', ['signed' => false])
            ->update();

    }
}
