<?php

namespace App\Models\hrrequest;

use Illuminate\Database\Eloquent\Model;

class RequestSubtypes extends Model
{
    protected $table = 'request_subtype';
    protected $primaryKey = 'id';

    protected $fillable = [
        'type_id',
        'code',
        'name_th',
        'name_en',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $timestamps = true;

    public function requestType()
    {
        return $this->belongsTo(RequestType::class, 'type_id');
    }
}
