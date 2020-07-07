<?php

namespace App\Http\Controllers\Gamification;

use App\Article;
use App\Gamification\Event;
use App\Idea;
use App\Page;
use App\Project;
use App\Events\Gamification\RegisterReadEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     * About access control: all users can use this method (see routes).
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $query = [];
            if(isset($request->points)) {
                $query[] = ['points', $request->points];
            }
            if(isset($request->project_id)) {
                $query[] = ['project_id', $request->project_id];
            }
            if(isset($request->page_id)) {
                $query[] = ['page_id', $request->page_id];
            }
            if(isset($request->player_id)) {
                $query[] = ['player_id', $request->player_id];
            }
            if(isset($request->created_at)) {
                $query[] = ['created_at', $request->created_at];
            }
            if(isset($request->updated_at)) {
                $query[] = ['updated_at', $request->updated_at];
            }
            $events = Event::where($query);

            if(isset($request->start_created_at) && isset($request->end_created_at)) {
                $events = $events->whereBetween('created_at', [
                    $request->start_created_at,
                    $request->end_created_at
                ]);
            }

            if(isset($request->has_rewards)) {
                if($request->has_rewards == true) {
                    $events = $events->has('rewards');
                } else if($request->has_rewards == false) {
                    $events = $events->doesntHave('rewards');
                }
            }

            $totalResults = $events->count();

            if($request->has('order_by')) {
                $order = $request->query('order', 'ASC');
                $events = $events->orderBy($request->order_by, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $events = $events
                ->offset($offset)
                ->limit($limit > 100 ? 100 : $limit);
            $events = $events->with(['player', 'project', 'page', 'terms', 'actions', 'rewards'])->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($events),
                'results' => $events
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: events were not found.'], 412);
        }
    }

    /**
     * Store a newly created resource in storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'action_type' => 'required|string|max:191',
                'project_id' => 'integer|nullable',
                'article_id' => 'integer|nullable',
                'idea_id' => 'integer|nullable',
                'page_id' => 'integer|nullable'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $project = null;
            $article = null;
            $idea = null;
            $page = null;
            if(isset($request->project_id)) {
                if(isset($request->article_id) || isset($request->idea_id) || isset($request->page_id)) {
                    throw new \Exception();
                }
                $project = Project::findOrFail($request->project_id);
            } else if(isset($request->article_id)) {
                if(isset($request->idea_id) || isset($request->page_id)) {
                    throw new \Exception();
                }
                $article = Article::findOrFail($request->article_id);
            } else if(isset($request->idea_id)) {
                if(isset($request->page_id)) {
                    throw new \Exception();
                }
                $idea = Idea::findOrFail($request->idea_id);
            } else if(isset($request->page_id)) {
                $page = Page::findOrFail($request->page_id);
            } else {
                throw new \Exception();
            }

            $pipes = [];
            $actionType = strtoupper($request->action_type);
            switch ($actionType) {
                case 'READ':
                    $pipes[] = RegisterReadEvent::class;
                    break;
                default:
                    throw new \Exception();
            }

            $content = (object) [
                'project' => $project,
                'article' => $article,
                'idea' => $idea,
                'page' => $page
            ];
            $gamificationResult = app(Pipeline::class)
                ->send($content)
                ->through($pipes)
                ->then(function($result) {
                    return $result;
                });

            if(!$gamificationResult) {
                throw new \Exception();
            }

            $data['gamification_result'] = $gamificationResult;

            return response()->json([
                'message' => 'Successfully created event!',
                'data' => $data
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the event was not created.'], 412);
        }
    }

    /**
     * Display the specified resource.
     * About access control: all users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            return response()->json(Event::with(['player', 'project', 'page', 'terms', 'actions', 'rewards'])->findOrFail($id), 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the event was not found.'], 412);
        }
    }

    /**
     * Remove the specified resource from storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'force_delete' => 'boolean'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $event = Event::withTrashed()->findOrFail($id);
            $forceDelete = $request->query('force_delete', false);
            if($forceDelete) {
                $event->forceDelete();
            } else {
                $event->delete();
            }
            return response()->json([
                'message' => 'Successfully deleted event!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the event was not deleted.'], 412);
        }
    }

    /**
     * Remove the specified resource from storage temporarily.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function undelete($id)
    {
        try {
            Event::onlyTrashed()->findOrFail($id)->restore();
            return response()->json([
                'message' => 'Successfully undeleted event!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the event was not undeleted.'], 412);
        }
    }
}
