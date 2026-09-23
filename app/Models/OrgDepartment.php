<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrgDepartment extends Model
{
    protected $connection = 'userkml2025';
    protected $table = 'departments';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'manager_id',
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id', 'id');
    }
}
