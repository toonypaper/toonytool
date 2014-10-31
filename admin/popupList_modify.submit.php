<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$fileUploader = new fileUploader();
	$validator = new validator();
	
	$method->method_param("POST","type,name,memo,void_use,void_link,link,bleft,btop,target,img_ed,start_level,end_level,pop_article,pop_article_txt");
	$method->method_param("FILE","img");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	검사
	*/
	//입력값 검사
	if($type=="new"||$type=="modify"){
		$validator->validt_idx("name",1,"");
		$validator->validt_null("memo","");
		$validator->validt_number("btop",0,5,1,"");
		$validator->validt_number("bleft",0,5,1,"");
		if($start_level<$end_level){
			$validator->validt_diserror("start_level","최소 레벨이 최대 레벨보다 클 수 없습니다.");
		}
		if($pop_article=="select" && trim($pop_article_txt)==""){
			$validator->validt_diserror("pop_article_txt","");
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
			$validator->validt_diserror("name","이미 존재하는 코드입니다.");
		}
		if(!$img['name']){
			$validator->validt_diserror("img","팝업 이미지를 등록해 주세요.");
		}
	}
	
	/*
	팝업 이미지 업로드
	*/
	$fileUploader->savePath = __DIR_PATH__."upload/siteInformations/";
	$fileUploader->filedotType = "jpg,bmp,gif,png";
	if(($type=="modify"||$type=="new")){
		if($img['size']>0){
			$fileUploader->saveFile = $img;
			//경로 및 파일 검사
			$fileUploader->filePathCheck();
			if($fileUploader->fileNameCheck()==false){
				$validator->validt_diserror("img","지원되지 않는 팝업 이미지입니다.");
			}
			//파일저장
			$img_name = date("ymdtis",mktime())."_".substr(md5($img['name']),4,10).".".$fileUploader->fileNameType();
			$img_name = str_replace(" ","_",$img_name);
			if($fileUploader->fileUpload($img_name)==false){
				$validator->validt_diserror("img","파일 저장에 실패 하였습니다.");
			}
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
		$validator->validt_success("성공적으로 추가 되었습니다.","admin/?p=popupList&vtype={$vtype}");
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
		$validator->validt_success("성공적으로 수정 되었습니다.","admin/?p=popupList_modify&type=modify&vtype={$vtype}&act={$name}");
	
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
		$validator->validt_success("성공적으로 삭제 되었습니다.","admin/?p=popupList&vtype={$vtype}");
	}
	
?>