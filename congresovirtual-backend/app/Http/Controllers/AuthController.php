<?php

namespace App\Http\Controllers;

use App\Gamification\Player;
use App\MemberOrg;
use App\LocationOrg;
use App\User;
use App\UserMeta;
use App\File;
use App\FileType;
use App\Notifications\SignupActivate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravolt\Avatar\Avatar;
use Webpatser\Uuid\Uuid;

class AuthController extends Controller
{
    /**
     * Standard sign up.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function signup(Request $request)
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

            $user->notify(new SignupActivate());

            DB::commit();
            return response()->json([
                'message' => 'Successfully created user!'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'error' => $exception->getMessage(),
                'message' => 'Error: the user was not created.'
            ], 412);
        }
    }

    /**
     * Standard login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|max:191|email',
                'password' => 'required|string|max:191',
                'remember_me' => 'boolean'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $credentials = request(['email', 'password']);
            $credentials['active'] = 1;
            $credentials['deleted_at'] = null;
            if(!Auth::attempt($credentials)) {
                throw new \Exception();
            }
            $user = $request->user();
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            if(!$request->remember_me) {
                $token->expires_at = Carbon::now()->addWeeks(1);
            }
            $token->save();
            return response()->json([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
                'user' => User::where('id', $request->user()->id)->withCount(['comments', 'votes'])->first()
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: unauthorized access.'], 401);
        }
    }

    /**
     * Logout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out.'], 200);
    }

    /**
     * Display the logged user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function user(Request $request)
    {
        try {
            $user = User::where('id', Auth::id())->with(['player'])->withCount(['comments', 'votes'])->first();
            return response()->json($user, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the user was not found.'], 412);
        }
    }

    /**
     * Activate user registration.
     *
     * @param  $token
     * @return mixed
     */
    public function signupActivate($token)
    {
        try {
            $user = User::where('activation_token', $token)->firstOrFail();
            $user->fill([
                'active' => true,
                'activation_token' => null
            ]);
            $user->save();
            return $user;
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: activation token is invalid.'], 404);
        }
    }

    /**
     * Redirect the user to the facebook authentication page.
     *
     * @param  $provider
     * @return mixed
     */
    public function redirectToProvider($provider)
    {
        if($provider === 'twitter') {                           // Twitter tiene problemas con stateless, por lo que en este caso no se usa
            return Socialite::driver($provider)->redirect();
        } else {
            return Socialite::driver($provider)->stateless()->redirect();
        }
    }

    public function handleProviderCallback($provider)
    {
        try {
            if($provider === 'twitter') {
                $providerUser = Socialite::driver($provider)->user();
            } else {
                $providerUser = Socialite::driver($provider)->stateless()->user();
            }

            $response = $this->signupRrss($providerUser, $provider);
            if(isset($response->error)) {
                $query = env('APP_CLIENT_URL') . '?error='. $response->error;
            } else {
                $query = $response->query;
            }
            echo "<script>window.open('" . $query . "', '_self')</script>";
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: problem with ' . $provider . ' api.',
                'exception_msg' => $exception->getMessage(),
                'exception_trace' => $exception->getTrace()
            ], 412);
        }
    }

    /**
     * Registration through social networks.
     *
     * @param  $providerUser
     * @param  $providerName
     * @return object
     */
    private function signupRrss($providerUser, $providerName)
    {
        try {
            $userData = (object) [];
            $userData->email = $providerUser->getEmail();   // Twitter no entrega email salvo al definir e informa una Política de Privacidad existente para el software

            $nameProviderToken = $providerName . '_token';
            $userData->$nameProviderToken = $providerUser->getId();

            if($userData->email) {
                $user = User::where('email', $userData->email)->first();
                if($user) {
                    return $this->loginRrss($user, $nameProviderToken, $userData->$nameProviderToken);
                }
            } else {
                $userMeta = UserMeta::where([['key', $nameProviderToken], ['value', $userData->$nameProviderToken]])->first();
                if(isset($userMeta->user)) {
                    return $this->loginRrss($userMeta->user, $nameProviderToken, $userData->$nameProviderToken);
                }
            }

            /* Algunas RRSS no traen apellido, solo el nombre por lo que se construye el nombre y apellido */
            $fullName = explode(' ', $providerUser->getName());
            if(count($fullName) == 2) {
                $userData->name = $fullName[0];
                $userData->surname = $fullName[1];
            } else if(count($fullName) == 3) {
                $userData->name = $fullName[0];
                $userData->surname = $fullName[1] . ' ' .  $fullName[2];
            } else if(count($fullName) >= 4) {
                $userData->name = $fullName[0] . ' ' . $fullName[1];
                $userData->surname = $fullName[2] . ' ' . $fullName[3];
            } else if($providerName === 'clave_unica') {
                $userData->name = $providerUser->first_name ?: 'Name_user_' . Str::random(5);
                $userData->surname = $providerUser->last_name ?: 'Surname_user_' . Str::random(5);
            } else {
                $userData->name = $fullName[0] ?: 'Name_user_' . Str::random(5);
                $userData->surname = 'Surname_user_' . Str::random(5);
            }

            /* Creacion del objeto usuario para almacenar en la BD */
            $user = new User([
                'email' => $userData->email,
                'name' => $userData->name,
                'surname' => $userData->surname,
                'active' => true
            ]);
            DB::beginTransaction();
            $user->save();

            $user->fill([
                $nameProviderToken => $userData->$nameProviderToken
            ]);
            $user->save();
            if(!$this->createAndSaveAvatar($user)) {
                throw new \Exception();
            }

            $player = new Player();
            $player->user()->associate($user);
            $player->save();

            /* Se crea el token y query para enviar al navegador */
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $query = env('APP_CLIENT_URL') . '?access_token='. $tokenResult->accessToken;
            $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            DB::commit();
            return (object) [ 'query' => $query ];
        } catch (\Exception $exception) {
            DB::rollBack();
            return (object) [ 'error' => 'EXTERNAL_SERVICE_NOT_LINKED_ERROR' ];
        }
    }

    /**
     * @param $user
     * @return object
     */
    private function loginRrss($user, $nameProviderToken = null, $tokenValue = null)
    {
        try {
            if(!$user->$nameProviderToken) {
                $user->fill([
                    $nameProviderToken =>$tokenValue
                ]);
                $user->save();
            } else if($user->$nameProviderToken != $tokenValue) {
                throw new \Exception();
            }

            if(!$user->active || $user->deleted_at) {
                throw new \Exception();
            }

            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $query = env('APP_CLIENT_URL') . '?access_token='. $tokenResult->accessToken;
            $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            return (object) [ 'query' => $query ];
        } catch (\Exception $exception) {
            return (object) [ 'error' => 'UNAUTHORIZED_USER_ERROR' ];
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
                'email'                 => 'required|string|email|unique:users',
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

            $user = new User([
                'name'                  => $request->name,
                'surname'               => $request->surname,
                'email'                 => $request->email,
                'password'              => bcrypt($request->password),
                'activation_token'      => Uuid::generate()->string
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

            $player = new Player();
            $player->user()->associate($user);
            $player->save();

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
            $extension = 'svg';
            $originalName = 'avatar';
            $storedName = \Str::random(40);
            $storedBasename = "{$storedName}.{$extension}";
            $avatarFile = (new Avatar)->create($user->name)
                ->setBackground('#2a335d')
                ->setBorder(2, '#2a335d')
                ->setFontFamily('Helvetica, sans-serif')
                ->toSvg();
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
}
