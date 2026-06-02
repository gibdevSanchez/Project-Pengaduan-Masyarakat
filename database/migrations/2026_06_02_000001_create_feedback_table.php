<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('masyarakat_id');
            $table->text('isi');
            $table->string('foto')->nullable();
            $table->enum('status', ['pending', 'sukses', 'invalid'])->default('pending');
            $table->timestamps();

            $table->foreign('masyarakat_id')->references('id')->on('masyarakat')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
