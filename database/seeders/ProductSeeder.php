<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    protected array $colors = [
        'Noir' => '#111111',
        'Écru' => '#EDE7DA',
        'Camel' => '#B99B72',
        'Bleu Marine' => '#22293B',
        'Beige' => '#D9CFBF',
        'Taupe' => '#8A8072',
    ];

    protected array $jewelryFinishes = [
        'Doré' => '#C9A24B',
        'Argenté' => '#B8B8B8',
        'Or Rose' => '#D6A98E',
    ];

    public function run(): void
    {
        Product::factory()
            ->count(48)
            ->create()
            ->each(function (Product $product) {
                $isJewelry = $product->collection === 'Bijoux';
                $palette = $isJewelry ? $this->jewelryFinishes : $this->colors;
                $colorNames = collect($palette)->keys()->random(min(count($palette), random_int(2, 3)));
                $sizes = $isJewelry ? ['Taille unique'] : ['XS', 'S', 'M', 'L', 'XL'];

                foreach ($colorNames as $index => $colorName) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'color_code' => $palette[$colorName],
                        'path' => "https://picsum.photos/seed/{$product->id}-{$index}/800/1000",
                        'position' => $index,
                        'is_primary' => $index === 0,
                    ]);

                    foreach ($sizes as $size) {
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'sku' => strtoupper('SKU-'.$product->id.'-'.substr($colorName, 0, 2).'-'.substr($size, 0, 2)),
                            'size' => $size,
                            'color_name' => $colorName,
                            'color_code' => $palette[$colorName],
                            'stock' => random_int(0, 30),
                        ]);
                    }
                }
            });
    }
}
