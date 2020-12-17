<?php

use Phinx\Migration\AbstractMigration;

class CreateTableRates extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('rates', ['id' => false, 'primary_key' => ['currency_base_id', 'currency_id', 'rate_date'], 'signed' => false]);

        $table->addColumn('currency_base_id', 'integer', ['signed' => false, 'null' => false]);
        $table->addColumn('currency_id', 'integer', ['signed' => false, 'null' => false]);
        $table->addColumn('rate_date', 'date', ['null' => false]);
        $table->addColumn('rate', 'float', ['null' => false]);

        $table->addForeignKey(
            'currency_base_id',
            'currencies',
            'id',
            [
                'constraint' => 'fk_rates_base_currency',
                'delete' => 'NO_ACTION',
                'update'=> 'NO_ACTION'
            ]
        );

        $table->addForeignKey(
            'currency_id',
            'currencies',
            'id',
            [
                'constraint' => 'fk_rates_currency',
                'delete' => 'NO_ACTION',
                'update'=> 'NO_ACTION'
            ]
        );

        $table->create();
    }
}
