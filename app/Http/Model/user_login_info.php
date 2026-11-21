<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class user_login_info extends BaseModel
{
    use HasUuids;
    protected $table = 'user_logged_info';
    protected $guarded = [];
    public $timestamps = false;

    public function user()
    {
        return  $this->belongsTo('App\Http\Model\user', "user_id", "id");
    }

}
