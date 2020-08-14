<?php

use App\Models\Organisation;
use Illuminate\Database\Seeder;

class OrganisationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Organisation::class, 7)->create();
    }
}
