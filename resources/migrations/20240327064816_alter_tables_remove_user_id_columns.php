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
            ->removeColumn('workspace_id')
            ->update();

        $this->table('budgets')
            ->removeColumn('workspace_id')
            ->update();

        $this->table('entries')
            ->removeColumn('workspace_id')
            ->update();

        $this->table('labels')
            ->removeColumn('workspace_id')
            ->update();

        $this->table('models')
            ->removeColumn('workspace_id')
            ->update();

        $this->table('payees')
            ->removeColumn('workspace_id')
            ->update();

        $this->table('planned_entries')
            ->removeColumn('workspace_id')
            ->update();

        $this->table('sub_categories')
            ->removeColumn('workspace_id')
            ->update();

        $this->table('user_settings')
            ->removeColumn('workspace_id')
            ->update();
    }
}
