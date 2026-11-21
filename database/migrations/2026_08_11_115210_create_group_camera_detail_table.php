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
        Schema::create('group_camera_detail', function (Blueprint $table) {
            BlueprintHelper::BaseEntity($table);
            $table->uuid("group_camera_id")->nullable()->index();
            $table->foreign("group_camera_id")->references("id")->on("group_camera")->onDelete("restrict");
            $table->uuid("camera_id")->nullable()->index();
            $table->foreign("camera_id")->references("id")->on("camera")->onDelete("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_camera_detail');
    }
};
