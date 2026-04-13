<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paintings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('year')->nullable();
            $table->string('size')->nullable();
            $table->decimal('price_rub', 10, 2)->nullable();
            $table->decimal('price_usd', 10, 2)->nullable();
            $table->text('short_desc')->nullable();
            $table->longText('full_desc')->nullable();
            $table->string('main_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paintings');
    }
};
