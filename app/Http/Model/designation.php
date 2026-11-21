<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class designation extends BaseModel
{
    use HasUuids;
    protected $table = 'designation';
    protected $guarded = [];
    public $timestamps = false;

    public function department()
    {
        return  $this->belongsTo('App\Http\Model\department', "department_id", "id");
    }
}
