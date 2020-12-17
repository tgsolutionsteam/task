<?php

use Phinx\Migration\AbstractMigration;

class CreateTableCurrencies extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('currencies', ['signed' => false]);

        $table->addColumn('symbol', 'string', ['limit' => 3]);
        $table->addIndex(
            ['symbol'],
            [
                'unique' => true,
                'name' => 'idx_symbol_currencies'
            ]
        );
        $table->create();
    }
}
