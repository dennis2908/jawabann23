<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;

class APIController extends \App\Http\Controllers\UltimateController
{    

	function __construct()
    {
		$this->db_tbl = new \App\Models\Api_user();
	}

    public function createUser(Request $request){
	//	print_r($request->post());die();
		$valid = $this->validate_data($request);
	    if($this->db_tbl->checkDataExists($request->post()))
		{
		   return $this->db_tbl->Response::json(['status'=>422,'message'=>$this->db_tbl->messages['data_found']]);	
		};

		$this->masuk = $this->filterArrSaveUpdateFind($request->post());

		$this->masuk['password'] = $this->db_tbl->Crypt::encryptString($this->masuk['password']);
		$this->masuk['IsActive'] = 1;

		$this->db_tbl->insert_data($this->masuk);

		return $this->db_tbl->Response::json(['status'=>200,'message'=>$this->db_tbl->messages['AddSucceed']]);
		
	}
	
	public function login(Request $request){

		$data = $this->db_tbl->cekLogin($request->post('username'));

		//print_r($data['password']);die();

		//echo Crypt::decryptString($data['password']);die();

		if(!$data)
		{
		   return $this->db_tbl->Response::json(['status'=>422,'message'=>$this->db_tbl->messages['userOrPasswordWrong']]);	
		};

		if($request->post('password')!= $this->db_tbl->Crypt::decryptString($data['password'])){
			return $this->db_tbl->Response::json(['status'=>422,'message'=>$this->db_tbl->messages['userOrPasswordWrong']]);	
		}

		//echo $this->db_tbl->message['LoginSucceed'];die();

		// Unique Token
    	$this->apiToken = uniqid(base64_encode(str_random(60)));
    	//Session()->put('__token', $this->apiToken);
    	Session::put('_token__',$this->apiToken);
		// dd(Session::all());
    	return $this->db_tbl->Response::json(['status'=>200,'token'=>$this->apiToken,'message'=>$this->db_tbl->messages['LoginSucceed']]);
			
	}		
	
	public function articles(Request $request){
	 
	}



}