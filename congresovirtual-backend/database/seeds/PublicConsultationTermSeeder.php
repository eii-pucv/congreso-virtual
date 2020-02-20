<?php

use App\PublicConsultation;
use App\PublicConsultationTerm;
use App\Term;
use Illuminate\Database\Seeder;

class PublicConsultationTermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $i = 0;
        while($i < 10) {
            do {
                $publicConsultationTermCandidate = [
                    'public_consultation_id' => PublicConsultation::all()->random()->id,
                    'term_id' => Term::all()->random()->id
                ];
            } while($this->itAlreadyExists($publicConsultationTermCandidate));

            PublicConsultationTerm::create([
                'public_consultation_id' => $publicConsultationTermCandidate['public_consultation_id'],
                'term_id' => $publicConsultationTermCandidate['term_id']
            ]);
            $i++;
        }
    }

    private function itAlreadyExists($candidate)
    {
        $publicConsultationTerm = PublicConsultationTerm::where([
            ['public_consultation_id', $candidate['public_consultation_id']],
            ['term_id', $candidate['term_id']]
        ])->first();

        return $publicConsultationTerm != null;
    }
}
