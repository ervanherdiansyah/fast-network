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
        Schema::create('pakets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("paket_nama");
            $table->integer("max_quantity");
            $table->integer("price");
            $table->integer("weight");
            $table->string("description");
            $table->string("image");
            $table->integer("point");
            $table->string("paket_kode");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pakets');
    }
};
