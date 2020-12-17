<?php

namespace App\Services;

class UtilsService
{

    public function formatSort(string $sort): array
    {
        $str_sort = [];

        $sort_fields = explode(';', $sort);

        foreach ($sort_fields as $sort_field) {
            $exp_sort = explode(",", $sort_field);
            $str_sort[] = $exp_sort[0] . " " . $exp_sort[1];
        }

        return $str_sort;
    }
}
