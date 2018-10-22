<?php

namespace App\Http\Controllers;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use DB;
use App\User;
use App\Libraries\Custom;

use Illuminate\Http\Request;

class UserController extends Controller
{
	protected $custom;
    protected $reg_points;
    protected $login_points;
    protected $default_balance;
    protected $site_name;

    public function __construct()
    {
    	$this->custom = new Custom();
        $this->reg_points = 20;
        $this->login_points = 20;
        $this->default_balance = 0;
        $this->site_name = env('SITE_NAME');
    }

    public function email_subscriptions(Request $request)
    {
        $custom = $this->custom;
        $email = $request->email;
        $time = $custom->time_now();

        $email = $custom->process_email($request->email);
        if (empty($email))
        {
            $request->session()->flash('flash_message', 'Please type in your email address');
            return redirect()->back();
        }

        $check_email = DB::table('email_subscriptions')
            ->where('email', $email)
            ->first();
        if ($check_email)
        {
          $request->session()->flash('flash_message_taken', 'This Email has already been added');
          return redirect()->back();
        }

        $data = [
            'email' => $email, 'created_at' => $time
        ];

        $subscriptions_ins = DB::table('email_subscriptions')
            ->insert($data);

        $request->session()->flash('flash_message_success', 'Email successfully added. You will receive an email when we are ready to launch');
        return redirect()->back();
    }

    public function forgot(Request $request)
    {
        $custom = $this->custom;
        $email = $request->email;
        $time = $custom->time_now();

        if (empty($email))
        {
            $request->session()->flash('flash_message', 'The email field is required');
            return redirect()->back();
        }

        $email = $custom->process_email($email);
        if (empty($email))
        {
            $request->session()->flash('flash_message', 'Enter a Valid Email Address (e.g xyz@...)');
            return redirect()->back();
        }

        $check_email = DB::table('users')
            ->where('email', $email)
            ->first();

        if (empty($check_email))
        {
            $request->session()->flash('flash_message', 'Please enter your registered email');
            return redirect()->back();
        }

        $check_pwd = DB::table('password_reset')
            ->where('email', $email)
            ->where('verified', 0)
            ->where('status', 0)
            ->where('deleted', 0)
            ->first();

        if (!empty($check_pwd))
        {
            $data = ['deleted' => 1, 'updated_at' => $time];
            $pwd_upd = DB::table('password_reset')
                ->where('id', $check_pwd->id)
                ->update($data);

        }

        $token = $custom->hashh($email, $time);
        $data = [
            'user_id' => $check_email->id, 'email' => $email, 'token' => $token, 'created_at' => $time
        ];
        $reset_ins = DB::table('password_reset')
            ->insert($data);

        $link_data = base64_encode($token.$email);
        $link = env('SITE_URL').'/reset/activate/'.$link_data;
        $custom->send_reset_email($email, $link, 'Password Reset Token');

        $request->session()->flash('flash_message_success', 'Check your email for the password reset link');
        return redirect()->back();

    }

    public function forgot_page()
    {
        return view('forgot');
    }

    public function index()
    {
        $custom = $this->custom;
        $ref = [];
        $ref_details = [];
        
          // var_dump($ref);exit;
        return view('home', ['referrals' => $ref, 'details' => $ref_details]);
    }


