<?php

use Illuminate\Database\Seeder;

class LevelsSubjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('levels_subjects')->insert([
            array('id' => '1','level_id' => '1','subject_id' => '1'),
            array('id' => '2','level_id' => '1','subject_id' => '2'),
            array('id' => '3','level_id' => '1','subject_id' => '3'),
            array('id' => '4','level_id' => '1','subject_id' => '4'),
            array('id' => '5','level_id' => '1','subject_id' => '5'),
            array('id' => '6','level_id' => '1','subject_id' => '9'),
            array('id' => '7','level_id' => '2','subject_id' => '1'),
            array('id' => '8','level_id' => '2','subject_id' => '2'),
            array('id' => '9','level_id' => '2','subject_id' => '3'),
            array('id' => '10','level_id' => '2','subject_id' => '4'),
            array('id' => '11','level_id' => '2','subject_id' => '5'),
            array('id' => '12','level_id' => '2','subject_id' => '9'),
            array('id' => '13','level_id' => '3','subject_id' => '1'),
            array('id' => '14','level_id' => '3','subject_id' => '2'),
            array('id' => '15','level_id' => '3','subject_id' => '3'),
            array('id' => '16','level_id' => '3','subject_id' => '4'),
            array('id' => '17','level_id' => '3','subject_id' => '5'),
            array('id' => '18','level_id' => '3','subject_id' => '9'),
            array('id' => '19','level_id' => '4','subject_id' => '2'),
            array('id' => '20','level_id' => '4','subject_id' => '4'),
            array('id' => '21','level_id' => '4','subject_id' => '6'),
            array('id' => '22','level_id' => '4','subject_id' => '7'),
            array('id' => '23','level_id' => '4','subject_id' => '8'),
            array('id' => '24','level_id' => '4','subject_id' => '10')
        ]);
    }
}
