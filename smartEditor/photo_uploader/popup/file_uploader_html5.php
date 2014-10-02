<?php
	include "../../../include/path.info.php";
	
 	$sFileInfo = '';
	$headers = array();
	 
	foreach($_SERVER as $k => $v) {
		if(substr($k, 0, 9) == "HTTP_FILE") {
			$k = substr(strtolower($k), 5);
			$headers[$k] = $v;
		} 
	}
	
	$file = new stdClass;
	$file->name = rawurldecode($headers['file_name']);
	$file->replace_name = rawurldecode(date("YmdHis")."_".$headers['file_name']);
	$file->size = $headers['file_size'];
	$file->content = file_get_contents("php://input");
	
	$filename_ext = strtolower(array_pop(explode('.',$file->name)));
	$allow_file = array("jpg", "png", "bmp", "gif"); 
	
	if(!in_array($filename_ext, $allow_file)) {
		echo "NOTALLOW_".$file->name;
	} else {
		$uploadDir = '../../../upload/smartEditor/';
		if(!is_dir($uploadDir)){
			mkdir($uploadDir, 0777);
		}
		
		$newPath = $uploadDir.iconv("utf-8", "cp949", $file->replace_name);
		
		if(file_put_contents($newPath, $file->content)) {
			$sFileInfo .= "&bNewLine=true";
			$sFileInfo .= "&sFileName=".$file->replace_name;
			$sFileInfo .= "&sFileURL=".__URL_PATH__."upload/smartEditor/".$file->replace_name;
		}
		
		echo $sFileInfo;
	}
?>