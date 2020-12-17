<?php

namespace App\Errors\Messages;

class PDOExceptionMessages implements ErrorsMessageInterface
{

    /**
     * {@inheritdoc}
     */
    public function getMessages(): array
    {
        return [
            23000 => [
                1062 => 'Duplicate entry for the value {{value}} in the database',
                1451 => 'Integrity constraint violation, cannot delete or update a parent row, a foreign key failed',
                1452 => 'Integrity constraint violation, cannot add or update a child row, a foreign key failed'
            ],
            1000 => [
                1265 => 'Unexpected value in the column {{value}} at the table of database',
            ]
        ];
    }
}
