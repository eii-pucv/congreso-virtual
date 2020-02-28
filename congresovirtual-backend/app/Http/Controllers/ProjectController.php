<?php

namespace App\Http\Controllers;

use App\Position;
use App\Project;
use App\Article;
use App\Comment;
use App\Stopword;
use App\StopwordType;
use App\Vote;
use App\User;
use App\Term;
use App\File;
use App\FileType;
use App\Notifications\NewProject;
use App\Notifications\UpdateProject;
use App\Notifications\CloseProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
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
            if($request->has('terms')) {
                if(is_array($request->terms)) {
                    $filter['terms'] = $request->terms;
                } else if(!empty($request->terms)) {
                    $filter['terms'] = preg_split('/[^0-9]+/', $request->terms);
                }
            }

            if(Auth::check() && Auth::user()->hasRole('ADMIN')) {
                $isPublic = $request->query('is_public', null);
            } else {
                $isPublic = true;
            }

            $whereAndFilter = [];
            if($isPublic !== null) {
                $whereAndFilter[] = ['is_public', $isPublic];
            }
            if(isset($request->estado)) {
                $whereAndFilter[] = ['estado', $request->estado];
            }
            if(isset($request->etapa)) {
                $whereAndFilter[] = ['etapa', $request->etapa];
            }
            if(isset($request->is_principal)) {
                $whereAndFilter[] = ['is_principal', $request->is_principal];
            }
            if(isset($request->is_enabled)) {
                $whereAndFilter[] = ['is_enabled', $request->is_enabled];
            }
            $projects = Project::filter($filter)->where($whereAndFilter);
            $totalResults = Project::filter($filter)->where($whereAndFilter)->count();

            if($request->has('order_by')) {
                $order = $request->query('order', 'ASC');
                $projects = $projects->orderBy($request->order_by, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $projects = $projects
                ->offset($offset)
                ->limit($limit);
            $projects = $projects->with(['terms'])->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($projects),
                'results' => $projects
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: projects were not found.'], 412);
        }
    }

    /**
     * Store a newly created resource in storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'titulo' => 'required|string',
                'postulante' => 'string|max:191|nullable',
                'estado' => 'string|max:191|nullable',
                'etapa' => 'integer|in:0,1,2|nullable',
                'detalle' => 'string|nullable',
                'resumen' => 'string|nullable',
                'fecha_inicio' => 'date_format:Y-m-d H:i:s|nullable',
                'fecha_termino' => 'date_format:Y-m-d H:i:s|after_or_equal:fecha_inicio|nullable',
                'boletin' => 'required|string|max:191',
                'is_principal' => 'boolean',
                'is_public' => 'boolean',
                'is_enabled' => 'boolean',
                'video' => 'string|max:191|nullable',
                'notificar_correo' => 'boolean',
                'terms_id' => 'array'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $project = new Project([
                'titulo' => $request->titulo,
                'postulante' => $request->postulante,
                'estado' => $request->estado,
                'etapa' => $request->etapa,
                'detalle' => $request->detalle,
                'resumen' => $request->resumen,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_termino' => $request->fecha_termino,
                'boletin' => $request->boletin,
                'is_principal' => $request->has('is_principal') ? $request->is_principal : false,
                'is_public' => $request->has('is_public') ? $request->is_public : false,
                'is_enabled' => $request->has('is_enabled') ? $request->is_enabled : true,
                'video' => $request->video
            ]);
            DB::beginTransaction();
            $project->save();

            $imageFile = $request->file('imagen');
            if($imageFile) {
                $allowedExtensions = [
                    'PRIMARY-IMAGE' => ['png', 'jpeg', 'jpg', 'bmp', 'gif', 'svg']
                ];
                $dataFileStored = $this->storeFile($project, $imageFile, $allowedExtensions);
                if(!$dataFileStored) {
                    throw new \Exception();
                }
                $project->imagenRelated()->associate($dataFileStored['id'])->save();
            }

            $files = $request->file('files');
            if($files) {
                if(!$this->storeFiles($project, $files)) {
                    throw new \Exception();
                }
            }

            if(isset($request->terms_id)) {
                $terms = Term::whereIn('id', $request->terms_id)->pluck('id');
                $project->terms()->sync($terms);
            }

            $usersToNotifyByEmail = [];
            if(isset($request->notificar_correo) && $request->notificar_correo == true) {
                $usersToNotifyByEmail = $this->usersWithProjectTerms($project->id);
                if(!$this->notifyByEmail($project->id, 'NEW_PROJECT', $usersToNotifyByEmail)) {
                    throw new \Exception();
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'Successfully created project!',
                'data' => $project->toArray(),
                'users_to_notify_by_email' => $usersToNotifyByEmail
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            if(isset($project) && isset($project->id)) {
                $projectDirectory = "projects/{$project->id}";
                \Storage::deleteDirectory($projectDirectory);
            }
            return response()->json([
                'message' => 'Error: the project was not created.'], 412);
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
            $project = Project::with(['articles', 'ideas', 'votes', 'terms'])->findOrFail($id);

            if(!$project->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            $data = $project;
            $data['files'] = $project->files();
            return response()->json($data, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the project was not found.'], 412);
        }
    }

    /**
     * Update the specified resource in storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'titulo' => 'required|string',
                'postulante' => 'string|max:191|nullable',
                'estado' => 'string|max:191|nullable',
                'etapa' => 'integer|in:0,1,2|nullable',
                'detalle' => 'string|nullable',
                'resumen' => 'string|nullable',
                'fecha_inicio' => 'date_format:Y-m-d H:i:s|nullable',
                'fecha_termino' => 'date_format:Y-m-d H:i:s|after_or_equal:fecha_inicio|nullable',
                'boletin' => 'required|string|max:191',
                'is_principal' => 'boolean',
                'is_public' => 'boolean',
                'is_enabled' => 'boolean',
                'video' => 'string|max:191|nullable',
                'notificar_correo' => 'boolean'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $project = Project::findOrFail($id);
            $project->fill([
                'titulo' => $request->titulo,
                'postulante' => $request->postulante,
                'estado' => $request->estado,
                'etapa' => $request->etapa,
                'detalle' => $request->detalle,
                'resumen' => $request->resumen,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_termino' => $request->fecha_termino,
                'boletin' => $request->boletin,
                'is_principal' => $request->has('is_principal') ? $request->is_principal : $project->is_principal,
                'is_public' => $request->has('is_public') ? $request->is_public : $project->is_public,
                'is_enabled' => $request->has('is_enabled') ? $request->is_enabled : $project->is_enabled,
                'video' => $request->video
            ]);
            DB::beginTransaction();
            $project->save();

            $usersToNotifyByEmail = [];
            if(isset($request->notificar_correo) && $request->notificar_correo == true) {
                $usersToNotifyByEmail = $this->usersWithProjectTerms($project->id);
                $typeNotification = 'UPDATE_PROJECT';
                if($request->etapa == 2) {
                    $typeNotification = 'CLOSE_PROJECT';
                }
                if(!$this->notifyByEmail($project->id, $typeNotification, $usersToNotifyByEmail)) {
                    throw new \Exception();
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'Successfully updated project!',
                'data' => $project->toArray(),
                'users_to_notify_by_email' => $usersToNotifyByEmail
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error: the project was not updated.'], 412);
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

            $project = Project::withTrashed()->findOrFail($id);
            $forceDelete = $request->query('force_delete', false);
            if($forceDelete) {
                $project->forceDelete();
            } else {
                $project->delete();
            }
            return response()->json([
                'message' => 'Successfully deleted project!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the project was not deleted.'], 412);
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
            Project::onlyTrashed()->findOrFail($id)->restore();
            return response()->json([
                'message' => 'Successfully undeleted project!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the project was not undeleted.'], 412);
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
    public function updateIsEnabled(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'is_enabled' => 'required|boolean'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $project = Project::findOrFail($id);
            $project->fill([
                'is_enabled' => $request->is_enabled
            ]);
            $project->save();
            return response()->json([
                'message' => 'Successfully updated "is_enabled" attribute in project!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the "is_enabled" attribute of project was not updated.'], 412);
        }
    }

    /**
     * Store and replace the image of project in public storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function updateImage(Request $request, $id)
    {
        try {
            $project = Project::findOrFail($id);

            DB::beginTransaction();
            if($project->imagenRelated) {
                $oldImageFile = File::findOrFail($project->imagen_id);
                if(!$this->removeFile($project->id, $oldImageFile->id)) {
                    throw new \Exception();
                }
            }

            $newImageFile = $request->file('imagen');
            $allowedExtensions = [
                'PRIMARY-IMAGE' => ['png', 'jpeg', 'jpg', 'bmp', 'gif', 'svg']
            ];
            $dataImageFileStored = $this->storeFile($project, $newImageFile, $allowedExtensions);
            if(!$dataImageFileStored) {
                throw new \Exception();
            }

            $project->imagenRelated()->associate($dataImageFileStored['id']);
            $project->save();

            DB::commit();
            return response()->json([
                'message' => 'Successfully updated image of project!'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            if(isset($dataImageFileStored) && isset($project)) {
                $projectDirectory = "projects/{$project->id}/";
                \Storage::delete("{$projectDirectory}{$dataImageFileStored['stored_name']}.{$dataImageFileStored['extension']}");
            }
            return response()->json([
                'message' => 'Error: the image of project was not updated.'], 412);
        }
    }

    /**
     * Remove the image of project in public storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function removeImage($id)
    {
        try {
            $project = Project::findOrFail($id);

            DB::beginTransaction();
            if(!$project->imagenRelated) {
                throw new \Exception();
            }
            $imageFile = File::findOrFail($project->imagen_id);
            if(!$this->removeFile($project->id, $imageFile->id)) {
                throw new \Exception();
            }
            $project->imagenRelated()->dissociate();
            $project->save();

            DB::commit();
            return response()->json([
                'message' => 'Successfully removed image of project!'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error: the image of project was not removed.'], 412);
        }
    }

    /**
     * Display the image associated.
     * About access control: all users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function image($id)
    {
        try {
            $project = Project::findOrFail($id);

            if(!$project->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            return response()->json($project->imagenRelated, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the project was not found.'], 412);
        }
    }

    /**
     * Display the articles.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function articles(Request $request, $id)
    {
        try {
            $project = Project::findOrFail($id);

            if(!$project->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            $request['project_id'] = $project->id;
            unset($request['is_public']);
            $articleController = new ArticleController();

            return response()->json($articleController->index($request)->getOriginalContent());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the project was not found.'], 412);
        }
    }

    /**
     * Display the ideas.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function ideas(Request $request, $id)
    {
        try {
            $project = Project::findOrFail($id);

            if(!$project->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            $request['project_id'] = $project->id;
            unset($request['is_public']);
            $ideaController = new IdeaController();

            return response()->json($ideaController->index($request)->getOriginalContent());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the project was not found.'], 412);
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
            $project = Project::findOrFail($id);

            if(!$project->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            $request['project_id'] = $project->id;
            unset($request['article_id'], $request['idea_id'], $request['public_consultation_id']);
            $commentController = new CommentController();

            return response()->json($commentController->index($request)->getOriginalContent());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the project was not found.'], 412);
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
            $project = Project::findOrFail($id);

            if(!$project->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            $request['project_id'] = $project->id;
            unset($request['article_id'], $request['idea_id'], $request['comment_id'], $request['public_consultation_id']);
            $voteController = new VoteController();

            return response()->json($voteController->index($request)->getOriginalContent());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the project was not found.'], 412);
        }
    }

    /**
     * Display the terms.
     * About access control: all users can use this method (see routes).
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function terms($id)
    {
        try {
            $project = Project::findOrFail($id);

            if(!$project->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            return response()->json($project->terms, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the project was not found.'], 412);
        }
    }

    /**
     * Display the stopwords.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function stopwords(Request $request, $id)
    {
        try {
            $project = Project::findOrFail($id);

            if(!$project->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            $stopwordType = StopwordType::where('table_name', $project->getTable())->firstOrFail();
            $request['stopword_type_id'] = $stopwordType->id;
            $request['object_id'] = $project->id;
            $stopwordController = new StopwordController();

            return response()->json($stopwordController->index($request)->getOriginalContent());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the project was not found.'], 412);
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
            $project = Project::findOrFail($id);

            if(!$project->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            return response()->json($project->files(), 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the project was not found.'], 412);
        }
    }

    /**
     * Request method to display the users associated terms of project.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function usersWithProjectTermsRequest($id)
    {
        try {
            $users = $this->usersWithProjectTerms($id);
            if(!$users) {
                throw new \Exception();
            }
            return response()->json($users, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the project was not found.'], 412);
        }
    }

    /**
     * Return the users associated terms of project.
     *
     * @param  $projectId
     * @return \Illuminate\Http\Response
     */
    private function usersWithProjectTerms($projectId)
    {
        try {
            $project = Project::findOrFail($projectId);
            $terms = $project->terms->pluck('id');

            $users = User::where('active', true)
                ->join('term_user', function ($join) use ($terms) {
                    $join->on('users.id', 'term_user.user_id')
                        ->whereIn('term_user.term_id', $terms);
                })
                ->select('users.*')
                ->distinct()
                ->get();

            return $users;
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * Display the users who have commented or voted on a project.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function usersParticipantsOnProject($id)
    {
        try {
            $project = Project::findOrFail($id);

            $firstQuery = User::where('active', true)
                ->join('votes', function ($join) use ($project) {
                    $join->on('users.id', 'votes.user_id')
                        ->where('votes.project_id', $project->id);
                })
                ->select('users.*');

            $users = User::where('active', true)
                ->join('comments', function ($join) use ($project) {
                    $join->on('users.id', 'comments.user_id')
                        ->where('comments.project_id', $project->id);
                })
                ->select('users.*')
                ->union($firstQuery)
                ->get();

            if(!$users) {
                throw new \Exception();
            }
            return response()->json($users, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the project was not found.'], 412);
        }
    }

    /**
     * Associate some terms with project in storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function associateTerms(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'terms_id' => 'required|array'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $project = Project::findOrFail($id);
            $project->terms()->sync($request->terms_id, false);
            return response()->json([
                'message' => 'Successfully associated terms with project!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the association terms with project was not created.'], 412);
        }
    }

    /**
     * Dissociate all terms with project in storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function dissociateAllTerms(Request $request, $id)
    {
        try {
            $project = Project::findOrFail($id);
            $project->terms()->sync([]);
            return response()->json([
                'message' => 'Successfully dissociate all terms with project!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the dissociation all terms with project was not executed.'], 412);
        }
    }

    /**
     * Associate some stopwords with project in storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function associateStopwords(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'stopwords' => 'required|array'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $project = Project::findOrFail($id);
            $stopwordType = StopwordType::where('table_name', $project->getTable())->firstOrFail();

            DB::beginTransaction();
            foreach($request->stopwords as $stopword) {
                $validator = Validator::make($stopword, [
                    'value' => 'required|string|max:191'
                ]);
                if($validator->fails()) {
                    return response()->json($validator->errors(), 412);
                }

                $newStopword = new Stopword([
                    'value' => $stopword['value'],
                    'object_id' => $project->id
                ]);
                $newStopword->stopwordType()->associate($stopwordType);
                $newStopword->save();
            }
            DB::commit();
            return response()->json([
                'message' => 'Successfully associated stopwords with project!'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error: the association stopwords with project was not created.'], 412);
        }
    }

    /**
     * Dissociate all stopwords with project in storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function dissociateAllStopwords(Request $request, $id)
    {
        try {
            $project = Project::findOrFail($id);
            $stopwordsIds = $project->stopwords()->pluck('id');
            Stopword::destroy($stopwordsIds);
            return response()->json([
                'message' => 'Successfully dissociate all stopwords with project!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the dissociation all stopwords with project was not executed.'], 412);
        }
    }

    /**
     * Store a newly created resource (comment associated with a project) in storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function storeComment(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'body' => 'required|string',
                'parent_id' => 'integer|nullable',
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

            $project = Project::findOrFail($id);
            if(!Auth::user()->hasRole('ADMIN')) {
                if(!$project->is_public || !$project->is_enabled) {
                    throw new \Exception();
                }
            }

            $user = Auth::user();

            $comment = new Comment([
                'body' => $request->body
            ]);
            $comment->parent()->associate($parent);
            $comment->project()->associate($project);
            $comment->user()->associate($user);

            DB::beginTransaction();
            $comment->save();

            if(isset($request->position_latitude) && isset($request->position_longitude)) {
                $position = new Position([
                    'latitude' => $request->position_latitude,
                    'longitude' => $request->position_longitude
                ]);
                $position->comment()->associate($comment);
                $position->save();
            }

            DB::commit();
            return response()->json([
                'message' => 'Successfully created comment of project!'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error: the comment of project was not created.'], 412);
        }
    }

    /**
     * Store a newly created resource (vote associated with a project) in storage.
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
                'user_id' => 'required|integer|unique:votes,user_id,NULL,NULL,project_id,' . $id
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $project = Project::findOrFail($id);
            if(!Auth::user()->hasRole('ADMIN')) {
                if(!$project->is_public || !$project->is_enabled || $project->etapa == 3) {
                    throw new \Exception();
                }
            }

            $currentDatetime = date("Y-m-d H:i:s", time());
            if($currentDatetime < $project->fecha_inicio || $currentDatetime > $project->fecha_termino) {
                throw new \Exception();
            }

            $user = Auth::user();

            $vote = new Vote([
                'vote' => $request->vote
            ]);
            $vote->project()->associate($project);
            $vote->user()->associate($user);
            $vote->save();

            $data = $vote->refresh()->toArray();
            unset($data['user']);

            return response()->json([
                'message' => 'Successfully created vote of project!',
                'data' => $data
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the vote of project was not created.'], 412);
        }
    }

    /**
     * Display the specified resource (user vote associated with a project) in storage.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function showUserVote(Request $request, $id)
    {
        try {
            $vote = Vote::where([
                ['project_id', $id],
                ['user_id', $request->user_id]
            ]);

            if(!(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                $vote = $vote->join('projects', 'votes.project_id', '=', 'projects.id')
                    ->select('votes.*')
                    ->where('projects.is_public', true);
            }
            $vote = $vote->firstOrFail();

            return response()->json($vote, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the user vote associated of project was not found.'], 412);
        }
    }

    /**
     * Request method to store a newly created resource (files associated with a project) in storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function storeFilesRequest(Request $request, $id)
    {
        try {
            $project = Project::findOrFail($id);

            $files = $request->file('files');
            $dataFilesStored = $this->storeFiles($project, $files);
            if(!$dataFilesStored) {
                throw new \Exception();
            }
            return response()->json([
                'message' => 'Successfully stored file of project!',
                'data_files_stored' => $dataFilesStored
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the files of project were not stored.'], 412);
        }
    }

    /**
     * Store a newly created resource (files associated with a project) in storage.
     *
     * @param  $project
     * @param  $files
     * @return array
     */
    private function storeFiles($project, $files)
    {
        try {
            if(!is_array($files)) {
                throw new \Exception();
            }

            DB::beginTransaction();
            $dataFilesStored = [];
            foreach($files as $file) {
                $dataFileStored = $this->storeFile($project, $file);
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
                $projectDirectory = "projects/{$project->id}/";
                foreach($dataFilesStored as $dataFile) {
                    \Storage::delete("{$projectDirectory}{$dataFile['stored_name']}.{$dataFile['extension']}");
                }
            }
            return null;
        }
    }

    /**
     * Store a newly created resource (file associated with a project) in storage.
     *
     * @param  $project
     * @param  $file
     * @param  $customAllowedExtensions
     * @return array
     */
    private function storeFile($project, $file, $customAllowedExtensions = null)
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
                    'DOCUMENT' => ['pdf', 'doc', 'docx', 'odt', 'ppt', 'pptx', 'pps', 'ppsx', 'odp']
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
                'table_name' => $project->getTable()
            ]);

            $projectDirectory = "projects/{$project->id}/";
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $storedBasename = \Str::random(40) . ".{$extension}";
            $storedName = pathinfo($file->storeAs($projectDirectory, $storedBasename), PATHINFO_FILENAME);

            $file = new File([
                'original_name' => $originalName,
                'stored_name' => $storedName,
                'extension' => $extension,
                'object_id' => $project->id
            ]);
            $file->fileType()->associate($fileType);
            $file->save();

            $dataFileStored = $file->toArray();
            unset($dataFileStored['file_type']);

            DB::commit();
            return $dataFileStored;
        } catch (\Exception $exception) {
            DB::rollBack();
            if(isset($extension) && isset($projectDirectory) && isset($storedName)) {
                \Storage::delete("{$projectDirectory}{$storedName}.{$extension}");
            }
            return null;
        }
    }

    /**
     * Request method to remove the specified resource (file associated with a project) from storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  $projectId
     * @param  $fileId
     * @return \Illuminate\Http\Response
     */
    public function removeFileRequest($projectId, $fileId)
    {
        try {
            if(!$this->removeFile($projectId, $fileId)) {
                throw new \Exception();
            }
            return response()->json([
                'message' => 'Successfully deleted file of project!'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error: the file of project was not deleted.'], 412);
        }
    }

    /**
     * Request method to remove the specified resource (files associated with a project) from storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function removeFilesRequest(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'files_id' => 'required|array'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            DB::beginTransaction();
            foreach($request->files_id as $fileId) {
                if(!$this->removeFile($id, $fileId)) {
                    throw new \Exception();
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'Successfully deleted files of project!'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error: the files of project was not deleted.'], 412);
        }
    }

    /**
     * Remove the specified resource (file associated with a project) from storage.
     *
     * @param  $projectId
     * @param  $fileId
     * @return boolean
     */
    public function removeFile($projectId, $fileId)
    {
        try {
            $project = Project::findOrFail($projectId);
            $file = File::where([
                ['files.id', $fileId],
                ['files.object_id', $project->id]
            ])
                ->join('file_types', 'files.file_type_id', '=', 'file_types.id')
                ->select('files.*')
                ->where('file_types.table_name', $project->getTable())
                ->firstOrFail();

            $projectDirectory = "projects/{$project->id}/";

            DB::beginTransaction();
            $file->forceDelete();
            \Storage::delete("{$projectDirectory}{$file->stored_name}.{$file->extension}");

            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Request method to send email to users that follow same term of the project specified.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function notifyByEmailRequest(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'string|nullable',
                'all_users' => 'boolean',
                'users_id' => 'array'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $typeNotification = strtoupper($request->query('type', 'UPDATE_PROJECT'));
            $typeNotification = $typeNotification === '' ? null : $typeNotification;
            $notifyAllUsers = $request->query('all_users', false);

            if($notifyAllUsers) {
                $usersToNotify = User::where('active', true)->get();
                if(!$this->notifyByEmail($id, $typeNotification, $usersToNotify)) {
                    throw new \Exception();
                }
            } else {
                $usersIdToNotify = $request->query('users_id', $this->usersWithProjectTerms($id));
                $usersToNotify = User::where('active', true)
                    ->whereIn('id', $usersIdToNotify)
                    ->distinct()
                    ->get();
                if(!$this->notifyByEmail($id, $typeNotification, $usersToNotify)) {
                    throw new \Exception();
                }
            }
            return response()->json([
                'message' => 'Successfully sent notifications!',
                'users_to_notify' => $usersToNotify
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: notifications was not sent.'], 412);
        }
    }

    /**
     * Send email to users that follow same term of the project specified.
     *
     * @param  $id
     * @param  $typeNotification
     * @param  $usersToNotify
     * @return boolean
     */
    private function notifyByEmail($id, $typeNotification = 'UPDATE_PROJECT', $usersToNotify = [])
    {
        try {
            $project = Project::findOrFail($id);

            switch ($typeNotification) {
                case 'NEW_PROJECT':
                    $notification = new NewProject($project);
                    break;
                case 'UPDATE_PROJECT':
                    $notification = new UpdateProject($project);
                    break;
                case 'CLOSE_PROJECT':
                    $notification = new CloseProject($project);
                    break;
                default:
                    throw new \Exception();
            }

            foreach($usersToNotify as $user) {
                $user->notify($notification);
            }
            return true;
        } catch (\Exception $exception) {
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

            switch ($option) {
                case 'TOTAL_ALL_VOTES':
                    $projects = Project::select(
                        'projects.*',
                        DB::raw('(votos_a_favor + votos_en_contra + abstencion) AS total_all_votes')
                    )
                        ->orderBy('total_all_votes', $order);
                    break;
                case 'PROPORTION_ACCORD_VOTES':
                    $projects = Project::select(
                        'projects.*',
                        DB::raw('(votos_a_favor/(votos_a_favor + votos_en_contra + abstencion)) AS proportion_accord_votes')
                    )
                        ->orderBy('proportion_accord_votes', $order);
                    break;
                case 'PROPORTION_DESACCORD_VOTES':
                    $projects = Project::select(
                        'projects.*',
                        DB::raw('(votos_en_contra/(votos_a_favor + votos_en_contra + abstencion)) AS proportion_desaccord_votes')
                    )
                        ->orderBy('proportion_desaccord_votes', $order);
                    break;
                case 'PROPORTION_ABSTENTION_VOTES':
                    $projects = Project::select(
                        'projects.*',
                        DB::raw('(abstencion/(votos_a_favor + votos_en_contra + abstencion)) AS proportion_abstention_votes')
                    )
                        ->orderBy('proportion_abstention_votes', $order);
                    break;
                default:
                    throw new \Exception();
            }
            $projects = $projects->offset($offset)
                ->limit($limit)
                ->get();
            return response()->json($projects, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: it was not possible to list the requested information.'], 412);
        }
    }

    /**
     * Build a report like a resume for one project.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function report($id)
    {
        try {
            $project = Project::findOrFail($id);
            $articles = Article::with('comments')->withCount(['comments'])->where('project_id', $id)->get();

            $pdf = \PDF::loadView('chartjs', array(
                'project_title' => $project->titulo,
                'project_boletin' => $project->boletin,
                'project_detail' => $project->resumen,
                'project' => $project,
                'articles' => $articles
            ));
            $pdf->setOption('enable-javascript', true);
            $pdf->setOption('javascript-delay', 5000);
            $pdf->setOption('enable-smart-shrinking', true);
            $pdf->setOption('no-stop-slow-scripts', true);
            $pdf->setOption('lowquality', false);
            $pdf->setOption('load-error-handling', 'ignore');

            return $pdf->download('Project report ' . date("Y-m-d H:i:s", time()) . '.pdf');
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: Report project was not be created.'], 412);
        }
    }
}
