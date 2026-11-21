<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class section extends BaseModel
{
    use HasUuids;
    protected $table = 'section';
    protected $guarded = [];
    public $timestamps = false;

    public function floor()
    {
        return  $this->belongsTo('App\Http\Model\floor', "floor_id", "id");
    }
}
