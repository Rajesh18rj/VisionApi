<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class nvr extends BaseModel
{
    use HasUuids;
    protected $table = 'nvr';
    protected $guarded = [];
    public $timestamps = false;

    public function section()
    {
        return  $this->belongsTo('App\Http\Model\section', "section_id", "id");
    }
}
