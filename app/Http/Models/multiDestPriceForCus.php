<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class multiDestPriceForCus extends MyModel
{
    public $table = 'multi_dest_price_for_cuses';
    protected $primaryKey = 'id';
    public $fillable = array('area_id','contract_no','service','harga_publish','start_month','end_month');
	public $validationField = array('area_id','contract_no','service','start_month');
    public $timestamps = true;	
	
	public function get_all_data(){
        return $this->select([$this->table.'.*',$this->couriers.".courier_code",$this->couriers.'.courier_name',$this->m_areas.'.tujuan_provinsi',$this->m_areas.'.tujuan_kota',$this->m_areas.'.tujuan_negara',$this->m_areas.'.asal_kota',$this->m_areas.'.asal_negara',
		$this->DB::raw('format('.$this->table.'.harga_publish,0) as harga_publish_mod,IF('.$this->m_areas.'.id < 10, CONCAT('.$this->couriers.'.courier_code,"0000000",'.$this->m_areas.'.id), 
		IF('.$this->m_areas.'.id < 100, CONCAT('.$this->couriers.'.courier_code,"000000",'.$this->m_areas.'.id), 
		IF('.$this->m_areas.'.id < 1000, CONCAT('.$this->couriers.'.courier_code,"00000",'.$this->m_areas.'.id), 
		IF('.$this->m_areas.'.id < 10000, CONCAT('.$this->couriers.'.courier_code,"0000",'.$this->m_areas.'.id), 
		IF('.$this->m_areas.'.id < 100000, CONCAT('.$this->couriers.'.courier_code,"000",'.$this->m_areas.'.id), 
		IF('.$this->m_areas.'.id < 1000000, CONCAT('.$this->couriers.'.courier_code,"00",'.$this->m_areas.'.id), 
		IF('.$this->m_areas.'.id < 10000000, CONCAT('.$this->couriers.'.courier_code,"0",'.$this->m_areas.'.id), 
		CONCAT('.$this->couriers.'.courier_code,'.$this->m_areas.'.id)))))))) as code_area')])
		->join($this->m_areas,$this->table.".area_id",'=',$this->m_areas.'.id')
		->join($this->couriers,$this->m_areas.".kurir",'=',$this->couriers.'.id')
		->orderBy($this->table.'.id','DESC');		
	}
}
