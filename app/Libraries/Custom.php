<?php
namespace App\Libraries;

use DateTime;
use DB;

/**
* 
*/
class Custom
{
    // function __construct(argument)
    // {
        
    // }
    protected $accounts_email;
    protected $support_email;
    protected $info_email;

    // protected $site_name = env('SITE_NAME');
    protected $site_name;
    function __construct()
    {
        $this->accounts_email = 'accounts@quiz9ja.com.ng';
        $this->support_email = 'support@quiz9ja.com.ng';
        $this->info_email = 'info@quiz9ja.com.ng';
        $this->site_name = env('SITE_NAME');
    }

    function generate_session($email, $time)
    {
        $newtoken = $this->hashh($email, $time);
        return  $newtoken;
    }
    
    function time_now()
    {
        $main = date('Y-m-d H:i:s');
        return $main;
    }
    
    /**
     * 
     * @param date Current datetime
     * @return date <b>Yesterday</b>
     */
    function yesterday()
    {
        $time = $this->time_now();
        $date = strtotime($time.' -1 day');
    
        return date('Y-m-d', $date);
    }
    
    /**
     * 
     * @return date <b>Today</b>
     */
    function today()
    {
//        $date = strtotime($time.' -'.$diff.' minute');
    
        return date('Y-m-d');
    }
    
    /**
     * 
     * @param date Current datetime
     * @return date <b>Tomorrow</b>
     */
    function tomorrow()
    {
        $time = $this->time_now();
        $date = strtotime($time.' +1 day');
    
        return date('Y-m-d', $date);
    }

    public function time_interval($date_first, $date_second)
    {

        $diff = (strtotime($date_second) - strtotime($date_first));
        return $diff;
    }
    
    function time_diff($time, $diff)
    {
        $date = strtotime($time.' -'.$diff.' year');
        
        return date('Y-m-d H:i:s', $date);
    }
    
    function minute_diff($time, $diff)
    {
        $date = strtotime($time.' -'.$diff.' minute');
    
        return date('Y-m-d H:i:s', $date);
    }
    
    function minute_add($time, $diff)
    {
        $date = strtotime($time.' +'.$diff.' minute');
    
        return date('Y-m-d H:i:s', $date);
    }
    
    function second_diff($time, $diff)
    {
        $date = strtotime($time.' -'.$diff.' second');
    
        return date('Y-m-d H:i:s', $date);
    }

    function timeout($time, $diff)
    {
        $date = strtotime($time.' +'.$diff.' second');
    
        return date('Y-m-d H:i:s', $date);
    }
    
    function hashh($em, $date, $length = 32)
    {
        $sto = "AGLRSTabcUVWXYZdefBCDEFghijkmnopqHIJKrstuvwxyz1023MNOPQ456789";
        $fst = 9;
        $sec = 8;
        $str_em = substr(md5($em.$sto), 1, $fst);
        srand((double)microtime()*1000000);
        $a = explode(":", $date);
        // $o =  $a[0].$a[1].$a[2];
        // $p = $a[1].$a[0];
        $o =  $a[1].$a[2];
        $p = $a[2].$a[1];
        $q = abs($o - $p);
        $q = substr(md5($q), 1, $sec);
        $i = 1;
        $confirm = '' ;
        
        $new_length = $length - ((int)$fst + (int)$sec);
        while ($i <= $new_length) {
            $num = rand() % 33;
            $temp = substr($sto, $num, 1);
            $confirm = $confirm . $temp;
            $i++;
    
        }
        $confirm = $str_em.$q.$confirm;
        return $confirm;
    }
    
    public function validate_time($time)
    {
        try {
            $date = new DateTime($time);
        } catch (Exception $e) {
            // For demonstration purposes only...
//             print_r(DateTime::getLastErrors());
        
            // The real object oriented way to do this is
            // echo $e->getMessage();
            if ($e->getMessage())
            {
                return 'err';
            }
        }
    }
    
