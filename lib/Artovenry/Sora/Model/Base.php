<?
namespace Artovenry\Sora\Model;
require "errors.php";
  
abstract class Base extends Model{
  use Validation;
  use Persistence;
  use Transaction;
  
  static function build($attrs=null){
    return parent::create($attrs);
  }


  function is_new(){
    return !isset($this->id);
  }

  static function exists($id){
    try{
      return self::find_one($id);
    }catch(RecordNotFound $e){
      return $false;
    }
  }
}