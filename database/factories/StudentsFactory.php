<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StudentsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'=>$this->faker->name(),
            'matric'=>'BZ23AC'.$this->faker->unique()->randomNumber(3),
            'email'=>$this->faker->email,
            'phone'=>$this->faker->phoneNumber,
            'address'=>$this->faker->address,
            'dob'=>$this->faker->date(),
            'gender'=>$this->faker->randomElement(['male', 'female']),
            'admission_batch_id'=>'2023',
            'campus_id'=>$this->faker->randomElement(\App\Models\Campus::pluck('id')->toArray()),
            'program_id'=>$this->faker->randomElement(\App\Models\SchoolUnits::pluck('id')->toArray()),
        ];
    }
}
