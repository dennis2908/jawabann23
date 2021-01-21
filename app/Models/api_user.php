<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Api_user extends Model
{
    public $table = 'api_users';
    protected $primaryKey = 'id';
    public $fillable = array('username','password');
    public $validationField = array('username');
	public $timestamps = true;

	public function cekLogin($username){
		return $this->select(['username','password'])->where('username',$username)
		->where('IsActive',1)
		->first();
	}
}
