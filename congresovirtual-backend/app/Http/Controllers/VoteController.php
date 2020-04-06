<?php

namespace App\Http\Controllers;

use App\Vote;
use App\Project;
use App\Article;
use App\Idea;
use App\Comment;
use App\PublicConsultation;
use App\Events\Gamification\RegisterVoteEvent;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VoteController extends Controller
{
    /**
     * Display a listing of the resource.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $filter = [];
            if($request->has('project_id')) {
                $filter['project'] = $request->project_id;
            }
            if($request->has('article_id')) {
                $filter['article'] = $request->article_id;
            }
            if($request->has('idea_id')) {
                $filter['idea'] = $request->idea_id;
            }
            if($request->has('comment_id')) {
                $filter['comment'] = $request->comment_id;
            }
            if($request->has('public_consultation_id')) {
                $filter['publicConsultation'] = $request->public_consultation_id;
            }
            if($request->has('user_id')) {
                $filter['user'] = $request->user_id;
            }

            if(!(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                $filter['projectIsPublic'] = true;
                $filter['articleIsPublic'] = true;
                $filter['ideaIsPublic'] = true;
                $filter['commentState'] = 0;
                $filter['commentProjectIsPublic'] = true;
                $filter['commentArticleIsPublic'] = true;
                $filter['commentIdeaIsPublic'] = true;
            }

            if($request->has('has') && is_array($request->has)) {
                $relations = $request->has;
                $votes = Vote::has($relations[0]);
                foreach($relations as $index => $relation) {
                    if($index > 0) {
                        $votes = $votes->orWhereHas($relation);
                    }
                }
                $votes = $votes->filter($filter);
            } else {
                $votes = Vote::filter($filter);
            }

            $totalResults = $votes->count();

            if($request->has('order_by')) {
                $order = $request->query('order', 'ASC');
                $votes = $votes->orderBy($request->order_by, $order);
            }

            if(!isset($request->return_all) || !$request->return_all) {
                $limit = $request->query('limit', 10);
                $offset = $request->query('offset', 0);
                $votes = $votes
                    ->offset($offset)
                    ->limit($limit);
            }
            $votes = $votes->with(['project', 'article', 'idea', 'comment', 'publicConsultation', 'idea.project', 'article.project'])->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($votes),
                'results' => $votes
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: votes were not found.'], 412);
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
            $request['user_id'] = Auth::id();
            $validator = Validator::make($request->all(), [
                'vote' => 'required|integer',
                'project_id' => 'integer|nullable|unique:votes,project_id,NULL,NULL,user_id,' . $request->user_id,
                'article_id' => 'integer|nullable|unique:votes,article_id,NULL,NULL,user_id,' . $request->user_id,
                'idea_id' => 'integer|nullable|unique:votes,idea_id,NULL,NULL,user_id,' . $request->user_id,
                'comment_id' => 'integer|nullable|unique:votes,comment_id,NULL,NULL,user_id,' . $request->user_id,
                'public_consultation_id' => 'integer|nullable|unique:votes,public_consultation_id,NULL,NULL,user_id,' . $request->user_id,
                'user_id' => 'required|integer'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $currentDatetime = date("Y-m-d H:i:s", time());
            $project = null;
            $article = null;
            $idea = null;
            $comment = null;
            $publicConsultation = null;
            if(isset($request->project_id)) {
                if(isset($request->article_id) || isset($request->idea_id) || isset($request->comment_id) || isset($request->public_consultation_id)) {
                    throw new \Exception();
                }
                $project = Project::findOrFail($request->project_id);
                if($currentDatetime < $project->fecha_inicio || $currentDatetime > $project->fecha_termino) {
                    throw new \Exception();
                }
            } else if(isset($request->article_id)) {
                if(isset($request->idea_id) || isset($request->comment_id) || isset($request->public_consultation_id)) {
                    throw new \Exception();
                }
                $article = Article::findOrFail($request->article_id);
                $projectArticle = $article->project;
                if($currentDatetime < $projectArticle->fecha_inicio || $currentDatetime > $projectArticle->fecha_termino) {
                    throw new \Exception();
                }
            } else if(isset($request->idea_id)) {
                if(isset($request->comment_id) || isset($request->public_consultation_id)) {
                    throw new \Exception();
                }
                $idea = Idea::findOrFail($request->idea_id);
                $projectIdea = $idea->project;
                if($currentDatetime < $projectIdea->fecha_inicio || $currentDatetime > $projectIdea->fecha_termino) {
                    throw new \Exception();
                }
            } else if(isset($request->comment_id)) {
                if(isset($request->public_consultation_id)) {
                    throw new \Exception();
                }
                $comment = Comment::findOrFail($request->comment_id);
            } else if(isset($request->public_consultation_id)) {
                $publicConsultation = PublicConsultation::findOrFail($request->public_consultation_id);
                if($currentDatetime < $publicConsultation->fecha_inicio || $currentDatetime > $publicConsultation->fecha_termino) {
                    throw new \Exception();
                }
            } else {
                throw new \Exception();
            }

            if(!Auth::user()->hasRole('ADMIN')) {
                if($project) {
                    if(!$project->is_public || !$project->is_enabled || $project->etapa == 3) {
                        throw new \Exception();
                    }
                } else if(isset($projectArticle)) {
                    if(!$projectArticle->is_public || !$projectArticle->is_enabled || $projectArticle->etapa != 2) {
                        throw new \Exception();
                    }
                } else if(isset($projectIdea)) {
                    if(!$projectIdea->is_public || !$projectIdea->is_enabled || $projectIdea->etapa == 3) {
                        throw new \Exception();
                    }
                } else if($comment) {
                    if($comment->state != 0
                        || $comment->project && $comment->project->is_public == false
                        || $comment->article && $comment->article->is_public == false
                        || $comment->idea && $comment->idea->is_public == false) {
                        throw new \Exception();
                    }
                } else if($publicConsultation && $publicConsultation->estado == 0) {
                    throw new \Exception();
                }
            }

            $user = Auth::user();

            $vote = new Vote([
                'vote' => $request->vote
            ]);
            $vote->project()->associate($project);
            $vote->article()->associate($article);
            $vote->idea()->associate($idea);
            $vote->comment()->associate($comment);
            $vote->publicConsultation()->associate($publicConsultation);
            $vote->user()->associate($user);
            $vote->save();

            $data = $vote->refresh()->toArray();
            unset($data['user']);

            $pipes = [
                RegisterVoteEvent::class
            ];
            $gamificationResult = app(Pipeline::class)
                ->send($vote)
                ->through($pipes)
                ->then(function($result) {
                    return $result;
                });

            $data['gamification_result'] = $gamificationResult;

            return response()->json([
                'message' => 'Successfully created vote!',
                'data' => $data
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the vote was not created.'], 412);
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
            $filter = [];
            if(!(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                $filter['projectIsPublic'] = true;
                $filter['articleIsPublic'] = true;
                $filter['ideaIsPublic'] = true;
                $filter['commentState'] = 0;
                $filter['commentProjectIsPublic'] = true;
                $filter['commentArticleIsPublic'] = true;
                $filter['commentIdeaIsPublic'] = true;
            }
            $vote = Vote::filter($filter)
                ->where('votes.id', '=', $id)
                ->with(['project', 'article', 'idea', 'comment', 'publicConsultation', 'user'])
                ->firstOrFail();

            return response()->json($vote, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the vote was not found.'], 412);
        }
    }

    /**
     * Update the specified resource in storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'vote' => 'required|integer'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $project = null;
            $publicConsultation = null;
            if(Auth::user()->hasRole('ADMIN')) {
                $vote = Vote::findOrFail($id);

                if($vote->project) {
                    $project = $vote->project;
                } else if($vote->article) {
                    $project = $vote->article->project;
                } else if($vote->idea) {
                    $project = $vote->idea->project;
                } else if($vote->publicConsultation) {
                    $publicConsultation = $vote->publicConsultation;
                }
            } else {
                $vote = Vote::where([
                    ['id', $id],
                    ['user_id', Auth::id()]
                ])->firstOrFail();

                if($vote->project) {
                    $project = $vote->project;
                } else if($vote->article) {
                    $project = $vote->article->project;
                    if($project->etapa != 2) {
                        throw new \Exception();
                    }
                } else if($vote->idea) {
                    $project = $vote->idea->project;
                } else if($vote->comment) {
                    if($vote->comment->state != 0
                        || $vote->comment->project && $vote->comment->project->is_public == false
                        || $vote->comment->article && $vote->comment->article->is_public == false
                        || $vote->comment->idea && $vote->comment->idea->is_public == false) {
                        throw new \Exception();
                    }
                } else if($vote->publicConsultation) {
                    $publicConsultation = $vote->publicConsultation;
                    if($publicConsultation->estado == 0) {
                        throw new \Exception();
                    }
                }
                if($project && !($project->is_public && $project->is_enabled && $project->etapa != 3)) {
                    throw new \Exception();
                }
            }

            $currentDatetime = date("Y-m-d H:i:s", time());
            if($project) {
                if($currentDatetime < $project->fecha_inicio || $currentDatetime > $project->fecha_termino) {
                    throw new \Exception();
                }
            } else if($publicConsultation) {
                if($currentDatetime < $publicConsultation->fecha_inicio || $currentDatetime > $publicConsultation->fecha_termino) {
                    throw new \Exception();
                }
            }

            $vote->fill([
                'vote' => $request->vote
            ]);
            $vote->save();

            $data = $vote->refresh()->toArray();
            unset($data['user']);

            return response()->json([
                'message' => 'Successfully updated vote!',
                'data' => $data
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the vote was not updated.'], 412);
        }
    }

    /**
     * Remove the specified resource from storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Vote::withTrashed()->findOrFail($id)->forceDelete();
            return response()->json([
                'message' => 'Successfully deleted vote!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the vote was not deleted.'], 412);
        }
    }
}
