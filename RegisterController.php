<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\AppUser;
use Illuminate\http\Request;
use Mail;
use Session;
use Auth;
use App\FreePostModel;
use App\NgoCategoriesModel;
use File;
use Storage;
use Illuminate\Support\Facades\Input;
use App\State;
use Illuminate\Support\Facades\log;

class RegisterController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Register Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users as well as their
	| validation and creation. By default this controller uses a trait to
	| provide this functionality without requiring any additional code.
	|
	*/

	use RegistersUsers;

	/**
	 * Where to redirect users after registration.
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
		$this->middleware('guest');
	}

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function validator(array $data)
	{
		if(isset($data['phone_number']))
		{
			if($data['phone_number'][0] == 0)
				$data['phone_number'][0] = 1;
			$data['phone_number'] = (int)$data['phone_number'];
		}
		if(isset($data['companyPhone']))
		{
			if($data['companyPhone'][0] == 0)
				$data['companyPhone'][0] = 1;
			$data['companyPhone'] = (int)$data['companyPhone'];
		}
		if(isset($data['companyFax']))
		{
			if($data['companyFax'][0] == 0)
				$data['companyFax'][0] = 1;
			$data['companyFax'] = (int)$data['companyFax'];
		}
		if(isset($data['postcode']))
		{
			if($data['postcode'][0] == 0)
				$data['postcode'][0] = 1;
			$data['postcode'] = (int)$data['postcode'];
		}

		$arr = [
			"companyName.required"=> "The company name field is required",
			"companyRegNumber.required"=> "The company registeration number is required",
			"companyAddress.required"=> "The company address is required",
			"companyPhone.required"=> "The company phone number field is required",
			"companyFax.required"=> "The company fax is required",
			"rosCertificate.required"=> "ROS/ROC certificate required",
			"rosCertificate.max"=> "ROS/ROC certificate not be greater than 10MB",
			"rosCertificate.mimes" => "Only allowed file type is rtf, pdf, png, bmp, jpeg",//20181025 Change by SHC 20180816_CP_002 - Start
			"rosCertificate2.max"=> "ROS/ROC certificate 2 not be greater than 10MB",
			"rosCertificate2.mimes" => "Only allowed file type is rtf, pdf, png, bmp, jpeg",
			"rosCertificate3.max"=> "ROS/ROC certificate 3 not be greater than 10MB",
			"rosCertificate3.mimes" => "Only allowed file type is rtf, pdf, png, bmp, jpeg",
			"phone_number.unique_space_check" => "The mobile phone number has already been taken",
			//20181025 Change by SHC 20180816_CP_002 - End
			"phone_number.required" => "The Mobile Phone Number field is required",
			"password_confirmation.same" => "The password confirmation does not match",
			"password_confirmation.required" => "The confirm password field is required",
			"password_confirmation.min" => "The confirm password must be at least 8 characters",
			"password.min" => "The password must be at least 8 characters",
			"password.different" => "The password must not be same as username",
			"password_confirmation.different" => "The confirm password must not be same as username",
			"phone_number.digits_between"=>"The mobile phone number must be between 10 to 11 digits",
			"companyPhone.digits_between"=>"The company phone number must be between 9 to 10 digits",
			"companyFax.digits_between"=>"The company fax number must be between 9 to 10 digits",
			"postcode.digits_between"=>"Post code must be between 4 to 5 digits",
			"ngoCategory.required"=>"NGO Category is required",
			"state.required"=>"State is required",
		];

		if($data['type'] != 1)
		{
			$arr["first_name.required"] = "The online store name field is required";
			$arr["first_name.unique"] = "The online store name has already been taken";
			$arr["first_name.regex"] = "The online store name format is invalid";
		}
		else
		{
			$arr["first_name.required"] = "User name is required";
			$arr["first_name.unique"] = "User name has already been taken";
			$arr["first_name.regex"] = "User name format is invalid";
		}

		$rules = [
			//20181030 Change by SHC 20180820_CP_003 
			//'email' => 'required|string|email|max:255|unique:users',
			'email' => 'required|string|email|max:255|unique_space_check:user',
			'password' => 'required|string|min:8|different:first_name',
			'password_confirmation' => 'required|string|min:8|different:first_name|same:password',
			'type' => 'required',
			//'phone_number' => 'required|integer|digits_between:10,11|unique:user',
			'phone_number' => 'required|integer|digits_between:10,11',
			//20181030 Change by SHC 20180820_CP_003 
		];

		if($data['type'] != 1)
		{
			if($data['type'] == 0)
			{
				$rules['ngoCategory'] = 'required';	
			}
			//20181025 Change by SHC 20180816_CP_001 
			//$rules['first_name'] = 'required|string|max:255|regex:/^[a-zA-Z-_]+$/u|unique:user';
			$rules['first_name'] = 'required|string|max:20|min:3|regex:/^[a-zA-Z-_0-9]+$/u|unique_space_check:user';
			$rules['companyName'] = 'required|string|max:255|regex:/^[a-zA-Z-_0-9 ]+$/u';
			//20181025 Change by SHC 20180816_CP_001 - End
			$rules['companyRegNumber'] = 'required';
			$rules['state'] = 'required';
			$rules['companyAddress'] = 'required|string';
			$rules['companyPhone'] = 'required|integer|digits_between:9,10';
			//20181025 Change by SHC 20180816_CP_001 
			//$rules['companyFax'] = 'required|integer|digits_between:9,10';
			if($data['companyFax']!=''){
				$rules['companyFax'] = 'integer|digits_between:9,10';
			}
			$rules['postcode'] = 'required|integer|digits_between:4,5';
			$rules['rosCertificate'] = 'required|max:10240|mimes:jpeg,bmp,png,application/vnd.openxmlformats-officedocument.wordprocessingml.document,rtf,pdf';
			//20181025 Change by SHC 20180816_CP_002 - Start			
			if (Input::hasFile('rosCertificate2')) {
					$rules['rosCertificate2'] = 'max:10240|mimes:jpeg,bmp,png,application/vnd.openxmlformats-officedocument.wordprocessingml.document,rtf,pdf';
			}
			if (Input::hasFile('rosCertificate3')) {
				$rules['rosCertificate3'] = 'max:10240|mimes:jpeg,bmp,png,application/vnd.openxmlformats-officedocument.wordprocessingml.document,rtf,pdf';
			}
			//20181025 Change by SHC 20180816_CP_002 - End
			$rules['password'] = 'required|string|min:8|different:first_name';
			$rules['password_confirmation'] = 'required|string|min:8|different:first_name|same:password';

			$arr['password.different'] = "The password must not be same as online store name";
			$arr['password_confirmation.different'] = "The confirm password must not be same as online store name";
		}
		else {
			if($data['type'] == 0)
			{
				$rules['ngoCategory'] = 'required';	
			}
			//20181025 Change by SHC 20180816_CP_001
			//$rules['first_name'] = 'required|string|max:255|regex:/^[a-zA-Z-_]+$/u|unique:user';
			$rules['first_name'] = 'required|string|max:20|min:3|regex:/^[a-zA-Z-_0-9]+$/u|unique_space_check:user';
		}
		return Validator::make($data, $rules, $arr);
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return \App\User
	 */
	protected function create(array $data)
	{
		if(!empty($data['first_name'])) {
			$name = $data['first_name'];
		}

		$userObject = User::create([
			'name' => $name,
			'email' => $data['email'],
			'password' => bcrypt($data['password']),
			'type' => $data['type'],
		]);

		$userId = $userObject->id;

		$insertedArray = [
			'user_id' => $userId,
			'first_name' => $name,
			'email' => $data['email'],
			'user_status' => 0,
			'email_activated' => 0,
			'account_type' => $data['type'],
			'phone_number' => $data['phone_number'],
		];

		if(isset($data['state']))
		{
			$insertedArray['address_state'] = $data['state'];
		}

		$insertedArray['address_country'] = "Malaysia";

		if($data['type'] != 1)
		{
			if(isset($data['city']))
				$insertedArray['address_city'] = $data['city'];
			if(isset($data['postcode']))
				$insertedArray['address_postcode'] = $data['postcode'];
			$insertedArray['company_name'] = $data['companyName'];
			$insertedArray['company_register_num'] = $data['companyRegNumber'];
			$insertedArray['address_1'] = $data['companyAddress'];
			$insertedArray['address_2'] = $data['companyAddress2'];
			$insertedArray['company_phone_num'] = $data['companyPhone'];
			$insertedArray['company_fax_num'] = $data['companyFax'];
			
			if($data['ngoDonation'][0] == "1")
			{
				$insertedArray['flag'] = 1;
			}

			if($data['type'] == 0)
			{
				$insertedArray['ngo_category'] = $data['ngoCategory'];
			}
		}

		AppUser::create($insertedArray);

		return $userObject;
	}

	public function showRegistrationForm()
	{
		$ngoCategoryData = NgoCategoriesModel::where('status', '1')->select(['id', 'category_name'])->get();

		if(count($ngoCategoryData->toArray())<=0)
		{
			$ngoCategories=[];
		}

		foreach($ngoCategoryData->toArray() as $cat)
		{
			$ngoCategories[$cat['id']] = $cat['category_name'];
		}

		$stateData = State::select(['id', 'name'])->get();

		foreach($stateData->toArray() as $statedata)
		{
			$location[] = $statedata;
		}

		return view('auth.register')->with('ngoCategories', $ngoCategories)->with('stateData', $location);
	}

	protected function register(Request $request)
	{
		$input = $request->all();

		$errorArray = [];

		if(isset($input['phone_number']))
		{
			$exp = ['(',')','-',' ','_'];
			$input['phone_number'] = str_replace($exp, '', $input['phone_number']);
		}

		if(isset($input['companyPhone']))
		{
			$exp = ['(',')','-',' ','_'];
			$input['companyPhone'] = str_replace($exp, '', $input['companyPhone']);
		}

		if(isset($input['companyFax']))
		{
			$exp = ['(',')','-',' ','_'];
			$input['companyFax'] = str_replace($exp, '', $input['companyFax']);
		}

		$validator = $this->validator($input);

		if($validator->passes())
		{
			$data = $this->create($input)->toArray();

			if($input['type'] != 1)
			{	
				
				//20181025 Change by SHC 20180816_CP_002 - Start
				$fileName = $this->addDocument($request,0, $input['first_name']);
				AppUser::where('user_id', $data['id'])->update(['company_certificate_url' => $fileName]);

				if (Input::hasFile('rosCertificate2')) {
					$fileName = $this->addDocument($request,1, $input['first_name']);
					AppUser::where('user_id', $data['id'])->update(['company_certificate_url2' => $fileName]);
				}
				if (Input::hasFile('rosCertificate3')) {
					$fileName = $this->addDocument($request,2, $input['first_name']);
					AppUser::where('user_id', $data['id'])->update(['company_certificate_url3' => $fileName]);
				}
				//20181025 Change by SHC 20180816_CP_002 - End
			}

			$data['activation_code'] = str_random(25);

			$user = User::find($data['id']);
			$user->activation_code = $data['activation_code'];

			$user->save();

			Mail::send('registeration_form.register_form' , ['data' => $data], function($message) use($data)
			{
				$message->to($data['email']);
				$message->cc(env('MAIL_BCC_ADDRESS', 'noreply@cilipadi.com.my'), env('MAIL_BCC_NAME', 'CiliPadi'));
				$message->from(env('MAIL_USERNAME', 'noreply@cilipadi.com.my'), env('MAIL_FROM_NAME', 'CiliPadi'));
				$message->subject('Registration Confirmation');
			});

			Auth::logout();

			//return view('register_done');
		}
		else
		{
			Session::flash('typeSelection', $input['type']);

			//$this->validator($request->all())->validate();

			//event(new Registered($user = $this->create($request->all())));

			//$this->guard()->login($user);
			$messages = $validator->messages();
			$errorArray[] = $messages->all();

			//return $this->registered($request, $user)?: redirect($this->redirectPath());
		}

		if(count($errorArray) > 0)
			return response()->json(['status' => false, 'registerationError' => $errorArray]);
		else			
			//20181025 Change by SHC 20180816_CP_001
			//return response()->json(['status' => true]);
			return response()->json(['status' => true, 'registerationError' => $errorArray]);
	}

	public function registerDone()
	{
		return view('register_done');
	}

	public function confirmation($token)
	{
		$user = User::where('activation_code' , $token)->first();

		if(!is_null($user))
		{
			$freePosts = FreePostModel::where('id', 1)->first();
			$appUser = AppUser::where('user_id' , $user->id)->first();

			if($user->type === 2 || $appUser->account_type === 2)//Corporate
			{
				$appUser->account_activated = 0;
				$appUser->account_type = 2;
				$user->type = 2;
			}
			else if ($user->type === 0 || $appUser->account_type === 0)//NGO
			{
				$appUser->account_activated = 0;
				$appUser->account_type = 0;
				$user->type = 0;
			}
			else//individual
			{
				$appUser->account_activated = 0;
				$appUser->account_type = 1;
				$user->type = 1;
			}

			$appUser->user_status = 1;
			$appUser->email_activated = 1;
			$user->activated = 0;
			$user->activation_code = '';
			$appUser->save();
			$user->save();

			$status = 'Account has been activated';
			
			if(Auth::loginUsingId($user->id))
			{
				Session::flash('browseMessage', $status);
				return redirect('home');
			}
		}
		Session::flash('status', 'Account cannot been activated');

		return redirect(route('home'));
	}