    public function explode_del_multi($string, $delimiters = [',', ':', ';'])
    {
        if ( ! is_array($delimiters)) $delimiters = (array) $delimiters;
    
        if ( ! count($delimiters)) return $string;
    
        // build escaped regex like /(delimiter_1|delimiter_2|delimiter_3)/
        $regex = '/(';
        $regex .= implode('|', array_map(function ($delimiter) {
            return preg_quote($delimiter);
        }, $delimiters));
            $regex .= ')/';
    
            return preg_split($regex, $string);
    }
    
    public function replace_multi($string, $find, $replace)
    {
//         $find = array(",","---");
//         $replace = array("");
//         $arr = 'some,thing---to:xplode444asd';
        $replaced = str_replace($find,$replace,$string);
        return $replaced;
    }
    
    public function process_mobile($mobile, $countryCode = "234")
    {
        $new_num = "";
        
        // ////////////////handle foreign numbers
        if (substr($mobile, 0, 1) == '+')
        {
            $mobile = str_replace('+', '', $mobile);
        }
        $len_fone = strlen($mobile);

        $code = substr($mobile, 0, 3);
        if ($len_fone > 9 && $len_fone < 18)
        {
            if ($code != '234')
            {
                return $mobile;
            }
        }
        // //////////////////foreign number ends here/////////////////////
        if ($len_fone == 13 || $len_fone == 11)
        {
            if ($len_fone == 13)
            {
                $cclen = strlen($countryCode);
                $numcode = substr($mobile, 0, $cclen);
                $number = substr($mobile, $cclen);
                $num = $number;
        
                if (strcmp($numcode, $countryCode) == 0 && strlen($number) == 10 && ctype_digit($number))
                {
                    $new_num = $countryCode.$number;
//                     echo $new_num;
                    //                array_push($param_null, 'mobile');
                }
                else
                {
                    $new_num = "";
//                     array_push($param_null, 'mobile');
                }
            }
            if ($len_fone == 11)
            {
                $sub = $mobile[0];
                $number = substr($mobile, 1);
                if ($sub == "0" && strlen($number) == 10 && ctype_digit($number))
                {
                    $new_num = $countryCode.$number;
//                     echo $new_num;
                }
                else
                {
                    $new_num = "";
//                     array_push($param_null, 'mobile');
                }
            }
        }
        //             consider
        else
        {
            $new_num = "";
//             array_push($param_null, 'mobile');
        }
        return $new_num;
    }

