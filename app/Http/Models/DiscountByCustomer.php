<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class discountByCustomer extends MyModel
{
    public $table = 'discount_by_customers';
    protected $primaryKey = 'id';
    public $fillable = array('courier_id','customer_id','discount','start_month_condition','end_month_condition');
	public $validationField = array('courier_id','customer_id','start_month_condition');
    public $timestamps = true;
	
	public function get_all_data(){
        return $this->select([$this->table.'.*',$this->couriers.".courier_name",'customer_name','customer_code','courier_name',
		$this->DB::raw('CONCAT('.$this->table.'.discount,"%") as discount_percent')])
		->join($this->couriers,$this->table.".courier_id",'=',$this->couriers.'.id')
		->join($this->customers,$this->table.".customer_id",'=',$this->customers.'.id')
		->orderBy($this->table.'.id','DESC');		
	}
}
