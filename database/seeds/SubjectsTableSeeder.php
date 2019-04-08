<?php

use Illuminate\Database\Seeder;

class SubjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('subjects')->insert([
            array('id' => '1','name_crc' => '2053857913','name' => 'Grammar','sort' => '0'),
            array('id' => '2','name_crc' => '3239771201','name' => 'Reading','sort' => '0'),
            array('id' => '3','name_crc' => '2425997691','name' => 'Vocabulary','sort' => '0'),
            array('id' => '4','name_crc' => '2837906509','name' => 'Math','sort' => '0'),
            array('id' => '5','name_crc' => '1729573288','name' => 'Science','sort' => '0'),
            array('id' => '6','name_crc' => '2227358946','name' => 'Biology','sort' => '0'),
            array('id' => '7','name_crc' => '3850168202','name' => 'Chemistry','sort' => '0'),
            array('id' => '8','name_crc' => '3627149283','name' => 'Physics','sort' => '0'),
            array('id' => '9','name_crc' => '966937758','name' => 'Social Studies','sort' => '0'),
            array('id' => '10','name_crc' => '666529867','name' => 'History','sort' => '0')
        ]);
    }
}
