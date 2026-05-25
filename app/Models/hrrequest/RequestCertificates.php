<?php

namespace App\Models\hrrequest;

use Illuminate\Database\Eloquent\Model;

class RequestCertificates extends Model
{
    protected $table = 'request_certificates';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'request_id',
        'certificate_type',
        'certificate_date',
        'certificate_reason',
        'created_at',
    ];

    protected $casts = [
        'certificate_date' => 'date',
        'created_at' => 'datetime',
    ];
}
