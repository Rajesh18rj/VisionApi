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
        Schema::create('person', function (Blueprint $table) {
            BlueprintHelper::BaseEntity($table);

            $table->string("name");
            $table->string("gender")->nullable();
            $table->string("register_number")->nullable();
            $table->string("mobile_number")->nullable();
            $table->string("email")->nullable();
            $table->longText("image")->nullable();
            $table->longText("address")->nullable();
            $table->enum('category', ['Person', 'Employee'])->default('Person');


            $table->uuid("person_type_id")->nullable()->index();
            $table->foreign("person_type_id")
                ->references("id")
                ->on("person_type")
                ->onDelete("restrict");

            $table->boolean("is_active")->default(0);
            $table->binary('face_embedding')->nullable();
            $table->unsignedBigInteger("display_order")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person');
    }
};
