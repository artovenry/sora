<?
namespace Artovenry\Sora\Helpers;

trait HelpersNeedsToBeRefactored{
  function url_for($path){
  return ART_SORA_ROOT . $path;
  }

  function csrf_token_field(){
  $el= '<input type="hidden" name="%s" value="%s">';
  return sprintf($el,Artovenry\Sora\CSRF_TOKEN, $this->csrf_token());
  }

  function csrf_token($json=false){
  $token= wp_create_nonce(Artovenry\Sora\CSRF_TOKEN);
  return $json? json_encode($token): $token;
  }

  function radio_field_for($record, $model_name, $attr, $value, $label){
    $el= '<input type="radio" id="%s" name="%s" value="%s" %s> %s';
    $id= "{$attr}-{$value}";
    $name= "{$model_name}[{$attr}]";
    $checked= $record->$attr == $value? 'checked="checked"': "";
    return sprintf($el, $id, $name, $value, $checked, $label);
  }
}