<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS after_stocks_qty_update');

        DB::unprepared('
            CREATE TRIGGER after_stocks_qty_update
            AFTER UPDATE ON stock_produit
            FOR EACH ROW
            BEGIN
                IF NEW.quantite != OLD.quantite THEN
                    INSERT INTO tracabilites (
                        ancienneQuantite,
                        nouvelleQuantite,
                        produit_id,
                        user_id,
                        dateAction,
                        created_at,
                        updated_at
                    )
                    VALUES (
                        OLD.quantite,
                        NEW.quantite,
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
