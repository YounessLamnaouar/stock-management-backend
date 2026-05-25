<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movement_stocks', function (Blueprint $table) {
            $table->string('statut')->default('En cours')->after('commentaire');
        });
    }

    public function down(): void
    {
        Schema::table('movement_stocks', function (Blueprint $table) {
            $table->dropColumn('statut');
        });
    }
};
