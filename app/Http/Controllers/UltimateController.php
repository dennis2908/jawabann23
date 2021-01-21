<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UltimateController extends Controller
{
	
	public $masuk = array();
	public $edit = array();
	public $checkValidate = array();
	public $url_method = "GET";
	public $url_params = [];
	public $db_tbl,$perPage,$tbl,$page,$url;
	
	public function __construct()
    {
		$this->middleware('auth');
		$this->db_tbl = new \App\Models\MyModel();
    }
	
	public function index(Request $request)
    {
       $this->setPagePerpage($request);
       $query = $this->db_tbl->OnWhereDate($this->getSearchData($this->db_tbl->get_all_data($request->post()),array_filter($request->post())),$request); 
	   $sql =  $this->getFullSql($query);

       
	   return $query->paginate($this->perPage);
    }

    public function combineAreaCode()
    { 
	    return $this->db_tbl->Response::json(['status'=>200,'data'=>$this->db_tbl->combineAreaCode(),'message'=>'']);
    }

    public function getAreaCode(){
    	
		 return $this->db_tbl->Response::json(['status'=>200,'data'=>$this->db_tbl->getAreaCode(),'message'=>'Add Successfully']);
    }

    public function searchAreaCode(Request $request){
    	//$query =  $this->db_tbl->searchAreaCode($request->get('q'));
    	//echo $this->getFullSql($query);die();
    	 return $this->db_tbl->Response::json(['status'=>200,'data'=>$this->db_tbl->searchAreaCode($request->get('q')),'message'=>'Add Successfully']);
    }

    public function getSearchData($query,$arr=[]){
     	return $this->db_tbl->getSearchData($query,$this->filterArrFindWithCodeArea($arr));

    }

    public function getSearchDataWithCodeArea($query,$arr=[]){
    	//print_r($arr);die();
     	return $this->db_tbl->getSearchDataWithCodeArea($query,$this->filterArrFindWithCodeArea($arr));

    }
	
	public function getDataWithCodeArea(Request $request)
    {
    	/*
		
		$this->setPagePerpage($request);

		$query = $this->db_tbl->OnWhereDate($this->getSearchData($this->db_tbl->get_all_data($request->post()),array_filter($request->post())),$request);	
         
        $sql =  "select * from (".$this->getFullSql($query).") as b ";

        $code_area = $request->post('code_area');
		
		if($code_area)
        {
            $sql .=  "where code_area like '".$code_area."'";
        }
		
		$sql .= " order by id DESC";
				
		return $this->renderDataIndex($sql);
		*/
	   $this->db_tbl->code_area = $request->post('code_area');
	   $arr = $request->post();

	   if(isset($arr['harga_publish'])){
	   	 $arr['harga_publish'] = str_replace(',','',$arr['harga_publish']);
	   }

	  // print_r($arr);die();

	   $this->setPagePerpage($request);
       $query = $this->db_tbl->OnWhereDate($this->getSearchDataWithCodeArea($this->db_tbl->get_all_data($arr),array_filter($arr)),$request); 
	   $sql =  $this->getFullSql($query);

	   //print_r($sql);die();
       
	   return $query->paginate($this->perPage);
    }

    public function setPagePerpage($arr){

    	$this->perPage = $arr['per_page'];
		$this->page = $arr['page'];
    }
	
	public function get_data($tbl=array()){
        return $tbl->db_tbl->select([$tbl->db_tbl->table.'.*',
		$this->db_tbl->DB::raw('CONCAT(courier_name, "-", tujuan_provinsi, "-", tujuan_kota, "-", tujuan_kecamatan, "-", tujuan_kecamatan,"-",tujuan_kecamatan, "-", tujuan_zip_code, "-", tujuan_zona) AS custom_name')])
		->join($this->db_tbl->m_areas,$this->db_tbl->table.".area_id",'=',$this->db_tbl->m_areas.'.id')
		->join($this->db_tbl->couriers,$this->db_tbl->m_areas.".kurir",'=',$this->db_tbl->couriers.'.id')
		->orderBy($this->db_tbl->table.'.id','DESC');		
	}	
	
	public function renderDataIndex($sql=''){
		
		$basicQuery =  $this->db_tbl->DB::select($this->db_tbl->DB::raw($sql));

		$data = array_slice($basicQuery, $this->page, $this->perPage, true);
        $flattened = array_flatten($data);

		return new \Illuminate\Pagination\LengthAwarePaginator($flattened, count($basicQuery), $this->perPage, $this->page);
	}
	
	public function validate_data($array=[]){
	   foreach($this->db_tbl->fillable as $k){
			$this->checkValidate[$k] = 'required|max:191';
	   }
	   $this->validate($array,$this->checkValidate);		
	}
	
    public function store(Request $request)
    { 
		$valid = $this->validate_data($request);
	    if($this->db_tbl->checkDataExists($request->post()))
		{
		   return $this->db_tbl->Response::json(['status'=>422,'data'=>'','message'=>$this->db_tbl->messages['data_found']]);	
		};
		$this->masuk = $this->filterArrSaveUpdateFind($request->post());
		$this->db_tbl->insert_data($this->masuk);
        $this->db_tbl->insertToLog(['name' => $this->db_tbl->Auth::user()->id, 'email' => $this->db_tbl->Auth::user()->email, 'table'=>$this->db_tbl->table ,'action' => 'insert', 'data' => json_encode($this->masuk)]);
        return $this->db_tbl->Response::json(['status'=>200,'data'=>'','message'=>$this->db_tbl ->message['AddSucceed']]);
    }

    public function storePrice(Request $request)
    { 
		$valid = $this->validate_data($request);

		$arr = $request->post();

		print_r($request->post());die();
		
	    if($this->db_tbl->checkDataExists($request->post()) || $this->db_tbl->checkExistingDataPrice($request->post()) || $this->db_tbl->checkExistingContract($request->post('contract_no')))
		{
		   return $this->db_tbl->Response::json(['status'=>422,'data'=>'','message'=>$this->db_tbl->messages['data_found']]);	
		};

		$this->masuk = $this->filterArrSaveUpdateFind($request->post());
		$this->db_tbl->insert_data($this->masuk);
        $this->db_tbl->insertToLog(['name' => $this->db_tbl->Auth::user()->id, 'email' => $this->db_tbl->Auth::user()->email, 'table'=>$this->db_tbl->table ,'action' => 'insert', 'data' => json_encode($this->masuk)]);
        return $this->db_tbl->Response::json(['status'=>200,'data'=>'','message'=>$this->db_tbl ->message['AddSucceed']]);
    }
	
	public function storeDiscount(Request $request)
    { 
		$valid = $this->validate_data($request);
		
	    if($this->db_tbl->checkDataExists($request->post()) || $this->db_tbl->checkExistingDataDiscount($request->post()) || $this->db_tbl->checkExistingContract($request->post('contract_no')))
		{
		   return $this->db_tbl->Response::json(['status'=>422,'data'=>'','message'=>$this->db_tbl->messages['data_found']]);	
		};

		$this->masuk = $this->filterArrSaveUpdateFind($request->post());
		$this->db_tbl->insert_data($this->masuk);
        $this->db_tbl->insertToLog(['name' => $this->db_tbl->Auth::user()->id, 'email' => $this->db_tbl->Auth::user()->email, 'table'=>$this->db_tbl->table ,'action' => 'insert', 'data' => json_encode($this->masuk)]);
        return $this->db_tbl->Response::json(['status'=>200,'data'=>'','message'=>$this->db_tbl ->message['AddSucceed']]);
    }
	
	public function filterArrSaveUpdateFind($array){
	 $arr = array();
  	 foreach($array as $k=>$v){
			if(in_array($k,$this->db_tbl->fillable)){
				if(strpos($k, "month")){
			    	$v = date('Y-M',strtotime($v));
				} 
				else
				{	
					$arr[$k] = $v;	
				}
			}
	 }
	 return str_replace('%','',str_replace(',','',$arr));
	}
	
	public function filterArrFindWithCodeArea($array){
	 $arr = array();
	 $this->db_tbl->fillable = array_merge($this->db_tbl->fillable,['kurir','asal_kota','asal_negara','tujuan_negara','tujuan_provinsi','tujuan_kota','tujuan_kota','tujuan_kecamatan','tujuan_kelurahan','tujuan_zona','eta']);
	// print_r($array);die();
  	 foreach($array as $k=>$v){
			if(in_array($k,$this->db_tbl->fillable)){
				$arr[$k] = $v;	
			}
	 }
	 return str_replace('%','',str_replace(',','',$arr));
	}

    public function destroy($id)
    {
        $cek = $this->db_tbl->get_data($id);
        if(!$cek)
        {
        	return $this->db_tbl->Response::json(['status'=>404,'data'=>'','message'=>['error'=>['Data Not Found']]]);
        }else{
            $this->db_tbl->insertToLog(['name' => $this->db_tbl->Auth::user()->id, 'email' => $this->db_tbl->Auth::user()->email, 'table'=>$this->db_tbl->table ,'action' => 'delete', 'data' => json_encode($cek)]);
            $this->db_tbl->delete_data($id);
            return $this->db_tbl->Response::json(['status'=>200,'data'=>'','message'=>'Delete Successfully']);
        } 

    }

    public function update(Request $request, $id)
    {
		$cek = $this->db_tbl->get_data($id);
        if(!$cek)
        {
			return $this->db_tbl->Response::json(['status'=>404,'data'=>'','message'=>['error'=>['Data Not Found']]]);
        }else{
			$valid = $this->validate_data($request);
			$this->edit = $this->filterArrSaveUpdateFind($request->post());
		    $this->db_tbl->update_data($this->edit,$id);
            $this->db_tbl->insertToLog(['name' => $this->db_tbl->Auth::user()->id, 'email' => $this->db_tbl->Auth::user()->email, 'table'=>$this->db_tbl->table,'action' => 'update', 'data' => json_encode($cek)]);
            return $this->db_tbl->Response::json(['status'=>200,'data'=>'','message'=>'Edit Successfully']);
	    }

    }
	
	public function getCourier()
    { 
	    return $this->db_tbl->Response::json(['status'=>200,'data'=>$this->db_tbl->getCourier(),'message'=>'']);
    }

    public function getCourierForTracking()
    { 
	    return $this->db_tbl->Response::json(['status'=>200,'data'=>$this->db_tbl->getCourierForTracking(),'message'=>'Get Courier']);
    }

    public function getCustomer(){ 
		return $this->db_tbl->Response::json(['status'=>200,'data'=>$this->db_tbl->getCustomer(),'message'=>'Get Customer']);
    }
	
	public function getService(){ 
		return $this->db_tbl->Response::json(['status'=>200,'data'=>$this->db_tbl->getService(),'message'=>'Get Service']);
    }
	
    public function deleteAll($id='')
    {
		$get =  $this->db_tbl->get_mass_data($id);
        $this->db_tbl->insertToLog(['name' => $this->db_tbl->Auth::user()->id, 'email' => $this->db_tbl->Auth::user()->email, 'table'=>$this->db_tbl->table,'action' => 'delete', 'data' => json_encode($get)]);		
		$this->db_tbl->delete_mass_data($id);
		return $this->db_tbl->Response::json(['status'=>200,'data'=>'','message'=>'Delete Successfully']);
	}

	public function chk_exists($cek){
   		$dest = "-";

   		if($cek)
   		{
		   $dest = ucwords(str_replace($this->replace," ",$cek));
		}

		return $dest;
   	}	

   	public function chk_existsv2($cek){
   		$dest = "";

   		if($cek)
   		{
		   $dest = $cek;
		}

		return $dest;
   	}

	public function myUrl($url){

		$this->url = $url;
		
		return $this;
	}

	public function myUrlMethod($url_method){

		$this->url_method = $url_method;
		return $this;
	}

	public function myUrlParams($url_params){

		$this->url_params = $url_params;
		return $this;
	}

   	public function getAPI(){

	  return $this->db_tbl->Client->request($this->url_method, $this->url, $this->url_params);
   	
   	}

   	public function destination($destination){

		$this->arr[0]['destination'] = $this->chk_exists($destination);
	}

	public function sender($sender){

		$this->arr[0]['sender'] = $this->chk_exists($sender);
	}

	public function date_received($date_received){

		$this->arr[0]['date_received'] = $this->chk_exists($date_received);
	}

	public function receiver($receiver){

		$this->arr[0]['receiver'] = $this->chk_exists($receiver);
	}

	public function status($status){

		$this->arr[0]['status'] = $this->chk_exists($status);
	}

	public function service($service){

		$this->arr[0]['service'] = $this->chk_exists($service);
	}

	public function checkDataHistory($no,$data,$field_name){

		if($data)
	    {
	    	$this->arrDetail[$no][$field_name] =  $data;
	    }
	    else{
	    	$this->arrDetail[$no][$field_name] =  '-';
	    }
	}
	
	function getFullSql($query) {
		  $sqlStr = $query->toSql();
		  foreach ($query->getBindings() as $iter=>$binding) {

			$type = gettype($binding);
			switch ($type) {
			  case "integer":
			  case "double":
				$bindingStr = "$binding";
				break;
			  case "string":
				$bindingStr = "'$binding'";
				break;
			  case "object":
				$class = get_class($binding);
				switch ($class) {
				  case "DateTime":
					$bindingStr = "'" . $binding->format('Y-m-d H:i:s') . "'";
					break;
				  default:
					throw new \Exception("Unexpected binding argument class ($class)");
				}
				break;
			  default:
				throw new \Exception("Unexpected binding argument type ($type)");
			}

			$currentPos = strpos($sqlStr, '?');
			if ($currentPos === false) {
			  throw new \Exception("Cannot find binding location in Sql String for bundung parameter $binding ($iter)");
			}

			$sqlStr = substr($sqlStr, 0, $currentPos) . $bindingStr . substr($sqlStr, $currentPos + 1);
		  }

		  $search = ["select", "distinct", "from", "where", "and", "order by", "asc", "desc", "inner join", "join"];
		  $replace = ["SELECT", "DISTINCT", "\n  FROM", "\n    WHERE", "\n    AND", "\n    ORDER BY", "ASC", "DESC", "\n  INNER JOIN", "\n  JOIN"];
		  $sqlStr = str_replace($search, $replace, $sqlStr);

		  return $sqlStr;
	}
}