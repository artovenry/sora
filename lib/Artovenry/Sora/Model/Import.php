<?
namespace Artovenry\Sora\Model;
  const NUM_OF_ROWS_FOR_INSERT = 1000;

/**
 * Bulk insert for a model class.
 * [CAUTION!!!]  No Callbacks and Validations are executed.
 */
trait Import{
  private static function bulk_insert($column_names, $rows){
    $table_name= static::_get_table_name(get_called_class());
    $column_names = join(",", $column_names);
    $rows= array_map(function($row){
      return sprintf('(%s)', join(",", $row));
    }, $rows);
    $rows= rtrim(join(",", $rows), ",");
    return static::raw_query('INSERT INTO :table_name (:column_names) VALUES :rows', compact("table_name", "column_names", "rows"));
  }

  static function import($column_names, $rows=[], $raise= false){
    try{
      static::transaction(function() use($column_names, $rows){
        foreach(array_chunk($rows, NUM_OF_ROWS_FOR_INSERT) as $item)
          static::bulk_insert($column_names, $item);
      });
    }catch(\PDOException $e){
      if($raise)throw new RecordNotSaved;
      else return false;
    }
    return true;
  }
}
