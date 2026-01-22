<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        $categoryId = $this->faker->numberBetween(1, 5);
        $gender     = $this->faker->numberBetween(1, 3);
        $tel        = $this->faker->numerify('0##########');

        return [
            'category_id' => $categoryId,
            'first_name'  => $this->faker->firstName(),
            'last_name'   => $this->faker->lastName(),
            'gender'      => $gender,
            'email'       => $this->faker->unique()->safeEmail(),
            'tel'         => $tel,
            'address'     => $this->faker->address(),
            'building'    => $this->faker->optional()->streetName(),
            'detail'      => $this->faker->realText(80),
        ];
    }
}
