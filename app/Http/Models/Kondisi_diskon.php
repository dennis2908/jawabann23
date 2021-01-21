<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Kondisi_diskon extends MyModel
{
    public $table = 'kondisi_diskons';
    protected $primaryKey = 'id';
    public $fillable = array('courier_id','diskon_8comm','start_month','end_month');
    public $validationField = array('courier_id','start_month');
    public $timestamps = true;
	
	public function get_all_data(){
        return $this->select([$this->table.'.*',$this->couriers.".courier_name",
		$this->DB::raw('CONCAT(diskon_8comm,"%") as diskon_8comm_detail'),
		'diskon_8comm',
		'courier_name'])
		->join($this->couriers,$this->table.".courier_id",'=',$this->couriers.'.id')
		->orderBy($this->table.'.id','DESC');		
	}
	
}
