<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class camera extends BaseModel
{
    use HasUuids;
    protected $table = 'camera';
    protected $guarded = [];
    public $timestamps = false;

    public function section()
    {
        return  $this->belongsTo('App\Http\Model\section', "section_id", "id");
    }

    public function nvr()
    {
        return  $this->belongsTo('App\Http\Model\nvr', "nvr_id", "id");
    }
}
