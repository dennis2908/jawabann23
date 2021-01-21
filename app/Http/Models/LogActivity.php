<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{

    protected $table = 'logs_activity';
    protected $primaryKey = 'id';
    protected $fillable = array('name','email','action','table','data');
    public $timestamps = true;
  
  
}
