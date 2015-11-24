<?
namespace Artovenry\Sora;
abstract class Helper{
    use Helpers\HelpersNeedsToBeRefactored;
	static function helpers(){
    $helper_klass= new \ReflectionClass(get_called_class());
    $rs= [];
    foreach($helper_klass->getMethods(\ReflectionMethod::IS_PUBLIC) as $item){
    	if(
    		$item->isStatic() ||
    		$item->isConstructor() ||
    		$item->isAbstract()
    	)continue;
    	$helper_name= "_" . $item->name;
    	$rs[$helper_name]= $item->getClosure(new static);
    }
		return $rs;
  }
}