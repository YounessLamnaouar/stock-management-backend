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
        Schema::create('alert_stocks', function (Blueprint $table) {
            $table->id();
            $table->date('dateAlerte');
            $table->text('message')->nullable();

            $table->foreignId('stock_id')
                ->constrained('stocks')
                ->cascadeOnDelete();

            $table->foreignId('niveau_id')
                ->constrained('niveaux')
                ->cascadeOnDelete();

            $table->foreignId('statut_id')
                ->constrained('statuts')
                ->cascadeOnDelete();

            $table->foreignId('produit_id')
                ->constrained('produits')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alert_stocks');
    }
};
