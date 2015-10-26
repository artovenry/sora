<?
namespace Artovenry\Sora\Controller;

class Flash{
  const SESSION_NAME= "art_flash";
  protected static $flash_error= null;
  protected static $flash_message= null;

  static function run(){
    session_name(self::SESSION_NAME);
    session_start();
    if(!empty($_SESSION["error"]))
      static::$flash_error= $_SESSION["error"];
    unset($_SESSION["error"]);
    if(!empty($_SESSION["message"]))
      static::$flash_message= $_SESSION["message"];
    unset($_SESSION["message"]);
  }

  static function errors(){
    return static::$flash_error;
  }
  static function messages(){
    return static::$flash_message;
  }
  static function set_error($error){
    $_SESSION["error"]= $error;
  }
  static function set_message($args){
    $_SESSION["message"]= $args;
  }
  static function set_success($message= "処理を完了しました"){
    $_SESSION["message"]["success"]= $message;
  }
}