<?php

namespace App\Models;

use App\Models\Interfaces\DAOInterface;
use App\Traits\TimestampableTrait;

class Currencies extends BaseModel implements DAOInterface
{
    public $id;
    public $symbol;
}
