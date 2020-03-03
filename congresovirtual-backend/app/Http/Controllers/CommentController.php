<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Position;
use App\Project;
use App\Article;
use App\Idea;
use App\PublicConsultation;
use App\Vote;
use App\File;
use App\FileType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
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
            $filter = [];
            if($request->has('query')) {
                $filter['query'] = $request['query'];
            }
            if($request->has('parent_id')) {
                $filter['parent'] = $request->parent_id;
            }
            if($request->has('project_id')) {
                $filter['project'] = $request->project_id;
            }
            if($request->has('article_id')) {
                $filter['article'] = $request->article_id;
            }
            if($request->has('idea_id')) {
                $filter['idea'] = $request->idea_id;
            }
            if($request->has('public_consultation_id')) {
                $filter['publicConsultation'] = $request->public_consultation_id;
            }
            if($request->has('user_id')) {
                $filter['user'] = $request->user_id;
            }

            if(Auth::check() && Auth::user()->hasRole('ADMIN')) {
                $state = $request->query('state', null);
            } else {
                $state = 0;
                $filter['projectIsPublic'] = true;
                $filter['articleIsPublic'] = true;
                $filter['ideaIsPublic'] = true;
            }

            $whereAndFilter = [];
            if($state !== null) {
                $whereAndFilter[] = ['comments.state', $state];
            }

            if(Auth::check() && Auth::user()->hasRole('ADMIN') && isset($request->only_trashed) && $request->only_trashed) {
                $comments = Comment::filter($filter)->where($whereAndFilter)->onlyTrashed();
                $totalResults = Comment::filter($filter)->where($whereAndFilter)->onlyTrashed()->count();
            } else {
                $comments = Comment::filter($filter)->where($whereAndFilter);
                $totalResults = Comment::filter($filter)->where($whereAndFilter)->count();
            }

            if(isset($request->has_denounces)) {
                if($request->has_denounces == true) {
                    $comments = $comments->has('denounces');
                } else if($request->has_denounces == false) {
                    $comments = $comments->doesntHave('denounces');
                }
            }

            if($request->has('order_by')) {
                $order = $request->query('order', 'ASC');
                $comments = $comments->orderBy($request->order_by, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $comments = $comments
                ->offset($offset)
                ->limit($limit);
            $comments = $comments->with(['project', 'article', 'idea', 'publicConsultation', 'user'])->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($comments),
                'results' => $comments
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: comments were not found.'], 412);
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
                'body' => 'required|string',
                'parent_id' => 'integer|nullable',
                'project_id' => 'integer|nullable',
                'article_id' => 'integer|nullable',
                'idea_id' => 'integer|nullable',
                'public_consultation_id' => 'integer|nullable',
                'position_latitude' => 'string|nullable',
                'position_longitude' => 'string|nullable'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $parent = null;
            if(isset($request->parent_id)) {
                $parent = Comment::findOrFail($request->parent_id);
            }

            $project = null;
            $article = null;
            $idea = null;
            $publicConsultation = null;
            if(isset($request->project_id)) {
                if(isset($request->article_id) || isset($request->idea_id) || isset($request->public_consultation_id)) {
                    throw new \Exception();
                }
                $project = Project::findOrFail($request->project_id);
            } else if(isset($request->article_id)) {
                if(isset($request->idea_id) || isset($request->public_consultation_id)) {
                    throw new \Exception();
                }
                $article = Article::findOrFail($request->article_id);
            } else if(isset($request->idea_id)) {
                if(isset($request->public_consultation_id)) {
                    throw new \Exception();
                }
                $idea = Idea::findOrFail($request->idea_id);
            } else if(isset($request->public_consultation_id)) {
                $publicConsultation = PublicConsultation::findOrFail($request->public_consultation_id);
            } else {
                throw new \Exception();
            }

            if(!Auth::user()->hasRole('ADMIN')) {
                if($project) {
                    if(!$project->is_public || !$project->is_enabled) {
                        throw new \Exception();
                    }
                } else if($article) {
                    $projectArticle = $article->project;
                    if(!$projectArticle->is_public || !$projectArticle->is_enabled) {
                        throw new \Exception();
                    }
                } else if($idea) {
                    $projectIdea = $idea->project;
                    if(!$projectIdea->is_public || !$projectIdea->is_enabled) {
                        throw new \Exception();
                    }
                } else if($publicConsultation && $publicConsultation->estado == 0) {
                    throw new \Exception();
                }
            }

            $user = Auth::user();

            $comment = new Comment([
                'body' => $request->body
            ]);
            $comment->parent()->associate($parent);
            $comment->project()->associate($project);
            $comment->article()->associate($article);
            $comment->idea()->associate($idea);
            $comment->publicConsultation()->associate($publicConsultation);
            $comment->user()->associate($user);

            DB::beginTransaction();
            $comment->save();

            $files = $request->file('files');
            if($files) {
                if(!$this->storeFiles($comment, $files)) {
                    throw new \Exception();
                }
                $comment->fill(['state' => 1])->save();
            }

            if(isset($request->position_latitude) && isset($request->position_longitude)) {
                $position = new Position([
                    'latitude' => $request->position_latitude,
                    'longitude' => $request->position_longitude
                ]);
                $position->comment()->associate($comment);
                $position->save();
            }

            $data = $comment->refresh()->toArray();
            unset($data['user']);
            $data['user'] = [
                'id' => $user->id,
                'name' => $user->name,
                'surname' => $user->surname,
                'username' => $user->username,
                'email' => $user->email,
                'avatar' => $user->avatar
            ];

            DB::commit();
            return response()->json([
                'message' => 'Successfully created comment!',
                'data' => $data
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            if(isset($comment) && isset($comment->id)) {
                $commentDirectory = "comments/{$comment->id}/";
                \Storage::deleteDirectory($commentDirectory);
            }
            return response()->json([
                'message' => 'Error: the comment was not created.'], 412);
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
            $comment = Comment::with(['project', 'article', 'idea', 'publicConsultation', 'user'])->findOrFail($id);

            if(!(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                if($comment->state != 0
                    || $comment->project && $comment->project->is_public == false
                    || $comment->article && $comment->article->is_public == false
                    || $comment->idea && $comment->idea->is_public == false) {
                    throw new \Exception();
                }
            }

            return response()->json($comment, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the comment was not found.'], 412);
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
                'body' => 'required|string'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            if(Auth::user()->hasRole('ADMIN')) {
                $comment = Comment::findOrFail($id);
            } else {
                $comment = Comment::where([
                    ['id', $id],
                    ['user_id', Auth::id()]
                ])->firstOrFail();

                if($comment->state != 0 && $comment->state != 1) {
                    throw new \Exception();
                }

                $project = null;
                if($comment->project) {
                    $project = $comment->project;
                } else if($comment->article) {
                    $project = $comment->article->project;
                } else if($comment->idea) {
                    $project = $comment->idea->project;
                } else if($comment->publicConsultation && $comment->publicConsultation->estado == 0) {
                    throw new \Exception();
                }
                if($project && (!$project->is_public || !$project->is_enabled)) {
                    throw new \Exception();
                }
            }

            $comment->fill(['body' => $request->body])->save();
            return response()->json([
                'message' => 'Successfully updated comment!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the comment was not updated.'], 412);
        }
    }

    /**
     * Remove the specified resource from storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
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

            if(Auth::user()->hasRole('ADMIN')) {
                $comment = Comment::withTrashed()->findOrFail($id);
            } else {
                $comment = Comment::withTrashed()
                    ->where([
                        ['id', $id],
                        ['user_id', Auth::id()]
                    ])->firstOrFail();

                if($comment->state != 0 && $comment->state != 1) {
                    throw new \Exception();
                }

                $project = null;
                if($comment->project) {
                    $project = $comment->project;
                } else if($comment->article) {
                    $project = $comment->article->project;
                } else if($comment->idea) {
                    $project = $comment->idea->project;
                } else if($comment->publicConsultation && $comment->publicConsultation->estado == 0) {
                    throw new \Exception();
                }
                if($project && (!$project->is_public || !$project->is_enabled)) {
                    throw new \Exception();
                }
            }
            $forceDelete = $request->query('force_delete', false);
            if($forceDelete) {
                $comment->forceDelete();
            } else {
                $comment->delete();
            }
            return response()->json([
                'message' => 'Successfully deleted comment!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the comment was not deleted.'], 412);
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
            Comment::onlyTrashed()->findOrFail($id)->restore();
            return response()->json([
                'message' => 'Successfully undeleted comment!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the comment was not undeleted.'], 412);
        }
    }

    /**
     * Update the specified attribute of resource in storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function updateState(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'state' => 'required|integer|in:0,1,2,3'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $comment = Comment::findOrFail($id);
            $comment->fill(['state' => $request->state])->save();
            return response()->json([
                'message' => 'Successfully updated "state" attribute in comment!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the "state" attribute of comment was not updated.'], 412);
        }
    }

    /**
     * Update the specified attribute of resource in storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePerception(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'perception' => 'required|numeric|nullable'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $comment = Comment::findOrFail($id);
            $comment->fill(['perception' => $request->perception])->save();
            return response()->json([
                'message' => 'Successfully updated "perception" attribute in comment!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the "perception" attribute of comment was not updated.'], 412);
        }
    }

    /**
     * Display the user associated.
     * About access control: all users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function user($id)
    {
        try {
            $comment = Comment::findOrFail($id);

            if(!(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                if($comment->state != 0
                    || $comment->project && $comment->project->is_public == false
                    || $comment->article && $comment->article->is_public == false
                    || $comment->idea && $comment->idea->is_public == false) {
                    throw new \Exception();
                }
            }

            return response()->json($comment->user, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the comment was not found.'], 412);
        }
    }

    /**
     * Display the comments.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function comments(Request $request, $id)
    {
        try {
            $comment = Comment::findOrFail($id);

            if(!(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                if($comment->state != 0
                    || $comment->project && $comment->project->is_public == false
                    || $comment->article && $comment->article->is_public == false
                    || $comment->idea && $comment->idea->is_public == false) {
                    throw new \Exception();
                }
            }

            $request['parent_id'] = $comment->id;
            unset($request['project_id'], $request['article_id'], $request['idea_id'], $request['public_consultation_id']);

            return response()->json($this->index($request)->getOriginalContent());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the comment was not found.'], 412);
        }
    }

    /**
     * Display the votes.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function votes(Request $request, $id)
    {
        try {
            $comment = Comment::findOrFail($id);

            if(!(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                if($comment->state != 0
                    || $comment->project && $comment->project->is_public == false
                    || $comment->article && $comment->article->is_public == false
                    || $comment->idea && $comment->idea->is_public == false) {
                    throw new \Exception();
                }
            }

            $request['comment_id'] = $comment->id;
            unset($request['project_id'], $request['article_id'], $request['idea_id'], $request['public_consultation_id']);
            $voteController = new VoteController();

            return response()->json($voteController->index($request)->getOriginalContent());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the comment was not found.'], 412);
        }
    }

    /**
     * Display the denounces.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function denounces(Request $request, $id)
    {
        try {
            $comment = Comment::findOrFail($id);
            $request['comment_id'] = $comment->id;
            $denounceController = new DenounceController();

            return response()->json($denounceController->index($request)->getOriginalContent());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the comment was not found.'], 412);
        }
    }

    /**
     * Display the files.
     * About access control: all users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function files($id)
    {
        try {
            $comment = Comment::findOrFail($id);

            if(!(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                if($comment->state != 0
                    || $comment->project && $comment->project->is_public == false
                    || $comment->article && $comment->article->is_public == false
                    || $comment->idea && $comment->idea->is_public == false) {
                    throw new \Exception();
                }
            }

            return response()->json($comment->files, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the comment was not found.'], 412);
        }
    }

    /**
     * Store a newly created resource (vote associated with a comment) in storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function storeVote(Request $request, $id)
    {
        try {
            $request['user_id'] = Auth::id();
            $validator = Validator::make($request->all(), [
                'vote' => 'required|integer',
                'user_id' => 'required|integer|unique:votes,user_id,NULL,NULL,comment_id,' . $id
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $comment = Comment::findOrFail($id);
            if(!Auth::user()->hasRole('ADMIN')) {
                if($comment->state != 0
                    || $comment->project && $comment->project->is_public == false
                    || $comment->article && $comment->article->is_public == false
                    || $comment->idea && $comment->idea->is_public == false) {
                    throw new \Exception();
                }
            }

            $user = Auth::user();

            $vote = new Vote([
                'vote' => $request->vote
            ]);
            $vote->comment()->associate($comment);
            $vote->user()->associate($user);
            $vote->save();

            $data = $vote->refresh()->toArray();
            unset($data['user']);

            return response()->json([
                'message' => 'Successfully created vote of comment!',
                'data' => $data
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the vote of comment was not created.'], 412);
        }
    }

    /**
     * Display the specified resource (user vote associated with a comment) in storage.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function showUserVote(Request $request, $id)
    {
        try {
            $filter = [];
            if(!(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                $filter['commentState'] = 0;
                $filter['commentProjectIsPublic'] = true;
                $filter['commentArticleIsPublic'] = true;
                $filter['commentIdeaIsPublic'] = true;
            }
            $filter['comment'] = $id;
            $filter['user'] = $request->user_id;
            $vote = Vote::filter($filter)->firstOrFail();

            return response()->json($vote, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the user vote associated of comment was not found.'], 412);
        }
    }

    /**
     * Request method to store a newly created resource (files associated with a comment) in storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function storeFilesRequest(Request $request, $id)
    {
        try {
            if(Auth::user()->hasRole('ADMIN')) {
                $comment = Comment::findOrFail($id);
                $state = $comment->state;
            } else {
                $comment = Comment::where([
                    ['id', $id],
                    ['user_id', Auth::id()]
                ])->firstOrFail();
                $state = 1;

                if($comment->state != 0 && $comment->state != 1) {
                    throw new \Exception();
                }

                $project = null;
                if($comment->project) {
                    $project = $comment->project;
                } else if($comment->article) {
                    $project = $comment->article->project;
                } else if($comment->idea) {
                    $project = $comment->idea->project;
                } else if($comment->publicConsultation && $comment->publicConsultation->estado == 0) {
                    throw new \Exception();
                }
                if($project && (!$project->is_public || !$project->is_enabled)) {
                    throw new \Exception();
                }
            }

            $files = $request->file('files');
            $dataFilesStored = $this->storeFiles($comment, $files);
            if(!$dataFilesStored) {
                throw new \Exception();
            }
            $comment->fill(['state' => $state])->save();
            return response()->json([
                'message' => 'Successfully stored file of comment!',
                'data_files_stored' => $dataFilesStored
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the files of comment were not stored.'], 412);
        }
    }

    /**
     * Store a newly created resource (files associated with a comment) in storage.
     *
     * @param  $comment
     * @param  $files
     * @return array
     */
    private function storeFiles($comment, $files)
    {
        try {
            if(!is_array($files)) {
                throw new \Exception();
            }

            DB::beginTransaction();
            $dataFilesStored = [];
            foreach($files as $file) {
                $dataFileStored = $this->storeFile($comment, $file);
                if(!$dataFileStored) {
                    throw new \Exception();
                } else {
                    $dataFilesStored[] = $dataFileStored;
                }
            }

            DB::commit();
            return $dataFilesStored;
        } catch (\Exception $exception) {
            DB::rollBack();
            if(isset($dataFilesStored)) {
                $commentDirectory = "comments/{$comment->id}/";
                foreach($dataFilesStored as $dataFile) {
                    \Storage::delete("{$commentDirectory}{$dataFile['stored_name']}.{$dataFile['extension']}");
                }
            }
            return null;
        }
    }

    /**
     * Store a newly created resource (file associated with a comment) in storage.
     *
     * @param  $comment
     * @param  $file
     * @param  $customAllowedExtensions
     * @return array
     */
    private function storeFile($comment, $file, $customAllowedExtensions = null)
    {
        try {
            if(!$file || !$file->isValid()) {
                throw new \Exception();
            }

            if($customAllowedExtensions) {
                if(!is_array($customAllowedExtensions)) {
                    throw new \Exception();
                }
                $allowedExtensions = $customAllowedExtensions;
            } else {
                $allowedExtensions = [
                    'IMAGE' => ['png', 'jpeg', 'jpg', 'bmp', 'gif', 'svg'],
                    'AUDIO' => ['amr', 'ogg', 'mp3', 'm4a', 'wav'],
                    'DOCUMENT' => ['pdf']
                ];
            }
            $extension = strtolower($file->getClientOriginalExtension());
            $valueFileType = null;
            foreach($allowedExtensions as $fileType => $extensions) {
                if(in_array($extension, $extensions)) {
                    $valueFileType = $fileType;
                }
            }
            if(!$valueFileType) {
                throw new \Exception();
            }

            DB::beginTransaction();
            $fileType = FileType::firstOrCreate([
                'value' => $valueFileType,
                'table_name' => $comment->getTable()
            ]);

            $commentDirectory = "comments/{$comment->id}/";
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $storedBasename = \Str::random(40) . ".{$extension}";
            $storedName = pathinfo($file->storeAs($commentDirectory, $storedBasename), PATHINFO_FILENAME);

            $file = new File([
                'original_name' => $originalName,
                'stored_name' => $storedName,
                'extension' => $extension,
                'object_id' => $comment->id
            ]);
            $file->fileType()->associate($fileType);
            $file->save();

            $dataFileStored = $file->toArray();
            unset($dataFileStored['file_type']);

            DB::commit();
            return $dataFileStored;
        } catch (\Exception $exception) {
            DB::rollBack();
            if(isset($extension) && isset($commentDirectory) && isset($storedName)) {
                \Storage::delete("{$commentDirectory}{$storedName}.{$extension}");
            }
            return null;
        }
    }

    /**
     * Request method to remove the specified resource (file associated with a comment) from storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
     *
     * @param  $commentId
     * @param  $fileId
     * @return \Illuminate\Http\Response
     */
    public function removeFileRequest($commentId, $fileId)
    {
        try {
            if(Auth::user()->hasRole('ADMIN')) {
                $comment = Comment::findOrFail($commentId);
            } else {
                $comment = Comment::where([
                    ['id', $commentId],
                    ['user_id', Auth::id()]
                ])->firstOrFail();

                if($comment->state != 0 && $comment->state != 1) {
                    throw new \Exception();
                }

                $project = null;
                if($comment->project) {
                    $project = $comment->project;
                } else if($comment->article) {
                    $project = $comment->article->project;
                } else if($comment->idea) {
                    $project = $comment->idea->project;
                } else if($comment->publicConsultation && $comment->publicConsultation->estado == 0) {
                    throw new \Exception();
                }
                if($project && (!$project->is_public || !$project->is_enabled)) {
                    throw new \Exception();
                }
            }

            if(!$this->removeFile($comment->id, $fileId)) {
                throw new \Exception();
            }

            $commentFiles = $comment->files;
            if(!count($commentFiles) && $comment->state == 1) {
                $comment->fill(['state' => 0])->save();
            }

            return response()->json([
                'message' => 'Successfully deleted file of comment!'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error: the file of comment was not deleted.'], 412);
        }
    }

    /**
     * Remove the specified resource (file associated with a comment) from storage.
     *
     * @param  $commentId
     * @param  $fileId
     * @return boolean
     */
    private function removeFile($commentId, $fileId)
    {
        try {
            $comment = Comment::findOrFail($commentId);
            $file = File::where([
                ['files.id', $fileId],
                ['files.object_id', $comment->id]
            ])
                ->join('file_types', 'files.file_type_id', '=', 'file_types.id')
                ->select('files.*')
                ->where('file_types.table_name', $comment->getTable())
                ->firstOrFail();

            $commentDirectory = "comments/{$comment->id}/";

            DB::beginTransaction();
            $file->forceDelete();
            \Storage::delete("{$commentDirectory}{$file->stored_name}.{$file->extension}");

            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Display a listing of the resource according to some sort criteria.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function indexSortedBy(Request $request)
    {
        try {
            $option = strtoupper($request->option);
            if(!$option) {
                throw new \Exception();
            }
            $order = $request->query('order', 'DESC');
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);


            $whereFilter = [];
            if(isset($request->project_id)) {
                if(isset($request->article_id) || isset($request->idea_id) || isset($request->public_consultation_id)) {
                    throw new \Exception();
                }
                $whereFilter[] = ['project_id', $request->project_id];
            } else if(isset($request->article_id)) {
                if(isset($request->idea_id) || isset($request->public_consultation_id)) {
                    throw new \Exception();
                }
                $whereFilter[] = ['article_id', $request->article_id];
            } else if(isset($request->idea_id)) {
                if(isset($request->public_consultation_id)) {
                    throw new \Exception();
                }
                $whereFilter[] = ['idea_id', $request->idea_id];
            } else if(isset($request->public_consultation_id)) {
                $whereFilter[] = ['public_consultation_id', $request->public_consultation_id];
            }

            switch ($option) {
                case 'TOTAL_ALL_VOTES':
                    $comments = Comment::where($whereFilter)
                        ->select(
                            'comments.*',
                            DB::raw('(votos_a_favor + votos_en_contra) AS total_all_votes')
                        )
                        ->orderBy('total_all_votes', $order);
                    break;
                case 'PROPORTION_ACCORD_VOTES':
                    $comments = Comment::where($whereFilter)
                        ->select(
                            'comments.*',
                            DB::raw('(votos_a_favor/(votos_a_favor + votos_en_contra)) AS proportion_accord_votes')
                    )
                        ->orderBy('proportion_accord_votes', $order);
                    break;
                case 'PROPORTION_DESACCORD_VOTES':
                    $comments = Comment::where($whereFilter)
                        ->select(
                            'comments.*',
                            DB::raw('(votos_en_contra/(votos_a_favor + votos_en_contra)) AS proportion_desaccord_votes')
                    )
                        ->orderBy('proportion_desaccord_votes', $order);
                    break;
                default:
                    throw new \Exception();
            }
            $comments = $comments->offset($offset)
                ->limit($limit)
                ->get();
            return response()->json($comments, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: it was not possible to list the requested information.'], 412);
        }
    }
}
