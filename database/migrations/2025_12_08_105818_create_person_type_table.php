<?php

use App\Helper\BlueprintHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('person_type', function (Blueprint $table) {
            BlueprintHelper::BaseEntity($table);
            $table->string("name");
            $table->string('rgb_code', 50)->nullable();
            $table->string('hex_code', 7)->nullable();
            $table->integer('display_order')->default(0);


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_type');
    }
};
