<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->string('customer_name');
            $table->string('contact');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->text('comment')->nullable();
            $table->json('cart_snapshot');
            $table->decimal('total_rub', 10, 2)->default(0);
            $table->decimal('total_usd', 10, 2)->default(0);
            $table->enum('status', ['new', 'processed', 'completed'])->default('new');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
