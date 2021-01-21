<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class algoritma extends Model
{
    protected $table = 'algoritmas';
    protected $primaryKey = 'id';
    protected $fillable = array('courier_id','name','note');
    public $timestamps = true;
}
