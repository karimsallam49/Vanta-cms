<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

/**
 * Spits out a ready-to-fill .xlsx template: a heading row plus one
 * example row so the expected shape is unmistakable.
 */
class TemplateExport implements FromArray
{
    public function __construct(private array $rows)
    {
    }

    public function array(): array
    {
        return $this->rows;
    }
}
