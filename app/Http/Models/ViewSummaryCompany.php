<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewSummaryCompany extends Model
{

    protected $table = 'vw_summary_by_company';
    protected $primaryKey = 'id';
    protected $fillable = array('invoice_files_date_invoice','couriers_courier_code',
        'couriers_courier_name','company_id','company_name','total_awb'
    );
    public $timestamps = false;
  
}
