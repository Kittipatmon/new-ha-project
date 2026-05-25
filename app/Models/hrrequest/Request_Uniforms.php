<?php

namespace App\Models\hrrequest;

use Illuminate\Database\Eloquent\Model;

class Request_Uniforms extends Model
{
    protected $table = 'request_uniforms';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'request_id',
        'uniform_gender',
        'uniform_size',
        'uniform_reason',
        'created_at',
    ];
}
