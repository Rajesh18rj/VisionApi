<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class device_connected extends BaseModel
{
    use HasUuids;
    protected $table = 'device_connected';
    protected $guarded = [];

    protected $keyType = 'string';

    public $timestamps = false;

}