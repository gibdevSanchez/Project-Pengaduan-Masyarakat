<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_penugasan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feedback_id');
            $table->enum('tipe', ['individual', 'global']);
            $table->unsignedBigInteger('petugas_id')->nullable();
            $table->enum('status', ['pending', 'proses', 'selesai', 'invalid'])->default('pending');
            $table->text('pesan')->nullable();
            $table->boolean('hidden_by_petugas')->default(false);
            $table->timestamps();

            $table->foreign('feedback_id')->references('id')->on('feedback')->cascadeOnDelete();
            $table->foreign('petugas_id')->references('id_petugas')->on('petugas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_penugasan');
    }
};
