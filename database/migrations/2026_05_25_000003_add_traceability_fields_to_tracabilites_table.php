<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tracabilites', function (Blueprint $table) {
            $table->integer('ancienneQuantite')->nullable()->after('description');
            $table->integer('nouvelleQuantite')->nullable()->after('ancienneQuantite');
            $table->foreignId('produit_id')
                ->nullable()
                ->after('user_id')
                ->constrained('produits')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tracabilites', function (Blueprint $table) {
            $table->dropForeign(['produit_id']);
            $table->dropColumn(['ancienneQuantite', 'nouvelleQuantite', 'produit_id']);
        });
    }
};
