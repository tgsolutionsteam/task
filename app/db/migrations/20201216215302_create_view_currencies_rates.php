<?php

use Phinx\Migration\AbstractMigration;

class CreateViewCurrenciesRates extends AbstractMigration
{
    public function change()
    {
        $this->execute(
            "CREATE OR REPLACE VIEW `view_currencies_rates` AS
                select
                    `a`.`symbol` AS `base_symbol`,
                    `c`.`symbol` AS `symbol`,
                    `b`.`rate_date`,
                    `b`.`rate`
                from `currencies` `a`
                inner join `rates` `b` on (`a`.`id` = `b`.`currency_base_id`)
                inner join `currencies` `c` on (`b`.`currency_id` = `c`.`id`)
                order by base_symbol asc, rate_date desc
            "
        );
    }
}
