<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        
        return [
            
    
                'title' => fake()->title,
                'description' =>  $this->faker->sentence(45),
                'price' => $this->faker->randomfloat(2,0,40),
                'stock' => $this->faker->numberbetween(0,100),
                'image' => fake()->randomelement(['images/image.jpeg','images/image2.png','images/image5.jpeg']),

        ];
    }
}
