<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class group_camera extends BaseModel
{
    use HasUuids;
    protected $table = 'group_camera';
    protected $guarded = [];
    public $timestamps = false;

    public function details()
    {
        return $this->hasMany(group_camera_detail::class, 'group_camera_id');
    }
}
