<?php namespace Weboap\Option\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use \Carbon\Carbon;
use Option as o;

class OptionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        o::set('option.version', '2.0.0');
    }
}
