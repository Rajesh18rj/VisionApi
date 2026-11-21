<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class department extends BaseModel
{
    use HasUuids;
    protected $table = 'department';
    protected $guarded = [];
    public $timestamps = false;

    public function category()
    {
        return  $this->belongsTo('App\Http\Model\category', "category_id", "id");
    }
}
