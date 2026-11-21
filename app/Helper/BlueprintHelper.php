<?php

namespace App\Helper;

use Illuminate\Database\Schema\Blueprint;

class BlueprintHelper
{

    static public function BaseEntity(Blueprint $blueprint)
    {
        $blueprint->uuid("id")->index();
        $blueprint->bigIncrements("unique_id")->index();
        $blueprint->boolean("status")->default(1);
        $blueprint->uuid("created_by_id");
        $blueprint->dateTime("created_on")->useCurrent();
        $blueprint->string("updated_by_id")->nullable();
        $blueprint->dateTime("updated_on")->nullable();
        // $blueprint->string("company_id")->nullable();
    }
}