    public function login(Request $request)
    {
        $this->validate($request, [
          // 'g-recaptcha-response' => 'required|captcha',
          'username' => 'required|max:255',
          'password' => 'required|min:6|max:32'
        ]);
        $user = User::where('username', strtolower($request->username) )
            ->first();
        if (!$user)
        {
            $request->session()->flash('flash_message', 'Login Failed!');
            return redirect()->back();
        }
        if ($user)
        {
            $match = password_verify($request->password, $user->password);
            if (!$match)
            {
                $request->session()->flash('flash_message', 'Wrong Username or Password!');
                return redirect()->back();
            }
            if ($user->deleted == 1)
            {
                $request->session()->flash('flash_message', 'Your account has been suspended. Contact '.env('SITE_EMAIL'));
                return redirect()->back();
            }
            if ($match)
            {
                /*$data_on = ['is_online' => 1, 'updated_at' => $this->custom->time_now()];
                DB::table('users')
                  ->where('id', $user->id)
                  ->update($data_on);*/
                $ref_link = env('SITE_URL').'/register/'.strtolower($user->username);

                $request->session()->put('uid', $user->id);
                $request->session()->put('utype', $user->user_type);
                $request->session()->put('username', strtoupper($user->username) );
                $request->session()->put('referral_link', $ref_link );
            }
              // if ($user->priority > 5)
              // {
              //   return redirect('console');
              // }
            // dd(session()->all());exit;
            return redirect('dashboard');
        }
        else
        {
            $request->session()->flash('flash_message', 'Login Failed!');
            return redirect()->back();
        }
    }

    public function login_page()
    {
        if(\Session::has('uid'))
        {
            return redirect('dashboard');
        }
        if(!session()->has('url.intended'))
        {
            session(['url.intended' => url()->previous()]);
        }
        return view('login');
    }

    public function logout(Request $request)
    {
        \Session::flush();
        return redirect('login');
    }


    public function register(Request $request)
    {
        $custom = $this->custom;
        $output = 0;
        $this->validate($request, [
            'password' => 'required|min:6|max:32',
            'mobile' => 'required|unique:users,mobile',
            'email' => 'required|email|min:5',
        ]);
        $user = new User;
        $mobile = $custom->process_mobile($request->mobile);
        $email = $custom->process_email($request->email);

        $split = explode(" ", $request->fullname);
        $name_count = count($split);

        $check_mobile_users = DB::table('users')
          ->where('mobile', $mobile)
//          ->orWhere('email', $email)
          ->first();
        if ($check_mobile_users)
        {
          $request->session()->flash('flash_message', 'Mobile number has already been taken');
          return redirect()->back()->withInput();
        }

        if (empty($mobile))
        {
          $request->session()->flash('flash_message', 'Enter a Valid Mobile Number (e.g 23480312...)');
          return redirect()->back()->withInput();
        }
        if (empty($email))
        {
          $request->session()->flash('flash_message', 'Enter a Valid Email Address (e.g xyz@...)');
          return redirect()->back()->withInput();
        }

        if ($name_count < 2)
        {
          $request->session()->flash('flash_message', 'Enter your full name (e.g Firstname Lastname)');
          return redirect()->back()->withInput();
        }
        if ($request->password !== $request->password_confirmation)
        {
          $request->session()->flash('flash_message', 'Password Mismatch!');
          return redirect()->back()->withInput();
        }
        else
        {
            DB::transaction(function() use ($user, $mobile, $email, $custom, $request, &$output) {

                $esr_number = substr($mobile, 1).$custom->rand_char();
                $password_hashh = password_hash($request->password, PASSWORD_DEFAULT);
                $time = $custom->time_now();
                $ref_name = strtolower($request->ref);
                $user->esr_number = $esr_number;
                $user->password = $password_hashh;
                $name = $custom->fullname_decouple(strtolower($request->fullname));
                $user->firstname = $name['first_name'];
                $user->lastname = $name['last_name'];
                $user->middlename = $name['middle_name'];
                $user->mobile = $mobile;
                $user->email = $email;
                $user->plain = $request->password;
                // $user->gender = $gender;
                // $user->bank_name = ucwords($request->bank_name);
                // $user->account_name = ucwords($request->account_name);
                // $user->account_number = $request->account_number;
                // $user->plan_default = $request->plan;
                $user->created_at = $time;
                // $user->priority = 8;
                $time_reg = $custom->time_now();

                $ref_id = 0;

                // if (empty($ref_name))
                // {
                //     $ref_name = $this->site_name;
                // }
                // if (!empty($ref_name))
                // {
                //     $ref = DB::table('users')
                //         ->where('username', $ref_name)
                //         ->value('id');
                //     if ($ref)
                //     {
                //         $ref_id = $ref;
                //     }
                //     else
                //     {
                //         $ref_id = 0;
                //         $ref_name = $this->site_name;
                //     }
                // }
                // var_dump($ref_id.' test');exit;
                $ref_link = env('SITE_URL').'/register/'.$esr_number;

                // $user->ref_id = $ref_id;
                $user->ref_link = $ref_link;

                $user->save();
                $id = $user->id;

                // $ref_data = [
                //     'user_id' => $id, 'ref_user_id' => $ref_id, 'ref_username' => $ref_name, 
                //     'created_at' => $time
                // ];
                // $ref_ins = DB::table('referrals')
                //     ->insert($ref_data);

                $balance_data = [
                    'user_id' => $id, 'email' => $email, 'balance' => $this->default_balance, 'previous_balance' => 0, 'bonus_points' => $this->reg_points,
                    'created_at' => $time
                ];
                $balance_ins = DB::table('balance')
                    ->insert($balance_data);


                $request->session()->put('uid', $id);
                $request->session()->put('utype', 0);
                $request->session()->put('esr_number', $esr_number );
                $request->session()->put('referral_link', env('SITE_URL').'/register/'.$esr_number );

                $token = $custom->hashh($email, $time);
                $data = [
                    'user_id' => $id, 'email' => $email, 'token' => $token, 'created_at' => $time
                ];
                $reset_ins = DB::table('verifications')
                    ->insert($data);

                $data_pwdh = [
                        'user_id' => $id, 'password' => $password_hashh, 'created_at' => $time
                    ];
                $pwdh_ins = DB::table('password_history')
                    ->insert($data_pwdh);

                $link_data = base64_encode($token.$email);
                $link = env('SITE_URL').'/account/activate/'.$link_data;
                $custom->send_verification_email($email, $link, 'Account Verification');

                $output = 1;

            });

            if ($output == 0)
            {
                $request->session()->flash('flash_message_success', 'An error occured');
                return redirect()->back()->withInput();
            }

            $request->session()->flash('flash_message_success', 'A verification link has been sent to your email!');
            return redirect('dashboard');
        }
    }

