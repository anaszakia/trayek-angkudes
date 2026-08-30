<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('routes')->cascadeOnDelete();
            $table->string('fare_code')->unique();
            $table->string('name');
            $table->enum('passenger_type', ['general', 'student', 'senior', 'disabled', 'children'])->default('general');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('IDR');
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fares');
    }
};
