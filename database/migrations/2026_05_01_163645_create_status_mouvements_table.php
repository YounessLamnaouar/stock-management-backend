<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_mouvements', function (Blueprint $table) {
            $table->id();
            $table->string('nomStatus');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_mouvements');
    }
};
