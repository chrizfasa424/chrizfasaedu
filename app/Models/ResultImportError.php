<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultImportError extends Model
{
    use HasFactory;

    protected $fillable = [
        'result_batch_id',
        'row_number',
        'column_name',
        'error_message',
        'raw_payload',
    ];

    protected $casts = [
        'raw_payload' => 'array',
    ];

    public function resultBatch()
    {
        return $this->belongsTo(ResultBatch::class);
    }
}

