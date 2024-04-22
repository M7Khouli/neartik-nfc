<?php

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
        Schema::create('profile_edits', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->string('old_value')->nullable();
            $table->string('new_value');
            $table->string('field');
            $table->string('status')->default('pending');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_edits');
    }
};
