<?php 
namespace Ceb\Models;

use Illuminate\Support\Facades\Validator;

/**
 * Items 
 *
 * @author Kamaro Lambert 
 * 
 */
class Item extends Model {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'items';
    protected $fillable = array('name', 'description','price','quantity');
    /**
     * Validation rules when adding,updating an item
     * @var type 
     */
    private $_validationRules = array(
        'name' => 'required|min:4',
        'description' => 'required',
        'price'		  => 'required|numeric',
        'quantity'	  => 'required|numeric'
    );

    public function insert($data) {
        $validator = Validator::make($data, $this->_validationRules);
        if ($validator->fails()) {
            return $validator;
        }
        self::create($data);
        return TRUE;
    }
    public function modify($data) {
        $validationRules = array_merge(array('id' => 'required'), $this->_validationRules);
        $validator = Validator::make($data, $validationRules);
        if ($validator->fails()) {
            return $validator;
        }
        $record = self::find($data['id']);
        if (!empty($record)) {
            foreach (array_keys($this->_validationRules) as $value) {
                $record->$value = $data[$value];
            }
            $record->save();
            return TRUE;
        }
        return FALSE;
    }
    public function remove($data) {
        $validationRules = array_merge(array('id' => 'required'));
        $validator = Validator::make($data, $validationRules);
        if ($validator->fails()) {
            return $validator;
        }
        $record = self::find($data['id']);
        if (!empty($record)) {
            $record->delete();
            return TRUE;
        }
        return FALSE;
    }
}

 ?>