<?
namespace Artovenry\Sora\Model;

class RecordError extends \Exception{
  function __construct($record=null){
    $this->record= $record;
  }
}
class RecordNotSaved extends RecordError{}
class RecordNotDestroyed extends RecordError{}
class RecordNotFound extends RecordError{}
class RecordNotValid extends RecordError{}