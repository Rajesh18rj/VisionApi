<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class app_settings extends BaseModel
{
    use HasUuids;
    protected $table = 'app_settings';
    protected $guarded = [];
    public $timestamps = false;
}
