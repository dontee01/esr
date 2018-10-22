<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\User;
use App\Libraries\Custom;
use App\Libraries\Transactions;

use Paystack;

class ExploreController extends Controller
{
    protected $custom;
    protected $transactions;

    protected $site_name;
    
    protected $reg_points;
    protected $max_level;
    protected $minimum_withdrawal;
    protected $minimum_transfer;
    protected $hyperlinks_rows;
    public function __construct()
    {
        $this->middleware('login')->except('how_to');
    	$this->custom = new Custom();
        $this->transactions = new Transactions();

        $this->site_name = env('SITE_NAME');
        
        $this->reg_points = 20;
        $this->max_level = 7;
        $this->minimum_withdrawal = env('MINIMUM_WITHDRAWAL');
        $this->minimum_transfer = env('MINIMUM_TRANSFER');
        $this->hyperlinks_rows = env('HYPERLINKS_ROWS');
    }

    public function cash_out(Request $request)
    {
        $custom = $this->custom;
        $transactions = $this->transactions;
        $user_id = \Session::get('uid');
        $amount = $request->amount;
        $bank_name = ucwords($request->bank_name);
        $account_name = ucwords($request->account_name);
        $account_number = $request->account_number;
        $account_type = ucwords($request->account_type);
        $time = $custom->time_now();
        $output = 0;

        if (empty($amount) || empty($bank_name) || empty($account_name) || empty($account_number) || empty($account_type) )
        {
            \Session::flash('flash_message', 'All fields are required');
            return redirect()->back();
        }
        if (!ctype_digit($amount))
        {
            \Session::flash('flash_message', 'Enter a valid amount');
            return redirect()->back();
        }
        if (!ctype_digit($account_number) || strlen($account_number) !== 10)
        {
            \Session::flash('flash_message', 'Enter a valid account number');
            return redirect()->back();
        }

        if ($amount < $this->minimum_cashout)
        {
            \Session::flash('flash_message', 'The minimum cash out is N'.$this->minimum_cashout.'. Please Try again with a higher amount');
            return redirect()->back();
        }

        $balance = $transactions->balance($user_id);
        if ($balance->balance < $this->minimum_cashout)
        {
            \Session::flash('flash_message', 'Your account balance is too low. The minimum cash out is N'.$this->minimum_cashout);
            return redirect()->back();
        }
        if (($balance->balance - $amount) < 0)
        {
            \Session::flash('flash_message', 'Your balance is insufficient for this request');
            return redirect()->back();
        }

        $data = [
            'user_id' => $user_id, 'amount' => $amount, 'bank_name' => $bank_name, 'account_name' => $account_name, 'account_number' => $account_number, 'account_type' => $account_type, 'created_at' => $time
        ];

        DB::transaction(function() use($data, $user_id, $amount, $balance, $time, $transactions, &$output) {
            // bill user
            $bill = $transactions->bill($user_id, $amount, $balance->balance, $time);

            // add to transactions
            $description = 'Cash out request';
            $data_trans = [
                'user_id' => $user_id, 'amount' => $amount, 'previous_balance' => $balance->balance, 'description' => $description, 'created_at' => $time
            ];
            $transactions_ins = DB::table('transactions')
                ->insert($data_trans);

            // add request to withdrawals tb
            $withdrawals_ins = DB::table('withdrawals')
                ->insert($data);

            $output = 1;

        });

        if($output == 0)
        {
            \Session::flash('flash_message', 'An error occurred');
            return redirect()->back();
        }
        
        \Session::flash('flash_message_success', 'Request submitted');
        return redirect()->back();
    }

