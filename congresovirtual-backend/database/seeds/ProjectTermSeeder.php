<?php

use App\ProjectTerm;
use App\Project;
use App\Term;
use Illuminate\Database\Seeder;

class ProjectTermSeeder extends Seeder
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
                $projectTermCandidate = [
                    'project_id' => Project::all()->random()->id,
                    'term_id' => Term::all()->random()->id
                ];
            } while($this->itAlreadyExists($projectTermCandidate));

            ProjectTerm::create([
                'project_id' => $projectTermCandidate['project_id'],
                'term_id' => $projectTermCandidate['term_id']
            ]);
            $i++;
        }
    }

    private function itAlreadyExists($candidate)
    {
        $projectTerm = ProjectTerm::where([
            ['project_id', $candidate['project_id']],
            ['term_id', $candidate['term_id']]
        ])->first();

        return $projectTerm != null;
    }
}
