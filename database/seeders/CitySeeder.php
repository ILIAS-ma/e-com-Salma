<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\ShippingZone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $casaRabat = ShippingZone::where('name', 'Casablanca - Rabat')->firstOrFail();
        $grandesVilles = ShippingZone::where('name', 'Grandes villes')->firstOrFail();
        $peripheriques = ShippingZone::where('name', 'Villes périphériques')->firstOrFail();

        $cities = [
            $casaRabat->id => ['Casablanca', 'Rabat', 'Salé', 'Mohammedia', 'Témara'],
            $grandesVilles->id => ['Marrakech', 'Tanger', 'Fès', 'Agadir', 'Meknès', 'Oujda', 'Kénitra', 'Tétouan'],
            $peripheriques->id => [
                'Essaouira', 'Chefchaouen', 'El Jadida', 'Béni Mellal', 'Nador',
                'Ouarzazate', 'Taza', 'Larache', 'Settat', 'Khouribga', 'Errachidia', 'Al Hoceïma',
            ],
        ];

        foreach ($cities as $zoneId => $names) {
            foreach ($names as $name) {
                City::create([
                    'shipping_zone_id' => $zoneId,
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'is_active' => true,
                ]);
            }
        }
    }
}