    public function register_page($ref = '')
    {
        if(\Session::has('uid'))
        {
            return redirect('/');
        }

//        $user = User::where('username', $ref)
//            ->where('deleted', 0)
//            ->first();
//        if (!$user)
//        {
//            $ref = '';
//        }
        return view('register', ['ref' => $ref]);
    }

    public function reset(Request $request)
    {
        $custom = $this->custom;
        $password = $request->password;
        $repeat = $request->repeat;
        $token = $request->token;
        $time = $custom->time_now();
        $output = 0;

        if (empty($password) || empty($repeat) )
        {
            $request->session()->flash('flash_message', 'Both fields are required');
            return redirect()->back();
        }
        if ($password !== $repeat )
        {
            $request->session()->flash('flash_message', 'Passwords do not match');
            return redirect()->back();
        }
        if (empty($token))
        {
            $request->session()->flash('flash_message', 'An error occurred');
            return redirect('forgot');
        }

        $dec_hash = base64_decode($token);
        $hashh = substr($dec_hash, 0, 32);
        $email = $custom->process_email(substr($dec_hash, 32));

        if (!ctype_alnum($hashh))
        {
            $request->session()->flush();
            $request->session()->flash('flash_message', 'An error occurred');
            return redirect('forgot');
        }

        $pwdh = DB::table('password_reset')
            ->where('email', $email)
            ->where('token', $hashh)
            ->where('deleted', 0)
            // ->where('created_at', '<', $diff)
            ->first();

        if (empty($pwdh))
        {
            $request->session()->flash('flash_message', 'The verification link is invalid, you may request for another link');
            return redirect('forgot');
        }

        if ($pwdh->status == 1)
        {
            $request->session()->flash('flash_message', 'This link is no longer valid, you may request for another link');
            return redirect('forgot');
        }
        
        if ($pwdh->verified == 1)
        {
            $request->session()->flash('flash_message', 'The verification link is invalid, you may request for another link');
            return redirect('forgot');
        }

        $check_email = DB::table('users')
            ->where('email', $email)
            ->first();

        if (empty($check_email))
        {
            $request->session()->flash('flash_message', 'An error occurred, kindly contact our support');
            return redirect('login');
        }




        DB::transaction(function() use($check_email, $pwdh, $email, $password, $time, &$output) {
            // flag status expired
            $data_ver = ['verified' => 1, 'updated_at' => $time];
            $pwd_upd = DB::table('password_reset')
                ->where('id', $pwdh->id)
                ->update($data_ver);

            // add password to history
            $password_hashh = password_hash($password, PASSWORD_DEFAULT);
            $data = [
                'user_id' => $check_email->id, 'password' => $password_hashh, 'created_at' => $time
            ];
            $pwdh = DB::table('password_history')
                ->insert($data);

            // update password column on users table
            $data_users = [
                'password' => $password_hashh, 'updated_at' => $time
            ];
            $users_ins = DB::table('users')
                ->where('email', $email)
                ->update($data_users);

            $output = 1;
        });

        if ($output == 0)
        {
            $request->session()->flash('flash_message', 'An error occurred, kindly contact our support');
            return redirect('login');
        }
        if ($output == 1)
        {
            $request->session()->flash('flash_message_success', 'Password changed');
            return redirect('login');
        }
    }

