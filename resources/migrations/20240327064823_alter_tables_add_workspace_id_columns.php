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
            ->addColumn('workspace_id', 'biginteger', ['signed' => false])
            ->update();

        $this->table('budgets')
            ->addColumn('workspace_id', 'biginteger', ['signed' => false])
            ->update();

        $this->table('entries')
            ->addColumn('workspace_id', 'biginteger', ['signed' => false])
            ->update();

        $this->table('labels')
            ->addColumn('workspace_id', 'biginteger', ['signed' => false])
            ->update();

        $this->table('models')
            ->addColumn('workspace_id', 'biginteger', ['signed' => false])
            ->update();

        $this->table('payees')
            ->addColumn('workspace_id', 'biginteger', ['signed' => false])
            ->update();

        $this->table('planned_entries')
            ->addColumn('workspace_id', 'biginteger', ['signed' => false])
            ->update();

        $this->table('sub_categories')
            ->addColumn('workspace_id', 'biginteger', ['signed' => false])
            ->update();

        $this->table('user_settings')
            ->addColumn('workspace_id', 'biginteger', ['signed' => false])
            ->update();

        $this->table('payments_types')
            ->addColumn('workspace_id', 'biginteger', ['signed' => false])
            ->update();

        $this->table('users')
            ->addColumn('workspace_id', 'biginteger', ['signed' => false])
            ->update();

        
    }
}
