<?php

namespace Ceb\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
      /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'leaves';
    protected $fillable = array('user_id', 'start', 'end','days','phone','backup', 'status');
    public static $rejected = 'rejected';
    public static $approved = 'approved';
    public static $applied = 'applied';
    
    // DEFINE RELATIONSHIPS
    public function user()
    {
        return $this->belongsTo('Ceb\Models\User');
    }

}