//20181025 Change by SHC 20180816_CP_002 - Start
	public function addDocument(Request $request,$ind,$name)
	{ 
		//log::info("Add Document ind: ".$ind);
		$fields = ['rosCertificate', 'rosCertificate2', 'rosCertificate3'];	
		//log::info("Add Document ".$ind." Client file name [".$_FILES[$fields[$ind]]['name']."] ErrNo:".$_FILES[$fields[$ind]]['error'] );
		$ext = Input::file($fields[$ind])->getClientOriginalExtension();
		$fileName = str_random(5)."-".date('his')."-".str_random(3).".".$ext;	
		//log::info("Add Document upload fileName: ".$fileName );

		$uploadedFolder = public_path() . '/getCert';
		//log::info("Add Document UploadFolder: ".$uploadedFolder );
		Input::file($fields[$ind])->move($uploadedFolder, $fileName);
		//log::info("Add Document uploaded: ".$uploadedFolder);
		if (Input::hasFile($fields[$ind])) {
			return $fileName;
		}
	}

	//20181025 Change by SHC 20180816_CP_002 - End

	public function resendEmailUser($email)
	{
		$data = User::where('email', $email)->first();

		if($data->count() > 0)
		{
			$dbUser = User::find($data['id']);

			$data['activation_code'] = str_random(25);

			$dbUser->activation_code = $data['activation_code'];

			$dbUser->save();

			Mail::send('registeration_form.register_form' , ['data' => $data], function($message) use($data)
			{
				$message->to($data['email']);
				$message->cc(env('MAIL_BCC_ADDRESS', 'noreply@cilipadi.com.my'), env('MAIL_BCC_NAME', 'CiliPadi'));
				$message->from(env('MAIL_USERNAME', 'noreply@cilipadi.com.my'), env('MAIL_FROM_NAME', 'CiliPadi'));
				$message->subject('Registration Confirmation');
			});

			return redirect(route('home'))->with('success','Confirmation Email has been sent. Please check your inbox!');
		}
		else
		{
			return redirect('home'); 
		}
	}
}