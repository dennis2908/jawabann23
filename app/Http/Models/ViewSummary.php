<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewSummary extends Model
{

    protected $table = 'vw_summary_by_file';
    protected $primaryKey = 'id';
    protected $fillable = array('courier_code','courier_name','date_invoice','true_order','false_order','total_order','amount_order');
    public $timestamps = false;
  
}
