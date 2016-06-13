<?php

use Illuminate\Database\Seeder;

class TaxonomyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vocabulary = Taxonomy::createVocabulary('cenario');

        Taxonomy::createTerm($vocabulary->id, 'caso_de_teste');

        $columns = Schema::getColumnListing('datas');

        foreach($columns as $column) {
            if ($column != 'cenario') {
                Taxonomy::createVocabulary($column);
            }
        }
    }
}