    public function change_password(Request $request)
    {
        $custom = $this->custom;
        $transactions = $this->transactions;
        $old_password = $request->old_password;
        $new_password = $request->password;
        $repeat = $request->repeat;
        $user_id = \Session::get('uid');
        $time = $custom->time_now();
        $output = 0;

        $this->validate($request, [
            'password' => 'required|min:6|max:32'
        ]);

        if (empty($old_password) || empty($new_password) || empty($repeat) )
        {
            \Session::flash('flash_message', 'All fields are required');
            return redirect()->back();
        }

        if ($new_password !== $repeat )
        {
            \Session::flash('flash_message', 'Password mismatched');
            return redirect()->back();
        }
        $user =DB::table('users')
            ->where('id', $user_id)
            ->first();
        if (!$user)
        {
            \Session::flash('flash_message', 'An error occurred');
            return redirect()->back();
        }
        if ($user)
        {
            $match = password_verify($old_password, $user->password);
            if (!$match)
            {
                \Session::flash('flash_message', 'Enter your correct password!');
                return redirect()->back();
            }
            if ($match)
            {
                $password_hashh = password_hash($new_password, PASSWORD_DEFAULT);
                DB::transaction(function() use($user, $user_id, $password_hashh, $custom, $transactions, $time, &$output) {
                    
                    $balance = $transactions->balance($user_id);
                    $data = [
                        'user_id' => $user_id, 'password' => $password_hashh, 'created_at' => $time
                    ];
                    $pwdh_ins = DB::table('password_history')
                        ->insert($data);

                    $data_user = ['password' => $password_hashh, 'updated_at' => $time];
                    $user_upd = DB::table('users')
                        ->where('id', $user_id)
                        ->update($data_user);
                    
                    
                    // add to transaction logs
                    $description_trans = 'Password Changed';
                    $data_trans = [
                        'user_id' => $user_id, 'previous_balance' => $balance->balance,
                        'description' => $description_trans, 'created_at' => $time
                    ];
                    $transactions_ins = DB::table('transaction_logs')
                        ->insert($data_trans);

                    $output = 1;

                    $message = 'Your '.$this->site_name.' account password was successfully updated';
                    $custom->send_generic_email($user->email, $message, 'Account Update');
                });

                if ($output == 0)
                {
                    \Session::flash('flash_message', 'An error occurred');
                    return redirect()->back();
                }

                \Session::flash('flash_message_success', 'Password successfully updated');
                return redirect()->back();
                
            }
        }

    }

    public function change_pin(Request $request)
    {
        $custom = $this->custom;
        $transactions = $this->transactions;
        $old_pin = $request->old_pin;
        $new_pin = $request->pin;
        $repeat = $request->repeat;
        $user_id = \Session::get('uid');
        $time = $custom->time_now();
        $output = 0;

        $this->validate($request, [
            'pin' => 'required|min:4|max:4'
        ]);

        if (empty($old_pin) || empty($new_pin) || empty($repeat) )
        {
            \Session::flash('flash_message', 'All fields are required');
            return redirect()->back();
        }

        if ($new_pin !== $repeat )
        {
            \Session::flash('flash_message', 'Pin mismatched');
            return redirect()->back();
        }
        $user =DB::table('users')
            ->where('id', $user_id)
            ->first();
        if (!$user)
        {
            \Session::flash('flash_message', 'An error occurred');
            return redirect()->back();
        }
        if ($user)
        {
            $match = password_verify($old_pin, $user->transaction_pin);
            if (!$match)
            {
                \Session::flash('flash_message', 'Enter your correct pin!');
                return redirect()->back();
            }
            if ($match)
            {
                $pin_hashh = password_hash($new_pin, PASSWORD_DEFAULT);
                DB::transaction(function() use($user, $user_id, $pin_hashh, $custom, $transactions, $time, &$output) {
                    
                    $balance = $transactions->balance($user_id);
                    $data = [
                        'user_id' => $user_id, 'pin' => $pin_hashh, 'created_at' => $time
                    ];
                    $pwdh_ins = DB::table('pin_history')
                        ->insert($data);

                    $data_user = ['transaction_pin' => $pin_hashh, 'updated_at' => $time];
                    $user_upd = DB::table('users')
                        ->where('id', $user_id)
                        ->update($data_user);
                    
                    
                    // add to transaction logs
                    $description_trans = 'Pin Changed';
                    $data_trans = [
                        'user_id' => $user_id, 'previous_balance' => $balance->balance,
                        'description' => $description_trans, 'created_at' => $time
                    ];
                    $transactions_ins = DB::table('transaction_logs')
                        ->insert($data_trans);

                    $output = 1;

                    $message = 'Your '.$this->site_name.' transaction pin was successfully updated';
                    $custom->send_generic_email($user->email, $message, 'Account Update');
                });

                if ($output == 0)
                {
                    \Session::flash('flash_message', 'An error occurred');
                    return redirect()->back();
                }

                \Session::flash('flash_message_success', 'Transaction pin successfully updated');
                return redirect()->back();
                
            }
        }

    }

    public function change_password_page()
    {
        return view('change-password');
    }

    public function dashboard()
    {
    	$custom = $this->custom;
    	// $matrix = $this->matrix;
    	$user_id = \Session::get('uid');


        $results = [];
        
        $countries = DB::table('countries')
                ->get();

        $profile = DB::table('users')
            ->join('balance', 'users.id', 'balance.user_id')
            ->where('users.id', \Session::get('uid'))
            ->where('users.deleted', 0)
            ->select('users.firstname as firstname', 'users.lastname as lastname', 'users.esr_number', 'users.user_type', 'users.email', 'balance.balance')
            ->first();
        
        $logs = DB::table('transaction_logs')
                ->where('user_id', $user_id)
                ->where('deleted', 0)
                ->get(['amount']);
        
//        $piped = $collection->pipe(function ($collection) {
//            return $collection->sum();
//        });
        
//        $all = $logs->whereIn('created_at', [$custom->yesterday(), $custom->tomorrow()]);
//        var_dump($all);exit;
            // var_dump(\Session::get('uid'));exit;

		return view('dashboard', ['results' => $results, 'profile' => $profile]);
    }

