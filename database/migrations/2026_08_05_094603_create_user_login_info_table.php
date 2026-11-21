<?php

use App\Helper\BlueprintHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_logged_info', function (Blueprint $table) {

            BlueprintHelper::BaseEntity($table);

            $table->uuid('user_id');

            $table->string('device_id');
            $table->string('system_name');
            $table->string('system_version')->nullable();
            $table->string('model_number')->nullable();
            $table->string('app_version')->nullable();
            $table->string('onesignal_subscription_id')->nullable();

            $table->string('ip_address')->nullable();

            $table->timestamp('login_time')->nullable();
            $table->timestamp('last_seen')->nullable();

            $table->index('user_id');
            $table->index('device_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_logged_info');
    }
};