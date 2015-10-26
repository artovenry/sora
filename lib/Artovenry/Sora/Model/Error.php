<?
namespace Artovenry\Sora\Model;

/*
Error#to_a:

results:
[
	"params"=>hash of sent post parameters

	"data"=>[ <-- error messages
		"value"=>
		"messages"=>[]
	]
]
*/



class Error{
	const BASE= "base";
	private $record;
	private $data= [];

	function __construct($record=null){
		$this->record= $record;
	}
	function to_a(){
		return [
			"params"=>$this->record->as_array(),
			"data"=>$this->data
		];
	}
	function is_empty(){
		return empty($this->data);
	}
	function append($name, $message){
		if(isset($this->data[$name])){
			array_push($this->data[$name]["messages"], $message);
		}else{
			$this->data[$name]= [
				"value"=> ($name == self::BASE)? null: $this->record->$name,
				"messages"=>[$message]
			];
		}
	}
	function clear(){
		$this->data= [];
	}
	function append_base($message){
		$this->append(self::BASE, $message);
	}
}
