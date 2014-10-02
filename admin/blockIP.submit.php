<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("POST","type,cnum,id,memo,ip");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/**************************************************
	추가 모드인 경우
	**************************************************/
	if($type=="new"){
		/*
		검사
		*/
		if(trim($ip)==""){
			echo '<!--error::null_ip-->'; exit;
		}
		if(trim($memo)==""){
			echo '<!--error::null_memo-->'; exit;
		}
		$mysql->select("
			SELECT *
			FROM toony_admin_security_ip
			WHERE ip='$ip'
		");
		if($mysql->numRows()>0){
			echo "<!--have_ip-->"; exit;
		}
		
		/*
		DB 저장
		*/
		$mysql->query("
			INSERT INTO toony_admin_security_ip (ip,memo,regdate)
			VALUES
			('$ip','$memo',now())
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
					DELETE FROM toony_admin_security_ip
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