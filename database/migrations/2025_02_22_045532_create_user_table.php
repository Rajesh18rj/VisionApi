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
        Schema::create('user', function (Blueprint $table) {
            BlueprintHelper::BaseEntity($table);
            $table->uuid("user_role_id")->nullable()->index();
            $table->foreign("user_role_id")->references("id")->on("user_role")->onDelete("restrict");
            $table->string("first_name");
            $table->string("last_name")->nullable();
            $table->longText("email");
            $table->longText("password");
            $table->string("mobile")->nullable();
            $table->longText("confirm_password");
            $table->longText("notification")->nullable();
            $table->longText('api_token', 100)->nullable();
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean("reset_password")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
