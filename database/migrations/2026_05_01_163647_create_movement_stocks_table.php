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
        Schema::create('movement_stocks', function (Blueprint $table) {
            $table->id();
            $table->date('dateMouvement');
            $table->integer('quantite');
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('produit_id')
                ->constrained('produits')
                ->cascadeOnDelete();

            $table->foreignId('status_mouvement_id')
                ->constrained('status_mouvements')
                ->cascadeOnDelete();

            $table->foreignId('entrepot_source_id')
                ->nullable()
                ->constrained('entrepots')
                ->nullOnDelete();

            $table->foreignId('entrepot_destination_id')
                ->nullable()
                ->constrained('entrepots')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movement_stocks');
    }
};
