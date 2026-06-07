<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Speed up the dashboard date-range GROUP BY query
        Schema::table('movement_stocks', function (Blueprint $table) {
            $table->index('dateMouvement', 'idx_movement_stocks_date');
        });

        // Speed up stock lookups by product+warehouse (used in movement validation)
        Schema::table('stock_produit', function (Blueprint $table) {
            $table->index(['produit_id', 'entrepot_id'], 'idx_stock_produit_composite');
        });
    }

    public function down(): void
    {
        Schema::table('movement_stocks', function (Blueprint $table) {
            $table->dropIndex('idx_movement_stocks_date');
        });

        Schema::table('stock_produit', function (Blueprint $table) {
            $table->dropIndex('idx_stock_produit_composite');
        });
    }
};