    public function how_to()
    {
        return view('how-to');
    }

    public function income_referrals()
    {
        $custom = $this->custom;
        $time = $custom->time_now();
        $user_id = \Session::get('uid');

        $refs = DB::table('referrals')
            ->join('users', 'referrals.user_id', '=', 'users.id')
            ->where('referrals.ref_user_id', $user_id)
            ->where('referrals.deleted', 0)
            ->select('referrals.created_at as date', 'users.username as ref_username', 
                    'users.firstname', 'users.lastname')
            ->paginate($this->hyperlinks_rows);
        
        $commission = 10;
        $remark = 'Referral Bonus';
        
        return view('referral-income', ['results' => $refs, 'commission' => $commission, 'remark' => $remark]);
    }

    public function get_states(Request $request)
    {
        $custom = $this->custom;
        $country_id = $request->country_id;

        $states = DB::table('states')
            ->where('country_id', $country_id)
            ->get();
// var_dump($states);exit;
        return $states;
    }

    public function profile()
    {
        $custom = $this->custom;
        $transactions = $this->transactions;
        $user_id = \Session::get('uid');


        $balance = DB::table('balance')
            ->where('user_id', $user_id)
            ->where('deleted', 0)
            ->first();

        $profile = DB::table('users')
            ->join('balance', 'users.id', 'balance.user_id')
            ->where('users.id', $user_id)
            ->where('users.deleted', 0)
            ->select('users.firstname as firstname', 'users.lastname as lastname', 'users.esr_number', 
                    'users.email', 'users.mobile', 'users.gender', 'users.bank_name', 
                    'users.account_name', 'users.account_number', 'users.country', 'users.state', 'balance.balance')
            ->first();

        if (empty($balance) || empty($profile))
        {
            \Session::flash('flash_message', 'An error occurred, kindly contact our support');
            return redirect()->back();
        }

        // $profile = collect($user)->except('password');
        // var_dump((object)$profile->toArray());exit;
        return view('profile', ['balance' => $balance, 'profile' => $profile]);
    }
    
