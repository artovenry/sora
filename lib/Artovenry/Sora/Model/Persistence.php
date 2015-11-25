<?
namespace Artovenry\Sora\Model;
trait Persistence{

  /**
   * Bulk insert method for a model class.
   * [CAUTION!!!]  No Callbacks and Validations are executed.
   */
  static function import($column_names, $rows=[], $raise= false){
    $table_name= static::_get_table_name(get_called_class());
    $column_names = join(",", $column_names);
    $rows= array_map(function($row){
      return sprintf('(%s)', join(",", $row));
    }, $rows);
    $rows= rtrim(join(",", $rows), ",");
    try{
      static::raw_query('INSERT INTO :table_name (:column_names) VALUES :rows', compact("table_name", "column_names", "rows"));
    }catch(\PDOException $e){
      if($raise)throw new RecordNotSaved;
      else return false;
    }
    return true;
  }

  static function create($attrs=[], $raise= false){
    $record= static::build($attrs);
    try{
      if(!$persisted= $record->save($raise))
        return $record;
    }catch(RecordNotSaved $e){
      return $record;
    }
    $class= get_called_class();
    if(method_exists($class, "after_create"))
      static::after_create($persisted);
    return $persisted;
  }


  function destroy($raise= false){
    try{
      $this->delete();
    }catch(PDOException $e){
      if($raise) throw new RecordNotDestroyed;
      return false;
    }
    if(method_exists($this, "after_destroy"))
      $this->after_destroy();
    return true;
  }

  static function delete_all(){
    try{
      return static::delete_many();
    }catch(PDOException $e){
      throw new RecordNotDestroyed;
    }
  }

  function update($attrs=[], $raise=false){
    foreach($attrs as $name=>$value)
      $this->set($name, $value);
    return $this->save($raise);
  }


  private function do_before_save(){
    if(method_exists($this, "before_save"))
      return $this->before_save();
  }

  function save($raise=false){
    if(!$this->validate($raise))return false;
    if($this->do_before_save() === false)return false;
    try{
      parent::save();
    }catch(\PDOException $e){
      if($raise)
        throw new RecordNotSaved($this);
      return false;
    }
    return $this;
  }



}
