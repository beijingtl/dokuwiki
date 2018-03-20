<?php
if(!defined('DOKU_INC')) define('DOKU_INC', dirname(__FILE__).'/');
require_once(DOKU_INC.'inc/init.php');

$hostpath=getBaseURL(false);
$attachDir='/data/media/editor/';//上传文件保存路径，结尾不要带/
//$dirType=1;//1:按天存入目录 2:按月存入目录 3:按扩展名存目录  建议使用按天存
$maxAttachSize = 2*1024*1024;  //最大上传大小，默认是2M
//$upExt="jpg,jpeg,gif,png";//上传扩展名

/*
$_FILES=    [editormd-image-file] => Array
    (
        [name] => mavenpro_submit_svn01.png
        [type] => image/png
        [tmp_name] => F:\wamp\tmp\php4B03.tmp
        [error] => 0
        [size] => 7229
    )
*/
function upEditorImg(){
    global $hostpath, $attachDir, $maxAttachSize;
    //获取文件的大小
    $file_size=$_FILES['editormd-image-file']['size'];
    //echo "$file_size $maxAttachSize";
    if($file_size > $maxAttachSize) {
        //echo "文件过大，不能上传大于2M的文件";
        echo '{"success":0,"message":"不能上传大于2M的文件"}';
        return false;
    }

    //获取文件类型
    $file_type=$_FILES['editormd-image-file']['type'];
    //echo $file_type;
    if($file_type!="image/jpeg" && $file_type!='image/pjpeg' && $file_type!="image/png") {
        //echo "文件类型只能为jpg格式";
        echo '{"success":0,"message":"图片格式异常"}';
        return false;
    }

    //判断是否上传成功（是否使用post方式上传）
    if(is_uploaded_file($_FILES['editormd-image-file']['tmp_name'])) {
        //把文件转存到你希望的目录（不要使用copy函数）
        $uploaded_file=$_FILES['editormd-image-file']['tmp_name'];

        //我们给每个用户动态的创建一个文件夹
        $save_path=$_SERVER['DOCUMENT_ROOT'].$hostpath.$attachDir;
        //判断该用户文件夹是否已经有这个文件夹
        if(!file_exists($save_path)) {
            mkdir($save_path);
        }

        //$move_to_file=$save_path."/".$_FILES['editormd-image-file']['name'];
        $file_true_name=$_FILES['editormd-image-file']['name'];
        $move_file_name=time().rand(1,1000).substr($file_true_name,strrpos($file_true_name,"."));
        $move_to_file=$save_path.$move_file_name;
        //echo "$uploaded_file   $move_to_file";
        if(move_uploaded_file($uploaded_file,iconv("utf-8","gb2312",$move_to_file))) {
            //echo $_FILES['editormd-image-file']['name']."上传成功";
            //echo '{"success":1,"message":"上传成功", "url":"'.$hostpath.$attachDir.$move_file_name.'"}';
            $result=array(
              'success'=> 1,
              'message'=>'上传成功',
              'url'=>$hostpath.$attachDir.$move_file_name
            );
            echo json_encode($result);
        } else {
            //echo "上传失败";
            echo '{"success":0,"message":"服务器保存文件失败"}';
        }
    } else {
        //echo "上传失败";
        echo '{"success":0,"message":"上传失败"}';
        return false;
    }
}

//$_POST= [screenshots] => data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAI0AAACcCAYAAABC1CibAAAL6UlEQVR4Ae2dbUiU6RrH...
function upEditorScreenshots(){
    global $hostpath, $attachDir, $maxAttachSize;

    $content = $_POST['screenshots'];

    if (preg_match('/^data:image\/(\w+);base64,(\S+)/', $content, $result)) {
        $file_type = $result[1];
        $base64data = $result[2];

        //echo "$file_type $base64data";
        $save_path = $_SERVER['DOCUMENT_ROOT'].$hostpath.$attachDir;
        if (!is_dir($save_path)) {
            mkdir($save_path, 0777);
        }

        $filedata = base64_decode($base64data);
        $filename = time().rand(1,1000).".".$file_type;
        if (!file_put_contents($save_path . $filename, $filedata)) {
            echo '{"success":0,"message":"服务器保存文件失败"}';
            return false;
        }
        unset($filedata);

        //$FILE = new File($file['save_path'] . $file['name']);
        //$file['size'] = $FILE->getSize();
        //$file['saveName'] = $file['save_path'] . $file['name'];
        //$file['build_path'] = DS . date('Ymd') . DS . $file['name'];
        //
        //$FILE->setUploadInfo($file);
        //return $FILE;
        echo '{"success":1,"message":"上传成功", "url":"'.$hostpath.$attachDir.$filename.'"}';
        return true;
    } else {
        echo '{"success":0,"message":"图片格式异常"}';
        return false;
    }
}

//print_r($_POST);
//print_r($_FILES);

if(isset($_FILES['editormd-image-file'])){
    upEditorImg();
    exit();
}

if(isset($_POST['screenshots'])){
    upEditorScreenshots();
    exit();
}
?>
