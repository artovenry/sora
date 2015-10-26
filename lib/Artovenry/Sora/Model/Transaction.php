<?
namespace Artovenry\Sora\Model;

trait Transaction{
  static function transaction($block){
    self::start_transaction();
    try{
      call_user_func($block);
    }catch(\Exception $e){
      self::rollback_transaction();
      throw $e;
    }
    self::commit_transaction();
  }

  static function start_transaction(){
    static::get_db()->beginTransaction();
  }

  static function rollback_transaction(){
    static::get_db()->rollback();
  }

  static function commit_transaction(){
    static::get_db()->commit();
  }
}