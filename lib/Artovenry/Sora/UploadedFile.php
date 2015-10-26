<?
namespace Artovenry\Sora;
//const UPLOAD_MAX_FILESIZE= "8M";
//const POST_MAX_SIZE= "20M";
/*
We support simple file upload request, 
GOOD: name="hoge"
NG:   name="hoge[]"
NG:   name="hoge[daba]"
*/



class UploadError extends \Exception{}
class InvalidUpload extends UploadError{
  var $message= "不正なアップロードが行われました、お手数ですがサイト管理者へご連絡ください";
}

class UploadFailed extends UploadError{
  var $message= "アップロードが正しく処理されませんでした、お手数ですがサイト管理者へご連絡ください";
}
class ExceedsMaxUploadSize extends UploadFailed{
  function __construct($code){
    switch ($code):
      case UPLOAD_ERR_INI_SIZE:
        $max= ini_get("upload_max_filesize");
        parent::__construct("アップロードファイルのサイズは{$max}までです");
        break;
      case UPLOAD_ERR_FORM_SIZE:
        parent::__construct("アップロードファイルのサイズが大きすぎます");
        break;
    endswitch;

  }
}

class UploadedFile{
  private $original_filename;
  private $content_type;
  private $temppath;
  private $error;

  function __construct($hash){
    $this->original_filename= $hash["name"];
    $this->content_type= $hash["type"];
    $this->temppath= $hash["tmp_name"];
    $this->error= $hash["error"];
  }

  function filepath(){
    return $this->temppath;
  }

  function check_errors(){
    if(!is_uploaded_file($this->["temppath"]))
      throw new InvalidUpload;
    $error= $this->error;
    switch ($error):
      case UPLOAD_ERR_OK:
        return;
        break;
      case UPLOAD_ERR_INI_SIZE:
        throw new ExceedsMaxUploadSize(UPLOAD_ERR_INI_SIZE);
        break;
      case UPLOAD_ERR_FORM_SIZE:
        throw new ExceedsMaxUploadSize(UPLOAD_ERR_FORM_SIZE);
        break;
      case UPLOAD_ERR_PARTIAL:
        throw new UploadFailed;
        break;
      case UPLOAD_ERR_NO_FILE:
        throw new UploadFailed;
        break;
      case UPLOAD_ERR_NO_TMP_DIR:
        throw new UploadFailed;
        break;
      case UPLOAD_ERR_CANT_WRITE:
        throw new UploadFailed;
        break;
      case UPLOAD_ERR_EXTENSION:
        throw new UploadFailed;
        break;
      default:
        throw new UploadFailed;
        break;
    endswitch;
  }
}
