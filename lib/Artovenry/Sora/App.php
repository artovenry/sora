<?
namespace Artovenry\Sora;

require "functions.php";
require "constants.php";

class APPError extends \Exception{}
class ControllerNotFound extends APPError{}
class TokenNotSent extends APPError{}

class App{
  static $path= "";
  static $root= "";

  static function run(){
    try{
      new self;
    }catch(APPError $e){
      if(defined("ART_ENV") && ART_ENV === "production")
        exit;
    }
  }
  private function __construct(){
    $this->load_paths();
    $this->load_controllers();
    $this->load_globals();
    Route::run();
    $this->delegate_to_controller();
    exit;
  }

  function delegate_to_controller(){
    $ticket= Route::resolve($this->request_uri, $this->method);
    $controller_name= $ticket["controller_name"];
    $action_name= $ticket["action_name"];
    $params= $ticket["params"];
    $method= $ticket["method"];
    $params= $this->parse_params($params, $method);
    new $controller_name($action_name, $params, $method);
  }

  function parse_params($params, $method){
    if($method == "GET")
      $params= array_merge($params, $_GET);
    if($method == "POST")
      $params= array_merge(array_merge($params, $_POST));
    if(!empty($_FILES))
      $params= array_merge($params, UploadedFile::parse());
    if($method == "POST" && !isset($params[CSRF_TOKEN]))
      throw new TokenNotSent;
    return $params;
  }

  function load_globals(){
    $request_uri= $_SERVER["REQUEST_URI"];
    $method= $_SERVER["REQUEST_METHOD"];
    $query_string= $_SERVER["QUERY_STRING"];

    $root= str_replace("/", '\/', self::$root);
    $request_uri= str_replace($query_string, "", $request_uri);

    if(!preg_match("/\A({$root})(\/?(.*))\z/", $request_uri, $matches))
      throw new APPError("Requested URL is invalid.");
    if(!preg_match("/^(GET|POST)$/", $method))
      throw new APPError("Requested HTTP method is invalid.");
    $request_uri= "/" . ltrim($matches[2], "/");

    $this->request_uri= $request_uri;
    $this->method= $method;
  }

  function load_paths(){
    if(!defined("ART_SORA_APP"))
      throw new APPError;
    self::$path= ART_SORA_APP;
    self::$root= defined("ART_SORA_ROOT")? ART_SORA_ROOT : ROOT;
  }

  function load_controllers(){
    spl_autoload_register(function($classname){
      if(!preg_match("/\A(.+)Controller\z/", $classname, $matches))return;
      $name= to_lowercase($matches[1]) . "_controller.php";
      if(!is_readable($path= join("/", [App::$path, CONTROLLERS, $name])))
        throw new ControllerNotFound;
      require $path;
    });
  }
}