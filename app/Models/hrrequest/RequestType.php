<?php

namespace App\Models\hrrequest;

use Illuminate\Database\Eloquent\Model;

class RequestType extends Model
{
    protected $table = 'request_type';
    protected $primaryKey = 'id';

    protected $fillable = [
        'category_id',
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

    public function requestCategory()
    {
        return $this->belongsTo(RequestCategories::class, 'category_id');
    }
}
