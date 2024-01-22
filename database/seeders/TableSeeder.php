<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = ['Table 1', 'Table 2', 'Table 3', 'Table 4', 'Table 5'];

        foreach ($names as $name) {
            $model = new Table();
            $model->fill([
                'name' => $name
            ]);
            $model->save();
        }
    }
}
