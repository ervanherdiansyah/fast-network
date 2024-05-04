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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId("user_id")->nullable()->index();
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->foreignId("paket_id")->nullable()->index();
            $table->foreign("paket_id")->references("id")->on("pakets")->onDelete("cascade");
            $table->string("order_code");
            $table->date("order_date");
            $table->string("status");
            $table->string("shipping_status");
            $table->string("shipping_courier");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
