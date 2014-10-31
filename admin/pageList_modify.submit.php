<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$validator = new validator();
	
	$method->method_param("POST","type,vtype,idno,name,scriptCode,memo,sourceCode,level");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/**************************************************
	추가 모드인 경우
	**************************************************/
	if($type=="new"){
		/*
		검사
		*/
		$validator->validt_idx("name",1,"");
		$validator->validt_null("memo","");
		$mysql->select("
			SELECT *
			FROM toony_page_list
			WHERE name='$name' AND vtype='$vtype'
		");
		if($mysql->numRows()>0){
			$validator->validt_diserror("name","이미 등록된 코드명입니다.");
		}
		
		/*
		DB추가
		*/
		$mysql->query("
			INSERT INTO toony_page_list
			(name,vtype,scriptCode,memo,source,level,regdate)
			VALUES
			('$name','$vtype','$scriptCode','$memo','$sourceCode','$level',now())
		");
		
		/*
		완료 후 리턴
		*/
		$validator->validt_success("성공적으로 추가 되었습니다.","admin/?p=pageList&vtype={$vtype}");
	
	/**************************************************
	수정 모드인 경우
	**************************************************/
	}else if($type=="modify"){
		/*
		검사
		*/
		$validator->validt_null("memo","");
		
		/*
		DB수정
		*/
		$mysql->query("
			UPDATE toony_page_list
			SET memo='$memo',scriptCode='$scriptCode',source='$sourceCode',level='$level'
			WHERE idno='$idno' AND vtype='$vtype'
		");
		
		/*
		완료 후 리턴
		*/
		$validator->validt_success("성공적으로 수정 되었습니다.","admin/?p=pageList_modify&type={$type}&vtype={$vtype}&act={$idno}");
	
	/**************************************************
	삭제 모드인 경우
	**************************************************/
	}else if($type=="delete"){
		/*
		DB삭제
		*/
		$mysql->query("
			DELETE FROM toony_page_list
			WHERE idno='$idno' AND vtype='$vtype'
		");
		
		/*
		완료 후 리턴
		*/
		$validator->validt_success("성공적으로 삭제 되었습니다.","admin/?p=pageList&vtype={$vtype}");
	}
?>