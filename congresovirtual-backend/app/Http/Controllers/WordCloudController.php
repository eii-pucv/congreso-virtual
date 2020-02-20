<?php

namespace App\Http\Controllers;

use App\Project;
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
            $words = $request->query('words', 10000);
            $project = Project::findOrFail($request->project_id);

            $client = new \GuzzleHttp\Client();
            $analyticRequest = $client->request(
                'GET',
                env('APP_ANALYTIC_URL') . '/wordcloud',
                [
                    'query' => [
                        'words' => $words,
                        'project_id' => $project->id
                    ]
                ]
            );
            $analyticResponse = json_decode(($analyticRequest->getBody()));
            if(!is_array($analyticResponse) || !isset($analyticResponse[0])) {
                $strWordCloud = $project->resumen;
            } else {
                $strWordCloud = $analyticResponse[0];
            }
            $wordCloudData = $this->loadWordCloud($strWordCloud, $project);

            return response()->json($wordCloudData);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the word cloud was not generated.'], 412);
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
