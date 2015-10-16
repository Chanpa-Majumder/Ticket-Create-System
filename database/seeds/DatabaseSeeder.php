<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(ItemDataSeeder::class);

        Model::reguard();
    }
}

class ItemDataSeeder extends Seeder {

    public function run()
    {
        DB::table('items')->delete();

        for ($i = 1; $i <= 100; $i++) {
            Item::create(array('title' => 'item test ' . $i));
        }
    }

}
