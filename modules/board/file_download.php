<?php
	include "../../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$method = new methodController();
	$method->method_param("GET","board_id,file");
	$file = urldecode($file);
	$filepath = __DIR_PATH__."modules/board/upload/".$board_id."/".$file;
	$filename = iconv("UTF-8","EUC-KR",$file);
	Header("Content-Type:application/octet-stream");
	Header("Content-Disposition:attachment;; filename=$filename");
	Header("Content-Transfer-Encoding:binary");
	Header("Content-Length:".(string)(filesize($filepath)));
	Header("Cache-Control:Cache,must-revalidate");
	Header("Pragma:No-Cache");
	Header("Expires:0");
	$fp=fopen($filepath,"rb");
	while(!feof($fp))
	{
	echo fread($fp,100*1024);
	flush();
	}
	fclose($fp);
?>
