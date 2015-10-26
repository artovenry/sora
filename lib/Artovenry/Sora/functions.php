<?
if(!function_exists("to_lowercase")){
  function to_lowercase($str){
    $str = preg_replace('/([A-Z])/', '_$1', $str);
    $str = strtolower($str);
    $str = str_replace('\\_', '/', $str);
    $str = ltrim($str, '_');
    return $str;  
  }
}


if(!function_exists("to_uppercase")){
  function to_uppercase($str){
    $words= explode("_", $str);
    foreach($words as &$word)$word= ucwords($word);
    return join("",$words);  
  }
}