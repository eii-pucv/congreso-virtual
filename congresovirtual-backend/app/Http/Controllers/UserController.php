<?php

namespace App\Http\Controllers;

use App\LocationOrg;
use App\MemberOrg;
use App\Notifications\PasswordResetSuccess;
use App\Notifications\Signup;
use App\Notifications\SignupActivate;
use App\User;
use App\Term;
use App\File;
use App\FileType;
use App\UserMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laravolt\Avatar\Avatar;
use Webpatser\Uuid\Uuid;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $filter = [];
            if($request->has('query')) {
                $filter['query'] = $request['query'];
            }
            if($request->has('name')) {
                $filter['name'] = $request->name;
            }
            if($request->has('surname')) {
                $filter['surname'] = $request->surname;
            }
            if($request->has('es_experto')) {
                $filter['esExperto'] = $request->es_experto;
            }
            if($request->has('es_organizacion')) {
                $filter['esOrganizacion'] = $request->es_organizacion;
            }
            if($request->has('terms')) {
                if(is_array($request->terms)) {
                    $filter['terms'] = $request->terms;
                } else if(!empty($request->terms)) {
                    $filter['terms'] = preg_split('/[^0-9]+/', $request->terms);
                }
            }

            if(Auth::check() && Auth::user()->hasRole('ADMIN') && isset($request->only_trashed) && $request->only_trashed) {
                $users = User::filter($filter)->onlyTrashed();
            } else {
                $users = User::filter($filter);
            }

            $totalResults = $users->count();

            if($request->has('order_by')) {
                $order = $request->query('order', 'ASC');
                $users = $users->orderBy($request->order_by, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $users = $users
                ->offset($offset)
                ->limit($limit);
            $users = $users->with(['terms'])->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($users),
                'results' => $users
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: users were not found.'], 412);
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
            DB::beginTransaction();
            if(User::where('email', $request->email)->first()) {
                throw new \Exception('USER_EMAIL_EXISTS_ERROR');
            }
            if(isset($request->username) && $request->username && UserMeta::where([['key', 'username'], ['value', $request->username]])->first()) {
                throw new \Exception('USER_USERNAME_EXISTS_ERROR');
            }

            $user = $this->basicRegister($request);
            if(!$user) {
                throw new \Exception('BASIC_REGISTER_ERROR');
            }
            if(!$this->expertRegister($request, $user)) {
                throw new \Exception('EXPERT_REGISTER_ERROR');
            }
            if(!$this->organizationRegister($request, $user)) {
                throw new \Exception('ORG_REGISTER_ERROR');
            }
            if(!$this->createAndSaveAvatar($user)) {
                throw new \Exception('AVATAR_CREATE_ERROR');
            }

            if(isset($request->active) && $request->active == true) {
                $user->notify(new Signup($request->password));
            } else {
                $user->notify(new SignupActivate(true, $request->password));
            }

            $data = $user->refresh()->toArray();
            $data['terms'] = $user->terms;
            $data['location_orgs'] = $user->locationOrgs;
            $data['member_orgs'] = $user->memberOrgs;

            DB::commit();
            return response()->json([
                'message' => 'Successfully created user!',
                'data' => $data
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
                'message' => 'Error: the user was not created.'], 412);
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
            return response()->json(User::with(['locationOrgs', 'memberOrgs', 'terms', 'avatarRelated'])->findOrFail($id), 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the user was not found.'], 412);
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
            if(Auth::user()->hasRole('ADMIN')) {
                $user = User::findOrFail($id);
            } else if(Auth::id() == $id) {
                $user = Auth::user();
            } else {
                throw new \Exception();
            }

            DB::beginTransaction();
            if(User::where([['email', $request->email], ['id', '!=', $id]])->first()) {
                throw new \Exception('USER_EMAIL_EXISTS_ERROR');
            }
            if(isset($request->username) && $request->username && UserMeta::where([['key', 'username'], ['value', $request->username], ['user_id', '!=', $id]])->first()) {
                throw new \Exception('USER_USERNAME_EXISTS_ERROR');
            }

            if(!$this->basicUpdate($request, $user)) {
                throw new \Exception('BASIC_UPDATE_ERROR');
            }
            if(!$this->expertUpdate($request, $user)) {
                throw new \Exception('EXPERT_UPDATE_ERROR');
            }
            if(!$this->organizationUpdate($request, $user)) {
                throw new \Exception('ORG_UPDATE_ERROR');
            }

            $data = $user->refresh()->toArray();
            $data['terms'] = $user->terms;
            $data['location_orgs'] = $user->locationOrgs;
            $data['member_orgs'] = $user->memberOrgs;

            DB::commit();
            return response()->json([
                'message' => 'Successfully updated user!',
                'data' => $data
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'error' => $exception->getMessage(),
                'message' => 'Error: the user was not updated.'], 412);
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

            if(Auth::id() == $id) {
                throw new \Exception();
            }

            $user = User::withTrashed()->findOrFail($id);
            $forceDelete = $request->query('force_delete', false);
            if($forceDelete) {
                $user->forceDelete();
            } else {
                $user->delete();
            }
            return response()->json([
                'message' => 'Successfully deleted user!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the user was not deleted.'], 412);
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
            User::onlyTrashed()->findOrFail($id)->restore();
            return response()->json([
                'message' => 'Successfully undeleted user!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the user was not undeleted.'], 412);
        }
    }

    /**
     * Update the specified attribute of resource in storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'old_password' => 'required|string',
                'new_password' => 'required|string|min:8|max:20|confirmed',
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $user = Auth::user();
            $credentials['email'] = $user->email;
            $credentials['password'] = $request->old_password;
            if(!Auth::guard('web')->attempt($credentials)) {
                throw new \Exception();
            }

            $user->fill(['password' => bcrypt($request->new_password)])->save();
            $user->notify(new PasswordResetSuccess());
            return response()->json([
                'message' => 'Successfully updated "password" attribute in user!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the "password" attribute of user was not updated.'], 412);
        }
    }

    /**
     * Store and replace the avatar of user in public storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAvatar(Request $request, $id)
    {
        try {
            if(Auth::id() == $id) {
                $user = Auth::user();
            } else if(Auth::user()->hasRole('ADMIN')) {
                $user = User::findOrFail($id);
            } else {
                throw new \Exception();
            }

            DB::beginTransaction();
            if($user->avatarRelated) {
                $oldAvatarFile = File::findOrFail($user->avatar_id);
                if(!$this->removeFile($user->id, $oldAvatarFile->id)) {
                    throw new \Exception();
                }
            }

            $newAvatarFile = $request->file('avatar');
            $allowedExtensions = [
                'AVATAR-IMAGE' => ['png', 'jpeg', 'jpg', 'bmp', 'gif', 'svg']
            ];
            $dataAvatarFileStored = $this->storeFile($user, $newAvatarFile, $allowedExtensions);
            if(!$dataAvatarFileStored) {
                throw new \Exception();
            }

            $user->avatarRelated()->associate($dataAvatarFileStored['id']);
            $user->save();

            DB::commit();
            return response()->json([
                'message' => 'Successfully stored avatar of user!'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            if(isset($dataAvatarFileStored) && isset($user)) {
                $userDirectory = "users/{$user->id}/";
                \Storage::delete("{$userDirectory}{$dataAvatarFileStored['stored_name']}.{$dataAvatarFileStored['extension']}");
            }
            return response()->json([
                'message' => 'Error: the avatar of user was not stored.'], 412);
        }
    }

    /**
     * Remove the avatar of user in public storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function removeAvatar($id)
    {
        try {
            if(Auth::id() == $id) {
                $user = Auth::user();
            } else if(Auth::user()->hasRole('ADMIN')) {
                $user = User::findOrFail($id);
            } else {
                throw new \Exception();
            }

            DB::beginTransaction();
            if(!$user->avatarRelated) {
                throw new \Exception();
            }
            $avatarFile = File::findOrFail($user->avatar_id);
            if(!$this->removeFile($user->id, $avatarFile->id)) {
                throw new \Exception();
            }
            $user->avatarRelated()->dissociate();
            $user->save();

            DB::commit();
            return response()->json([
                'message' => 'Successfully removed avatar of user!'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error: the avatar of user was not removed.'], 412);
        }
    }

    /**
     * Display the avatar associated.
     * About access control: all users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function avatar($id)
    {
        try {
            return response()->json(User::findOrFail($id)->avatarRelated, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the user was not found.'], 412);
        }
    }

    /**
     * Display the locations of organization associated.
     * About access control: all users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function locationOrgs($id)
    {
        try {
            return response()->json(User::findOrFail($id)->locationOrgs, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the user was not found.'], 412);
        }
    }

    /**
     * Display the members of organization associated.
     * About access control: all users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function memberOrgs($id)
    {
        try {
            return response()->json(User::findOrFail($id)->memberOrgs, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the user was not found.'], 412);
        }
    }

    /**
     * Display the comments associated.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function comments(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $request['user_id'] = $user->id;
            $commentController = new CommentController();

            return response()->json($commentController->index($request)->getOriginalContent());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the user was not found.'], 412);
        }
    }

    /**
     * Display the votes associated.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function votes(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $request['user_id'] = $user->id;
            $voteController = new VoteController();

            return response()->json($voteController->index($request)->getOriginalContent());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the user was not found.'], 412);
        }
    }

    /**
     * Display the votes of urgency associated.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function urgencies(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $request['user_id'] = $user->id;
            $urgencyController = new UrgencyController();

            return response()->json($urgencyController->index($request)->getOriginalContent());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the user was not found.'], 412);
        }
    }

    /**
     * Display the proposals associated.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function proposals(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $request['user_id'] = $user->id;
            $proposalController = new ProposalController();

            return response()->json($proposalController->index($request)->getOriginalContent());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the user was not found.'], 412);
        }
    }

    /**
     * Display the denounces associated.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function denounces(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $request['user_id'] = $user->id;
            $denounceController = new DenounceController();

            return response()->json($denounceController->index($request)->getOriginalContent());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the user was not found.'], 412);
        }
    }

    /**
     * Display the terms associated.
     * About access control: all users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function terms($id)
    {
        try {
            return response()->json(User::findOrFail($id)->terms, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the user was not found.'], 412);
        }
    }

    /**
     * Associate a term with user in storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function associateTerm(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'term_id' => 'required|integer'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            if(Auth::id() == $id) {
                $user = Auth::user();
            } else if(Auth::user()->hasRole('ADMIN')) {
                $user = User::findOrFail($id);
            } else {
                throw new \Exception();
            }
            $term = Term::findOrFail($request->term_id);

            $user->terms()->attach($term);
            return response()->json([
                'message' => 'Successfully associated term with user!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the association term with user was not created.'], 412);
        }
    }

    /**
     * Associate some terms with user in storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
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

            if(Auth::id() == $id) {
                $user = Auth::user();
            } else if(Auth::user()->hasRole('ADMIN')) {
                $user = User::findOrFail($id);
            } else {
                throw new \Exception();
            }

            $user->terms()->sync($request->terms_id, false);
            return response()->json([
                'message' => 'Successfully associated terms with user!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the association terms with user was not created.'], 412);
        }
    }

    /**
     * Dissociate all terms with user in storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function dissociateAllTerms(Request $request, $id)
    {
        try {
            if(Auth::id() == $id) {
                $user = Auth::user();
            } else if(Auth::user()->hasRole('ADMIN')) {
                $user = User::findOrFail($id);
            } else {
                throw new \Exception();
            }

            $user->terms()->detach();
            return response()->json([
                'message' => 'Successfully dissociate all terms with user!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the dissociation all terms with user was not executed.'], 412);
        }
    }

    /**
     * Store a newly created resource (file associated with a user) in storage.
     *
     * @param  $user
     * @param  $file
     * @param  $customAllowedExtensions
     * @return array
     */
    private function storeFile($user, $file, $customAllowedExtensions = null)
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
                    'AVATAR-IMAGE' => ['png', 'jpeg', 'jpg', 'bmp', 'gif', 'svg']
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
                'table_name' => $user->getTable()
            ]);

            $userDirectory = "users/{$user->id}/";
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $storedBasename = \Str::random(40) . ".{$extension}";
            $storedName = pathinfo($file->storeAs($userDirectory, $storedBasename), PATHINFO_FILENAME);

            $file = new File([
                'original_name' => $originalName,
                'stored_name' => $storedName,
                'extension' => $extension,
                'object_id' => $user->id
            ]);
            $file->fileType()->associate($fileType);
            $file->save();

            $dataFileStored = $file->toArray();
            unset($dataFileStored['file_type']);

            DB::commit();
            return $dataFileStored;
        } catch (\Exception $exception) {
            DB::rollBack();
            if(isset($extension) && isset($userDirectory) && isset($storedName)) {
                \Storage::delete("{$userDirectory}{$storedName}.{$extension}");
            }
            return null;
        }
    }

    /**
     * Remove the specified resource (file associated with a user) from storage.
     *
     * @param  $userId
     * @param  $fileId
     * @return boolean
     */
    private function removeFile($userId, $fileId)
    {
        try {
            $user = User::findOrFail($userId);
            $file = File::where([
                ['files.id', $fileId],
                ['files.object_id', $user->id]
            ])
                ->join('file_types', 'files.file_type_id', '=', 'file_types.id')
                ->select('files.*')
                ->where('file_types.table_name', $user->getTable())
                ->firstOrFail();

            $userDirectory = "users/{$user->id}/";

            DB::beginTransaction();
            $file->forceDelete();
            \Storage::delete("{$userDirectory}{$file->stored_name}.{$file->extension}");

            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Register the basic attributes of user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $user
     * @return mixed
     */
    private function basicRegister(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'                  => 'required|string|max:191',
                'surname'               => 'required|string|max:191',
                'username'              => 'string|max:191|nullable',
                'rol'                   => 'string|in:USER,ADMIN|nullable',
                'email'                 => 'required|string|email|unique:users',
                'active'                => 'boolean',
                'password'              => 'required|string|min:8|max:20|confirmed',
                'birthday'              => 'date_format:Y-m-d|before:' . date('Y-m-d') . '|nullable',
                'dni'                   => 'string|max:191|nullable',
                'pais'                  => 'string|max:191|nullable',
                'region'                => 'string|max:191|nullable',
                'comuna'                => 'string|max:191|nullable',
                'sector'                => 'integer|nullable',
                'nivel_educacional'     => 'integer|nullable',
                'genero'                => 'integer|nullable',
                'actividad'             => 'integer|nullable'
            ]);
            if($validator->fails()) {
                throw new \Exception();
            }

            if(isset($request->active) && $request->active == true) {
                $active = true;
                $activationToken = null;
            } else {
                $active = false;
                $activationToken = Uuid::generate()->string;
            }

            $user = new User([
                'name'                  => $request->name,
                'surname'               => $request->surname,
                'rol'                   => isset($request->rol) ? $request->rol : 'USER',
                'email'                 => $request->email,
                'active'                => $active,
                'password'              => bcrypt($request->password),
                'activation_token'      => $activationToken
            ]);
            $user->save();

            $user->fill([
                'username'              => $request->username,
                'birthday'              => $request->birthday,
                'dni'                   => $request->dni,
                'pais'                  => $request->pais,
                'region'                => $request->region,
                'comuna'                => $request->comuna,
                'sector'                => $request->sector,
                'nivel_educacional'     => $request->nivel_educacional,
                'genero'                => $request->genero,
                'actividad'             => $request->actividad
            ]);
            $user->save();
            return $user;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Register the expert attributes of user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $user
     * @return mixed
     */
    private function expertRegister(Request $request, $user)
    {
        try {
            $validator = Validator::make($request->all(), [
                'es_experto'            => 'boolean'
            ]);
            if($validator->fails()) {
                throw new \Exception();
            }

            if(!$request->has('es_experto') || !$request->es_experto) {
                $fillArray = [
                    'es_experto'            => false
                ];
            } else {
                $validator = Validator::make($request->all(), [
                    'titulo_profesional'    => 'integer|nullable',
                    'estudios_adicionales'  => 'string|nullable',
                    'anios_experiencia_laboral' => 'integer|nullable',
                    'areas_desempenio'      => 'array|nullable',
                    'temas_trabajo'         => 'string|nullable'
                ]);
                if($validator->fails()) {
                    throw new \Exception();
                }

                if(!$request->has('nivel_educacional') || !($request->nivel_educacional == 7 || $request->nivel_educacional == 8)) { // 7: Estudios univeristarios completos, 8: Estudios de postgrado
                    throw new \Exception();
                }

                $fillArray = [
                    'es_experto'            => true,
                    'titulo_profesional'    => $request->titulo_profesional,
                    'estudios_adicionales'  => $request->estudios_adicionales,
                    'anios_experiencia_laboral' => $request->anios_experiencia_laboral,
                    'areas_desempenio'      => json_encode($request->areas_desempenio),
                    'temas_trabajo'         => $request->temas_trabajo
                ];
            }
            $user->fill($fillArray)->save();
            return $user;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Register the organization attributes of user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $user
     * @return mixed
     */
    private function organizationRegister(Request $request, $user)
    {
        try {
            $validator = Validator::make($request->all(), [
                'es_organizacion'       => 'boolean'
            ]);
            if($validator->fails()) {
                throw new \Exception();
            }

            if(!$request->has('es_organizacion') || !$request->es_organizacion) {
                $fillArray = [
                    'es_organizacion'       => false
                ];
            } else {
                $validator = Validator::make($request->all(), [
                    'nombre_org'            => 'required|string|max:191',
                    'email_org'             => 'required|string|max:191|nullable',
                    'enlace_org'            => 'string|max:191|nullable',
                    'tiene_per_jur'         => 'boolean',
                    'location_orgs'         => 'array|nullable',
                    'member_orgs'           => 'array|nullable'
                ]);
                if($validator->fails()) {
                    throw new \Exception();
                }

                if(!$request->has('tiene_per_jur') || !$request->tiene_per_jur) {
                    $fillArray = [
                        'tiene_per_jur'         => false
                    ];
                } else {
                    $validator = Validator::make($request->all(), [
                        'dni_org'               => 'required|string|max:191',
                        'rep_legal_org'         => 'required|string|max:191',
                        'tipo_org'              => 'required|integer'
                    ]);
                    if($validator->fails()) {
                        throw new \Exception();
                    }

                    $fillArray = [
                        'tiene_per_jur'         => true,
                        'dni_org'               => $request->dni_org,
                        'rep_legal_org'         => $request->rep_legal_org,
                        'tipo_org'              => $request->tipo_org
                    ];
                }

                $fillArray = array_merge($fillArray, [
                    'es_organizacion'       => true,
                    'nombre_org'            => $request->nombre_org,
                    'email_org'             => $request->email_org,
                    'enlace_org'            => $request->enlace_org
                ]);

                if($request->has('location_orgs') && $request->location_orgs) {
                    foreach($request->location_orgs as $locationDate) {
                        $validator = Validator::make($locationDate, [
                            'es_principal' => 'boolean',
                            'direccion' => 'required|string|max:191',
                            'pais' => 'required|string|max:191',
                            'region' => 'string|max:191|nullable',
                            'comuna' => 'string|max:191|nullable',
                            'sector' => 'required|integer'
                        ]);
                        if($validator->fails()) {
                            throw new \Exception();
                        }

                        $newLocation = new LocationOrg([
                            'es_principal' => isset($locationDate['es_principal']) ? $locationDate['es_principal'] : false,
                            'direccion' => $locationDate['direccion'],
                            'pais' => $locationDate['pais'],
                            'region' => $locationDate['region'],
                            'comuna' => $locationDate['comuna'],
                            'sector' => $locationDate['sector']
                        ]);
                        $newLocation->user()->associate($user);
                        $newLocation->save();
                    }
                }
                if($request->has('member_orgs') && $request->member_orgs) {
                    foreach($request->member_orgs as $memberDate) {
                        $validator = Validator::make($memberDate, [
                            'name' => 'required|string|max:191',
                            'surname' => 'required|string|max:191',
                            'dni' => 'string|max:191|nullable'
                        ]);
                        if($validator->fails()) {
                            throw new \Exception();
                        }

                        $newMember = new MemberOrg([
                            'name' => $memberDate['name'],
                            'surname' => $memberDate['surname'],
                            'dni' => $memberDate['dni']
                        ]);
                        $newMember->user()->associate($user);
                        $newMember->save();
                    }
                }
            }
            $user->fill($fillArray)->save();
            return $user;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Create and save user avatar.
     *
     * @param  $user
     * @return mixed
     */
    private function createAndSaveAvatar($user)
    {
        try {
            $fileType = FileType::firstOrCreate([
                'value' => 'AVATAR-IMAGE',
                'table_name' => $user->getTable()
            ]);

            $userDirectory = "users/{$user->id}/";
            $extension = 'png';
            $originalName = 'avatar';
            $storedName = \Str::random(40);
            $storedBasename = "{$storedName}.{$extension}";
            $avatarFile = (new Avatar)->create($user->name)->getImageObject()->encode($extension);
            Storage::put("{$userDirectory}{$storedBasename}", $avatarFile);

            $file = new File([
                'original_name' => $originalName,
                'stored_name' => $storedName,
                'extension' => $extension,
                'object_id' => $user->id
            ]);
            $file->fileType()->associate($fileType);
            $file->save();
            $user->avatarRelated()->associate($file);
            $user->save();
            return $user;
        } catch (\Exception $exception) {
            if(isset($userDirectory)) {
                \Storage::deleteDirectory($userDirectory);
            }
            return false;
        }
    }

    /**
     * Update the basic attributes of user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $user
     * @return mixed
     */
    private function basicUpdate(Request $request, $user)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'                  => 'required|string|max:191',
                'surname'               => 'required|string|max:191',
                'username'              => 'string|max:191|nullable',
                'rol'                   => 'string|in:USER,ADMIN|nullable',
                'email'                 => 'required|string|email|unique:users,email,' . $user->id,
                'active'                => 'boolean',
                'birthday'              => 'date_format:Y-m-d|before:' . date('Y-m-d') . '|nullable',
                'dni'                   => 'string|max:191|nullable',
                'pais'                  => 'string|max:191|nullable',
                'region'                => 'string|max:191|nullable',
                'comuna'                => 'string|max:191|nullable',
                'sector'                => 'integer|nullable',
                'nivel_educacional'     => 'integer|nullable',
                'genero'                => 'integer|nullable',
                'actividad'             => 'integer|nullable'
            ]);
            if($validator->fails()) {
                throw new \Exception();
            }

            $active = $user->active;
            $activationToken = $user->activation_token;
            $rol = $user->rol;
            if(Auth::user()->hasRole('ADMIN') && isset($request->active)) {
                if(isset($request->active)) {
                    $active = $request->active;
                    if($active && $activationToken) {
                        $activationToken = null;
                    }
                }
                if(isset($request->rol)) {
                    $rol = $request->rol;
                }
            }

            $user->fill([
                'name'                  => $request->name,
                'surname'               => $request->surname,
                'email'                 => $request->email,
                'username'              => $request->username,
                'rol'                   => $rol,
                'active'                => $active,
                'activation_token'      => $activationToken,
                'birthday'              => $request->birthday,
                'dni'                   => $request->dni,
                'pais'                  => $request->pais,
                'region'                => $request->region,
                'comuna'                => $request->comuna,
                'sector'                => $request->sector,
                'nivel_educacional'     => $request->nivel_educacional,
                'genero'                => $request->genero,
                'actividad'             => $request->actividad
            ]);
            $user->save();
            return $user;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Update the expert attributes of user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $user
     * @return mixed
     */
    private function expertUpdate(Request $request, $user)
    {
        try {
            $validator = Validator::make($request->all(), [
                'es_experto'            => 'boolean'
            ]);
            if($validator->fails()) {
                throw new \Exception();
            }

            if(!$request->has('es_experto') || !$request->es_experto) {
                    $fillArray = [
                        'es_experto'            => false,
                        'titulo_profesional'    => null,
                        'estudios_adicionales'  => null,
                        'anios_experiencia_laboral' => null,
                        'areas_desempenio'      => null,
                        'temas_trabajo'         => null
                    ];
            } else {
                $validator = Validator::make($request->all(), [
                    'titulo_profesional'    => 'integer|nullable',
                    'estudios_adicionales'  => 'string|nullable',
                    'anios_experiencia_laboral' => 'integer|nullable',
                    'areas_desempenio'      => 'array|nullable',
                    'temas_trabajo'         => 'string|nullable'
                ]);
                if($validator->fails()) {
                    throw new \Exception();
                }

                if(!$request->has('nivel_educacional') || !($request->nivel_educacional == 7 || $request->nivel_educacional == 8)) { // 7: Estudios univeristarios completos, 8: Estudios de postgrado
                    throw new \Exception();
                }

                $fillArray = [
                    'es_experto'            => true,
                    'titulo_profesional'    => $request->titulo_profesional,
                    'estudios_adicionales'  => $request->estudios_adicionales,
                    'anios_experiencia_laboral' => $request->anios_experiencia_laboral,
                    'areas_desempenio'      => json_encode($request->areas_desempenio),
                    'temas_trabajo'         => $request->temas_trabajo
                ];
            }
            $user->fill($fillArray)->save();
            return $user;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Update the organization attributes of user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $user
     * @return mixed
     */
    private function organizationUpdate(Request $request, $user)
    {
        try {
            $validator = Validator::make($request->all(), [
                'es_organizacion'       => 'boolean'
            ]);
            if($validator->fails()) {
                throw new \Exception();
            }

            if(!$request->has('es_organizacion') || !$request->es_organizacion) {
                $fillArray = [
                    'es_organizacion'       => false,
                    'nombre_org'            => null,
                    'email_org'             => null,
                    'enlace_org'            => null,
                    'tiene_per_jur'         => null,
                    'dni_org'               => null,
                    'rep_legal_org'         => null,
                    'tipo_org'              => null
                ];
                $user->locationOrgs()->forceDelete();
                $user->memberOrgs()->forceDelete();
            } else {
                $validator = Validator::make($request->all(), [
                    'nombre_org'            => 'required|string|max:191',
                    'email_org'             => 'required|string|max:191|nullable',
                    'enlace_org'            => 'string|max:191|nullable',
                    'tiene_per_jur'         => 'boolean',
                    'location_orgs'         => 'array|nullable',
                    'member_orgs'           => 'array|nullable'
                ]);
                if($validator->fails()) {
                    throw new \Exception();
                }

                if(!$request->has('tiene_per_jur') || !$request->tiene_per_jur) {
                    $fillArray = [
                        'tiene_per_jur'         => false,
                        'dni_org'               => null,
                        'rep_legal_org'         => null,
                        'tipo_org'              => null
                    ];
                } else {
                    $validator = Validator::make($request->all(), [
                        'dni_org'               => 'required|string|max:191',
                        'rep_legal_org'         => 'required|string|max:191',
                        'tipo_org'              => 'required|integer'
                    ]);
                    if($validator->fails()) {
                        throw new \Exception();
                    }

                    $fillArray = [
                        'tiene_per_jur'         => true,
                        'dni_org'               => $request->dni_org,
                        'rep_legal_org'         => $request->rep_legal_org,
                        'tipo_org'              => $request->tipo_org
                    ];
                }

                $fillArray = array_merge($fillArray, [
                    'es_organizacion'       => true,
                    'nombre_org'            => $request->nombre_org,
                    'email_org'             => $request->email_org,
                    'enlace_org'            => $request->enlace_org
                ]);

                if($request->has('location_orgs') && $request->location_orgs) {
                    $user->locationOrgs()->forceDelete();
                    foreach($request->location_orgs as $locationDate) {
                        $validator = Validator::make($locationDate, [
                            'es_principal' => 'boolean',
                            'direccion' => 'required|string|max:191',
                            'pais' => 'required|string|max:191',
                            'region' => 'string|max:191|nullable',
                            'comuna' => 'string|max:191|nullable',
                            'sector' => 'required|integer'
                        ]);
                        if($validator->fails()) {
                            throw new \Exception();
                        }

                        $newLocation = new LocationOrg([
                            'es_principal' => isset($locationDate['es_principal']) ? $locationDate['es_principal'] : false,
                            'direccion' => $locationDate['direccion'],
                            'pais' => $locationDate['pais'],
                            'region' => $locationDate['region'],
                            'comuna' => $locationDate['comuna'],
                            'sector' => $locationDate['sector']
                        ]);
                        $newLocation->user()->associate($user);
                        $newLocation->save();
                    }
                }
                if($request->has('member_orgs') && $request->member_orgs) {
                    $user->memberOrgs()->forceDelete();
                    foreach($request->member_orgs as $memberDate) {
                        $validator = Validator::make($memberDate, [
                            'name' => 'required|string|max:191',
                            'surname' => 'required|string|max:191',
                            'dni' => 'string|max:191|nullable'
                        ]);
                        if($validator->fails()) {
                            throw new \Exception();
                        }

                        $newMember = new MemberOrg([
                            'name' => $memberDate['name'],
                            'surname' => $memberDate['surname'],
                            'dni' => $memberDate['dni']
                        ]);
                        $newMember->user()->associate($user);
                        $newMember->save();
                    }
                }
            }
            $user->fill($fillArray)->save();
            return $user;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
