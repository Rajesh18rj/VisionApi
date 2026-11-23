<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class person_type extends BaseModel
{
    use HasUuids;
    protected $table = 'person_type';
    protected $guarded = [];
    public $timestamps = false;

}
