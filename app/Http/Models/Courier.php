<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courier extends MyModel
{

    public  $table = 'couriers';
    protected $primaryKey = 'id';
    public $fillable = array('courier_code','courier_name');
    public $validationField = array('courier_code','courier_name');
    public $timestamps = true;

    public function get_all_data(){

    	return $this->select(['*'])->orderBy($this->table.'.id','DESC');
    }
  
}
