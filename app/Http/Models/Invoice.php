<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class Invoice extends Model
{

    protected $table = 'invoice_files';
    protected $primaryKey = 'id';
    protected $fillable = array('courier_id','name','date_invoice','status','file_name','remarks','data','algoritma_id','potongan_8comm','potongan_customer','grand_total_8commerce','grand_total_customer');
    public $timestamps = true;
 
 
    public function invoiceDetail()
    {
        return $this->belongsTo('App\Models\InvoiceDetail','invoice_files_id');
    }

    public function courier()
    {
        return $this->hasOne('App\Models\Courier','id','courier_id');
    }
	
	public function get_algoritma_name($id='')
    {
		$tbl_algoritmas = 'algoritmas';
        return DB::table($this->table)->join($tbl_algoritmas,$tbl_algoritmas.'.id','=',$this->table.'.algoritma_id')->where($this->table.'.id',$id)->first();
    }
	
	public function getCourierId($id='')
    {
	    return DB::table($this->table)->select(['courier_id'])->where('id',$id)->first();
    }
	
	public function getAllDiscount($courier_id,$money,$customer,$date_invoice){
		
		return DB::select("
		select SUM(commercePriceByRange) AS commercePriceByRange, SUM(customerPriceByDiscount) AS customerPriceByDiscount from(
		select CONCAT(discount*$money/100) as commercePriceByRange,CONCAT(null) as customerPriceByDiscount from discount_by_ranges 
		where courier_id = $courier_id and min_value <= $money and max_value >= $money 
		and STR_TO_DATE(start_month, '%Y-%m') <= STR_TO_DATE('$date_invoice', '%m/%Y')
		and STR_TO_DATE(end_month, '%Y-%m') >= STR_TO_DATE('$date_invoice', '%m/%Y')
		union all 
		select CONCAT(null) as commercePriceByRange,CONCAT(discount*$money/100) as customerPriceByDiscount from discount_by_customers
		join customers on discount_by_customers.customer_id = customers.id
		where courier_id = $courier_id and customer_code = '$customer' 
		and STR_TO_DATE(start_month_condition, '%Y-%m') <= STR_TO_DATE('$date_invoice', '%m/%Y')
		and STR_TO_DATE(end_month_condition, '%Y-%m') >= STR_TO_DATE('$date_invoice', '%m/%Y')
		union all 
		select CONCAT(diskon_8comm*$money/100) as commercePriceByRange,CONCAT(null) as customerPriceByDiscount from kondisi_diskons
		where courier_id = $courier_id
		and STR_TO_DATE(start_month, '%Y-%m') <= STR_TO_DATE('$date_invoice', '%m/%Y')
		and STR_TO_DATE(end_month, '%Y-%m') >= STR_TO_DATE('$date_invoice', '%m/%Y')
		)as a
		");
	
	}
	
	public function getDataInvoiceDetail($invoice){
		
		return DB::table('invoice_file_details')->select('invoice_file_details.*','orders.company_id','invoice_files.courier_id','invoice_files.date_invoice')
		->leftJoin('orders','invoice_file_details.id','=','orders.invoice_file_details_id')
		->leftJoin('invoice_files','invoice_file_details.invoice_files_id','=','invoice_files.id')
		->where('invoice_file_details.invoice_files_id',$invoice)->get();
	
	
	}

	public function getHargaPublish($invoice_detail_id,$courier_id,$customer_code,$date_invoice){

		return DB::select("
		select SUM(publishPriceByDestination) AS publishPriceByDestination, SUM(commercePriceByDestination) AS commercePriceByDestination, SUM(customerPriceByDestination) AS customerPriceByDestination from(

		select harga_publish as publishPriceByDestination, CONCAT(null) as commercePriceByDestination, CONCAT(null) as customerPriceByDestination 
		from price_by_publishes 
		join m_areas on price_by_publishes.area_id = m_areas.id
		join orders on m_areas.tujuan_zip_code = orders.dest_postal_code and m_areas.asal_zip_code = orders.ori_postal_code 
		where kurir = $courier_id and orders.invoice_file_details_id = $invoice_detail_id 

		union all 

		select CONCAT(null) as publishPriceByDestination, harga_publish as commercePriceByDestination,CONCAT(null) as customerPriceByDestination 
		from prices
		join m_areas on prices.area_id = m_areas.id
		join orders on m_areas.tujuan_zip_code = orders.dest_postal_code and m_areas.asal_zip_code = orders.ori_postal_code 
		where kurir = $courier_id and orders.invoice_file_details_id = $invoice_detail_id
		and STR_TO_DATE(start_month, '%Y-%m') <= STR_TO_DATE('$date_invoice', '%m/%Y') and STR_TO_DATE(end_month, '%Y-%m') >= STR_TO_DATE('$date_invoice', '%m/%Y')

		union all 

		select CONCAT(null) as publishPriceByDestination,CONCAT(null) as commercePriceByDestination,harga_publish as customerPriceByDestination
		from price_by_customers
		join m_areas on price_by_customers.area_id = m_areas.id
		join orders on m_areas.tujuan_zip_code = orders.dest_postal_code and m_areas.asal_zip_code = orders.ori_postal_code 
		join customers on price_by_customers.customer_id  = customers.id
		where kurir = $courier_id and orders.invoice_file_details_id = $invoice_detail_id and STR_TO_DATE(start_month, '%Y-%m') <= STR_TO_DATE('$date_invoice', '%m/%Y') and STR_TO_DATE(end_month, '%Y-%m') >= STR_TO_DATE('$date_invoice', '%m/%Y') and customer_code = '$customer_code'

		)as a
		");
	}

	public function getCourierCode($courier_id='')
    {
	    return DB::table('couriers')->select(['courier_code'])->where('couriers.id',$courier_id)->first();
    }

    protected $casts = [
        'date' => 'date:Y-m'
    ];
  
}
