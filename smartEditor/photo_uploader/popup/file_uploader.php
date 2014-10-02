<?php
include "../../../include/path.info.php";

// default redirection
$url = $_REQUEST["callback"].'?callback_func='.$_REQUEST["callback_func"];
$bSuccessUpload = is_uploaded_file($_FILES['Filedata']['tmp_name']);

// SUCCESSFUL
if(bSuccessUpload) {
	$tmp_name = $_FILES['Filedata']['tmp_name'];
	$name = $_FILES['Filedata']['name'];
	$replace_name = date("YmdHis")."_".$_FILES['Filedata']['name'];
	
	$filename_ext = strtolower(array_pop(explode('.',$name)));
	$allow_file = array("jpg", "png", "bmp", "gif");
	
	if(!in_array($filename_ext, $allow_file)) {
		$url .= '&errstr='.$name;
	} else {
		$uploadDir = '../../../upload/smartEditor/';
		if(!is_dir($uploadDir)){
			mkdir($uploadDir, 0777);
		}
		
		$newPath = $uploadDir.urlencode($replace_name);
		
		@move_uploaded_file($tmp_name, $newPath);
		
		$url .= "&bNewLine=true";
		$url .= "&sFileName=".urlencode(urlencode($replace_name));
		$url .= "&sFileURL=../upload/smartEditor/".urlencode(urlencode($replace_name));
	}
}
// FAILED
else {
	$url .= '&errstr=error';
}
	
header('Location: '. $url);
?>