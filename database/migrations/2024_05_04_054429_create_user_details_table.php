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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId("user_id")->nullable()->index();
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->string("nik")->nullable();
            $table->string("nomor_wa")->nullable();
            $table->string("provinsi")->nullable();
            $table->string("kota")->nullable();
            $table->string("alamat")->nullable();
            $table->string("nama_bank")->nullable();
            $table->string("no_rek")->nullable();
            $table->string("nama_kontak")->nullable();
            $table->string("no_kontak")->nullable();
            $table->string("referral_use")->nullable();
            $table->boolean("first_order")->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
