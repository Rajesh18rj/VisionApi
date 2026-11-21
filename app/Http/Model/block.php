<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class block extends BaseModel
{
    use HasUuids;
    protected $table = 'block';
    protected $guarded = [];
    public $timestamps = false;
}
