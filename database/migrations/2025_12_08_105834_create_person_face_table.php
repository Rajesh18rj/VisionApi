<?php

use App\Helper\BlueprintHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('person_face', function (Blueprint $table) {
            BlueprintHelper::BaseEntity($table);
            $table->uuid("person_id")->nullable()->index();
            $table->foreign("person_id")->references("id")->on("person")->onDelete("restrict");
            $table->uuid("camera_id")->nullable()->index();
            $table->foreign("camera_id")->references("id")->on("camera")->onDelete("restrict");
            $table->bigInteger("camera_unique_id")->nullable();
            $table->string("person_no")->nullable();
            $table->longText("image")->nullable();
            $table->date("date")->nullable();
            $table->string("title")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_face');
    }
};
