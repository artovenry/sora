<?
namespace Artovenry\Sora;

class RouteNotFound extends APPError{}

class Route{
  private static $route;
  private static $request_uri;

  static function run(){
    require_once join("/", [App::$path, CONFIG]) . "/route.php";
  }

  static function draw($map){
    new self($map);
  }

  static function dump(){
    echo "path\tcontroller\taction\tmethod\n";
    foreach(self::$route as $item){
      echo join("\t", array_values($item));
      echo "\n";
    }
  }

  static function ask(){
    list($path, $query)= explode("?", ltrim(self::$request_uri, "/"), 1);
    parse_str($query, $query);
    return ["path"=>$path, "query"=>$query];
  }

  static function resolve($request_uri, $method){
    self::$request_uri =$request_uri;

    foreach(self::$route as $item){
      $path= str_replace("/", '\/', $item["path"]);
      if($item["method"] != $method)continue;
      if(!preg_match("/\A{$path}\z/", $request_uri, $matches))continue;
      $params= [];
      //foreach($item["params"] as $_item)
      //  $params[$_item]= $matches[$_item];
      foreach($matches as $key=>$_item)
        if(is_string($key))
          $params[$key]= $matches[$key];
      return [
        "controller_name"=>$item["controller"],
        "action_name"=>$item["action"],
        "params"=>$params,
        "method"=>$method
      ];
    }
    throw new RouteNotFound;
  }

  private function __construct($map){
    $this->map= $map;
    self::$route= [];
    foreach($this->map as $top)$this->parse($top);
  }

  function parse($args){
    $path= $args[0];
    if($_action= $this->is_action_name($args[1])){
      $controller_name= $_action[0];
      $action_name= $_action[1];
      $method= (isset($args[2]) && $args[2] == "post") ? "POST": "GET";
      $this->append_route($path, $controller_name, $action_name, $method);
    }else{
      $hash= $args[1];
      if(isset($hash["get"])){
        if($name= $this->is_action_name($hash["get"]))
          $this->append_route($path, $name[0],$name[1], "GET");
      }
      if(isset($hash["post"])){
        if($name= $this->is_action_name($hash["post"]))
          $this->append_route($path, $name[0],$name[1], "POST");
      }
      if(isset($hash["children"]) && is_array($hash["children"])){
        $children = $hash["children"];
        foreach($children as &$item){
          $item[0]= join("/", [$path, $item[0]]);
          $this->parse($item);
        }
      }
    }
  }

  private function append_route(){
    $args= func_get_args();
    self::$route[]=[
      "path"=> array_shift($args) . "[/\?]?",
      "controller"=>array_shift($args),
      "action"=>array_shift($args),
      "method"=>array_shift($args)
    ];
  }

  private function is_action_name($str){
    if(!is_string($str))return false;
    if(!preg_match("/\A((.+)Controller)#(.+)\z/", $str, $matches))return false;
    return [$matches[1], $matches[3]];
  }


}