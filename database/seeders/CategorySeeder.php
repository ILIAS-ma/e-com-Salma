<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tree = [
            'Femme' => ['Robes', 'Hauts & T-shirts', 'Pantalons & Jeans', 'Vestes & Manteaux', 'Maille'],
            'Bijoux' => ['Colliers', 'Bracelets', 'Boucles d\'oreilles', 'Bagues'],
            'Homme' => ['Chemises', 'T-shirts', 'Pantalons', 'Vestes', 'Costumes'],
            'Enfant' => ['Fille 2-8 ans', 'Garçon 2-8 ans', 'Bébé'],
            'Accessoires' => ['Sacs', 'Ceintures', 'Foulards'],
        ];

        $position = 0;

        foreach ($tree as $rootName => $children) {
            $root = Category::create([
                'name' => $rootName,
                'slug' => Str::slug($rootName),
                'position' => $position++,
                'is_active' => true,
            ]);

            $childPosition = 0;

            foreach ($children as $childName) {
                Category::create([
                    'parent_id' => $root->id,
                    'name' => $childName,
                    'slug' => Str::slug($rootName.'-'.$childName),
                    'position' => $childPosition++,
                    'is_active' => true,
                ]);
            }
        }
    }
}
