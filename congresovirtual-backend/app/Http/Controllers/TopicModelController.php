<?php

namespace App\Http\Controllers;

use App\Project;
use App\TopicModel;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopicModelController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try {
            set_time_limit(1800);
            $project = Project::findOrFail($request->project_id);

            if(isset($request->from_cache) && $request->from_cache) {
                $topicModelData = $project->topicModel;
            } else {
                $topicModelData = $this->getTopicModelData($project);
                $this->refreshProjectTopicModel($project, $topicModelData);
            }
            return response()->json($topicModelData, 200);
        } catch (RequestException $exception) {
            if($exception->getResponse()) {
                $error = json_decode($exception->getResponse()->getBody());
                return response()->json($error, $exception->getCode());
            } else {
                return response()->json([
                    'message' => 'Error: the topic model was not generated.'], 412);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error: the topic model was not generated.'], 412);
        }
    }

    private function getTopicModelData($project)
    {
        $client = new Client();
        $analyticRequest = $client->request(
            'GET',
            env('APP_ANALYTIC_URL') . '/topicmodel',
            [
                'query' => [
                    'project_id' => $project->id
                ]
            ]
        );
        return json_decode($analyticRequest->getBody());
    }

    private function refreshProjectTopicModel($project, $topicModelData)
    {
        DB::beginTransaction();
        if($project->topicModel) {
            $project->topicModel->forceDelete();
        }

        $topicModel = new TopicModel([
            'value' => $topicModelData->value
        ]);
        $topicModel->project()->associate($project);
        $topicModel->save();
        DB::commit();
    }
}
