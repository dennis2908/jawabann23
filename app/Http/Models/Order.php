<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $fillable = array('invoice_files_id','invoice_file_details_id','order_header_id','company_id',
    'company_name','order_no','order_date','due_date','status','courier_id','awb_no','insured',
    'insured_by_id','cod','payment_status','create_by','dest_name','dest_address1','dest_address2',
    'dest_province','dest_city','dest_area','dest_sub_area','dest_postal_code','dest_village',
    'dest_remarks','ori_name','ori_address1','ori_address2','ori_province','ori_city','ori_area',
    'ori_sub_area','ori_postal_code','ori_village','ori_remarks','fulfillment_center_id','order_source',
    'create_time','update_time','dest_phone','dest_mobile','dest_phone2','dest_email','ori_country','dest_country','special_packaging',
    'promo_code','order_amount',
    'shipping_amount','insurance_amount');
    public $timestamps = false;
 
  
    public function details()
    {
        return $this->hasMany('App\Models\OrderDetail', 'order_id');
    }
 
    public function invoice()
    {
        return $this->belongsTo('App\Models\InvoiceDetail','invoice_file_details_id','id');
    }
  
}
