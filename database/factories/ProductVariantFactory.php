<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductVariantFactory extends Factory
{
    protected array $colors = [
        'Terracotta' => '#C1440E',
        'Écru' => '#EDE4D3',
        'Noir' => '#1A1A1A',
        'Camel' => '#C19A6B',
        'Vert Olive' => '#708238',
        'Bleu Marine' => '#1B2A4A',
        'Rose Poudré' => '#E8C4C4',
        'Ocre' => '#CC7722',
    ];

    public function definition(): array
    {
        $colorName = $this->faker->randomElement(array_keys($this->colors));

        return [
            'product_id' => Product::factory(),
            'sku' => strtoupper(Str::random(8)),
            'size' => $this->faker->randomElement(['XS', 'S', 'M', 'L', 'XL']),
            'color_name' => $colorName,
            'color_code' => $this->colors[$colorName],
            'stock' => $this->faker->numberBetween(0, 40),
        ];
    }
}
