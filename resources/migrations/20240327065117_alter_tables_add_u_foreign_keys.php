<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AlterTablesAddUForeignKeys extends AbstractMigration
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
            ->addForeignKey('workspace_id', 'workspaces', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->update();

        $this->table('budgets')
            ->addForeignKey('workspace_id', 'workspaces', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->update();

        $this->table('entries')
            ->addForeignKey('workspace_id', 'workspaces', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->update();

        $this->table('labels')
            ->addForeignKey('workspace_id', 'workspaces', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->update();

        $this->table('models')
            ->addForeignKey('workspace_id', 'workspaces', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->update();

        $this->table('payees')
            ->addForeignKey('workspace_id', 'workspaces', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->update();

        $this->table('planned_entries')
            ->addForeignKey('workspace_id', 'workspaces', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->update();

        $this->table('user_settings')
            ->addForeignKey('workspace_id', 'workspaces', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->update();

    }
}
