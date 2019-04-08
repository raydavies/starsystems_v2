<?php

use Illuminate\Database\Seeder;

class StateProvincesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('state_provinces')->insert([
            array('id' => '1','name' => 'Alabama','abbreviation' => 'AL','country' => 'USA','sort' => '0'),
            array('id' => '2','name' => 'Alaska','abbreviation' => 'AK','country' => 'USA','sort' => '0'),
            array('id' => '3','name' => 'Arizona','abbreviation' => 'AZ','country' => 'USA','sort' => '0'),
            array('id' => '4','name' => 'Arkansas','abbreviation' => 'AR','country' => 'USA','sort' => '0'),
            array('id' => '5','name' => 'California','abbreviation' => 'CA','country' => 'USA','sort' => '0'),
            array('id' => '6','name' => 'Colorado','abbreviation' => 'CO','country' => 'USA','sort' => '0'),
            array('id' => '7','name' => 'Connecticut','abbreviation' => 'CT','country' => 'USA','sort' => '0'),
            array('id' => '8','name' => 'Delaware','abbreviation' => 'DE','country' => 'USA','sort' => '0'),
            array('id' => '9','name' => 'Florida','abbreviation' => 'FL','country' => 'USA','sort' => '0'),
            array('id' => '10','name' => 'Georgia','abbreviation' => 'GA','country' => 'USA','sort' => '0'),
            array('id' => '11','name' => 'Hawaii','abbreviation' => 'HI','country' => 'USA','sort' => '0'),
            array('id' => '12','name' => 'Idaho','abbreviation' => 'ID','country' => 'USA','sort' => '0'),
            array('id' => '13','name' => 'Illinois','abbreviation' => 'IL','country' => 'USA','sort' => '0'),
            array('id' => '14','name' => 'Indiana','abbreviation' => 'IN','country' => 'USA','sort' => '0'),
            array('id' => '15','name' => 'Iowa','abbreviation' => 'IA','country' => 'USA','sort' => '0'),
            array('id' => '16','name' => 'Kansas','abbreviation' => 'KS','country' => 'USA','sort' => '0'),
            array('id' => '17','name' => 'Kentucky','abbreviation' => 'KY','country' => 'USA','sort' => '0'),
            array('id' => '18','name' => 'Louisiana','abbreviation' => 'LA','country' => 'USA','sort' => '0'),
            array('id' => '19','name' => 'Maine','abbreviation' => 'ME','country' => 'USA','sort' => '0'),
            array('id' => '20','name' => 'Maryland','abbreviation' => 'MD','country' => 'USA','sort' => '0'),
            array('id' => '21','name' => 'Massachusetts','abbreviation' => 'MA','country' => 'USA','sort' => '0'),
            array('id' => '22','name' => 'Michigan','abbreviation' => 'MI','country' => 'USA','sort' => '0'),
            array('id' => '23','name' => 'Minnesota','abbreviation' => 'MN','country' => 'USA','sort' => '0'),
            array('id' => '24','name' => 'Mississippi','abbreviation' => 'MS','country' => 'USA','sort' => '0'),
            array('id' => '25','name' => 'Missouri','abbreviation' => 'MO','country' => 'USA','sort' => '0'),
            array('id' => '26','name' => 'Montana','abbreviation' => 'MT','country' => 'USA','sort' => '0'),
            array('id' => '27','name' => 'Nebraska','abbreviation' => 'NE','country' => 'USA','sort' => '0'),
            array('id' => '28','name' => 'Nevada','abbreviation' => 'NV','country' => 'USA','sort' => '0'),
            array('id' => '29','name' => 'New Hampshire','abbreviation' => 'NH','country' => 'USA','sort' => '0'),
            array('id' => '30','name' => 'New Jersey','abbreviation' => 'NJ','country' => 'USA','sort' => '0'),
            array('id' => '31','name' => 'New Mexico','abbreviation' => 'NM','country' => 'USA','sort' => '0'),
            array('id' => '32','name' => 'New York','abbreviation' => 'NY','country' => 'USA','sort' => '0'),
            array('id' => '33','name' => 'North Carolina','abbreviation' => 'NC','country' => 'USA','sort' => '0'),
            array('id' => '34','name' => 'North Dakota','abbreviation' => 'ND','country' => 'USA','sort' => '0'),
            array('id' => '35','name' => 'Ohio','abbreviation' => 'OH','country' => 'USA','sort' => '0'),
            array('id' => '36','name' => 'Oklahoma','abbreviation' => 'OK','country' => 'USA','sort' => '0'),
            array('id' => '37','name' => 'Oregon','abbreviation' => 'OR','country' => 'USA','sort' => '0'),
            array('id' => '38','name' => 'Pennsylvania','abbreviation' => 'PA','country' => 'USA','sort' => '0'),
            array('id' => '39','name' => 'Rhode Island','abbreviation' => 'RI','country' => 'USA','sort' => '0'),
            array('id' => '40','name' => 'South Carolina','abbreviation' => 'SC','country' => 'USA','sort' => '0'),
            array('id' => '41','name' => 'South Dakota','abbreviation' => 'SD','country' => 'USA','sort' => '0'),
            array('id' => '42','name' => 'Tennessee','abbreviation' => 'TN','country' => 'USA','sort' => '0'),
            array('id' => '43','name' => 'Texas','abbreviation' => 'TX','country' => 'USA','sort' => '0'),
            array('id' => '44','name' => 'Utah','abbreviation' => 'UT','country' => 'USA','sort' => '0'),
            array('id' => '45','name' => 'Vermont','abbreviation' => 'VT','country' => 'USA','sort' => '0'),
            array('id' => '46','name' => 'Virginia','abbreviation' => 'VA','country' => 'USA','sort' => '0'),
            array('id' => '47','name' => 'Washington','abbreviation' => 'WA','country' => 'USA','sort' => '0'),
            array('id' => '48','name' => 'West Virginia','abbreviation' => 'WV','country' => 'USA','sort' => '0'),
            array('id' => '49','name' => 'Wisconsin','abbreviation' => 'WI','country' => 'USA','sort' => '0'),
            array('id' => '50','name' => 'Wyoming','abbreviation' => 'WY','country' => 'USA','sort' => '0'),
            array('id' => '51','name' => 'Washington DC','abbreviation' => 'DC','country' => 'USA','sort' => '0')
        ]);
    }
}
