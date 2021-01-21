<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyModel extends Model
{
	public $kondisi_diskons = "kondisi_diskons";
	public $m_areas = "m_areas";
	public $users = "users";
	public $roles = "roles";
	public $couriers = "couriers";
	public $customers = 'customers';
	public $discount_by_ranges = "discount_by_ranges";
	public $discount_by_customers = "discount_by_customers";
	public $price_by_customers = "price_by_customers";
	public $price_by_publishes  = "price_by_publishes";
	public $prices  = "prices";
	public $mservices  = "mservices";
	public $logs_activity  = "logs_activity";
	public $DB,$messages,$Auth,$Client,$Session,$Crypt,$code_area;
	
	function __construct()
	{
		 $this->messages['data_found'] = "Data Already Exists";
		 $this->messages['userOrPasswordWrong'] = "Incorrect Username Or Password";
		 $this->messages['AddSucceed'] = "Add Successfully";
		 $this->messages['LoginSucceed'] = "Login Successfully";
		 $this->messages['WrongToken'] = "Incorrect Token";
		 $this->DB = new \DB();	
		 $this->Auth = new \Auth();	
		 $this->Response = new \Response();
		 //$this->Client = new \GuzzleHttp\Client();
		 $this->Session = new \Session();
		 $this->Crypt = new \Illuminate\Support\Facades\Crypt();
	}

	public function insert_data($array)
    {
		$array['created_at'] = \Carbon\Carbon::now()->toDateTimeString();
        $array['updated_at'] = \Carbon\Carbon::now()->toDateTimeString();
	    return $this->DB::table($this->table)->insertGetId($array);
    }
	
	public function update_data($array=[],$id='')
    {
	    $array['updated_at'] = \Carbon\Carbon::now()->toDateTimeString();
	    return $this->where('id',$id)->update($array);
    }
	
	public function where_in_data($id)
    {
	    return $this->whereIn('id',explode(",",$id))->get();
    }
	
	
	public function get_data($id)
    {
	    return $this->find($id);
    }
	
	public function get_mass_data($id)
    {
	    return $this->whereIn('id',explode(",",$id))->get();
    }
	
	public function cek_data($id)
    {
	    return $this->DB::table($this->table)->find($id);
    }
	
	public function delete_data($id)
    {
		//$get_data DB::table($this->table)->whereIn('id',explode(",",$id))->get();
		
	    return $this->DB::table($this->table)->delete($id);
    }
	
	public function delete_mass_data($id)
    {
		//dd($id);
		//$get_data DB::table($this->table)->whereIn('id',explode(",",$id))->get();
		
	    return $this->whereIn('id',explode(",",$id))->delete($id);
    }

    public function getSearchData($query,$arr)
    {
		foreach($arr as $k=>$v){
			
				$query = $query->where($k, 'LIKE', $v);		
		}
		return $query;
    }

    public function getSearchDataWithCodeArea($query,$arr)
    {
    	//print_r($arr);die();
		foreach($arr as $k=>$v){
			
				$query = $query->where($k, 'LIKE', $v);		
		}

		if($this->code_area){
			$code_area = $this->code_area;
			if($this->table == "m_areas"){
				$query = $query->where($this->DB::raw("m_areas.id"),'=', $this->DB::raw("SUBSTR('$code_area',-8)"))->where('courier_code', '=', $this->DB::raw("SUBSTR('$code_area',1,LENGTH('$code_area')-8)"))->orderBy($this->couriers.'.courier_code','ASC');	
			}
			else
			{	
				$query = $query->where('area_id', '=', $this->DB::raw("SUBSTR('$code_area',-8)"));	
			}
					
		}

		return $query;
    }
	
	public function checkDataExists($array)
    {
		//$get_data DB::table($this->table)->whereIn('id',explode(",",$id))->get();
		$query = $this::select('id');
		
	   foreach($this->validationField as $k){
			$query = $query->where($k, $array[$k]);
	   }
	   return $query->first();
    }

    public function getAreas(){
		return $this->select([$this->table.'.*,'.$this->couriers.".courier_name"])->join($this->couriers,$this->table.".kurir",'=',$this->couriers.'.id')->orderBy('id','ASC')->get(); 
	}
	
	public function getCourierName($id){
		return $this->DB::table('couriers')->select(['id',"courier_name"])->where('id',$id)->first(); 
	}

	public function checkExistingDataPrice($array){

		$start_month = "";	

		if(isset($array['start_month'])){
			$start_month = "and start_month = '".$array['start_month']."'";
		} 
			
		$cek = $this->DB::select("
				select id from prices where area_id = '".$array['area_id']."'

				and service = '".$array['service']."' $start_month

				union all

				select id from price_by_customers where area_id = '".$array['area_id']."'

				and service = '".$array['service']."' $start_month

				union all

				select id from price_by_publishes where area_id = '".$array['area_id']."'

				and service = '".$array['service']."'
				
				union all

				select id from multi_dest_price_for8coms where area_id = '".$array['area_id']."'

				and service = '".$array['service']."' $start_month
				
				union all

				select id from multi_dest_price_for_cuses where area_id = '".$array['area_id']."'

				and service = '".$array['service']."' $start_month
				
				union all

				select id from kondisi_diskons where courier_id = (select kurir from m_areas where id = ".$array['area_id'].")
				
				union all

				select id from discount_by_customers where courier_id = (select kurir from m_areas where id = ".$array['area_id'].")
				
				union all

				select id from discount_by_ranges where courier_id = (select kurir from m_areas where id = ".$array['area_id'].")

		");
		
		if($cek){
			return $cek[0]->id;
		}
		else{
			return null;
		}
			
	}
	
	public function checkExistingDataDiscount($array){

		$start_month = "";	
		$min_value =  "";
		$start_month_condition = "";

		if(isset($array['start_month'])){
			$start_month = "and start_month = '".$array['start_month']."'";
		} 
		
		if(isset($array['min_value'])){
			$min_value = "and min_value = '".$array['min_value']."'";
		}
		
		if(isset($array['start_month_condition'])){
			$start_month_condition = "and start_month_condition = '".$array['start_month_condition']."'";
		}
			
		$cek = $this->DB::select("
				select id from kondisi_diskons where courier_id = ".$array['courier_id']."

				$start_month

				union all

				select id from discount_by_ranges where courier_id = ".$array['courier_id']."

				$min_value

				union all

				select id from discount_by_customers where courier_id = ".$array['courier_id']."

				$start_month_condition
				
				union all
				
				select id from prices 
				
				join m_areas on prices.area_id = m_areas.id
				
				where kurir = ".$array['courier_id']."

				union all

				select id from price_by_customers 
				
				join m_areas on price_by_customers.area_id = m_areas.id
				
				where kurir = ".$array['courier_id']."

				union all

				select id from price_by_publishes 
				
				join m_areas on price_by_publishes.area_id = m_areas.id
				
				where kurir = ".$array['courier_id']."
				
				union all

				select id from multi_dest_price_for8coms 
				
				join m_areas on multi_dest_price_for8coms.area_id = m_areas.id
				
				where kurir = ".$array['courier_id']."
				
				union all

				select id from multi_dest_price_for_cuses 
				
				join m_areas on multi_dest_price_for_cuses.area_id = m_areas.id
				
				where kurir = ".$array['courier_id']."

		");

		if($cek){
			return $cek[0]->id;
		}
		else{
			return null;
		}
	}

	public function checkExistingContract($contract_no){
	
		
		$cek = $this->DB::select("

				select id from prices where contract_no = '$contract_no'

				union all

				select id from price_by_customers where contract_no = '$contract_no'

				union all

				select id from price_by_publishes where contract_no = '$contract_no'
				
				union all

				select id from multi_dest_price_for8coms where contract_no = '$contract_no'
				
				union all

				select id from multi_dest_price_for_cuses where contract_no = '$contract_no'
				
				union all
				
				select id from kondisi_diskons where contract_no = '$contract_no'

				union all

				select id from discount_by_ranges where contract_no = '$contract_no'

				union all

				select id from discount_by_customers where contract_no = '$contract_no'

		");

		if($cek){
			return $cek[0]->id;
		}
		else{
			return null;
		}

	}
	
    public function combineAreaCode(){
		
		return $this->DB::table($this->prices)->select([$this->prices.'.id',
		$this->DB::raw('IF('.$this->m_areas.'.id < 10, CONCAT('.$this->couriers.'.courier_code,"0000000",'.$this->m_areas.'.id,"-",service), 
		IF('.$this->m_areas.'.id < 100, CONCAT('.$this->couriers.'.courier_code,"000000",'.$this->m_areas.'.id,"-",service), 
		IF('.$this->m_areas.'.id < 1000, CONCAT('.$this->couriers.'.courier_code,"00000",'.$this->m_areas.'.id,"-",service), 
		IF('.$this->m_areas.'.id < 10000, CONCAT('.$this->couriers.'.courier_code,"0000",'.$this->m_areas.'.id,"-",service), 
		IF('.$this->m_areas.'.id < 100000, CONCAT('.$this->couriers.'.courier_code,"000",'.$this->couriers.'.id,"-",service), 
		IF('.$this->m_areas.'.id < 1000000, CONCAT('.$this->couriers.'.courier_code,"00",'.$this->m_areas.'.id,"-",service), 
		IF('.$this->m_areas.'.id < 10000000, CONCAT('.$this->couriers.'.courier_code,"0",'.$this->m_areas.'.id,"-",service), 
		CONCAT('.$this->couriers.'.courier_code,'.$this->m_areas.'.id,"-",service)))))))) as custom_name')])
		->join($this->m_areas,$this->prices.".area_id",'=',$this->m_areas.'.id')
		->join($this->couriers,$this->m_areas.".kurir",'=',$this->couriers.'.id')
		->orderBy($this->prices.'.id','DESC')->get(); 
	}

	public function getAreaCode(){
		return $this->DB::table($this->m_areas)->select([$this->m_areas.'.id',
		$this->DB::raw('IF('.$this->m_areas.'.id < 10, CONCAT('.$this->couriers.'.courier_code,"0000000",'.$this->m_areas.'.id), 
		IF('.$this->m_areas.'.id < 100, CONCAT('.$this->couriers.'.courier_code,"000000",'.$this->m_areas.'.id), 
		IF('.$this->m_areas.'.id < 1000, CONCAT('.$this->couriers.'.courier_code,"00000",'.$this->m_areas.'.id), 
		IF('.$this->m_areas.'.id < 10000, CONCAT('.$this->couriers.'.courier_code,"0000",'.$this->m_areas.'.id), 
		IF('.$this->m_areas.'.id < 100000, CONCAT('.$this->couriers.'.courier_code,"000",'.$this->m_areas.'.id), 
		IF('.$this->m_areas.'.id < 1000000, CONCAT('.$this->couriers.'.courier_code,"00",'.$this->m_areas.'.id), 
		IF('.$this->m_areas.'.id < 10000000, CONCAT('.$this->couriers.'.courier_code,"0",'.$this->m_areas.'.id), 
		CONCAT('.$this->couriers.'.courier_code,'.$this->m_areas.'.id)))))))) as code_area')])
		->join($this->couriers,$this->m_areas.".kurir",'=',$this->couriers.'.id')->orderBy($this->couriers.'.courier_code','ASC')->get();
	}

	public function searchAreaCode($keyword){
		//$this->DB::connection()->enableQueryLog();
		return $this->DB::table($this->m_areas)->select([$this->m_areas.'.id',
		$this->DB::raw('IF('.$this->m_areas.'.id < 10, CONCAT('.$this->couriers.'.courier_code,"0000000",'.$this->m_areas.'.id), 
		IF('.$this->m_areas.'.id < 100, CONCAT('.$this->couriers.'.courier_code,"000000",'.$this->m_areas.'.id), 
		IF('.$this->m_areas.'.id < 1000, CONCAT('.$this->couriers.'.courier_code,"00000",'.$this->m_areas.'.id), 
		IF('.$this->m_areas.'.id < 10000, CONCAT('.$this->couriers.'.courier_code,"0000",'.$this->m_areas.'.id), 
		IF('.$this->m_areas.'.id < 100000, CONCAT('.$this->couriers.'.courier_code,"000",'.$this->m_areas.'.id), 
		IF('.$this->m_areas.'.id < 1000000, CONCAT('.$this->couriers.'.courier_code,"00",'.$this->m_areas.'.id), 
		IF('.$this->m_areas.'.id < 10000000, CONCAT('.$this->couriers.'.courier_code,"0",'.$this->m_areas.'.id), 
		CONCAT('.$this->couriers.'.courier_code,'.$this->m_areas.'.id)))))))) as code_area')])
		->join($this->couriers,$this->m_areas.".kurir",'=',$this->couriers.'.id')->where($this->DB::raw("m_areas.id"),'=', $this->DB::raw("SUBSTR('$keyword',-8)"))->where('courier_code', '=', $this->DB::raw("SUBSTR('$keyword',1,LENGTH('$keyword')-8)"))->orderBy($this->couriers.'.courier_code','ASC')->get();
		//dd($this->getFullSqlDB());
	}
	
    public function insertToLog($array){
		
		return $this->DB::table($this->logs_activity)->insert($array); 
	}
	
	public function getCourier()
    {
        return $this->DB::table($this->couriers)->orderBy('courier_name','ASC')->get(); 
    }
	
	public function getService()
    {
        return $this->DB::table($this->mservices)->orderBy('service','ASC')->get(); 
    }

    public function getCourierForTracking()
    {
        return $this->DB::table($this->couriers)->whereNotIn('courier_code',['adex','ninja'])
        ->orderBy('courier_name','ASC')->get(); 
    }

    public function getCustomer(){
	   return $this->DB::table($this->customers)->select(['*'])->orderBy($this->table.'.id','ASC')->get(); 
	}

    public function OnWhereDate($query,$arr)
    {
	    $min = $arr->min;
        $max = $arr->max;
        if($min && !$max)
        {
            $query = $query->whereDate('created_at','=',$min);
        }
        if(!$min && $max)
        {
            $query = $query->whereDate('created_at','=',$max);
        }
        if($min && $max)
        {
            $query = $query->whereDate('created_at','>=',$min)->whereDate('created_at','<=',$max);
        }
         
        return $query;
    }

}
