<?php

namespace App\Models\hrrequest;

use Illuminate\Database\Eloquent\Model;

class Request_Safety_Docs extends Model
{
    protected $table = 'request_safety_docs';

    protected $primaryKey = 'id';

    public $timestamps = false; 

    protected $fillable = [
        'request_id',
        'safety_reason',
        'created_at',
    ];

    

}
