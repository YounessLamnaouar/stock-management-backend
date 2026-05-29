<?php

namespace Database\Factories;

use App\Models\Categorie;
use App\Models\Produit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Produit>
 */
class ProduitFactory extends Factory
{
    private static array $produits = [
        ['nom' => 'Ordinateur portable Dell',      'unite' => 'Pièce'],
        ['nom' => 'Écran Samsung 24"',              'unite' => 'Pièce'],
        ['nom' => 'Clavier mécanique Logitech',    'unite' => 'Pièce'],
        ['nom' => 'Souris sans fil HP',             'unite' => 'Pièce'],
        ['nom' => 'Imprimante laser Brother',       'unite' => 'Pièce'],
        ['nom' => 'Bureau ergonomique chêne',       'unite' => 'Pièce'],
        ['nom' => 'Chaise de bureau premium',       'unite' => 'Pièce'],
        ['nom' => 'Armoire métallique 4 tiroirs',   'unite' => 'Pièce'],
        ['nom' => 'Table de réunion 10 places',     'unite' => 'Pièce'],
        ['nom' => 'Étagère industrielle acier',     'unite' => 'Pièce'],
        ['nom' => 'Huile moteur 5W30',              'unite' => 'Litre'],
        ['nom' => 'Graisse industrielle',           'unite' => 'Kg'],
        ['nom' => 'Solvant dégraissant',            'unite' => 'Litre'],
        ['nom' => 'Peinture époxy grise',           'unite' => 'Litre'],
        ['nom' => 'Colle structurale',              'unite' => 'Kg'],
        ['nom' => 'Roulement à billes 6205',        'unite' => 'Pièce'],
        ['nom' => 'Courroie trapézoïdale A42',      'unite' => 'Pièce'],
        ['nom' => 'Verin pneumatique 80mm',         'unite' => 'Pièce'],
        ['nom' => 'Moteur électrique 2.2kW',        'unite' => 'Pièce'],
        ['nom' => 'Pompe centrifuge 50/160',        'unite' => 'Pièce'],
        ['nom' => 'Câble électrique 2.5mm²',        'unite' => 'Mètre'],
        ['nom' => 'Disjoncteur 16A biphasé',        'unite' => 'Pièce'],
        ['nom' => 'Prise industrielle 32A',         'unite' => 'Pièce'],
        ['nom' => 'Téléphone IP Cisco',             'unite' => 'Pièce'],
        ['nom' => 'Switch réseau 24 ports',         'unite' => 'Pièce'],
        ['nom' => 'Ramette papier A4 500f',         'unite' => 'Ramette'],
        ['nom' => 'Stylo bille noir Bic (boîte)',   'unite' => 'Boîte'],
        ['nom' => 'Classeur rigide A4',             'unite' => 'Pièce'],
        ['nom' => 'Enveloppe C4 (boîte 250)',       'unite' => 'Boîte'],
        ['nom' => 'Ruban adhésif transparent',      'unite' => 'Rouleau'],
        ['nom' => 'Gants de protection L',          'unite' => 'Paire'],
        ['nom' => 'Casque de sécurité jaune',       'unite' => 'Pièce'],
        ['nom' => 'Lunettes de protection',         'unite' => 'Pièce'],
        ['nom' => 'Chaussures de sécurité T42',     'unite' => 'Paire'],
        ['nom' => 'Gilet haute visibilité',         'unite' => 'Pièce'],
        ['nom' => 'Clé à molette 200mm',            'unite' => 'Pièce'],
        ['nom' => 'Tournevis plat 6x150',           'unite' => 'Pièce'],
        ['nom' => 'Perceuse visseuse 18V',          'unite' => 'Pièce'],
        ['nom' => 'Meuleuse angulaire 125mm',       'unite' => 'Pièce'],
        ['nom' => 'Niveau laser rotatif',           'unite' => 'Pièce'],
        ['nom' => 'Détergent industriel 25L',       'unite' => 'Bidon'],
        ['nom' => 'Désinfectant surfaces 5L',       'unite' => 'Bidon'],
        ['nom' => 'Papier hygiénique (carton)',     'unite' => 'Carton'],
        ['nom' => 'Savon liquide mains 5L',         'unite' => 'Bidon'],
        ['nom' => 'Sac poubelle 100L (rouleau)',    'unite' => 'Rouleau'],
        ['nom' => 'Tissu polyester 1.5m',           'unite' => 'Mètre'],
        ['nom' => 'Fil à coudre industriel',        'unite' => 'Bobine'],
        ['nom' => 'Bouton pression 15mm (sachet)',  'unite' => 'Sachet'],
        ['nom' => 'Fermeture éclair 50cm',          'unite' => 'Pièce'],
        ['nom' => 'Étiquette tissée 100x50mm',      'unite' => 'Boîte'],
    ];

    private static int $index = 0;

    public function definition(): array
    {
        $p = self::$produits[self::$index++ % count(self::$produits)];

        return [
            'nomProduit'   => $p['nom'],
            'unite'        => $p['unite'],
            'dateCreation' => now()->toDateString(),
            'categorie_id' => Categorie::inRandomOrder()->first()?->id ?? 1,
        ];
    }
}
