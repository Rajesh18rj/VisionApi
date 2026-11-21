<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class group_camera_detail extends BaseModel
{
    use HasUuids;
    protected $table = 'group_camera_detail';
    protected $guarded = [];
    public $timestamps = false;

    public function group_camera()
    {
        return  $this->belongsTo('App\Http\Model\group_camera', "group_camera_id", "id");
    }

    public function camera()
    {
        return  $this->belongsTo('App\Http\Model\camera', "camera_id", "id");
    }
}
