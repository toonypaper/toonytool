<?php
	include "../../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("GET","board_id,file");
	
	$file = urldecode($file);
	$filepath = __DIR_PATH__."modules/board/upload/".$board_id."/".$file;
	$filename = iconv("UTF-8","EUC-KR",$file);
	
	/* 
	게시물 정보 로드 
	*/ 
	$mysql->select("
		SELECT *
		FROM toony_module_board_data_$board_id
		WHERE file1='$file' OR file2='$file'
	"); 
	$file1_name = $mysql->fetch("file1");
	$file2_name = $mysql->fetch("file2");
	
	/* 
	첨부된 파일이 file1 인지 file2 인지 확인 
	*/ 
	if($file1_name==$file){ 
		$file_tar = 'file1';
		$td_tar = 'file1_cnt';
	}else if($file2_name==$file){
		$file_tar = 'file2';
		$td_tar = 'file2_cnt';
	}else{ 
		exit; 
	} 
	
	/* 
	파일 다운로드 횟수 증가 
	*/ 
	$mysql->query(" 
		UPDATE toony_module_board_data_$board_id 
		SET $td_tar=$td_tar+1 
		WHERE $file_tar='$file' 
	"); 
	
	/*
	파일을 다운로드 받을 수 있도록 스트림
	*/
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
