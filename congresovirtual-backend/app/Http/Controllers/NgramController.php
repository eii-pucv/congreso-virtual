<?php

namespace App\Http\Controllers;

use App\Project;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class NgramController extends Controller
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
            $minWords = $request->query('min_words', null);
            $project = Project::findOrFail($request->project_id);
            $ngramData = $this->getNgramData($project, $minWords);

            return response()->json($ngramData, 200);
        } catch (RequestException $exception) {
            if($exception->getResponse()) {
                $error = json_decode($exception->getResponse()->getBody());
                return response()->json($error, $exception->getCode());
            } else {
                return response()->json([
                    'message' => 'Error: the n-gram was not generated.'], 412);
            }
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the n-gram was not generated.'], 412);
        }
    }

    public function getNgramData($project, $minWords = null)
    {
        $client = new Client();
        $analyticRequest = $client->request(
            'GET',
            env('APP_ANALYTIC_URL') . '/ngram',
            [
                'query' => [
                    'min_words' => $minWords,
                    'project_id' => $project->id
                ]
            ]
        );
        return json_decode($analyticRequest->getBody());
    }
}
