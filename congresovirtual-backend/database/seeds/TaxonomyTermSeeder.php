<?php

use App\Taxonomy;
use App\Term;
use App\TaxonomyTerm;
use Illuminate\Database\Seeder;

class TaxonomyTermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $taxonomies = Taxonomy::all();
        $terms = Term::all();

        foreach ($taxonomies as $taxonomy) {
            foreach($terms as $term) {
                TaxonomyTerm::create([
                    'taxonomy_id' => $taxonomy->id,
                    'term_id' => $term->id
                ]);
            }
        }
    }
}
