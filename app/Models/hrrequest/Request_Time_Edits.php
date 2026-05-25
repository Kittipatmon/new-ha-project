<?php

namespace App\Models\hrrequest;

use Illuminate\Database\Eloquent\Model;

class Request_Time_Edits extends Model
{
    protected $table = 'request_time_edits';

    protected $primaryKey = 'id';

    public $timestamps = false; // If you only use created_at, not updated_at

    protected $fillable = [
        'request_id',
        'edit_reason',
        'edit_start_date',
        'edit_end_date',
        'edit_start_time',
        'edit_end_time',
        'timefile',
        'created_at',
    ];
}
