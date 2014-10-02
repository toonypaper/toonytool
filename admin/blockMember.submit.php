<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("POST","type,cnum,id,memo");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/**************************************************
	추가 모드인 경우
	**************************************************/
	if($type=="new"){
		/*
		검사
		*/
		if(trim($id)==""){
			echo '<!--error::null_id-->'; exit;
		}
		if(trim($memo)==""){
			echo '<!--error::null_memo-->'; exit;
		}
		$mysql->select("
			SELECT *
			FROM toony_member_list 
			WHERE me_id='$id' AND me_drop_regdate IS NULL
		");
		if($mysql->numRows()<1){
			echo '<!--error::not_member-->'; exit;
		}
		$mysql->fetchArray("me_idno,me_admin");
		$array = $mysql->array;
		$mysql->select("
			SELECT *
			FROM toony_admin_security_member
			WHERE me_id='$id';
		");
		if($mysql->numRows()>0){
			echo '<!--error::have_member-->'; exit;
		}
		if($array[me_admin]=="Y"){
			echo '<!--error::admin_member-->'; exit;
		}
		
		/*
		DB 저장
		*/
		$mysql->query("
			INSERT INTO toony_admin_security_member
			(me_idno,me_id,memo,regdate)
			VALUES
			('{$array['me_idno']}','$id','$memo',now())
		");
		
		/*
		완료 후 리턴
		*/
		echo '<!--success::1-->';
		
	/**************************************************
	삭제 모드인 경우
	**************************************************/
	}else if($type=="delete"){
		$b = 0;
		for($i=0;$i<sizeof($cnum);$i++){
			if($cnum[$i]!=""){
				/*
				DB Delete
				*/
				$mysql->query("
					DELETE FROM toony_admin_security_member
					WHERE idno='$cnum[$i]'
				");
			}
			$b++;
		}
		
		/*
		완료 후 리턴
		*/
		echo "Success_".$b;
	}
	
	
?>