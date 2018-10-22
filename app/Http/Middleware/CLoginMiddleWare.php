<?php

namespace App\Http\Middleware;

use Closure;
use DB;

class CLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // if (! \Session::has('id'))
        if (empty(\Session::get('cuid')))
        {
            return redirect('login');
        }
        else
        {
            $user = DB::table('admin')
                ->where('id', \Session::get('cuid'))
                ->first();

            if (!$user)
            {
                \Session::flush();
                $request->session()->flash('flash_message', 'Somehting went wrong');
                return redirect('login');
            }
            
            if ($user->deleted == 1)
            {
                \Session::flush();
                $request->session()->flash('flash_message', 'Your account has been suspended. Contact '.env('SITE_EMAIL'));
                return redirect('login');
            }

            \Session::put('cutype', $user->user_type);
        }
        return $next($request);
    }
}
