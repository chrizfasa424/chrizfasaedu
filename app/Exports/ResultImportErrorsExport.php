<?php

namespace App\Exports;

use App\Models\ResultBatch;
use Maatwebsite\Excel\Concerns\FromArray;

class ResultImportErrorsExport implements FromArray
{
    public function __construct(private readonly ResultBatch $batch)
    {
    }

    public function array(): array
    {
        $rows = [['row_number', 'column_name', 'error_message']];

        foreach ($this->batch->errors()->orderBy('id')->get() as $error) {
            $rows[] = [
                $error->row_number,
                $error->column_name,
                $error->error_message,
            ];
        }

        return $rows;
    }
}

