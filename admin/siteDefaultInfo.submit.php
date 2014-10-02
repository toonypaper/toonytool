<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$fileUploader = new fileUploader();
	
	$method->method_param("POST","site_name,site_title,use_msite,use_www,ad_email,ad_phone,del_pavicon,pavicon_ed");
	$method->method_param("FILE","pavicon");
	$method->method_param("FILE","logo");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	변수 처리
	*/
	if($use_msite=="checked"){
		$use_msite = "Y";
	}else{
		$use_msite = "N";
	}
	if($use_www=="checked"){
		$use_www = "Y";
	}else{
		$use_www = "N";
	}
	
	/*
	검사
	*/
	if(trim($site_name)==""){
		echo 'error::null_site_name';
		exit;
	}
	if(trim($site_title)==""){
		echo 'error::null_site_title';
		exit;
	}
	if(trim($ad_email)==""){
		echo 'error::null_ad_email';
		exit;
	}
	if(trim($ad_phone)==""){
		echo 'error::null_ad_phone';
		exit;
	}
	
	/*
	파비콘 업로드
	*/
	$fileUploader->savePath = __DIR_PATH__."upload/siteInformations/";
	$fileUploader->file_type_filter = array("ico");
	if($pavicon['size']>0){
		$fileUploader->saveFile = $pavicon;
		//경로 및 파일 검사
		$fileUploader->filePathCheck();
		if($fileUploader->fileNameCheck()==false){ echo 'error::not_paviconType'; exit; }
		//파일저장
		$pavicon_name = date("ymdtis",mktime())."_".substr(md5($pavicon['name']),4,10).".".$fileUploader->fileNameType();
		$pavicon_name = str_replace(" ","_",$pavicon_name);
		if($fileUploader->fileUpload($pavicon_name)==false){ echo 'error::fail_paviconSave'; exit; }
		//이전에 첨부한 파일이 있다면 삭제
		if($pavicon_ed&&$del_pavicon!="checked"){
			$fileUploader->fileDelete($pavicon_ed);
		}
	}
	
	/*
	이전 파비콘 삭제
	*/
	if($del_pavicon=="checked"){ $fileUploader->fileDelete($pavicon_ed); }
	if($pavicon_ed!=""&&!$pavicon['name']&&$del_pavicon!="checked"){ $pavicon_name=$pavicon_ed; }
	
	/*
	로고 업로드
	*/
	if($logo['size']>0){
		$fileUploader->savePath = __DIR_PATH__."upload/siteInformations/";
		$fileUploader->file_type_filter = array("png","gif","jpg","bmp");
		$fileUploader->saveFile = $logo;
		//경로 및 파일 검사
		$fileUploader->filePathCheck();
		if($fileUploader->fileNameCheck()==false){ echo 'error::not_logoType'; exit; }
		//파일저장
		$logo_name = date("ymdtis",mktime())."_".substr(md5($logo['name']),4,10).".".$fileUploader->fileNameType();
		$logo_name = str_replace(" ","_",$logo_name);
		if($fileUploader->fileUpload($logo_name)==false){ echo 'error::fail_logoSave'; exit; }
		//이전에 첨부한 파일이 있다면 삭제
		if($logo_ed&&$del_logo!="checked"){
			$fileUploader->fileDelete($logo_ed);
		}
	}else{
		$logo_name = $site_config['ad_logo'];
	}
	
	/*
	DB수정
	*/
	$mysql->query("
		UPDATE toony_admin_siteconfig
		SET ad_site_name='$site_name', ad_site_title='$site_title', ad_email='$ad_email', ad_phone='$ad_phone', ad_pavicon='$pavicon_name', ad_logo='$logo_name', ad_use_msite='$use_msite'
	");
	
	/*
	완료 후 리턴
	*/
	echo 'success::1';
	
	
?>