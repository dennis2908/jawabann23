<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrlApiTracking extends MyModel
{
    public $table = 'url_api_trackings';
    protected $primaryKey = 'id';
    public $fillable = array('type','url','form_params','headers');
	public $validationField = array('awb_no','courier_id');
    public $timestamps = true;
	
	public function getBody($courier_id){
		
		return $this->where('courier_id',$courier_id)->first();
	}
}
