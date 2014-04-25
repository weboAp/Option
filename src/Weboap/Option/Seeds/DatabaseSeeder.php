<?php namespace Weboap\Option\Seeds;

use \Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();
        // $this->call('UserTableSeeder');
        $this->call('Weboap\Option\Seeds\OptionsTableSeeder');
    }
}