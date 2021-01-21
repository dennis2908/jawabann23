<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{

    protected $table = 'order_details';
    protected $primaryKey = 'id';
    protected $fillable = array('invoice_files_id','order_id','order_detail_id','order_header_id','sku_code','sku_description',
                            'qty_order','price','amount_order','qty_ship','amount_ship','remarks',
                            'status','create_time','update_time','insured','sku_parent','order_price');
    public $timestamps = false;
  
    public function order()
    {
        return $this->belongsTo('App\Models\OrderDetail', 'id','order_id');
    }
  
}
