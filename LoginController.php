<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Socialite;
use App\User;
use File;
use Session;
use App\AppUser;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\FreePostModel;
use DB;
use Carbon\Carbon; //add by ChenCW 20181108

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the OAuth Provider.
     *
     * @return Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from provider.  Check if the user already exists in our
     * database by looking up their provider_id in the database.
     * If the user exists, log them in. Otherwise, create a new user then log them in. After that 
     * redirect them to the authenticated users homepage.
     *
     * @return Response
     */
    public function handleProviderCallback($provider)
    {
        $user = Socialite::driver('facebook')->stateless(false)->user();

        if($user->email)
        {
            $userCreated = User::where('email', $user->email)->first();
            $authUser = $this->findOrCreateUser($user, $provider);
            if($authUser !== 0 && $authUser !== -1)
            {
                Auth::login($authUser, true);
                Session::put('facebookId', $authUser->id);
                if($userCreated)
                    return redirect('/');
                else
                    return redirect('userProfile');
            }
            else if($authUser == -1)
            {
                //return redirect(route('login'))->with('status','Account has been removed');
                return redirect(route('home'))->with('status','Account has been removed');
            }
            else
            {
                //return redirect(route('login'))->with('status','Your account is pending for approval');
                return redirect(route('home'))->with('status','Your account is pending for approval');
            }
        }
        else
        {
            Session::flash('status', trans('header.Email Required Facebook'));
            return redirect('register');
        }
    }

    /**
     * If a user has registered before using social auth, return the user
     * else, create a new user object.
     * @param  $user Socialite user object
     * @param $provider Social auth provider
     * @return  User
     */
    public function findOrCreateUser($user, $provider)
    {

        $userCreated = User::with('user')->where('email', $user->email)->first();
        $fileContents = file_get_contents($user->getAvatar());

        $uploadedFolder = public_path() . '/profile_image';

        File::put($uploadedFolder . '/profile_image' . $user->getId() . ".jpg", $fileContents);

        $picture ='/profile_image' . $user->getId() . ".jpg";

        if($userCreated)
        {
            if($userCreated->activated != 2)
            {
                $userCreated->provider = $provider;
                $userCreated->provider_id = $user->id;

                $userCreated->image=$picture;

                if($userCreated->type === 1)
                {
                    $userCreated->activation_code = '';
                    $userCreated->activated = 1;
                }
                
                if(is_null($userCreated->name))
                {
                    $userCreated->name = $user->name;
                }

                $appUser = AppUser::where('email' , $user->email)->first();

                if($appUser->account_activated === 0 && $appUser->account_type === 1)
                {
                    $freePosts = FreePostModel::where('id', 1)->first();
                    $appUser->free_post = is_null($freePosts)?0:$freePosts->free_post_no;
                }

                if(is_null($appUser->first_name) && is_null($appUser->last_name))
                {

                    $splitName = explode(' ', $userCreated->name, 2); // Restricts it to only 2 values, for names like Billy Bob Jones
                    $first_name = $splitName[0];
                    $last_name = !empty($splitName[1]) ? $splitName[1] : '';
                    
                    $appUser->first_name = $first_name;
                    $appUser->last_name = $last_name;
                }
                $appUser->facebook_id = $userCreated->provider_id;
                $appUser->user_status = 1;

                $userCreated->save();
                $appUser->save();

                if($userCreated->type !== 1 && $userCreated->activated === 0)
                {
                    return 0;
                }
            }
            else
            {
                return -1;
            }
        }
        else
        {
            $userCreated = User::create([
                'name'     => $user->name,
                'email'    => $user->email,
                'provider' => $provider,
                'provider_id' => $user->id,
                'activated' => 1,
                'activation_code' => '',
                'type' => 1,
                'image'=>$picture,
            ]);
           
            $splitName = explode(' ', $userCreated->name, 2); // Restricts it to only 2 values, for names like Billy Bob Jones
            $first_name = $splitName[0];
            $last_name = !empty($splitName[1]) ? $splitName[1] : '';

            $freePosts = FreePostModel::where('id', 1)->first();

            AppUser::create([
                'user_id' => $userCreated->id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $user->email,
                'user_status' => 1,
                'email_activated' => 1,
                'facebook_id' => $userCreated->provider_id,
                'free_post' => is_null($freePosts)?0:$freePosts->free_post_no,
                'account_type' => 1,
                'account_activated' => 1,
            ]);
        }

        return $userCreated;
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {

        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request) === 0)
        {
            return redirect(route('home'))->with('success','Confirmation Email has already been sent. Please check your inbox! <a href="/resendEmailUser/'.$request->email.'">Resend Confirmation Email</a>');
        }
        else if($this->attemptLogin($request) === -1)
        {
            return redirect(route('home'))->with('status','Account has been removed');
        }
        else if($this->attemptLogin($request) === -99)
        {
            return redirect(route('home'))->with('status','User is already logged in with these credentials');
        }
        else if($this->attemptLogin($request) !== false)
        {
            //$lastUrl = $_SERVER['HTTP_REFERER'];
            $lastUrl = Session::get('last_view_url');
            $this->redirectTo = $lastUrl;

            //add by ChenCW 20181108
            DB::table('users')->where('email', $request->email)->update(['last_login' => Carbon::now()]);
            return $this->sendLoginResponse($request);   
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $input = $request->all();
    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
        $validation = Validator::make($input, [
            'email' => 'required',
            'password' => 'required',
        ]);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => trans('auth.failed')];

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect('/?ref=login')->withInput($request->only($this->username(), 'remember'))->withErrors($errors);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $input = $request->all();
        
        $userCreated = AppUser::where('email', $input['email'])->first();
        
        $AppuserCreated = User::where('email', $input['email'])->exists();

        //add by ChenCW 20181107, temporary to disable session
        //$AppSession = DB::table('sessions')->where('user_id', $userCreated->user_id)->get();
       // if(count($AppSession) > 0){
        //    DB::table('sessions')->where('user_id', $userCreated->user_id)->delete();
        //}

        
        if($userCreated == '' || $AppuserCreated == '')
        {
            return $request->has('remember');
        }
        if($userCreated['email_activated'] === 0)
        {
            return 0;
        }
        else if($userCreated['account_activated'] === 2)
        {
            return -1;
        }
        else
        {
            $myUserId = $userCreated->user_id;
            $loginSessionUser = DB::table('sessions')->where('user_id', $myUserId)->get();

            if(count($loginSessionUser) > 0)
            {
                if($loginSessionUser[0]->ip_address === $_SERVER['REMOTE_ADDR'] && $loginSessionUser[0]->user_agent === $_SERVER['HTTP_USER_AGENT'])
                {
                    return $this->guard()->attempt(
                        $this->credentials($request), $request->has('remember')
                    );
                }
                else
                {
                    return -99;
                }
            }
            else
            {
                return $this->guard()->attempt(
                    $this->credentials($request), $request->has('remember')
                );
            }
        }
        
    }
}