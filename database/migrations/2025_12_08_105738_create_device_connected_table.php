<?php

use App\Helper\BlueprintHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('device_connected', function (Blueprint $table) {
            BlueprintHelper::BaseEntity($table);

            $table->date('date')->nullable();
            $table->string('camera_name')->nullable();
            $table->string('camera_ip')->nullable();
            $table->string('connecting_status')->nullable();
            $table->integer('display_order')->default(0);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_connected');
    }
};
