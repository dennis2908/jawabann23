<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model,DB;

class Customer extends MyModel
{
    public $table = 'customers';
    protected $primaryKey = 'id';
    public $fillable = array('customer_code','customer_name');
	public $validationField = array('customer_code');
    public $timestamps = true;
	
	public function get_all_data(){
        return $this->select(['*'])->orderBy($this->table.'.id','DESC');		
	}
}
