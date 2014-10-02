<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
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
		if(trim($name)==""){
			echo '<!--error::null_name-->'; exit;
		}
		$lib->func_method_param_check("idx",$name,"<!--error::not_name-->");
		if(trim($memo)==""){
			echo '<!--error::null_memo-->'; exit;
		}
		//이름 중복 검사
		$mysql->select("
			SELECT *
			FROM toony_page_list
			WHERE name='$name' AND vtype='$vtype'
		");
		if($mysql->numRows()>0){
			echo '<!--error::have_name-->'; exit;
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
		echo '<!--success::1-->';
	
	/**************************************************
	수정 모드인 경우
	**************************************************/
	}else if($type=="modify"){
		/*
		검사
		*/
		if(trim($memo)==""){
			echo '<!--error::null_memo-->'; exit;
		}
		
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
		echo '<!--success::2-->';
	
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
		echo '<!--success::3-->';
	}
?>