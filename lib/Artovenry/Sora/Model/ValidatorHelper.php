<?
namespace Artovenry\Sora\Model;

trait ValidatorHelper{
  protected function is_duplicated($attr_name, $val, $options=[]){
    $scopes= empty($options["scopes"])? []: $options["scopes"];
    $record= $this;
    $selected= array_reduce(
      $scopes,
      function($rs,$item) use($record){
        return $rs->where($item, $record->$item);
      },
      self::factory(get_class($this))
    )->where($attr_name, $val)->take();

    if($selected == false)return false;
    if($this->is_new())return true;
    if($selected->id == $this->id)return false;
    return true;
  }
  
}
