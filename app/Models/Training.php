<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $fillable = [
        'branch',
        'hours',
        'format',
        'start_date',
        'end_date',
        'department',
        'status',
        'details',
        'document',
        'document_link',
        'image',
    ];

    public function applies()
    {
        return $this->hasMany(TrainingApply::class);
    }
}
