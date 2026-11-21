<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class user_role extends BaseModel
{
    use HasUuids;
    protected $table = 'user_role';
    protected $guarded = [];
    public $timestamps = false;
}
