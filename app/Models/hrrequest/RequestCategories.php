<?php


namespace App\Models\hrrequest;

use Illuminate\Database\Eloquent\Model;

class RequestCategories extends Model
{
    protected $table = 'request_categories';
    protected $primaryKey = 'id';

    protected $fillable = [
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
}
