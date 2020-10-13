<?php

use Illuminate\Database\Seeder;

class GradeLevelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('grade_levels')->insert([
            array('name' => 'Kindergarten', 'level' => '0'),
            array('name' => 'First Grade', 'level' => '1'),
            array('name' => 'Second Grade', 'level' => '2'),
            array('name' => 'Third Grade', 'level' => '3'),
            array('name' => 'Fourth Grade', 'level' => '4'),
            array('name' => 'Fifth Grade', 'level' => '5'),
            array('name' => 'Sixth Grade', 'level' => '6'),
            array('name' => 'Seventh Grade', 'level' => '7'),
            array('name' => 'Eighth Grade', 'level' => '8'),
            array('name' => 'Ninth Grade', 'level' => '9'),
            array('name' => 'Tenth Grade', 'level' => '10'),
            array('name' => 'Eleventh Grade', 'level' => '11'),
            array('name' => 'Twelfth Grade', 'level' => '12'),
        ]);
    }
}
