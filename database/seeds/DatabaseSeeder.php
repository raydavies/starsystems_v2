<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            LevelsTableSeeder::class,
            SubjectsTableSeeder::class,
            LevelsSubjectsTableSeeder::class,
            LessonsTableSeeder::class,
            StateProvincesTableSeeder::class,
            TestimonialsTableSeeder::class
        ]);
    }
}
