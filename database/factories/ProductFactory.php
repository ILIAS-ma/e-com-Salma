<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected array $names = [
        'Femme' => ['Robe Midi Plissée', 'Trench Coat Structuré', 'Chemisier en Satin', 'Pantalon Palazzo', 'Pull Côtelé', 'Blazer Oversize', 'Jupe Portefeuille', 'Combinaison Fluide'],
        'Homme' => ['Chemise Oxford', 'Veste en Toile', 'Pantalon Chino', 'Pull Col Rond', 'Costume Slim Fit', 'Polo Piqué', 'Manteau en Laine', 'Jean Droit'],
        'Enfant' => ['Robe à Fleurs', 'T-shirt Imprimé', 'Salopette Denim', 'Sweat à Capuche', 'Short en Coton'],
        'Bijoux' => ['Collier Fin Plaqué Or', 'Bracelet Chaîne', 'Boucles d\'Oreilles Perle', 'Bague Signet', 'Collier Pendentif Nacre', 'Bracelet Jonc'],
        'Accessoires' => ['Foulard en Soie', 'Sac Cabas Cuir', 'Ceinture Tressée', 'Écharpe en Laine'],
    ];

    protected array $collections = ['Nouvelle Collection', 'Édition Capsule', 'Essentiels', 'Ramadan Essentials'];

    public function definition(): array
    {
        $category = Category::whereNotNull('parent_id')->with('parent')->inRandomOrder()->first();
        $root = $category?->parent?->name ?? 'Femme';
        $isJewelry = $root === 'Bijoux';

        $name = $this->faker->randomElement($this->names[$root] ?? $this->names['Femme']);
        $name .= $isJewelry ? '' : ' '.$this->faker->numberBetween(100, 999);

        $price = $isJewelry
            ? $this->faker->randomFloat(2, 89, 590)
            : $this->faker->randomFloat(2, 149, 1290);

        $onSale = $this->faker->boolean(25);

        $gender = match ($root) {
            'Homme' => 'homme',
            'Enfant' => 'enfant',
            'Bijoux', 'Accessoires' => 'mixte',
            default => 'femme',
        };

        return [
            'category_id' => $category?->id,
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(5),
            'description' => $this->faker->paragraphs(2, true),
            'composition' => $isJewelry ? 'Laiton plaqué or 18 carats, sans nickel' : '98% Coton, 2% Élasthanne',
            'care_instructions' => $isJewelry
                ? 'Éviter le contact avec l\'eau et les parfums, ranger à l\'abri de la lumière.'
                : 'Lavage à 30°C, ne pas repasser sur les impressions, ne pas utiliser de sèche-linge.',
            'price' => $price,
            'compare_at_price' => $onSale ? round($price * 1.3, 2) : null,
            'collection' => $isJewelry ? 'Bijoux' : $this->faker->randomElement($this->collections),
            'gender' => $gender,
            'is_active' => true,
            'is_featured' => $this->faker->boolean(20),
            'sales_count' => $this->faker->numberBetween(0, 500),
            'published_at' => now()->subDays($this->faker->numberBetween(0, 60)),
        ];
    }
}
