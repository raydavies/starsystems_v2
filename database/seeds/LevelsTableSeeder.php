<?php

use Illuminate\Database\Seeder;

class LevelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('levels')->insert([
            array('id' => '1','name' => 'Primary','grade_range' => 'K-3rd','sort' => '0'),
            array('id' => '2','name' => 'Intermediate','grade_range' => '4-6th','sort' => '0'),
            array('id' => '3','name' => 'Advanced','grade_range' => '7-9th','sort' => '0'),
            array('id' => '4','name' => 'Pre-College','grade_range' => '10-12th','sort' => '0')
        ]);
    }
}
