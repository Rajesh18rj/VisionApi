<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class person_face extends BaseModel
{
    use HasUuids;
    protected $table = 'person_face';
    protected $guarded = [];
    public $timestamps = false;

    public function person()
    {
        return  $this->belongsTo('App\Http\Model\person', "person_id", "id");
    }
}
