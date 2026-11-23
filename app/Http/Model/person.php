<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class person extends BaseModel
{
    use HasUuids;
    protected $table = 'person';
    protected $guarded = [];
    public $timestamps = false;

    protected $hidden = [
        'face_embedding',
    ];

//    public function category()
//    {
//        return  $this->belongsTo('App\Http\Model\category', "category_id", "id");
//    }
//
//    public function department()
//    {
//        return  $this->belongsTo('App\Http\Model\department', "department_id", "id");
//    }
//
//    public function designation()
//    {
//        return  $this->belongsTo('App\Http\Model\designation', "designation_id", "id");
//    }

    public function personType()
    {
        return $this->belongsTo(
            'App\Http\Model\person_type',
            'person_type_id',
            'id'
        );
    }
}
