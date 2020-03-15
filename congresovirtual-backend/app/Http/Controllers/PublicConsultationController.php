<?php

namespace App\Http\Controllers;

use App\Position;
use App\PublicConsultation;
use App\Comment;
use App\Term;
use App\Vote;
use App\File;
use App\FileType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PublicConsultationController extends Controller
{
    /**
     * Display a listing of the resource.
     * About access control: all users can use this method (see routes).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $filter = [];
            if($request->has('query')) {
                $filter['query'] = $request['query'];
            }
            if($request->has('titulo')) {
                $filter['titulo'] = $request->titulo;
            }
            if($request->has('autor')) {
                $filter['autor'] = $request->autor;
            }
            if($request->has('detalle')) {
                $filter['detalle'] = $request->detalle;
            }
            if($request->has('resumen')) {
                $filter['resumen'] = $request->resumen;
            }
            if($request->has('estado')) {
                $filter['estado'] = $request->estado;
            }
            if($request->has('terms')) {
                if(is_array($request->terms)) {
                    $filter['terms'] = $request->terms;
                } else if(!empty($request->terms)) {
                    $filter['terms'] = preg_split('/[^0-9]+/', $request->terms);
                }
            }

            $publicConsultations = PublicConsultation::filter($filter);
            $totalResults = $publicConsultations->count();

            if($request->has('order_by')) {
                $order = $request->query('order', 'ASC');
                $publicConsultations = $publicConsultations->orderBy($request->order_by, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $publicConsultations = $publicConsultations
                ->offset($offset)
                ->limit($limit);
            $publicConsultations = $publicConsultations->with(['terms'])->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($publicConsultations),
                'results' => $publicConsultations
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: public consultations were not found.'], 412);
        }
    }

    /**
     * Store a newly created resource in storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'titulo' => 'required|string',
                'autor' => 'string|max:191|nullable',
                'estado' => 'integer|in:0,1',
                'detalle' => 'required|string',
                'resumen' => 'required|string',
                'fecha_inicio' => 'required|date_format:Y-m-d H:i:s',
                'fecha_termino' => 'required|date_format:Y-m-d H:i:s|after_or_equal:fecha_inicio',
                'icono' => 'string|max:191|nullable',
                'video_code' => 'string|max:191|nullable',
                'video_source' => 'string|max:191|nullable',
                'terms_id' => 'array'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $publicConsultation = new PublicConsultation([
                'titulo' => $request->titulo,
                'autor' => $request->autor,
                'estado' => $request->has('estado') ? $request->estado : 0,
                'detalle' => $request->detalle,
                'resumen' => $request->resumen,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_termino' => $request->fecha_termino,
                'icono' => $request->icono,
                'video_code' => $request->video_code,
                'video_source' => $request->video_source
            ]);
            DB::beginTransaction();
            $publicConsultation->save();

            $imageFile = $request->file('imagen');
            if($imageFile) {
                $allowedExtensions = [
                    'PRIMARY-IMAGE' => ['png', 'jpeg', 'jpg', 'bmp', 'gif', 'svg']
                ];
                $dataFileStored = $this->storeFile($publicConsultation, $imageFile, $allowedExtensions);
                if(!$dataFileStored) {
                    throw new \Exception();
                }
                $publicConsultation->imagenRelated()->associate($dataFileStored['id'])->save();
            }

            if(isset($request->terms_id)) {
                $terms = Term::whereIn('id', $request->terms_id)->pluck('id');
                $publicConsultation->terms()->sync($terms);
            }

            DB::commit();
            return response()->json([
                'message' => 'Successfully created public consultation!',
                'data' => $publicConsultation->toArray()
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            if(isset($publicConsultationDirectory)) {
                \Storage::deleteDirectory($publicConsultationDirectory);
            }
            return response()->json([
                'message' => 'Error: the public consultation was not created.'], 412);
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
            $publicConsultation = PublicConsultation::with(['votes', 'terms'])->findOrFail($id);
            $data = $publicConsultation->toArray();
            $data['files'] = $publicConsultation->files();
            return response()->json($data, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the public consultation was not found.'], 412);
        }
    }

    /**
     * Update the specified resource in storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param \Illuminate\Http\Request $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'titulo' => 'required|string',
                'autor' => 'string|max:191|nullable',
                'estado' => 'integer|in:0,1',
                'detalle' => 'required|string',
                'resumen' => 'required|string',
                'fecha_inicio' => 'required|date_format:Y-m-d H:i:s',
                'fecha_termino' => 'required|date_format:Y-m-d H:i:s|after_or_equal:fecha_inicio',
                'icono' => 'string|max:191|nullable',
                'video_code' => 'string|max:191|nullable',
                'video_source' => 'string|max:191|nullable'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $publicConsultation = PublicConsultation::findOrFail($id);
            $publicConsultation->fill([
                'titulo' => $request->titulo,
                'autor' => $request->autor,
                'estado' => $request->has('estado') ? $request->estado : $publicConsultation->estado,
                'detalle' => $request->detalle,
                'resumen' => $request->resumen,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_termino' => $request->fecha_termino,
                'icono' => $request->icono,
                'video_code' => $request->video_code,
                'video_source' => $request->video_source
            ]);
            $publicConsultation->save();
            return response()->json([
                'message' => 'Successfully updated public consultation!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the public consultation was not updated.'], 412);
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

            $publicConsultation = PublicConsultation::withTrashed()->findOrFail($id);
            $forceDelete = $request->query('force_delete', false);
            if($forceDelete) {
                $publicConsultation->forceDelete();
            } else {
                $publicConsultation->delete();
            }
            return response()->json([
                'message' => 'Successfully deleted public consultation!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the public consultation was not deleted.'], 412);
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
            PublicConsultation::onlyTrashed()->findOrFail($id)->restore();
            return response()->json([
                'message' => 'Successfully undeleted public consultation!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the public consultation was not undeleted.'], 412);
        }
    }

    /**
     * Store and replace the image of public consultation in public storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function updateImage(Request $request, $id)
    {
        try {
            $publicConsultation = PublicConsultation::findOrFail($id);

            DB::beginTransaction();
            if($publicConsultation->imagenRelated) {
                $oldImageFile = File::findOrFail($publicConsultation->imagen_id);
                if(!$this->removeFile($publicConsultation->id, $oldImageFile->id)) {
                    throw new \Exception();
                }
            }

            $newImageFile = $request->file('imagen');
            $allowedExtensions = [
                'PRIMARY-IMAGE' => ['png', 'jpeg', 'jpg', 'bmp', 'gif', 'svg']
            ];
            $dataImageFileStored = $this->storeFile($publicConsultation, $newImageFile, $allowedExtensions);
            if(!$dataImageFileStored) {
                throw new \Exception();
            }

            $publicConsultation->imagenRelated()->associate($dataImageFileStored['id']);
            $publicConsultation->save();

            DB::commit();
            return response()->json([
                'message' => 'Successfully updated image of public consultation!'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            if(isset($dataImageFileStored) && isset($publicConsultation)) {
                $publicConsultationDirectory = "consultations/{$publicConsultation->id}/";
                \Storage::delete("{$publicConsultationDirectory}{$dataImageFileStored['stored_name']}.{$dataImageFileStored['extension']}");
            }
            return response()->json([
                'message' => 'Error: the image of public consultation was not updated.'], 412);
        }
    }

    /**
     * Remove the image of public consultation in public storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function removeImage($id)
    {
        try {
            $publicConsultation = PublicConsultation::findOrFail($id);

            DB::beginTransaction();
            if(!$publicConsultation->imagenRelated) {
                throw new \Exception();
            }
            $imageFile = File::findOrFail($publicConsultation->imagen_id);
            if(!$this->removeFile($publicConsultation->id, $imageFile->id)) {
                throw new \Exception();
            }
            $publicConsultation->imagenRelated()->dissociate();
            $publicConsultation->save();

            DB::commit();
            return response()->json([
                'message' => 'Successfully removed image of public consultation!'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error: the image of public consultation was not removed.'], 412);
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
            return response()->json(PublicConsultation::findOrFail($id)->imagenRelated, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the public consultation was not found.'], 412);
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
            $publicConsultation = PublicConsultation::findOrFail($id);
            $request['public_consultation_id'] = $publicConsultation->id;
            unset($request['project_id'], $request['article_id'], $request['idea_id']);
            $commentController = new CommentController();

            return response()->json($commentController->index($request)->getOriginalContent());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the public consultation was not found.'], 412);
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
            $publicConsultation = PublicConsultation::findOrFail($id);
            $request['public_consultation_id'] = $publicConsultation->id;
            unset($request['project_id'], $request['article_id'], $request['idea_id'], $request['comment_id']);
            $voteController = new VoteController();

            return response()->json($voteController->index($request)->getOriginalContent());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the public consultation was not found.'], 412);
        }
    }

    /**
     * Display the terms.
     * About access control: all users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function terms($id)
    {
        try {
            return response()->json(PublicConsultation::findOrFail($id)->terms, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the public consultation was not found.'], 412);
        }
    }

    /**
     * Associate some terms with public consultation in storage.
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

            $publicConsultation = PublicConsultation::findOrFail($id);
            $publicConsultation->terms()->sync($request->terms_id, false);
            return response()->json([
                'message' => 'Successfully associated terms with public consultation!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the association terms with public consultation was not created.'], 412);
        }
    }

    /**
     * Dissociate all terms with public consultation in storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function dissociateAllTerms(Request $request, $id)
    {
        try {
            $publicConsultation = PublicConsultation::findOrFail($id);
            $publicConsultation->terms()->sync([]);
            return response()->json([
                'message' => 'Successfully dissociate all terms with public consultation!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the dissociation all terms with public consultation was not executed.'], 412);
        }
    }

    /**
     * Store a newly created resource (comment associated with a public consultation) in storage.
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

            $publicConsultation = PublicConsultation::findOrFail($id);
            if(!Auth::user()->hasRole('ADMIN')) {
                if($publicConsultation->estado == 0) {
                    throw new \Exception();
                }
            }

            $user = Auth::user();

            $comment = new Comment([
                'body' => $request->body
            ]);
            $comment->parent()->associate($parent);
            $comment->publicConsultation()->associate($publicConsultation);
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
                'message' => 'Successfully created comment of public consultation!'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error: the comment of public consultation was not created.'], 412);
        }
    }

    /**
     * Store a newly created resource (vote associated with a public consultation) in storage.
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
                'user_id' => 'required|integer|unique:votes,user_id,NULL,NULL,public_consultation_id,' . $id
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $publicConsultation = PublicConsultation::findOrFail($id);
            if(!Auth::user()->hasRole('ADMIN')) {
                if($publicConsultation->estado == 0) {
                    throw new \Exception();
                }
            }

            $currentDatetime = date("Y-m-d H:i:s", time());
            if($currentDatetime < $publicConsultation->fecha_inicio || $currentDatetime > $publicConsultation->fecha_termino) {
                throw new \Exception();
            }

            $user = Auth::user();

            $vote = new Vote([
                'vote' => $request->vote
            ]);
            $vote->publicConsultation()->associate($publicConsultation);
            $vote->user()->associate($user);
            $vote->save();

            $data = $vote->refresh()->toArray();
            unset($data['user']);

            return response()->json([
                'message' => 'Successfully created vote of public consultation!',
                'data' => $data
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the vote of public consultation was not created.'], 412);
        }
    }

    /**
     * Display the specified resource (user vote associated with a public consultation) in storage.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function showUserVote(Request $request, $id)
    {
        try {
            $vote = Vote::where('public_consultation_id', $id)
                ->where('user_id', $request->user_id)
                ->firstOrFail();
            return response()->json($vote, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the user vote associated of public consultation was not found.'], 412);
        }
    }

    /**
     * Store a newly created resource (file associated with a public consultation) in storage.
     *
     * @param  $publicConsultation
     * @param  $file
     * @param  $customAllowedExtensions
     * @return array
     */
    private function storeFile($publicConsultation, $file, $customAllowedExtensions = null)
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
                    'PRIMARY-IMAGE' => ['png', 'jpeg', 'jpg', 'bmp', 'gif', 'svg']
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
                'table_name' => $publicConsultation->getTable()
            ]);

            $publicConsultationDirectory = "consultations/{$publicConsultation->id}/";
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $storedBasename = \Str::random(40) . ".{$extension}";
            $storedName = pathinfo($file->storeAs($publicConsultationDirectory, $storedBasename), PATHINFO_FILENAME);

            $file = new File([
                'original_name' => $originalName,
                'stored_name' => $storedName,
                'extension' => $extension,
                'object_id' => $publicConsultation->id
            ]);
            $file->fileType()->associate($fileType);
            $file->save();

            $dataFileStored = $file->toArray();
            unset($dataFileStored['file_type']);

            DB::commit();
            return $dataFileStored;
        } catch (\Exception $exception) {
            DB::rollBack();
            if(isset($extension) && isset($publicConsultationDirectory) && isset($storedName)) {
                \Storage::delete("{$publicConsultationDirectory}{$storedName}.{$extension}");
            }
            return null;
        }
    }

    /**
     *  Remove the specified resource (file associated with a public consultation) from storage.
     *
     * @param  $publicConsultationId
     * @param  $fileId
     * @return boolean
     */
    public function removeFile($publicConsultationId, $fileId)
    {
        try {
            $publicConsultation = PublicConsultation::findOrFail($publicConsultationId);
            $file = File::where([
                ['files.id', $fileId],
                ['files.object_id', $publicConsultation->id]
            ])
                ->join('file_types', 'files.file_type_id', '=', 'file_types.id')
                ->select('files.*')
                ->where('file_types.table_name', $publicConsultation->getTable())
                ->firstOrFail();

            $publicConsultationDirectory = "consultations/{$publicConsultation->id}/";

            DB::beginTransaction();
            $file->forceDelete();
            \Storage::delete("{$publicConsultationDirectory}{$file->stored_name}.{$file->extension}");

            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }
}
