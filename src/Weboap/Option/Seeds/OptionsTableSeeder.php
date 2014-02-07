<?php namespace Weboap\Option\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use \Carbon\Carbon;



class OptionsTableSeeder extends Seeder {

	/**
	 * Auto generated seed file
	 *
	 * @return void
	 */
	public function run()
	{
		
		DB::table('options')->insert(array (
			0 => 
			array (
				'key' 	=> 'option.version',
				'value' => '1.0',
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now()
			)
			
		));
	}

}
