<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class category extends BaseModel
{
    use HasUuids;
    protected $table = 'category';
    protected $guarded = [];
    public $timestamps = false;
}
