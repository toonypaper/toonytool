<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$fileUploader = new fileUploader();
	
	$method->method_param("POST","mode,file_ed,json,html");
	$method->method_param("FILE","file");
	
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	html변수 내용 replace
	*/
	$html = str_replace("\n","",$html);
	$html = str_replace("\t","",$html);
	
	/*
	배너공간 이미지 파일 업로드
	*/
	if($mode=="banner_addfile"){
		//이미지 저장 옵션
		$fileUploader->savePath = __DIR_PATH__."upload/siteInformations/";
		$fileUploader->filedotType = "jpg,bmp,gif,png";
		//이미지 저장
		$file_name = "";
		if($file['size']>0){
			$fileUploader->saveFile = $file;
			//경로 및 파일 검사
			$fileUploader->filePathCheck();
			if($fileUploader->fileNameCheck()==false){ echo 'error::not_imgType'; exit; }
			//파일저장
			$file_name = date("ymdtis",mktime())."_".substr(md5($file['name']),4,10).".".$fileUploader->fileNameType();
			$file_name = str_replace(" ","_",$file_name);
			if($fileUploader->fileUpload($file_name)==false){ echo "error::fail_imgSave"; exit; }
			//이전에 첨부한 파일이 있다면 삭제
			if($file_ed!=""){
				$fileUploader->fileDelete($file_ed);
			}
		}
		if($file_ed!=""&&!$file['name']){ $file_name=$file_ed; }
		echo $file_name;
	}
	
	/*
	홈페이지 메인화면 디자인 DB 저장
	*/
	if($mode=="p_add_data"){
		$mysql->query("
			UPDATE toony_admin_siteconfig SET
			ad_site_main='".$html."',ad_site_jsmain='".$json."'
		");
		echo '<!--success::1-->';
	}
	
	/*
	모바일페이지 메인화면 디자인 DB 저장
	*/
	if($mode=="m_add_data"){
		$mysql->query("
			UPDATE toony_admin_siteconfig SET
			ad_msite_main='".$html."',ad_msite_jsmain='".$json."'
		");
		echo '<!--success::2-->';
	}
	
	
?>