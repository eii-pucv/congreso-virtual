<?php

namespace App\Http\Controllers;

use App\MemberOrg;
use App\LocationOrg;
use App\User;
use App\File;
use App\FileType;
use App\Notifications\SignupActivate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
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
            $user = $this->basicRegister($request);
            if(!$user) {
                throw new \Exception();
            }
            if(!$this->expertRegister($request, $user)) {
                throw new \Exception();
            }
            if(!$this->organizationRegister($request, $user)) {
                throw new \Exception();
            }
            if(!$this->createAndSaveAvatar($user)) {
                throw new \Exception();
            }

            $user->notify(new SignupActivate());

            DB::commit();
            return response()->json([
                'message' => 'Successfully created user!'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error: the user was not created.'], 412);
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
                'remember_me' => 'boolean',
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
            if($request->remember_me) {
                $token->expires_at = Carbon::now()->addWeeks(1); // <<<
            }
            $token->save();
            return response()->json([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
                'user' => User::where('id', $request->user()->id)->withCount(['comments', 'votes'])->first(),
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
            $user = User::where('id', Auth::id())->withCount(['comments', 'votes'])->first();
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
     * Registration through social networks.
     *
     * @param  $user
     * @return \Illuminate\Http\Response
     */
    private function signupRrss($user)
    {
        $request = json_decode($user);
        $providerTokens = ['facebook_token', 'twitter_token', 'google_token', 'clave_unica_token', 'github_token'];

        try {
            $facebookToken = null;
            $twitterToken = null;
            $googleToken = null;
            $claveUnicaToken = null;
            $githubToken = null;
            $bufferToken = null;

            /* Verificacion de tokens (si solo viene uno por objeto $user) */
            if(isset($request->facebook_token)) {
                if(isset($request->twitter_token) || isset($request->google_token)) {
                    throw new \Exception();
                }
                $facebookToken = $request->facebook_token;
                $bufferToken = 'facebook_token';
            } else if(isset($request->twitter_token)) {
                if(isset($request->google_token)) {
                    throw new \Exception();
                }
                $twitterToken = $request->twitter_token;
                $bufferToken = 'twitter_token';
            } else if(isset($request->google_token)) {
                $googleToken = $request->google_token;
                $bufferToken = 'google_token';
            } else if(isset($request->clave_unica_token)) {
                $claveUnicaToken = $request->clave_unica_token;
                $bufferToken = 'clave_unica_token';
            } else if(isset($request->github_token)) {
                $githubToken = $request->github_token;
                $bufferToken = 'github_token';
            } else {
                throw new \Exception();
            }

            /* Verificacion si existe usuario con el email. Si existe, intenta el login */
            $user = User::where('email', $request->email)->first();
            if(isset($user)) {                              // Si existe usuario con email igual al registrante
                foreach($providerTokens as $token) {        // se busca si el usuario tiene asignado alguna red social
                    if(isset($user->$token)) {
                        if($token === $bufferToken) {       // Si esta asignada, se intenta el login
                            return $this->loginRrss($user);
                        }
                    }
                }
            } else {
                /* Verificacion si existe token registrado. Si existe, intenta el login */
                foreach($providerTokens as $token) {  /* Igual que la verificacion de
                                                         arriba pero en caso de que no venga el email */
                    if(isset($request->$token)) {
                        $user = User::where($token, $request->$token)->first();
                    }
                    if(isset($user)) {
                        if(isset($user->$token)) {
                            return $this->loginRrss($user);
                        }
                    }
                }
            }

            /* Creacion del objeto usuario para almacenar en la BD */
            $user = new User([
                'email' => $request->email,
                'name' => $request->name,
                'surname' => $request->surname,
                'active' => true
            ]);
            DB::beginTransaction();
            $user->save();

            $user->fill([
                'facebook_token' => $facebookToken,
                'twitter_token' => $twitterToken,
                'google_token' => $googleToken,
                'clave_unica_token' => $claveUnicaToken,
                'github_token' => $githubToken
            ]);
            $user->save();
            if(!$this->createAndSaveAvatar($user)) {
                throw new \Exception();
            }

            /* Se crea el token y query para enviar al navegador */
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $query = env('APP_CLIENT_URL') . '?access_token='. $tokenResult->accessToken;
            $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            DB::commit();
            return response()->json([
                'message' => 'Successfully created user!',
                'query' => $query,
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Rrss_not_linked',
                'code' => 0,
            ], 402);
        }
    }

    /**
     * @param $user
     * @return \Illuminate\Http\Response
     */
    private function loginRrss($user)
    {
        $request = json_decode($user);
        $user = null;

        try {
            /* Verifica si el usuario logeado tiene una cuenta con alguna red social */
            if(isset($request->facebook_token)) {
                if(isset($request->twitter_token) ||
                    isset($request->google_token) ||
                    isset($request->clave_unica_token) ||
                    isset($request->github_token)) {
                        throw new \Exception();
                }
                $user = User::where('facebook_token', $request->facebook_token)->firstOrFail();
            } else if(isset($request->twitter_token)) {
                if(isset($request->google_token)) {
                    throw new \Exception();
                }
                $user = User::where('twitter_token', $request->twitter_token)->firstOrFail();
            } else if(isset($request->google_token)) {
                $user = User::where('google_token', $request->google_token)->firstOrFail();
            } else if(isset($request->clave_unica_token)) {
                $user = User::where('clave_unica_token', $request->clave_unica_token)->firstOrFail();
            } else if(isset($request->github_token)) {
                $user = User::where('github_token', $request->github_token)->firstOrFail();
            } else {
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

            return response()->json([
                'message' => 'Ingresado_correctamente',
                'query' => $query,
            ], 202);

        } catch (\Exception $exception) {
            return response()->json([
                'code' => 23000,
                'message' => 'Usuario_no_autorizado'
            ], 401);
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

    public function handleProviderCallback(Request $provider)
    {
        $userRrss = (object)[];

        try {
            if($provider->provider === 'twitter') {
                $user = Socialite::driver($provider->provider)->user();
            } else if($provider->provider === 'claveunica') {
                $user = Socialite::driver($provider->provider)->stateless()->user();
            } else {
                $user = Socialite::driver($provider->provider)->stateless()->user();
            }

            $userRrss->email = $user->getEmail();

            /* Algunas RRSS no traen apellido, solo el nombre por lo que se construye el nombre y apellido */
            $fullName = explode(' ', $user->getName());
            if(count($fullName) == 2) {
                $userRrss->name = $fullName[0];
                $userRrss->surname = $fullName[1];
            } else if(count($fullName) == 3) {
                $userRrss->name = $fullName[0];
                $userRrss->surname = $fullName[1] . ' ' .  $fullName[2];
            } else if(count($fullName) >= 4) {
                $userRrss->name = $fullName[0] . ' ' . $fullName[1];
                $userRrss->surname = $fullName[2] . ' ' . $fullName[3];
            } else if($provider->provider === 'claveunica') {
                $userRrss->name = $user->first_name ?:'';
                $userRrss->surname = $user->last_name ?:'';
            } else {
                $userRrss->name = $fullName[0]?:'';
                $userRrss->surname = '';
            }
            if($provider->provider === 'claveunica') {
                $userRrss->clave_unica_token = $user->getId();
            } else {
                $userRrss->{$provider->provider . '_token'} = $user->getId();
            }

            $user = json_encode($userRrss);
            $response = $this->signupRrss($user);
            $message = json_decode(json_encode($response))->original->message;

            if(isset(json_decode(json_encode($response))->original->code)) {
                $code = json_decode(json_encode($response))->original->code;
                $query = env('APP_CLIENT_URL') . '?error='. $code .'&message='. $message;
                echo "<script>window.open('".$query."','_self')</script>";
            } else {
                $query = json_decode(json_encode($response))->original->query;
                $message = json_decode(json_encode($response))->original->message;
                echo "<script>window.open('".$query."','_self')</script>";
            }
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: problem with ' . $provider->provider . ' api.' . "  " . $exception ], 412);
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
                'birthday'              => 'date_format:Y-m-d|before:'. date('Y-m-d') .'|nullable',
                'dni'                   => 'string|max:191|nullable',
                'pais'                  => 'string|max:191|nullable',
                'region'                => 'string|max:191|nullable',
                'comuna'                => 'string|max:191|nullable',
                'sector'                => 'integer|nullable',
                'nivel_educacional'     => 'integer|nullable',
                'genero'                => 'string|max:191|nullable',
                'actividad'             => 'string|max:191|nullable'
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
                'titulo_profesional'    => 'string|max:191|nullable',
                'estudios_adicionales'  => 'array|nullable',
                'anios_experiencia_laboral' => 'string|max:191|nullable',
                'areas_desempenio'      => 'array|nullable',
                'temas_trabajo'         => 'array|nullable'
            ]);
            if($validator->fails()) {
                throw new \Exception();
            }

            if(!$request->has('es_experto') || !$request->es_experto) {
                $fillArray = [
                    'es_experto'            => false
                ];
            } else {
                if(!$request->has('nivel_educacional') || !($request->nivel_educacional == 7 || $request->nivel_educacional == 8)) { // 7: Estudios univeristarios completos, 8: Estudios de postgrado
                    throw new \Exception();
                }

                $fillArray = [
                    'es_experto'            => true,
                    'titulo_profesional'    => $request->titulo_profesional,
                    'estudios_adicionales'  => json_encode($request->estudios_adicionales),
                    'anios_experiencia_laboral' => $request->anios_experiencia_laboral,
                    'areas_desempenio'      => json_encode($request->areas_desempenio),
                    'temas_trabajo'         => json_encode($request->temas_trabajo)
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
                    'email_org'             => 'string|max:191|nullable',
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
}
