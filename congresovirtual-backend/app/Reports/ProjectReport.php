<?php

namespace App\Reports;

use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\WordCloudController;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class ProjectReport
{
    protected $report, $project, $projectControllerInstance, $commentControllerInstance, $wordCloudControllerInstance;

    /**
     * Create a new project report instance.
     *
     * @param object $project
     * @param object $projectControllerInstance
     * @return void
     */
    public function __construct($project, $projectControllerInstance = null)
    {
        $this->project = $project;

        if($projectControllerInstance) {
            $this->projectControllerInstance = $projectControllerInstance;
        } else {
            $this->projectControllerInstance = new ProjectController();
        }

        $this->commentControllerInstance = new CommentController();
        $this->wordCloudControllerInstance = new WordCloudController();
    }

    public function generateReport()
    {
        return \PDF::loadView('report.project', $this->generateDataArray());
    }

    public function generateView()
    {
        return view('report.project', $this->generateDataArray());
    }

    private function generateDataArray()
    {
        $usersParticipantsOnProject = $this->projectControllerInstance->usersParticipantsOnProject($this->project->id);
        $usersWithProjectTerms = $this->projectControllerInstance->usersWithProjectTerms($this->project->id);
        $usersParticipantsGenderCount = $this->usersParticipantsGenderCount($usersParticipantsOnProject);
        $usersParticipantsAgeRangeCount = $this->usersParticipantsAgeRangeCount($usersParticipantsOnProject);
        $usersParticipantsAgeRangeGenderCount = $this->usersParticipantsAgeRangeGenderCount($usersParticipantsOnProject);
        $usersParticipantsProvincesCount = $this->usersParticipantsProvincesCount($usersParticipantsOnProject);
        $wordCloudData = $this->getWordCloudData($this->project);
        $wordCloudData = $wordCloudData ? array_slice($wordCloudData, 0, 100) : false;
        $projectCommentsRanking = (object) [
            'total_all_votes' => $this->getProjectCommentsRanking($this->project->id, 'TOTAL_ALL_VOTES'),
            'accord_votes' => $this->getProjectCommentsRanking($this->project->id, 'ACCORD_VOTES'),
            'desaccord_votes' => $this->getProjectCommentsRanking($this->project->id, 'DESACCORD_VOTES')
        ];
        $ideasCommentsRanking = $this->getIdeasCommentsRanking($this->project->ideas);
        $articlesCommentsRanking = $this->getArticlesCommentsRanking($this->project->articles);

        return [
            'project' => $this->project,
            'fechaInicio' =>  Carbon::create($this->project->fecha_inicio),
            'fechaTermino' =>  Carbon::create($this->project->fecha_termino),
            'usersParticipantsOnProject' => $usersParticipantsOnProject,
            'usersWithProjectTerms' => $usersWithProjectTerms,
            'usersParticipantsGenderCount' => $usersParticipantsGenderCount,
            'usersParticipantsAgeRangeCount' => $usersParticipantsAgeRangeCount,
            'usersParticipantsAgeRangeGenderCount' => $usersParticipantsAgeRangeGenderCount,
            'usersParticipantsProvincesCount' => $usersParticipantsProvincesCount,
            'wordCloudData' => $wordCloudData,
            'projectCommentsRanking' => $projectCommentsRanking,
            'ideasCommentsRanking' => $ideasCommentsRanking,
            'articlesCommentsRanking' => $articlesCommentsRanking
        ];
    }

    private function usersParticipantsGenderCount($userParticipants)
    {
        $usersParticipantsGenderCount = (object) [
            'male' => 0,
            'female' => 0,
            'other' => 0,
            'not_answer' => 0
        ];
        foreach($userParticipants as $userParticipant) {
            $userGender = $this->determineUserGender($userParticipant);
            $usersParticipantsGenderCount->{$userGender}++;
        }
        return $usersParticipantsGenderCount;
    }

    private function usersParticipantsAgeRangeCount($userParticipants)
    {
        $usersParticipantsAgeRangeCount = (object) [
            '_10_19' => 0,
            '_20_29' => 0,
            '_30_39' => 0,
            '_40_49' => 0,
            '_50_59' => 0,
            '_60_69' => 0,
            '_70_79' => 0,
            '_80_89' => 0,
            'total' => 0
        ];
        foreach($userParticipants as $userParticipant) {
            $userAgeRange = $this->determineUserAgeRange($userParticipant);
            if($userAgeRange) {
                $usersParticipantsAgeRangeCount->{$userAgeRange}++;
                $usersParticipantsAgeRangeCount->total++;
            }
        }
        return $usersParticipantsAgeRangeCount;
    }

    private function usersParticipantsAgeRangeGenderCount($userParticipants)
    {
        $usersParticipantsAgeRangeGenderCount = (object) [
            '_10_19' => (object) [
                'male' => 0,
                'female' => 0,
                'other' => 0,
                'not_answer' => 0
            ],
            '_20_29' => (object) [
                'male' => 0,
                'female' => 0,
                'other' => 0,
                'not_answer' => 0
            ],
            '_30_39' => (object) [
                'male' => 0,
                'female' => 0,
                'other' => 0,
                'not_answer' => 0
            ],
            '_40_49' => (object) [
                'male' => 0,
                'female' => 0,
                'other' => 0,
                'not_answer' => 0
            ],
            '_50_59' => (object) [
                'male' => 0,
                'female' => 0,
                'other' => 0,
                'not_answer' => 0
            ],
            '_60_69' => (object) [
                'male' => 0,
                'female' => 0,
                'other' => 0,
                'not_answer' => 0
            ],
            '_70_79' => (object) [
                'male' => 0,
                'female' => 0,
                'other' => 0,
                'not_answer' => 0
            ],
            '_80_89' => (object) [
                'male' => 0,
                'female' => 0,
                'other' => 0,
                'not_answer' => 0
            ]
        ];
        foreach($userParticipants as $userParticipant) {
            $userAgeRange = $this->determineUserAgeRange($userParticipant);
            if($userAgeRange) {
                $userGender = $this->determineUserGender($userParticipant);
                $usersParticipantsAgeRangeGenderCount->{$userAgeRange}->{$userGender}++;
            }
        }
        return $usersParticipantsAgeRangeGenderCount;
    }

    private function determineUserAgeRange($user)
    {
        $now = Carbon::now();
        $userBirthday = Carbon::create($user->birthday);
        $userAges = $userBirthday->diffInYears($now);

        if($userAges >= 10 && $userAges <= 19) {
            return '_10_19';
        } else if($userAges >= 20 && $userAges <= 29) {
            return '_20_29';
        } else if($userAges >= 30 && $userAges <= 39) {
            return '_30_39';
        } else if($userAges >= 40 && $userAges <= 49) {
            return '_40_49';
        } else if($userAges >= 50 && $userAges <= 59) {
            return '_50_59';
        } else if($userAges >= 60 && $userAges <= 69) {
            return '_60_69';
        } else if($userAges >= 70 && $userAges <= 79) {
            return '_70_79';
        } else if($userAges >= 80 && $userAges <= 89) {
            return '_80_89';
        }
        return null;
    }

    private function determineUserGender($user)
    {
        if($user->genero == 1) {
            return 'male';
        } else if($user->genero == 2) {
            return 'female';
        } else if($user->genero == 3) {
            return 'other';
        }
        return 'not_answer';
    }

    private function usersParticipantsProvincesCount($userParticipants)
    {
        $usersParticipantsProvincesCount = (object) [
            (object) [
                'code' => 'CL-AN',
                'name' => 'Antofagasta',
                'count' => 0
            ],
            (object) [
                'code' => 'CL-AR',
                'name' => 'Araucanía',
                'count' => 0
            ],
            (object) [
                'code' => 'CL-AP',
                'name' => 'Arica y Parinacota',
                'count' => 0
            ],
            (object) [
                'code' => 'CL-AT',
                'name' => 'Atacama',
                'count' => 0
            ],
            (object) [
                'code' => 'CL-AI',
                'name' => 'Aysén del Gral. Carlos Ibáñez del Campo',
                'count' => 0
            ],
            (object) [
                'code' => 'CL-BI',
                'name' => 'Bío Bío',
                'count' => 0
            ],
            (object) [
                'code' => 'CL-CO',
                'name' => 'Coquimbo',
                'count' => 0
            ],
            (object) [
                'code' => 'CL-LI',
                'name' => 'Libertador General Bernardo O\'Higgins',
                'count' => 0
            ],
            (object) [
                'code' => 'CL-LL',
                'name' => 'Los Lagos',
                'count' => 0
            ],
            (object) [
                'code' => 'CL-LR',
                'name' => 'Los Ríos',
                'count' => 0
            ],
            (object) [
                'code' => 'CL-MA',
                'name' => 'Magallanes y de la Antártica Chilena',
                'count' => 0
            ],
            (object) [
                'code' => 'CL-ML',
                'name' => 'Maule',
                'count' => 0
            ],
            (object) [
                'code' => 'CL-RM',
                'name' => 'Metropolitana de Santiago',
                'count' => 0
            ],
            (object) [
                'code' => 'CL-NB', // = 'CL-BI'
                'name' => 'Ñuble',
                'count' => 0
            ],
            (object) [
                'code' => 'CL-TA',
                'name' => 'Tarapacá',
                'count' => 0
            ],
            (object) [
                'code' => 'CL-VS',
                'name' => 'Valparaíso',
                'count' => 0
            ]
        ];
        foreach($userParticipants as $userParticipant) {
            if($userParticipant->pais == 'Chile') {
                foreach($usersParticipantsProvincesCount as $usersParticipantsProvinceCount) {
                    if($usersParticipantsProvinceCount->name == $userParticipant->region) {
                        $usersParticipantsProvinceCount->count++;
                    }
                }
            }
        }

        return array_values(Arr::sort((array)$usersParticipantsProvincesCount, function ($usersParticipantsProvinceCount) {
            return $usersParticipantsProvinceCount->count;
        }));
    }

    private function getWordCloudData($project)
    {
        try {
            return $this->wordCloudControllerInstance->getWordCloudData($project);

            /*$client = new \GuzzleHttp\Client();
            $analyticRequest = $client->request(
                'GET',
                'https://api-congresovirtual.senado.cl/api/wordcloud',
                [
                    'query' => [
                        'project_id' => $project->id
                    ]
                ]
            );
            return json_decode(($analyticRequest->getBody()));*/
        } catch (\Exception $exception) {
            return [];
        }
    }

    private function getProjectCommentsRanking($projectId, $option)
    {
        return $this->commentControllerInstance->indexSortedBy($option, 'DESC', 3, 0, $projectId);
    }

    private function getIdeasCommentsRanking($ideas)
    {
        $ideasCommentsRanking = (object) [];
        foreach($ideas as $idea) {
            $ideasCommentsRanking->{$idea->id} = (object) [
                'total_all_votes' => $this->commentControllerInstance->indexSortedBy('TOTAL_ALL_VOTES', 'DESC', 3, 0, null, null, $idea->id),
                'accord_votes' => $this->commentControllerInstance->indexSortedBy('ACCORD_VOTES', 'DESC', 3, 0, null, null, $idea->id),
                'desaccord_votes' => $this->commentControllerInstance->indexSortedBy('DESACCORD_VOTES', 'DESC', 3, 0, null, null, $idea->id)
            ];
        }

        return $ideasCommentsRanking;
    }

    private function getArticlesCommentsRanking($articles)
    {
        $articlesCommentsRanking = (object) [];
        foreach($articles as $article) {
            $articlesCommentsRanking->{$article->id} = (object) [
                'total_all_votes' => $this->commentControllerInstance->indexSortedBy('TOTAL_ALL_VOTES', 'DESC', 3, 0, null, $article->id),
                'accord_votes' => $this->commentControllerInstance->indexSortedBy('ACCORD_VOTES', 'DESC', 3, 0, null, $article->id),
                'desaccord_votes' => $this->commentControllerInstance->indexSortedBy('DESACCORD_VOTES', 'DESC', 3, 0, null, $article->id)
            ];
        }

        return $articlesCommentsRanking;
    }
}
