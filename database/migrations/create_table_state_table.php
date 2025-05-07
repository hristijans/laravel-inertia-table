<?php

declare(strict_types=1);

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
        Schema::create('table_state', function (Blueprint $table) {
            $table->id();
            $table->string('table_name');
            $table->string('user_id')->nullable();
            $table->json('state');
            $table->timestamps();

            $table->index(['table_name', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_state');
    }
};
