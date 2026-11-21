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
        Schema::create('camera', function (Blueprint $table) {
            BlueprintHelper::BaseEntity($table);
            $table->uuid("section_id")->nullable()->index();
            $table->foreign("section_id")->references("id")->on("section")->onDelete("restrict");
            $table->uuid("nvr_id")->nullable()->index();
            $table->foreign("nvr_id")->references("id")->on("nvr")->onDelete("restrict");
            $table->uuid("department_id")->nullable()->index();
            $table->foreign("department_id")->references("id")->on("department")->onDelete("restrict");
            $table->string("asset_no")->nullable();
            $table->string("type")->nullable();
            $table->string("serial_number")->nullable();
            $table->string("model_name")->nullable();
            $table->string("ip_address")->nullable();
            $table->unsignedBigInteger("port")->default(80);
            $table->string("manufacturer")->nullable();
            $table->string("vendor")->nullable();
            $table->date("install_date")->nullable();
            $table->dateTime("last_working_on")->nullable();
            $table->boolean("is_working")->default(0);
            $table->string("instance_url")->nullable();
            $table->string("stream_url")->nullable();
            $table->bigInteger("roi_x")->nullable();
            $table->bigInteger("roi_y")->nullable();
            $table->bigInteger("roi_height")->nullable();
            $table->bigInteger("roi_width")->nullable();
            $table->string("longitude")->nullable();
            $table->string("latitude")->nullable();
            $table->unsignedBigInteger("display_order")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camera');
    }
};
