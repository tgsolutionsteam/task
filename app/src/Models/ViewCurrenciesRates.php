<?php

namespace App\Models;

use App\Models\Interfaces\DAOInterface;

class ViewCurrenciesRates extends BaseModel implements DAOInterface
{
    public int $currency_base_id;
    public string $base_symbol;
    public int $currency_id;
    public string $symbol;
    public timstampe $rate_date;
    public double $rate;
}