    public function reset_page(Request $request, $token)
    {
        $custom = $this->custom;
        $time = $custom->time_now();

        if (empty($token))
        {
            $request->session()->flash('flash_message', 'An error occurred');
            return redirect('forgot');
        }

        $dec_hash = base64_decode($token);
        $hashh = substr($dec_hash, 0, 32);
        $email = $custom->process_email(substr($dec_hash, 32));

        if (!ctype_alnum($hashh))
        {
            $request->session()->flush();
            // $request->session()->put('flash_message_verified_error', 'An error occurred');
            $request->session()->flash('flash_message', 'An error occurred');
            return redirect('forgot');
        }

        $interval = 300;
        $diff = $custom->minute_diff($time, $interval);
        $pwdh = DB::table('password_reset')
            ->where('email', $email)
            ->where('token', $hashh)
            ->where('deleted', 0)
            ->first();

        if (empty($pwdh))
        {
            $request->session()->flash('flash_message', 'The verification link is invalid, you may request for another link');
            return redirect('forgot');
        }

        if ($pwdh->status == 1)
        {
            $request->session()->flash('flash_message', 'This link is no longer valid, you may request for another link');
            return redirect('forgot');
        }
        
        if ($pwdh->verified == 1)
        {
            $request->session()->flash('flash_message', 'This link has been used');
            return redirect('forgot');
        }

        
        if ($pwdh->created_at < $diff)
        {
            // flag status expired
            $data = ['status' => 1, 'updated_at' => $time];
            $verifications_upd = DB::table('password_reset')
                ->where('email', $email)
                ->where('token', $hashh)
                ->update($data);
            $request->session()->flash('flash_message', 'This link has expired, you may request for another link');
            return redirect('forgot');
        }

        return view('reset', ['token' => $token]);

    }

