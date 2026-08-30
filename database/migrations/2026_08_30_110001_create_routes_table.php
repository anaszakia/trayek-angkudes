<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('route_type', ['loop', 'round_trip', 'one_way'])->default('one_way');
            $table->string('start_point')->nullable();
            $table->string('end_point')->nullable();
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
