<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends MyModel
{

    protected $table = 'roles';
    protected $primaryKey = 'id';
    public $fillable = array('role_code','role_name');
    public $validationField = array('role_code');

    public $timestamps = true;
 
    /**
     * Get the user that owns the role.
     */
    public function users()
    {
        return $this->belongsTo('App\Models\User','role_id');
    }
  
}
