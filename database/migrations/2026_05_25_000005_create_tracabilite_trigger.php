<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing trigger if it exists (safe re-run)
        DB::unprepared('DROP TRIGGER IF EXISTS after_stocks_qty_update');

        // MySQL trigger: fires AFTER any UPDATE that changes quantiteDisponible.
        // Reads the current user_id from the stock_user_context helper table
        // (set by the PHP controller before each stock update).
        DB::unprepared('
            CREATE TRIGGER after_stocks_qty_update
            AFTER UPDATE ON stocks
            FOR EACH ROW
            BEGIN
                IF NEW.quantiteDisponible != OLD.quantiteDisponible THEN
                    INSERT INTO tracabilites (
                        action,
                        description,
                        ancienneQuantite,
                        nouvelleQuantite,
                        produit_id,
                        user_id,
                        dateAction,
                        created_at,
                        updated_at
                    )
                    VALUES (
                        \'UPDATE_STOCK\',
                        CONCAT(\'Quantité modifiée de \', OLD.quantiteDisponible, \' à \', NEW.quantiteDisponible),
                        OLD.quantiteDisponible,
                        NEW.quantiteDisponible,
                        NEW.produit_id,
                        COALESCE((SELECT user_id FROM stock_user_context LIMIT 1), 1),
                        NOW(),
                        NOW(),
                        NOW()
                    );
                END IF;
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS after_stocks_qty_update');
    }
};
