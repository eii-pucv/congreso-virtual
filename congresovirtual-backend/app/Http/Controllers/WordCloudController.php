<?php

namespace App\Http\Controllers;

use App\Project;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class WordCloudController extends Controller
{
    /**
     * Display the specified resource.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try {
            $maxWords = $request->query('max_words', 100);
            $project = Project::findOrFail($request->project_id);
            $wordCloudData = $this->getWordCloudData($project, $maxWords);

            return response()->json($wordCloudData, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the word cloud was not generated.'
            ], 412);
        }
    }

    public function getWordCloudData($project, $maxWords = 100)
    {
        try {
            $client = new Client();
            $analyticRequest = $client->request(
                'GET',
                env('APP_ANALYTIC_URL') . '/wordcloud',
                [
                    'query' => [
                        'max_words' => $maxWords,
                        'project_id' => $project->id
                    ]
                ]
            );
            $analyticResponse = json_decode($analyticRequest->getBody());
            $strWordCloud = $analyticResponse->value;

            return $this->loadWordCloud($strWordCloud, $project);
        } catch (RequestException $exception) {
            $strWordCloud = $project->resumen;
            return $this->loadWordCloud($strWordCloud, $project);
        }
    }

    private function loadWordCloud($strWordCloud, $project)
    {
        $wordCloud = [];
        $projectStopwords = $project->stopwords()->pluck('value')->toArray();
        $matrixWords = $this->wordsCount($strWordCloud, $projectStopwords);

        foreach($matrixWords as $word => $count) {
            $wordCloud[] = [
                'word' => $word,
                'freq' => $count
            ];
        }
        return $wordCloud;
    }

    private function wordsCount($strWordCloud, $projectStopwords)
    {
        $strWordCloud = mb_strtolower($strWordCloud);
        $arrWordCloud = preg_split("/[\s,.;:?¿!¡=+-_)(%<>°]+/u", $strWordCloud);
        $arrWordCloudClean = array_diff($arrWordCloud, $projectStopwords);
        $arrWordCloudCount = array_count_values($arrWordCloudClean);
        arsort($arrWordCloudCount);

        return $arrWordCloudCount;
    }
}
