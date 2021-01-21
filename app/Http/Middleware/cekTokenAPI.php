<?php

namespace App\Http\Middleware;

use Closure;

use Session;

class cekTokenAPI extends \App\Models\MyModel
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
		//dd(Session::get(''));
		
		if(!$this->Session::get('_token__') || !$request->header('token') || $this->Session::get('_token__') != $request->header('token')){

        	return $this->Response::json(['status'=>422,'message'=>$this->messages['WrongToken']]);	 
        }
		
		$this->Session::put('_token__','');
		
        return $next($request);
    }
}