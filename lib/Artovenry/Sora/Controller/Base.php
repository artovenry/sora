<?
namespace Artovenry\Sora\Controller;

class ActionNotDefined extends \Artovenry\Sora\APPError{}
class RedirectError extends \Artovenry\Sora\APPError{}

abstract class Base{
  use Renderer;

  protected $action_name;
  protected $params;
  protected $method= "GET";

  function __construct($action_name, $params=[], $method= "GET"){
    $this->action_name= $action_name;
    $this->method= $method;
    $this->params= $params;
    if(!is_callable([$this, $action_name]))
      throw new ActionNotDefined;

    Flash::run();
    if(is_callable([$this, "before_action"]))
      $this->before_action();
    $this->$action_name();
    if($this->rendered())exit;
    $this->render();
  }

  function redirect_to($path){
    if(headers_sent())throw new RedirectError;
    $location= $path;
    if(!preg_match("/\Ahttps?:\/\//", $path))
      $location= join("/", [\Artovenry\Sora\App::$root, $path]);  
    header("Location: {$location}");
    exit;
  }

  function flash_errors($errors){
    Flash::set_error($errors->to_a());
  }
  function flash_notice($args){
    Flash::set_message($args);
  }

  function flash_success(){
    Flash::set_success();
  }


}
