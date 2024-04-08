<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AlterTablesRemoveUserIdColumns extends AbstractMigration
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
            ->renameColumn('user_id', 'workspace_id')
            ->update();

        $this->table('budgets')
            ->renameColumn('user_id', 'workspace_id')
            ->update();

        $this->table('entries')
            ->renameColumn('user_id', 'workspace_id')
            ->update();

        $this->table('labels')
            ->renameColumn('user_id', 'workspace_id')
            ->update();

        $this->table('models')
            ->renameColumn('user_id', 'workspace_id')
            ->update();

        $this->table('payees')
            ->renameColumn('user_id', 'workspace_id')
            ->update();

        $this->table('planned_entries')
            ->renameColumn('user_id', 'workspace_id')
            ->update();

        $this->table('sub_categories')
            ->removeColumn('user_id')
            ->update();

        $this->table('user_settings')
            ->renameColumn('user_id', 'workspace_id')
            ->update();
    }
}