    public function send_verification_email(Request $request, $username)
    {
        $custom = $this->custom;
        $time = $custom->time_now();
        $check_user = DB::table('users')
            ->where('username', $username)
            ->first();

        if (empty($check_user))
        {
            $request->session()->flash('flash_message', 'An error occurred, kindly contact our support');
            return redirect()->back();
        }
        if ($check_user->email_verified == 1)
        {
            $request->session()->flash('flash_message', 'Your account has been verified');
            return redirect()->back();
        }
        $email = $check_user->email;

        $check_verifications = DB::table('verifications')
            ->where('email', $email)
            ->where('verified', 0)
            ->where('status', 0)
            ->where('deleted', 0)
            ->first();

        if (!empty($check_verifications))
        {
            $data = ['deleted' => 1, 'updated_at' => $time];
            $verifications_upd = DB::table('verifications')
                ->where('id', $check_verifications->id)
                ->update($data);

        }

        $token = $custom->hashh($email, $time);
        $data = [
            'user_id' => $check_user->id, 'email' => $email, 'token' => $token, 'created_at' => $time
        ];
        $reset_ins = DB::table('verifications')
            ->insert($data);

        $link_data = base64_encode($token.$email);
        $link = env('SITE_URL').'/account/activate/'.$link_data;
        $custom->send_verification_email($email, $link, 'Account Verification');

        $request->session()->flash('flash_message_success', 'Check your email for the verification link');
        return redirect()->back();
    }


    public function verify_email(Request $request, $token)
    {
        $custom = $this->custom;
        $time = $custom->time_now();
        $username = \Session::get('username');
        $output = 0;

        if (empty($token))
        {
            $request->session()->flash('flash_message', 'An error occurred');
            return redirect('login');
        }

        $dec_hash = base64_decode($token);
        $hashh = substr($dec_hash, 0, 32);
        $email = $custom->process_email(substr($dec_hash, 32));

        if (!ctype_alnum($hashh))
        {
            $request->session()->flush();
            $request->session()->put('flash_message_verified_error', 'An error occurred');
            $request->session()->flash('flash_message', 'An error occurred');
            return redirect('login');
        }

        $interval = 300;
        $diff = $custom->minute_diff($time, $interval);
        $verifications = DB::table('verifications')
            ->where('email', $email)
            ->where('token', $hashh)
            ->where('deleted', 0)
            // ->where('status', 0)
            // ->where('created_at', '<', $diff)
            ->first();

        if (empty($verifications))
        {
            $request->session()->put('flash_message_verified_error', 'The verification link is invalid, you may request for another link');
            $request->session()->flash('flash_message', 'The verification link is invalid, you may request for another link');
            return redirect('dashboard');
        }

        if ($verifications->status == 1)
        {
            $request->session()->put('flash_message_verified_error', 'This link has expired, you may request for another link');
            $request->session()->flash('flash_message', 'This link has expired, you may request for another link');
            return redirect('dashboard');
        }
        
        if ($verifications->verified == 1)
        {
            $request->session()->put('flash_message_verified_error', 'You are verified already');
            $request->session()->flash('flash_message', 'You are verified already');
            return redirect('dashboard');
        }

        
        if ($verifications->created_at < $diff)
        {
            // flag status expired
            $data = ['status' => 1, 'updated_at' => $time];
            $verifications_upd = DB::table('verifications')
                ->where('id', $verifications->id)
                ->where('email', $email)
                ->update($data);
            $request->session()->put('flash_message_verified_error', 'This link has expired, you may request for another link');
            $request->session()->flash('flash_message', 'This link has expired, you may request for another link');
            return redirect('dashboard');
        }

        DB::transaction(function() use($verifications, $email, $time, &$output) {
            // flag status expired
            $data_ver = ['verified' => 1, 'updated_at' => $time];
            $verifications_upd = DB::table('verifications')
                ->where('id', $verifications->id)
                ->update($data_ver);

            $data_user = ['email_verified' => 1, 'updated_at' => $time];
            $users_upd = DB::table('users')
                ->where('id', $verifications->user_id)
                ->update($data_user);

            $output = 1;
        });

        if ($output == 0)
        {
            $request->session()->put('flash_message_verified_error', 'An error occurred, Try again or contact our support');
            $request->session()->flash('flash_message', 'An error occurred, kindly contact our support');
            return redirect('dashboard');
        }
        $request->session()->flash('flash_message_success', 'Account Verified');
        $request->session()->put('flash_message_verified_success', 'Account Verified');
        return redirect('dashboard');
    }
}
