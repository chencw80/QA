<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use LaravelAcl\Authentication\Classes\Statistics\UserStatistics;
use Illuminate\Support\MessageBag;
use LaravelAcl\Authentication\Exceptions\PermissionException;
use LaravelAcl\Authentication\Exceptions\ProfileNotFoundException;
use LaravelAcl\Authentication\Helpers\DbHelper;
use LaravelAcl\Authentication\Models\UserProfile;
use LaravelAcl\Authentication\Presenters\UserPresenter;
use LaravelAcl\Authentication\Services\UserProfileService;
use LaravelAcl\Authentication\Validators\UserProfileAvatarValidator;
use LaravelAcl\Library\Exceptions\NotFoundException;
// use LaravelAcl\Authentication\Models\User;
use LaravelAcl\Authentication\Helpers\FormHelper;
use LaravelAcl\Authentication\Exceptions\UserNotFoundException;
use App\UserValidator;
use LaravelAcl\Library\Exceptions\JacopoExceptionsInterface;
use LaravelAcl\Authentication\Validators\UserProfileValidator;
use View, Redirect, App, Config;
use LaravelAcl\Authentication\Interfaces\AuthenticateInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use LaravelAcl\Library\Form\FormModel;
use Illuminate\Support\Facades\Validator;
use Hash;

use Illuminate\Support\Facades\DB; //add by ChenCW 20181108

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void\

     */
    protected $user_repository;
    protected $user_validator;
    /**
     * @var \LaravelAcl\Authentication\Helpers\FormHelper
     */
    protected $form_helper;
    protected $profile_repository;
    protected $profile_validator;
    /**
     * @var use LaravelAcl\Authentication\Interfaces\AuthenticateInterface;
     */
    protected $auth;
    protected $register_service;
    protected $custom_profile_repository;
    public function __construct(UserValidator $v, FormHelper $fh, UserProfileValidator $vp, AuthenticateInterface $auth)
    {
        $this->user_repository = App::make('user_repository');
        $this->user_validator = $v;
        //@todo use IOC correctly with a factory and passing the correct parameters
        $this->f = new FormModel($this->user_validator, $this->user_repository);
        $this->form_helper = $fh;
        $this->profile_validator = $vp;
        $this->profile_repository = App::make('profile_repository');
        $this->auth = $auth;
        $this->register_service = App::make('register_service');
        $this->custom_profile_repository = App::make('custom_profile_repository');

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
    
    public function authenticate()
    {
        return view('AuthenticationError');
    }
    public function getList(Request $request)
    {
        //$users = new User(); Change by ChenCW20181108
        $users = DB::table('users')
            ->leftjoin('user', 'users.id', '=', 'user.user_id')
            ->select('users.*', 'user.user_id', 'user.account_activated', 'user.email_activated', 'user.first_name');

        if($request->email)
            $users = $users->where('users.email',$request->email);

        //Change by ChenCW20181108
        //if($request->first_name)
        //    $users = $users->where('first_name',$request->first_name);

        //if($request->activated)
        //    $users = $users->where('activated',$request->activated);
        if($request->name)
            $users = $users->where('users.name',$request->name);

        if($request->account_activated<>'')
        {
            if ($request->account_activated=='1')
            {
                $users = $users->where('user.account_activated','>',0);
            }
            else
            {
                $users = $users->where('user.account_activated', 0);
            }
        }

        if($request->banned)
            $users = $users->where('banned',$request->banned);

        if($request->order_by)
            $users = $users->orderBy($request->order_by, $request->ordering);
        else
            $users = $users->orderBy('user.email');

        $users = $users->paginate();

        return view('laravel-authentication-acl::admin.user.list',compact('users','request'));
    }

    public function postEditUser(Request $request)
    {
        $id = $request->get('id');

        DbHelper::startTransaction();
        try
        {
            if(!$request->get('editMode'))
            {
                $user = $this->f->process($request->all());
                $this->profile_repository->attachEmptyProfile($user);
                $id = $user->id;
            }
            else
            {
                $inputs = $request->except(['_token', '__to_hide_password_autocomplete', 'form_name', 'editMode', 'email', 'id']);
                if($inputs['password'] != null || $inputs['password_confirmation'] != null)
                {
                    $arr = [
                        "password_confirmation.same" => "The password confirmation does not match",
                        "password_confirmation.required" => "The confirm password field is required",
                        "password_confirmation.min" => "The confirm password must be at least 8 characters",
                        "password.min" => "The password must be at least 8 characters",
                    ];

                    $rules = [
                        'password' => 'required|string|min:8',
                        'password_confirmation' => 'required|string|min:8|same:password',
                    ];

                    $validation = Validator::make($inputs, $rules, $arr);

                    if($validation->passes())
                    {
                        $inputs['password'] = Hash::make($inputs['password']);
                        array_forget($inputs, 'password_confirmation');
                        $user = User::where('id', $id)->update($inputs);
                    }
                    else
                    {
                        return Redirect::route("users.edit", $id ? ["id" => $id] : [])->withInput()->withErrors($validation->messages());
                    }
                }
                else
                {
                    array_forget($inputs, 'password');
                    array_forget($inputs, 'password_confirmation');
                    $user = User::where('id', $id)->update($inputs);
                }
            }
        } catch(JacopoExceptionsInterface $e)
        {
            DbHelper::rollback();
            $errors = $this->f->getErrors();
            // passing the id incase fails editing an already existing item
            return Redirect::route("users.edit", $id ? ["id" => $id] : [])->withInput()->withErrors($errors);
        }

        DbHelper::commit();

        if(!$request->get('editMode'))
        {
            return Redirect::route('users.edit', ["id" => $id])
                       ->withMessage(Config::get('acl_messages.flash.success.user_create_success'));
        }
        else
        {
            return Redirect::route('users.edit', ["id" => $id])
                       ->withMessage(Config::get('acl_messages.flash.success.user_edit_success'));   
        }
    }

    public function editPermission(Request $request)
    {
        // prepare input
        $input = $request->all();
        $operation = $request->get('operation');
        $this->form_helper->prepareSentryPermissionInput($input, $operation);
        $id = $request->get('id');

        try
        {
            $obj = $this->user_repository->update($id, $input);
        } catch(JacopoExceptionsInterface $e)
        {
            return Redirect::route("users.edit")->withInput()
                           ->withErrors(new MessageBag(["permissions" => Config::get('acl_messages.flash.error.user_permission_not_found')]));
        }

        if($operation == 0)
            return Redirect::route('users.edit', ["id" => $obj->id])
                       ->withMessage(Config::get('acl_messages.flash.success.user_permission_delete_success'));
        else
            return Redirect::route('users.edit', ["id" => $obj->id])
                       ->withMessage(Config::get('acl_messages.flash.success.user_permission_add_success'));
    }
}