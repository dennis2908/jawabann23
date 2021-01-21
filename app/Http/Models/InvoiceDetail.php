<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class InvoiceDetail extends Model
{

    protected $table = 'invoice_file_details';
    protected $primaryKey = 'id';
    protected $fillable = array('invoice_files_id','customer_code','status','service','awb_no','qty','weight_total','bag_total','cod_amount','insurance_amount','weight_price','other_amount','8comm_price','price','customer_price');
    public $timestamps = true;
  
    public function order()
    {
        return $this->belongsTo('App\Models\Order','id','invoice_file_details_id');
    }
	
	public function total_price($id='')
    {
		/*
        return $result = DB::select('
		 select sum(qty*price) as total_harga from :Table
		 where invoice_files_id :ID
		',['ID'=>$id,'Table'=>$this->table]);
		*/
		return DB::table($this->table)->where('invoice_files_id', $id)->sum(DB::raw("price"));
    }
  
}
