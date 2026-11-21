<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class floor extends BaseModel
{
    use HasUuids;
    protected $table = 'floor';
    protected $guarded = [];
    public $timestamps = false;

    public function block()
    {
        return  $this->belongsTo('App\Http\Model\block', "block_id", "id");
    }
}