    public function process_email($email)
    {
        $new_email = $email;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $new_email = "";
        }
        return strtolower($new_email);
    }

    public function process_username($username)
    {
        $new_username = "";
        $valid = ['-', '_'];
        // if (ctype_space($username))
        // {
        //     $new_username = "";
        // }
        if (ctype_alnum(str_replace($valid, '', $username)))
        {
            $new_username = strtolower($username);
        }
        return $new_username;
    }
    
    function validate_date($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    function format_date($date, $format = 'Y-m-d')
    {
        // $d = DateTime::createFromFormat($format, $date);
        $date = date_create($date);
        return date_format($date, $format);
    }

    function format_timestamp($date, $format = 'Y-m-d H:i:s')
    {
        // $d = DateTime::createFromFormat($format, $date);
        $date = date_create($date);
        return date_format($date, $format);
    }
    
    function fullname_decouple($fullname)
    {
        $fn = '';
        $ln = '';
        $mn = '';

        $split = explode(" ", $fullname);
        $name_count = count($split);
        if (count($split) == 3)
        {
            $fn = ucfirst($split[0]);
            $mn = ucfirst($split[1]);
            $ln = ucfirst($split[2]);
        }
        else if (count($split) == 2)
        {
            $fn = ucfirst($split[0]);
            $mn = '';
            $ln = ucfirst($split[1]);
        }
        $name = [
            'first_name' => $fn, 'last_name' => $ln, 'middle_name' => $mn
        ];

        return $name;
    }


    public function users_total()
    {
        $count = DB::table('users')
            ->count();
        return $count;
    }

    public function users_active_total()
    {
        $count = DB::table('users')
            ->where('deleted', 0)
            ->count();
        return $count;
    }

    public function users_suspended_total()
    {
        $count = DB::table('users')
            ->where('deleted', 1)
            ->count();
        return $count;
    }

    public function games_total()
    {
        $count = DB::table('games')
            ->count();
        return $count;
    }

    public function gen_arith_exp()
    {
        $lft = mt_rand(8, 40);
        $rgt = mt_rand(12, 50);
        $resp['lft'] = $lft;
        $resp['rgt'] = $rgt;
        return $resp;
    }


    public function defaulters_worker()
    {
        $start = $this->time_now();
        $timeout = $this->timeout($start, 20);
//         if runtime is less than x seconds, keep running
        while($this->time_now() < $timeout)
        {
            $mins = 60 * env('TIME_DEACTIVATE');
            $time_diff = $this->minute_diff($this->time_now(), $mins);

            $pairing_del = DB::table('pairings')
                // ->where('deleted', 0)
                ->where('is_activated', '<', 2)
                ->where(function($query) use($time_diff) {
                   $query->where('first_paired_user_id', '<>', 0)
                    ->where('first_pair_confirm', 0)
                    ->where('first_pair_time', '<', $time_diff);
                })
                ->orWhere(function($query) use($time_diff) {
                   $query->orWhere('second_paired_user_id', '<>', 0)
                    ->where('second_pair_confirm', 0)
                    ->where('second_pair_time', '<', $time_diff);
                })
                ->get();


                        // var_dump($pairing_del);exit;
            if (! $pairing_del)
            {
                sleep(2);
            }
            else if ($pairing_del)
            {
                foreach ($pairing_del as $pdel)
                {
                //print_r($pairing_del[1]);exit;
                    if ($pdel->first_pair_confirm == 0 && $pdel->first_paired_user_id != 0 && strtotime($pdel->first_pair_time ) < strtotime($time_diff))
                    // if (strtotime($pdel->first_pair_time ) < strtotime($time_diff) )
                    {
                        //var_dump($pdel->first_paired_user_id);exit;
                        $this->delete_paired_auto($pdel->first_paired_user_id);
                    }
                    if ($pdel->second_pair_confirm == 0 && $pdel->second_paired_user_id != 0 && strtotime($pdel->second_pair_time ) < strtotime($time_diff) )
                    // if (strtotime($pdel->second_pair_time ) < strtotime($time_diff) )
                    {
                        //var_dump($pdel->second_paired_user_id);exit;
                        $this->delete_paired_auto($pdel->second_paired_user_id);
                    }
                }
            }

        }

    }

    public function get_user_custom($param, $val, $ret)
    {
        $val = DB::table('users')
            ->where($param, $val)
            ->value($ret);

        return $val;
    }

    public function get_user_email($user_id, $table)
    {
        $email = DB::table($table)
            ->where('id', $user_id)
            ->value('email');

        return $email;
    }

    public function get_username($user_id)
    {
        $username = DB::table('users')
            ->where('id', $user_id)
            ->value('username');

        return $username;
    }

    public function name_algo()
    {
        $str = "AGLRSTabcUVWXYZdefBCDEFghijkmnopqHIJKrstuvwxyzMNOPQ";
        $mod = rand() % 50;
        $rand_char = substr($str, $mod, 2);
        $rand_number = rand(1000001, 9999999);
        $name = $rand_number.$rand_char;

        return $name;
    }

    public function card_algo($len = 8)
    {
        $str = "AGLRSTabcUVW278XYZdefBCDhij4EFg5IJKrstuv6nopqHwxyzMNOPQ0km319";
        $i = 0;
        $char = '';

        while ($i < $len)
        {
            $mod = rand() % 50;
            $char .= substr($str, $mod, 1);
            
            $i++;
        }

        return $char;
    }

    public function rand_char($len = 4)
    {
        $str = "AGLRSTabcUVWXYZdefBCDEFghijkmnopqHIJKrstuvwxyzMNOPQ";
        $i = 0;
        $char = '';

        while ($i < $len)
        {
            $mod = rand() % 50;
            $char .= substr($str, $mod, 1);

            $i++;
        }

        return $char;
    }

    public function generate_card_number($table)
    {
        $char = $this->card_algo();
        $check = DB::table($table)
            ->where('pin', $char)
            ->first();

        if ($check)
        {
            $this->generate_card_number($table);
        }
        else
        return $char;
    }

    public function generate_quiz_number($table)
    {
        $name = $this->name_algo();
        $check = DB::table($table)
            ->where('quiz_number', $name)
            ->first();

        if ($check)
        {
            $this->generate_quiz_number($table);
        }
        else
        return $name;
    }

    public function verify_exam_hashh($hashh)
    {
        $check = DB::table('results')
            ->where('hashh', $hashh)
            ->first();

        if (!$check)
        {
            return 0;
        }
        return 1;
    }

    public function generate_exam_hashh($email, $tbl_prefix, $time)
    {
        $hashh = $this->hashh($email, $time);
        $check = DB::table($tbl_prefix.'_results')
            ->where('hashh', $hashh)
            ->first();

        if (!$check)
        {
            return $hashh;
        }

        $this->generate_exam_hashh($email, $time);
    }

    public function get_questions($param, $total)
    {
        $questions = DB::table('questions')
            ->where('game_id', $game_id)
            ->where('deleted', 0)
            ->get();

        $shuffled = $questions->shuffle();

        return $shuffled->take($total);
    }

    public function get_attempted($user_id, $game_id)
    {
        $questions = collect([]);
        $final_array = collect([]);
        $qids = [];
        $results = DB::table('results')
            ->where('user_id', $user_id)
            ->where('game_id', $game_id)
            ->first();

        if (!empty($results))
        {
            $questions_arr = DB::table('questions')
                ->where('game_id', $game_id)
                ->where('deleted', 0)
                ->get();

            $question_ids = explode('-', $results->question_id);

            // foreach ($questions_arr as $key => $question_arr)
            // {
            //     array_push($qids, $question_arr->id);
            // }

            $questions_arr->map(function($item) use($questions, $question_ids) {
                if (in_array($item->id, $question_ids))
                {
                    $key = array_search($item->id, $question_ids);
                    // $questions->push($item);
                    $questions[$key] = $item;
                }
            });
            for ($i = 0; $i < $questions->count(); $i++)
            {
                $final_array->push($questions[$i]);
            }

            // // $qids = array_keys($questions);
            // foreach ($question_ids as $question_id)
            // {
            //     // $question = DB::table('questions')
            //     //     ->where('id', $question_id)
            //     //     ->first(['question', 'optionA', 'optionB', 'optionC', 'optionD']);
            //     if (in_array($question_id, $qids))
            //     {
            //         $questions->push($question);
            //     }

            //     // $questions->push($question);
            // }
            
        }
        return (object)['result_id' => $results->id, 'time_sent' => $results->time_sent, 'elapsed' => $results->time_elapsed, 'calculated' => $results->calculated, 'questions' => $final_array];
    }

    public function get_attempted_old($user_id, $game_id)
    {
        $questions = collect([]);
        $results = DB::table('results')
            ->where('user_id', $user_id)
            ->where('game_id', $game_id)
            ->first();

        if (!empty($results))
        {
            $question_ids = explode('-', $results->question_id);
            foreach ($question_ids as $question_id)
            {
                $question = DB::table('questions')
                    ->where('id', $question_id)
                    ->first(['question', 'optionA', 'optionB', 'optionC', 'optionD']);

                $questions->push($question);
            }
            
        }
        return (object)['result_id' => $results->id, 'questions' => $questions];
    }

    public function process_answers($chosen_options, $result_id, $user_id, $game_id, $duration, $time)
    {
        $wrong = 0;
        $correct = 0;
        $percent = 0;
        $time_taken = 0;
        $time_returned = $time;
        $chosen = '';
        $status = 'red-text';
        $status_duration = 'red-text';


        // loop through user's chosen options, assign 0 if user didnt select an option for a question
        // then implode chosen string with '-' and trim trailing '-'
        
        // check if user didnt answer any question
        if (!empty($chosen_options))
        {
            foreach ($chosen_options as $chos)
            {
                if (empty($chos))
                {
                    $chos = 0;
                }
                $chosen .= $chos.'-';
            }
        }
        
        $chosen = rtrim($chosen, '-');

        // fetch answers for loaded questions using result_id passed from controller to calculate correct answers and percentage
        $results = DB::table('results')
            ->where('id', $result_id)
            ->where('user_id', $user_id)
            ->where('game_id', $game_id)
            ->first();

        $answers = $results->answers;
        $exploded_answers = $answers;
        $exploded_answers = explode('-', $exploded_answers);
        $exploded_chosen = $chosen;
        $exploded_chosen = explode('-', $exploded_chosen);

        // if answers equals chosen option assign total score
        if ($answers === $chosen)
        {
            $correct = count($exploded_answers);
        }
        else
        {
            // loop through answers to process correct and wrong answers
            foreach ($exploded_answers as $key => $exploded_answer)
            {
                // check if correct answer at this index is equal to chosen answer at this index
                if (isset($exploded_chosen[$key]))
                {
                    if ($exploded_answer === $exploded_chosen[$key])
                    {
                        $correct += 1;
                    }
                    else
                    {
                        $wrong += 1;
                    }
                }
            }
        }
        $percent = ($correct / count($exploded_answers) ) * 100;
        $time_taken = $this->time_interval($results->time_sent, $time_returned);
        $microtime = microtime();
        $micro = (float)substr($microtime, 0, strpos($microtime, ' ') );

        // calculate percentage of unused time
        $total_time = (float) $time_taken + $micro;
        $unused = (float) $duration - $total_time;

        // $unused = $unused < 1 ? 1 : $unused;

        if ($unused < 1)
        {
            $unused = 1;
        }
        $percent_duration = ($unused / $duration) * 100;

        // handle how percentage displays after game has been played
        if (($percent_duration >= 90) && ($percent_duration <= 100) )
        {
            $status_duration = 'green-text';
        }
        if (($percent_duration >= 80) && ($percent_duration < 90) )
        {
            $status_duration = 'cyan-text';
        }
        if (($percent_duration >= 60) && ($percent_duration < 80) )
        {
            $status_duration = 'orange-text';
        }

        // handle how percentage displays after game has been played
        if (($percent >= 90) && ($percent <= 100) )
        {
            $status = 'green-text';
        }
        if (($percent >= 80) && ($percent < 90) )
        {
            $status = 'cyan-text';
        }
        if (($percent >= 60) && ($percent < 80) )
        {
            $status = 'orange-text';
        }

        // var_dump(strtotime($results->time_sent));
        // var_dump(strtotime($time_returned));exit;

        $data = [
            'chosen' => $chosen, 'correct' => $correct, 'wrong' => $wrong, 'time_returned' => $time_returned, 'percentage' => $percent, 'percentage_duration' => $percent_duration, 'time_taken' => $time_taken, 'micro' => $micro, 'calculated' => 1, 'updated_at' => $this->time_now()
        ];

        $results_upd = DB::table('results')
            ->where('id', $results->id)
            ->where('user_id', $user_id)
            ->where('game_id', $game_id)
            ->update($data);

        $resp = (object) [
            'score' => $percent,
            'duration' => floor($percent_duration),
            'status' => $status,
            'status_duration' => $status_duration
        ];

        return $resp;

    }

    public function process_points($pts, $multiplier)
    {
        $start = $this->time_now();
        $timeout = $this->timeout($start, 20);
//         if runtime is less than x seconds, keep running
        while($this->time_now() < $timeout)
        {
            $results = DB::table('results')
                ->join('games', 'results.game_id', '=', 'games.id')
                ->where('results.calculated', 1)
                ->where('results.processed', 0)
                ->where('results.time_elapsed', 0)
                ->select('games.duration as duration', 'results.id as id', 'results.time_taken as taken', 'results.micro as micro', 'results.percentage as percentage', 'results.percentage_duration as percentage_duration', 'results.correct as correct')
                ->take(400)
                ->get();
    
            // loop through results, calculate points, then flag processed column
            foreach ($results as $res)
            {
                $points = $res->percentage * $res->percentage_duration * $pts;

                // $total_time = (float)$res->taken + $res->micro;
                // $inverse = (float)(1 / $total_time);

                // $points = $inverse * $pts * $res->correct * $multiplier;
    
                $data = [
                    'total_points' => $points, 'processed' => 1, 'updated_at' => $this->time_now()
                ];
                $results_upd = DB::table('results')
                    ->where('id', $res->id)
                    ->update($data);
            }
        }
    }

    public function ref_commission($amount, $percentage, $user_id, $ref_id, $transactions, $time)
    {
        $commission = $amount * $percentage;
        $balance = $transactions->balance($ref_id);
            // update user's balance
        $new_bal = $balance->balance + $commission;
        $new_ref_bal = $balance->commission_referral + $commission;
        $data = [
            'previous_balance' => $balance->balance, 'balance' => $new_bal, 'commission_referral' => $new_ref_bal, 'updated_at' => $time
        ];
        $bill = DB::table('balance')
            ->where('user_id', $ref_id)
            ->update($data);

        // add record to transactions
        $description = 'Referral commission from '.strtoupper($this->get_username($user_id));
        $data_trans = [
            'user_id' => $ref_id, 'amount' => $commission, 'previous_balance' => $balance->balance, 'description' => $description, 'created_at' => $time
        ];
        $transactions_ins = DB::table('transactions')
            ->insert($data_trans);
    }

    public function encode($data)
    {
        // $dec_hash = base64_decode($data);
        // $hashh = substr($dec_hash, 0, 32);
        // $private_token = substr($dec_hash, 32, -1);
        // $pairing_type = substr($dec_hash, -1);
        $hashh = base64_encode($data);
        return $hashh;
    }

    public function decode($hashh)
    {
        $data = base64_decode($hashh);
        return $data;
    }


    public function send_verification_email($email, $link, $email_subject = "Account Verification", $from = "accounts@quiz9ja.com.ng" )
    {
        $to = $email;
        $reply_to = $this->support_email;
        $request_message = 'Please click the following URL to activate your account:<br />'.
        $link.'<br />
        If clicking the URL above does not work, copy and paste the URL into a browser window.';
    
    
        // global $email_from,$email_subject;
        $headers = 'From: '.$this->site_name.' <'.$from . ">\r\n" .
            'Reply-To: '.$reply_to . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html\r\n";
    
        mail($to, $email_subject, $request_message, $headers);
    
        //         Mail::send('emails.signup',
        //              ['testVar' => 'Just a silly test'],
        //              function($message) {
        //                    $message->to('YOUR@EMAIL.com')
        //                            ->subject('A simple test')
        //                            ->from($address, $name = null)
        //                            ->sender($address, $name = null);
        //                 });
        //         ->attach($file, array $options = [])
    
    }

    public function send_reset_email($email, $link, $email_subject = "Account ", $from = "accounts@quiz9ja.com.ng" )
    {
        $to = $email;
        $reply_to = $this->support_email;
        //         $email_subject = "Account Verification";
    
    
        //         $signup_message = <<<EOF
        $request_message = '<p>Please click the following URL to reset your password:<br />'.
        $link.'</p><br /><br />
        <p>If clicking the URL above does not work, copy and paste the URL into a browser window.</p>';
    
        // EOF;
    
        // global $email_from,$email_subject;
        $headers = 'From: '.$this->site_name.' <'.$from . ">\r\n" .
            'Reply-To: '.$reply_to . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html\r\n";
    
        mail($to, $email_subject, $request_message, $headers);
    
        //         Mail::send('emails.signup',
        //              ['testVar' => 'Just a silly test'],
        //              function($message) {
        //                    $message->to('YOUR@EMAIL.com')
        //                            ->subject('A simple test')
        //                            ->from($address, $name = null)
        //                            ->sender($address, $name = null);
        //                 });
        //         ->attach($file, array $options = [])
    
    }

    public function send_generic_email($email, $message, $email_subject = "Account ", $from = "accounts@lifematrix.com" )
    {
        $to = $email;
        $reply_to = $this->support_email;

        $request_message = $message;
    
        $headers = 'From: '.$this->site_name.' <'.$from . ">\r\n" .
            'Reply-To: '.$reply_to . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html\r\n";
    
        mail($to, $email_subject, $request_message, $headers);
    
    }

    public function check_empty($param, $redirect, $message = 'An error occurred')
    {
        if (empty($param))
        {
            \Session::flash('flash_message', $message);
            if ($redirect == 'back_redir')
            {
                return redirect()->back();
            }
            return redirect($redirect);
        }
    }


}