    public function profile_update(Request $request)
    {
        $custom = $this->custom;
        $transactions = $this->transactions;
        $fullname = $request->fullname;
        $email = $request->email;
        $mobile = $request->mobile;
        $time = $custom->time_now();
        $user_id = \Session::get('uid');
        
        $mobile = $custom->process_mobile($request->mobile);
        $email = $custom->process_email($request->email);

        $check_mobile_users = DB::table('users')
            ->where('mobile', $mobile)
            ->first();
        if ($check_mobile_users && $check_mobile_users->id != $user_id)
        {
          $request->session()->flash('flash_message', 'Mobile number has already been taken');
          return redirect()->back()->withInput();
        }
        
        $check_email_users = DB::table('users')
            ->orWhere('email', $email)
            ->first();
        
        if ($check_email_users && $check_email_users->id != $user_id)
        {
          $request->session()->flash('flash_message', 'Email has already been taken');
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
        
        $split = explode(" ", $request->fullname);
        $name_count = count($split);
        
        if ($name_count < 2)
        {
          $request->session()->flash('flash_message', 'Enter your full name (e.g Firstname Lastname)');
          return redirect()->back()->withInput();
        }
        
        $name = $custom->fullname_decouple(strtolower($request->fullname));
        
        DB::transaction(function() use($user_id, $name, $email, $mobile, $custom, $transactions, $time, &$output) {

            $balance = $transactions->balance($user_id);
            
            $firstname = $name['first_name'];
            $lastname = $name['last_name'];

            $data_user = [
                'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 
                'mobile' => $mobile, 'updated_at' => $time
            ];
            $user_upd = DB::table('users')
                    ->where('id', $user_id)
                    ->update($data_user);

            // add to transaction logs
            $description_trans = 'Profile Updated';
            $data_trans = [
                'user_id' => $user_id, 'previous_balance' => $balance->balance,
                'description' => $description_trans, 'created_at' => $time
            ];
            $transactions_ins = DB::table('transaction_logs')
                ->insert($data_trans);

            $output = 1;

//            $message = 'Your '.$this->site_name.' transaction pin was successfully updated';
//            $custom->send_generic_email($user->email, $message, 'Account Update');
        });

        if ($output == 0)
        {
            \Session::flash('flash_message', 'An error occurred');
            return redirect()->back();
        }

        \Session::flash('flash_message_success', 'Profile successfully updated');
        return redirect()->back();
        
    }

    public function referrals(Request $request)
    {
        $custom = $this->custom;
        $time = $custom->time_now();
        $user_id = \Session::get('uid');

        $refs = DB::table('referrals')
            ->join('users', 'referrals.user_id', '=', 'users.id')
            ->where('referrals.ref_user_id', $user_id)
            ->where('referrals.deleted', 0)
            ->select('referrals.created_at as date', 'users.username as ref_username')
            ->paginate($this->hyperlinks_rows);
        
        $total = DB::table('referrals')
                ->where('ref_user_id', $user_id)
                ->where('deleted', 0)
                ->count();
        
        return view('referrals', ['results' => $refs, 'total' => $total]);
    }

    public function topup_page()
    {
    	$custom = $this->custom;
    	$user_id = \Session::get('uid');
    	$email = $custom->get_user_email($user_id, 'users');
    	return view('recharge', ['email' => $email]);
    }

    public function topup(Request $request)
    {
    	$custom = $this->custom;
    	$user_id = \Session::get('uid');
    	$email = $request->email;
    	$amount = $request->amount;
    	$reference = $request->reference;
    	$key = $request->key;
        
        $amount_min = 18000;

        if (empty($amount) )
        {
            \Session::flash('flash_message', 'Enter an amount');
            return redirect()->back();
        }
        if (!ctype_digit($amount) )
        {
            \Session::flash('flash_message', 'Please enter a valid amount');
            return redirect()->back();
        }
        if ($amount < $amount_min )
        {
            \Session::flash('flash_message', 'Amount cannot be less than '.$amount_min);
            return redirect()->back()->withInput();
        }

        $email = $custom->process_email($email);

        if (empty($email) || empty($reference) || empty($key) )
        {
            \Session::flash('flash_message', 'An error occured');
            return redirect()->back();
        }
        // to be stored on payment tb
        $original = $amount;
        // pass gateway fee to user
        $amount = (($amount * 0.019) + 10 + $amount) * 100;

        return view('funding-confirm', ['email' => $email, 'amount' => $amount, 
            'original' => $original, 'reference' => $reference, 'key' => $key]);
    }

    public function redirectToGateway(Request $request)
    {
    	$custom = $this->custom;
    	$user_id = \Session::get('uid');
    	$email = $request->email;
    	$amount = $request->amount;
    	$original = $request->original;
    	$reference = $request->reference;
    	$key = $request->key;
        $time = $custom->time_now();

        $email = $custom->process_email($email);

        if (empty($email) || empty($original) || empty($amount) || empty($reference) || empty($key) )
        {
            \Session::flash('flash_message', 'An error occured');
            return redirect('account');
        }

        if (!ctype_digit($amount) )
        {
            \Session::flash('flash_message', 'An error occured');
            return redirect('account');
        }

        $check_reference = DB::table('payment_processor')
            ->where('reference', $reference)
            ->first();

        if ($check_reference)
        {
            \Session::flash('flash_message', 'Timeout: please try again');
            return redirect('account');
        }

        $data = [
        	'user_id' => $user_id, 'email' => $email, 'reference' => $reference, 'amount' => $original, 'amount_sent' => $amount, 'created_at' => $time
        ];
        $payment_ins = DB::table('payment_processor')
        	->insert($data);

    	if ($payment_ins)
        {
        	return Paystack::getAuthorizationUrl()->redirectNow();
        }
        else
        {
        	\Session::flash('flash_message', 'An error occured. Please try again');
        	return redirect('dashboard');
        }
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback(Request $request)
    {
        $custom = $this->custom;
        $transactions = $this->transactions;
        $time = $custom->time_now();

        $paymentDetails = Paystack::getPaymentData();

        // dd($paymentDetails);
        $tranx = $paymentDetails;
        $data = $tranx['data'];

        if(!$tranx['status'])
        {
            // there was an error from the API
            \Session::flash('flash_message', 'An error occured: '.$tranx['message']);
            return redirect('dashboard');
        }

        // fetch transaction reference from url query string
        $reference = $request->query('trxref');
        if (empty($reference))
        {
            \Session::flash('flash_message', 'An error occured while crediting your account');
            return redirect('dashboard');
        }

        if('success' == $data['status'])
        {
            // transaction was successful...
            // please check other things like whether you already gave value for this ref
            // if the email matches the customer who owns the product etc
            // Give value
            $processor = DB::table('payment_processor')
                ->where('reference', $reference)
                ->latest()
                ->first();

            // if reference could not be found(i.e the transaction has not been initialized or something went wrong)
            if (!$processor)
            {
                \Session::flash('flash_message', 'An error occured: Reference error');
                return redirect('dashboard');
            }

            // check if user has been credited before (avoid multiple billing)
            if ($processor->credited == 1)
            {
                return redirect('dashboard');
            }
            $email = $data['customer']['email'];
            $authorization_code = $data['authorization']['authorization_code'];
            $amount = $processor->amount;

            // check if email matches the customer who owns the current transaction
            if (strtolower($processor->email) !== strtolower($email))
            {
                \Session::flash('flash_message', 'An error occured: Reference error');
                return redirect('dashboard');
            }

            // give value to user and also give referral commission accordingly
            $topup = $transactions->top_up($processor->user_id, $reference, $amount, $processor->id, 
                    $authorization_code, json_encode($paymentDetails), $time);

            // if databse transaction failed
            if ($topup == 0)
            {   
                \Session::flash('flash_message_error', 'Error');
                return redirect('dashboard');
            }

            // notify user by email
            $message = 'Your '.$this->site_name.' Wallet funding of '.$amount.' was successful';
            $custom->send_generic_email($email, $message, 'Wallet Funding');

            \Session::flash('flash_message_success', 'Balance updated');
            return redirect('dashboard');
        }
        // Now you have the payment details,
        // you can store the authorization_code in your db to allow for recurrent subscriptions
        // you can then redirect or do whatever you want
    }

    
    public function gift_generate(Request $request)
    {
        $custom = $this->custom;
        $transactions = $this->transactions;
        $time = $custom->time_now();
        $user_id = \Session::get('uid');
        $quantity = $request->quantity;
        $amount = $request->amount;
        $validity = $request->validity;
        $pin = $request->pin;
        
        if (empty($amount)  || empty($pin)  || empty($validity) )
        {
            \Session::flash('flash_message', 'All fields are required');
            return redirect()->back()->withInput();
        }
        
        if (!ctype_digit($amount))
        {
            \Session::flash('flash_message', 'Please enter a valid amount');
            return redirect()->back()->withInput();
        }
        
        if (!ctype_digit($quantity))
        {
            \Session::flash('flash_message', 'Please enter a valid quantity');
            return redirect()->back()->withInput();
        }
        
        if ($quantity > 10)
        {
            \Session::flash('flash_message', 'Maximum quantity allowed per time is 10');
            return redirect()->back()->withInput();
        }

        $quantity = empty($quantity) ? 1 : $quantity;
        
        if ($amount < $this->minimum_transfer)
        {
            \Session::flash('flash_message', 'The minimum card allowed is '
                    . 'N'.$this->minimum_transfer.'. Please Try again with a higher amount');
            return redirect()->back()->withInput();
        }
        $total = $amount * $quantity;

        $balance = $transactions->balance($user_id);

        if (($balance->balance - $total) < 0)
        {
            \Session::flash('flash_message', 'Balance is insufficient for this request, please fund your account');
            return redirect()->back()->withInput();
        }
        
        $transaction_pin = DB::table('users')
                ->where('id', $user_id)
//                ->where('transaction_pin', $pin)
                ->where('deleted', 0)
                ->value('transaction_pin');
        
        $match = password_verify($pin, $transaction_pin);
        if (!$match)
        {
            \Session::flash('flash_message', 'Incorrect Transaction Pin');
            return redirect()->back()->withInput();
        }

        $date = strtotime($time.' +'.$validity.' days');
        
        $expiry = date('Y-m-d H:i:s', $date);
        
        DB::transaction(function() use($user_id, $amount, $quantity, $balance, $expiry, $time, $custom, $transactions, &$output){
            $sender = \Session::get('esr_number');
//            $receiver = ucfirst($verify_receiver->username);
            
            $bill = $transactions->bill($user_id, ($amount * $quantity), $balance->balance, $time);
            $i = 0;
            while ($i < $quantity)
            {
                $card_pin = $custom->generate_card_number('epins');
                $data = [
                    'user_id' => $user_id, 'pin' => $card_pin, 'amount' => $amount, 'expired_at' => $expiry, 'created_at' => $time
                ];
                $pin_ins = DB::table('epins')
                    ->insert($data);

                $i++;
            }

            // add to transaction logs
            $description_trans = $quantity.' N'.$amount.' worth gift cards generated';
            $data_trans = [
                'user_id' => $user_id, 'amount' => ($amount * $quantity), 'previous_balance' => $balance->balance,
                'description' => $description_trans, 'created_at' => $time
            ];
            $transactions_ins = DB::table('transaction_logs')
                ->insert($data_trans);
            
            $output = 1;
        });

        if($output == 0)
        {
            \Session::flash('flash_message', 'An error occurred');
            return redirect()->back()->withInput();
        }
        
        \Session::flash('flash_message_success', 'Request successfully processed');
        return redirect('history/gifts');
    }
    
    public function gift_history()
    {
        $custom = $this->custom;
        $time = $custom->time_now();
        $user_id = \Session::get('uid');
        
        $transactions = DB::table('epins')
                ->where('user_id', $user_id)
                ->where('deleted', 0)
                ->paginate($this->hyperlinks_rows);
        
        $balance = DB::table('balance')
                ->where('user_id', $user_id)
                ->where('deleted', 0)
                ->first();
        
        return view('giftcards', ['transactions' => $transactions, 'balance' => $balance]);
    }
    
    public function request_history()
    {
        $custom = $this->custom;
        $time = $custom->time_now();
        $user_id = \Session::get('uid');
        
        $transactions = DB::table('requests')
                ->where('user_id', $user_id)
                ->where('deleted', 0)
                ->paginate($this->hyperlinks_rows);
        
        $balance = DB::table('balance')
                ->where('user_id', $user_id)
                ->where('deleted', 0)
                ->first();
        
        return view('payment-requests', ['transactions' => $transactions, 'balance' => $balance]);
    }

    
    public function payment_request(Request $request)
    {
        $custom = $this->custom;
        $transactions = $this->transactions;
        $time = $custom->time_now();
        $user_id = \Session::get('uid');
        $receiver = $request->receiver;
        $amount = $request->amount;
        $description = $request->description;
//        $pin = $request->pin;
        
        if (empty($receiver) || empty($amount) )
        {
            \Session::flash('flash_message', 'All fields are required');
            return redirect('history/requests');
        }
        
        if (!ctype_digit($amount))
        {
            \Session::flash('flash_message', 'Please enter a valid amount');
            return redirect('history/requests');
        }
        
        if ($amount < $this->minimum_transfer)
        {
            \Session::flash('flash_message', 'The minimum transaction allowed is '
                    . '$'.$this->minimum_transfer.'. Please Try again with a higher amount');
            return redirect('history/requests');
        }
        
        $verify_receiver = DB::table('users')
                ->where('esr_number', $receiver)
//                ->where('deleted', 0)
                ->first(['id', 'firstname', 'lastname', 'esr_number', 'email', 'deleted']);
        
        if (empty($verify_receiver))
        {
            \Session::flash('flash_message', 'User not found');
            return redirect('history/requests');
        }
        
        if ($verify_receiver->deleted == 1)
        {
            \Session::flash('flash_message', 'Cannot send request at the moment');
            return redirect('history/requests');
        }

        $balance = $transactions->balance($verify_receiver->id);
        // if ($balance->balance < $this->minimum_transfer)
        // {
        //     \Session::flash('flash_message', 'Your account balance is too low. The minimum transfer allowed is $'.$this->minimum_transfer);
        //     return redirect('history/requests');
        // }
        if (($balance->balance - $amount) < 0)
        {
            \Session::flash('flash_message', 'Recipient balance is insufficient for this request');
            return redirect('history/requests');
        }
        
//        bill user if he has sufficient balance in his wallet and requested amount is higher than 
//        minimum transfer
        DB::transaction(function() use($user_id, $receiver, $verify_receiver, $amount, $description, $balance, $time, $transactions, &$output){
            $sender = \Session::get('esr_number');
//            $receiver = ucfirst($verify_receiver->username);
            
            // $bill = $transactions->bill($user_id, $amount, $balance->balance, $time);

            // add to transaction logs
            $description_trans = 'Payment request of N'.$amount.' from '.$receiver;
            $data_trans = [
                'user_id' => $user_id, 'amount' => $amount, 'previous_balance' => $balance->balance,
                'description' => $description_trans, 'created_at' => $time
            ];
            $transactions_ins = DB::table('transaction_logs')
                ->insert($data_trans);

            $data = [
                'user_id' => $user_id, 'receiver_id' => $verify_receiver->id, 'sender' => $sender,
                'receiver' => $receiver, 'amount' => $amount, 'description' => ucfirst($description), 'created_at' => $time
            ];

            $requests_ins = DB::table('requests')
                    ->insert($data);
            
            $output = 1;
        });

        if($output == 0)
        {
            \Session::flash('flash_message', 'An error occurred');
            return redirect('history/requests');
        }
        
        \Session::flash('flash_message_success', 'Request sent, please wait for approval');
        return redirect('history/requests');
    }
    
    public function transaction_history()
    {
        $custom = $this->custom;
        $time = $custom->time_now();
        $user_id = \Session::get('uid');
        
        $transactions = DB::table('transaction_logs')
                ->where('user_id', $user_id)
                ->where('deleted', 0)
                ->paginate($this->hyperlinks_rows);
        
        $balance = DB::table('balance')
                ->where('user_id', $user_id)
                ->where('deleted', 0)
                ->first();
        
        return view('transaction-history', ['transactions' => $transactions, 'balance' => $balance]);
    }
    
    public function transfer_complete(Request $request)
    {
        $custom = $this->custom;
        $transactions = $this->transactions;
        $time = $custom->time_now();
        $user_id = \Session::get('uid');
        $receiver = $request->receiver;
        $amount = $request->amount;
        $description = $request->description;
//        $pin = $request->pin;
        
        if (empty($receiver) || empty($amount) || empty($description))
        {
            \Session::flash('flash_message', 'All fields are required');
            return redirect('history/transfer');
        }
        
        if (!ctype_digit($amount))
        {
            \Session::flash('flash_message', 'Please enter a valid amount');
            return redirect('history/transfer');
        }
        
        if ($amount < $this->minimum_transfer)
        {
            \Session::flash('flash_message', 'The minimum transfer allowed is '
                    . '$'.$this->minimum_transfer.'. Please Try again with a higher amount');
            return redirect('history/transfer');
        }
        
        $verify_receiver = DB::table('users')
                ->where('username', $receiver)
//                ->where('deleted', 0)
                ->first(['id', 'firstname', 'lastname', 'username', 'email', 'deleted']);
        
        if (empty($verify_receiver))
        {
            \Session::flash('flash_message', 'User not found');
            return redirect('history/transfer');
        }
        
        if ($verify_receiver->deleted == 1)
        {
            \Session::flash('flash_message', 'Cannot transfer to receipient at the moment');
            return redirect('history/transfer');
        }

        $balance = $transactions->balance($user_id);
        if ($balance->balance < $this->minimum_transfer)
        {
            \Session::flash('flash_message', 'Your account balance is too low. The minimum transfer allowed is $'.$this->minimum_transfer);
            return redirect('history/transfer');
        }
        if (($balance->balance - $amount) < 0)
        {
            \Session::flash('flash_message', 'Your balance is insufficient for this request');
            return redirect('history/transfer');
        }
        
//        bill user if he has sufficient balance in his wallet and requested amount is higher than 
//        minimum transfer
        DB::transaction(function() use($user_id, $receiver, $verify_receiver, $amount, $description, $balance, $time, $transactions, &$output){
            $sender = ucfirst(strtolower(\Session::get('username') ) );
//            $receiver = ucfirst($verify_receiver->username);
            
            $bill = $transactions->bill($user_id, $amount, $balance->balance, $time);

            // add to transaction logs
            $description_trans = 'Fund transfer of $'.$amount.' to '.$receiver;
            $data_trans = [
                'user_id' => $user_id, 'amount' => $amount, 'previous_balance' => $balance->balance,
                'description' => $description_trans, 'created_at' => $time
            ];
            $transactions_ins = DB::table('transaction_logs')
                ->insert($data_trans);

            $data = [
                'user_id' => $user_id, 'receiver_id' => $verify_receiver->id, 'sender' => $sender,
                'receiver' => $receiver, 'amount' => $amount, 'description' => ucfirst($description), 'created_at' => $time
            ];

            $withdrawal_ins = DB::table('fund_transfers')
                    ->insert($data);
            
            $output = 1;
        });

        if($output == 0)
        {
            \Session::flash('flash_message', 'An error occurred');
            return redirect('history/transfer');
        }
        
        \Session::flash('flash_message_success', 'Request sent, please wait for approval');
        return redirect('history/transfer');
    }
    
    public function transfer_history()
    {
        $custom = $this->custom;
        $time = $custom->time_now();
        $user_id = \Session::get('uid');
        
        $transactions = DB::table('fund_transfers')
                ->where('user_id', $user_id)
                ->where('deleted', 0)
                ->paginate($this->hyperlinks_rows);
        
        $balance = DB::table('balance')
                ->where('user_id', $user_id)
                ->where('deleted', 0)
                ->first();
        
        return view('fund-transfer', ['transactions' => $transactions, 'balance' => $balance]);
    }
    
    public function transfer_init(Request $request)
    {
        $custom = $this->custom;
        $transactions = $this->transactions;
        $time = $custom->time_now();
        $user_id = \Session::get('uid');
        $receiver = $request->receiver;
        $amount = $request->amount;
        $description = $request->description;
        $pin = $request->pin;
        
        if (empty($receiver) || empty($amount) || empty($description) || empty($pin)  )
        {
            \Session::flash('flash_message', 'All fields are required');
            return redirect()->back()->withInput();
        }
        
        if (!ctype_digit($amount))
        {
            \Session::flash('flash_message', 'Please enter a valid amount');
            return redirect()->back()->withInput();
        }
        
        if ($amount < $this->minimum_transfer)
        {
            \Session::flash('flash_message', 'The minimum transfer allowed is '
                    . '$'.$this->minimum_transfer.'. Please Try again with a higher amount');
            return redirect()->back()->withInput();
        }
        
        $transaction_pin = DB::table('users')
                ->where('id', $user_id)
//                ->where('transaction_pin', $pin)
                ->where('deleted', 0)
                ->value('transaction_pin');
        
        $match = password_verify($pin, $transaction_pin);
        if (!$match)
        {
            \Session::flash('flash_message', 'Incorrect Pin');
            return redirect()->back()->withInput();
        }
        
        $verify_receiver = DB::table('users')
                ->where('username', $receiver)
//                ->where('deleted', 0)
                ->first(['firstname', 'lastname', 'username', 'email', 'deleted']);
        
        if (empty($verify_receiver))
        {
            \Session::flash('flash_message', 'User not found');
            return redirect()->back()->withInput();
        }
        
        if ($verify_receiver->deleted == 1)
        {
            \Session::flash('flash_message', 'Cannot transfer to receipient at the moment');
            return redirect()->back()->withInput();
        }

        $balance = $transactions->balance($user_id);
        if ($balance->balance < $this->minimum_transfer)
        {
            \Session::flash('flash_message', 'Your account balance is too low. The minimum transfer allowed is $'.$this->minimum_transfer);
            return redirect()->back()->withInput();
        }
        if (($balance->balance - $amount) < 0)
        {
            \Session::flash('flash_message', 'Your balance is insufficient for this request');
            return redirect()->back()->withInput();
        }
        
        $data = (object)[
            'username' => $receiver,
            'amount' => $amount,
            'description' => $description
        ];
        
//        return response()->json(['data' => $data, 'details' => $verify_receiver]);
        return view('fund-transfer-complete', ['data' => $data, 'details' => $verify_receiver]);
        
    }
    
    public function withdrawal_history()
    {
        $custom = $this->custom;
        $time = $custom->time_now();
        $user_id = \Session::get('uid');
        
        $transactions = DB::table('withdrawal_requests')
                ->where('user_id', $user_id)
                ->where('deleted', 0)
                ->paginate($this->hyperlinks_rows);
        
        $balance = DB::table('balance')
                ->where('user_id', $user_id)
                ->where('deleted', 0)
                ->first();
        
        return view('withdrawal-history', ['transactions' => $transactions, 'balance' => $balance]);
    }
    
    public function withdrawal_request(Request $request)
    {
        $custom = $this->custom;
        $transactions = $this->transactions;
        $time = $custom->time_now();
        $user_id = \Session::get('uid');
        $amount = $request->amount;
        $pin = $request->pin;
        
        if (empty($amount) || empty($pin))
        {
            \Session::flash('flash_message', 'All fields are required');
            return redirect()->back()->withInput();
        }
        
        if (!ctype_digit($amount))
        {
            \Session::flash('flash_message', 'Please enter a valid amount');
            return redirect()->back()->withInput();
        }
        
        if ($amount < $this->minimum_withdrawal)
        {
            \Session::flash('flash_message', 'The minimum withdrawal is $'.$this->minimum_withdrawal.'. Please Try again with a higher amount');
            return redirect()->back()->withInput();
        }
        
        $verify_pin = DB::table('users')
                ->where('id', $user_id)
                ->where('transaction_pin', $pin)
                ->where('deleted', 0)
                ->value('transaction_pin');
        
        if (empty($verify_pin))
        {
            \Session::flash('flash_message', 'Incorrect Pin');
            return redirect()->back()->withInput();
        }

        $balance = $transactions->balance($user_id);
        if ($balance->balance < $this->minimum_withdrawal)
        {
            \Session::flash('flash_message', 'Your account balance is too low. The minimum withdrawal is $'.$this->minimum_withdrawal);
            return redirect()->back()->withInput();
        }
        if (($balance->balance - $amount) < 0)
        {
            \Session::flash('flash_message', 'Your balance is insufficient for this request');
            return redirect()->back()->withInput();
        }
        
//        bill user if he has sufficient balance in his wallet and requested amount is higher than 
//        minimum withdrawal
        DB::transaction(function() use($user_id, $amount, $balance, $time, $transactions, &$output){
            $bill = $transactions->bill($user_id, $amount, $balance->balance, $time);

            // add to transaction logs
            $description = 'Withdrawal request';
            $data_trans = [
                'user_id' => $user_id, 'amount' => $amount, 'previous_balance' => $balance->balance, 'description' => $description, 'created_at' => $time
            ];
            $transactions_ins = DB::table('transaction_logs')
                ->insert($data_trans);

            $data = [
                'user_id' => $user_id, 'amount' => $amount, 'created_at' => $time
            ];

            $withdrawal_ins = DB::table('withdrawal_requests')
                    ->insert($data);
            
            $output = 1;
        });

        if($output == 0)
        {
            \Session::flash('flash_message', 'An error occurred');
            return redirect()->back();
        }
        
        \Session::flash('flash_message_success', 'Request sent, please wait for approval');
        return redirect()->back();
        
    }

}
