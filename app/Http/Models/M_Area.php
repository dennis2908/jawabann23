<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class M_area extends MyModel
{
    public $table = 'm_areas';
    protected $primaryKey = 'id';
    public $fillable = array('kurir','asal_negara','asal_zip_code','asal_kota','tujuan_provinsi','tujuan_kota','tujuan_kecamatan','tujuan_kelurahan','tujuan_zip_code','tujuan_zona','tujuan_negara','cod','eta');
    public $validationField = array('kurir','asal_zip_code','tujuan_zip_code','cod','eta');
	
	public function get_all_data(){
		
        return $this->select([$this->table.'.*',$this->couriers.".courier_name",
		$this->DB::raw('IF('.$this->table.'.cod = 2, CONCAT("BISA COD"),CONCAT("TIDAK BISA COD")) as cod_detail,
		IF('.$this->table.'.id < 10, CONCAT('.$this->couriers.'.courier_code,"0000000",'.$this->table.'.id), 
		IF('.$this->table.'.id < 100, CONCAT('.$this->couriers.'.courier_code,"000000",'.$this->table.'.id), 
		IF('.$this->table.'.id < 1000, CONCAT('.$this->couriers.'.courier_code,"00000",'.$this->table.'.id), 
		IF('.$this->table.'.id < 10000, CONCAT('.$this->couriers.'.courier_code,"0000",'.$this->table.'.id), 
		IF('.$this->table.'.id < 100000, CONCAT('.$this->couriers.'.courier_code,"000",'.$this->table.'.id), 
		IF('.$this->table.'.id < 1000000, CONCAT('.$this->couriers.'.courier_code,"00",'.$this->table.'.id), 
		IF('.$this->table.'.id < 10000000, CONCAT('.$this->couriers.'.courier_code,"0",'.$this->table.'.id), 
		CONCAT('.$this->couriers.'.courier_code,'.$this->table.'.id)))))))) as code_area')])
		->join($this->couriers,$this->table.".kurir",'=',$this->couriers.'.id')->orderBy($this->table.'.id','DESC');
		
		
	}
	
}

