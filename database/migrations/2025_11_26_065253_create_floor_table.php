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
        Schema::create('floor', function (Blueprint $table) {
            BlueprintHelper::BaseEntity($table);
            $table->uuid("block_id")->nullable()->index();
            $table->foreign("block_id")->references("id")->on("block")->onDelete("restrict");
            $table->string("name");
            $table->string("description")->nullable();
            $table->unsignedBigInteger("display_order")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('floor');
    }
};
