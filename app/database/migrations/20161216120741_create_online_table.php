<?php

use Phinx\Migration\AbstractMigration;

class CreateOnlineTable extends AbstractMigration
{
    /**
     * Change Method.
     */
    public function change()
    {
        if (! $this->hasTable('online')) {
            $table = $this->table('online', ['engine' => 'MyISAM', 'collation' => 'utf8mb4_unicode_ci']);
            $table->addColumn('ip', 'string', ['limit' => 15])
                ->addColumn('brow', 'string', ['limit' => 25])
                ->addColumn('time', 'integer')
                ->addColumn('user', 'string', ['limit' => 20, 'null' => true])
                ->addIndex('ip')
                ->addIndex('time')
                ->addIndex('user')
                ->create();
        }
    }
}
