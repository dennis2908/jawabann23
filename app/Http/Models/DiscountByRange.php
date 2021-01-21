<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountByRange extends MyModel
{
    public $table = 'discount_by_ranges';
    protected $primaryKey = 'id';
    public $fillable = array('courier_id','min_value','max_value','discount','start_month','end_month');
    public $validationField = array('courier_id','start_month');
    public $timestamps = true;
	
	public function get_all_data(){
        return $this->select([$this->table.'.*',$this->couriers.".courier_name",
		$this->DB::raw('format('.$this->table.'.min_value,0) as min_value_mod,format('.$this->table.'.max_value,0) as max_value_mod,CONCAT('.$this->table.'.discount,"%") as discount_percent')])
		->join($this->couriers,$this->table.".courier_id",'=',$this->couriers.'.id')
		->orderBy($this->table.'.id','DESC');		
	}
}
