<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class session extends BaseModel
{
    use HasUuids;
    protected $table = 'session';
    protected $guarded = [];
    public $timestamps = false;
}
