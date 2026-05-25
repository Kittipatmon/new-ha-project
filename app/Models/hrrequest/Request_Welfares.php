<?php

namespace App\Models\hrrequest;

use Illuminate\Database\Eloquent\Model;

class RequestWelfare extends Model
{
    protected $table = 'request_welfares';

    protected $primaryKey = 'welfares_id';

    public $timestamps = false; // If you don't use Laravel's created_at/updated_at

    protected $fillable = [
        'request_id',
        'welfare_type',
        'welfare_other',
        'welfare_reason',
        'welfare_date',
        'created_at',
    ];
}
