<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingApply extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_id',
        'employee_code',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'employee_code', 'employee_code');
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}
