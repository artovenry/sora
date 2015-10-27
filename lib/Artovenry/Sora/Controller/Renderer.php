<?
namespace Artovenry\Sora\Controller;
use Artovenry\Haml;

trait Renderer{
  protected $rendered= false;
  var $layout= "default";

  function rendered(){
    return $this->rendered;
  }


  function render_template(){
    list($name, $args)= $this->parse_args(func_get_args());
    Haml::run(join("/", [\Artovenry\Sora\App::$path, \Artovenry\Sora\VIEW]));
    Haml::render_template($name, $args);
    $this->rendered= true;
  }


  function render(){
    list($name, $args)= $this->parse_args(func_get_args());
    Haml::run(join("/", [\Artovenry\Sora\App::$path, \Artovenry\Sora\VIEW]));

    Haml::render_view($name, $this->layout, $args);
    $this->rendered= true;
  }

  private function parse_args($_args){
    $name= null;
    $args= [];
    if(!empty($_args)){
      if(is_array($_args[0])){
        $args= $_args[0];
      }else{
        $name= $_args[0];
        if(is_array($_args[1]))$args= $_args[1];
      }
    }
    if(empty($name))$name= $this->template_path();
    $args= array_merge($args, ["controller"=>$this]);
    return [$name, $args];
  }


  private function template_path(){
    $cls= to_lowercase(get_class($this));
    $name= str_replace("_controller","", $cls);
    return join("/", [
      $name,      
      $this->action_name
    ]);
  }
}



