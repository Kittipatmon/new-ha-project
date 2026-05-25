<?php

namespace App\Models\hrrequest;

use Illuminate\Database\Eloquent\Model;

class Request_Safety_Items extends Model
{
    protected $table = 'request_safety_items';

    protected $primaryKey = 'safety_items_id';

    public $timestamps = false; // If you want to use created_at and updated_at manually

    protected $fillable = [
        'request_id',
        'item_name',
        'quantity',
        'note',
        'created_at',
        'updated_at',
    ];

    

}
