<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceTemplete extends Model
{

    protected $table = 'invoice_templete';
    protected $primaryKey = 'id';
    protected $fillable = array('courier_id','templete_name');
    public $timestamps = true;
  
  
    public function courier()
    {
        return $this->hasOne('App\Models\Courier','id','courier_id');
    }
}
