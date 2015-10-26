<?
namespace Artovenry\Sora\Model;

trait Validation{
  use ValidatorHelper;
  protected $error;

  function validate($raise= false){
    $this->do_validation($raise);
    return $this->valid();
  }
  function valid(){
    return $this->error->is_empty();
  }
  function invalid(){
    return !$this->valid();
  }
  function errors(){
    return $this->error;
  }

  function set_error($name, $message){
    $this->error->append($name, $message);
  }

  private function do_before_validation(){
    if(method_exists($this, "before_validation"))
      return $this->before_validation();
  }
  private function do_validation($raise= false){
    $this->error= new Error($this);
    if($this->do_before_validation() === false)
      return;
    foreach(get_class_methods(get_class($this)) as $method){
      if(preg_match("/\Avalidates_(.+)/", $method, $matches))
        $this->$method($this->$matches[1]);
    }
    if($this->invalid() && $raise)
      throw new RecordNotValid($this);
  }
  
}