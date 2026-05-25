<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
    protected $fillable = [
        'complaint_type',
        'topic',
        'to_person',
        'user_id',
        'fullname',
        'age',
        'phone',
        'address_no',
        'moo',
        'soi',
        'road',
        'subdistrict',
        'district',
        'province',
        'details',
        'demands',
        'docs',
        'other_docs_detail',
        'attachments',
        'history',
        'status',
        'progress_notes',
    ];

    protected $casts = [
        'docs' => 'array',
        'attachments' => 'array',
    ];
}
