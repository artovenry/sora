<?
namespace Artovenry\Sora\Model;
trait Query{
  function take($limit=null){
    if(empty($limit) || $limit == 1)
      return $this->find_one();
    return $this->limit($limit)->find_many();
  }
  function all(){
    return $this->find_many();
  }
  function find($id){
    return $this->find_one($id);
  }
  function find_one($id=null){
    $rs= $this->_create_model_instance(parent::find_one($id));
    if(is_numeric($id) && !empty($id)){
      if(empty($rs))throw new RecordNotFound;
      return $rs;
    }
    return $rs;
  }
}
