<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$fileUploader = new fileUploader();
	$validator = new validator();
	
	$method->method_param("POST","site_name,site_title,use_msite,ad_email,ad_phone,del_pavicon,pavicon_ed,logo_ed,use_smtp,smtp_server,smtp_port,smtp_id,smtp_pwd");
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
	if($use_smtp=="checked"){
		$use_smtp = "Y";
	}else{
		$use_smtp = "N";
	}
	
	/*
	검사
	*/
	$validator->validt_null("site_name","");
	$validator->validt_null("site_title","");
	$validator->validt_email("ad_email",1,"");
	$validator->validt_null("ad_phone","");
	if($use_smtp=="Y"){
		$validator->validt_null("smtp_server","");
		$validator->validt_number("smtp_port",1,"");
		$validator->validt_null("smtp_id","");
		$validator->validt_null("smtp_pwd","");
	}else{
		$validator->validt_number("smtp_port",0,"");
	}
	
	/*
	파비콘 업로드
	*/
	$fileUploader->savePath = __DIR_PATH__."upload/siteInformations/";
	$fileUploader->filedotType = "ico";
	$pavicon_name = "";
	if($pavicon['size']>0){
		$fileUploader->saveFile = $pavicon;
		//경로 및 파일 검사
		$fileUploader->filePathCheck();
		if($fileUploader->fileNameCheck()==false){
			$validator->validt_diserror("pavicon","지원되지 않는 파비콘 파일입니다.");
		}
		//파일저장
		$pavicon_name = date("ymdtis",mktime())."_".substr(md5($pavicon['name']),4,10).".".$fileUploader->fileNameType();
		$pavicon_name = str_replace(" ","_",$pavicon_name);
		if($fileUploader->fileUpload($pavicon_name)==false){
			$validator->validt_diserror("pavicon","파비콘 파일 저장에 실패 하였습니다.");
		}
		//이전에 첨부한 파일이 있다면 삭제
		if($pavicon_ed&&$del_pavicon!="checked"){
			$fileUploader->fileDelete($pavicon_ed);
		}
	}
	
	/*
	이전 파비콘 삭제
	*/
	if($del_pavicon=="checked"){
		$fileUploader->fileDelete($pavicon_ed);
	}
	if($pavicon_ed!=""&&!$pavicon['name']&&$del_pavicon!="checked"){
		$pavicon_name=$pavicon_ed;
	}
	
	/*
	로고 업로드
	*/
	if($logo['size']>0){
		$fileUploader->savePath = __DIR_PATH__."upload/siteInformations/";
		$fileUploader->filedotType = "png,gif,jpg,bmp";
		$fileUploader->saveFile = $logo;
		//경로 및 파일 검사
		$fileUploader->filePathCheck();
		if($fileUploader->fileNameCheck()==false){
			$validator->validt_diserror("logo","지원되지 않는 로고 이미지입니다.");
		}
		//파일저장
		$logo_name = date("ymdtis",mktime())."_".substr(md5($logo['name']),4,10).".".$fileUploader->fileNameType();
		$logo_name = str_replace(" ","_",$logo_name);
		if($fileUploader->fileUpload($logo_name)==false){
			$validator->validt_diserror("logo","로고 이미지 저장에 실패 하였습니다.");
		}
		//이전에 첨부한 파일이 있다면 삭제
		if($logo_ed){
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
		SET ad_site_name='$site_name',ad_site_title='$site_title',ad_email='$ad_email',ad_phone='$ad_phone',ad_pavicon='$pavicon_name',ad_logo='$logo_name',ad_use_msite='$use_msite',ad_use_smtp='$use_smtp',ad_smtp_server='$smtp_server',ad_smtp_port='$smtp_port',ad_smtp_id='$smtp_id',ad_smtp_pwd='$smtp_pwd'
	");
	
	/*
	완료 후 리턴
	*/
	$validator->validt_success("성공적으로 수정 되었습니다.","admin/?p=siteDefaultInfo");
?>