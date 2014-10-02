<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$fileUploader = new fileUploader();
	
	$method->method_param("POST","type,name,memo,void_use,void_link,link,bleft,btop,target,img_ed,start_level,end_level,pop_article,pop_article_txt");
	$method->method_param("FILE","img");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	검사
	*/
	//입력값 검사
	if($type=="new"||$type=="modify"){
		if(trim($name)==""){
			echo 'error::null_name';
			exit;
		}
		$lib->func_method_param_check("idx",$name,"error::not_name");
		if(trim($memo)==""){
			echo 'error::null_memo';
			exit;
		}
		if(trim($btop)==""){
			echo 'error::null_btop';
			exit;
		}
		if(trim($bleft)==""){
			echo 'error::null_bleft';
			exit;
		}
		if($start_level<$end_level){
			echo 'error::not_level';
			exit;
		}
		if($pop_article=="select" && trim($pop_article_txt)==""){
			echo 'error::null_pop_article_txt';
			exit;
		}
	}
	//추가 모드인 경우 추가 입력값 검사
	if($type=="new"){
		$mysql->select("
			SELECT *
			FROM toony_admin_popupconfig
			WHERE name='$name'
		");
		if($mysql->numRows()>0){
			echo 'error::have_name'; exit;
		}
		if(!$img['name']){
			echo 'error::null_img';
			exit;
		}
	}
	
	/*
	팝업 이미지 업로드
	*/
	$fileUploader->savePath = __DIR_PATH__."upload/siteInformations/";
	$fileUploader->file_type_filter = array("jpg","bmp","gif","png");
	if(($type=="modify"||$type=="new")){
		if($img['size']>0){
			$fileUploader->saveFile = $img;
			//경로 및 파일 검사
			$fileUploader->filePathCheck();
			if($fileUploader->fileNameCheck()==false){ echo 'error::not_imgType'; exit; }
			//파일저장
			$img_name = date("ymdtis",mktime())."_".substr(md5($img['name']),4,10).".".$fileUploader->fileNameType();
			$img_name = str_replace(" ","_",$img_name);
			if($fileUploader->fileUpload($img_name)==false){ echo 'error::fail_imgSave'; exit; }
			//이전에 첨부한 파일이 있다면 삭제
			if($img_ed){
				$fileUploader->fileDelete($img_ed);
			}
		}else{
			$img_name = $img_ed;	
		}
	}
	
	/**************************************************
	추가 모드인 경우
	**************************************************/
	if($type=="new"){
		/*
		DB수정
		*/
		$mysql->query("
			INSERT INTO toony_admin_popupconfig
			(name,img,memo,void_use,void_link,link,bleft,btop,target,start_level,end_level,pop_article,pop_article_txt,regdate)
			VALUES
			('$name','$img_name','$memo','$void_use','$void_link','$link','$bleft','$btop','$target','$start_level','$end_level','$pop_article','$pop_article_txt',now())
		");
		
		/*
		완료 후 리턴
		*/
		echo 'success::1';
	}
	
	/**************************************************
	수정 모드인 경우
	**************************************************/
	if($type=="modify"){
		/*
		DB수정
		*/
		$mysql->query("
			UPDATE toony_admin_popupconfig SET
			img='$img_name',bleft='$bleft',btop='$btop',target='$target',link='$link',void_link='$void_link',void_use='$void_use',memo='$memo',start_level='$start_level',end_level='$end_level',pop_article='$pop_article',pop_article_txt='$pop_article_txt'
			WHERE name='$name'
		");
		
		/*
		완료 후 리턴
		*/
		echo 'success::2';
	
	/**************************************************
	삭제 모드인 경우
	**************************************************/
	}else if($type=="delete"){
		/*
		DB삭제
		*/
		$mysql->query("
			DELETE FROM toony_admin_popupconfig
			WHERE name='$name'
		");
		
		/*
		완료 후 리턴
		*/
		echo 'success::3';
	}
	
?>