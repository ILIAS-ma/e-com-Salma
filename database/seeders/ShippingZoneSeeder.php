<?php

namespace Database\Seeders;

use App\Models\ShippingZone;
use Illuminate\Database\Seeder;

class ShippingZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            [
                'name' => 'Casablanca - Rabat',
                'description' => 'Livraison express intra-muros pour l\'axe économique du pays.',
                'rate' => 25,
                'cod_surcharge' => 0,
                'free_shipping_threshold' => 500,
                'estimated_days_min' => 1,
                'estimated_days_max' => 2,
            ],
            [
                'name' => 'Grandes villes',
                'description' => 'Marrakech, Tanger, Fès, Agadir, Meknès, Oujda et autres grandes villes.',
                'rate' => 35,
                'cod_surcharge' => 10,
                'free_shipping_threshold' => 500,
                'estimated_days_min' => 2,
                'estimated_days_max' => 4,
            ],
            [
                'name' => 'Villes périphériques',
                'description' => 'Autres villes et zones hors des grands axes logistiques.',
                'rate' => 45,
                'cod_surcharge' => 15,
                'free_shipping_threshold' => 700,
                'estimated_days_min' => 3,
                'estimated_days_max' => 6,
            ],
        ];

        foreach ($zones as $zone) {
            ShippingZone::create($zone);
        }
    }
}
