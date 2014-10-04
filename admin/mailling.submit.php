<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$mailSender = new mailSender();
	
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	$method->method_param("POST","min_level,max_level,receiver_id,subject,memo");
	
	/*
	검사
	*/
	if(trim($subject)==""){
		echo '<!--error::null_subject-->'; exit;
	}
	if(trim($memo)==""){
		echo '<!--error::null_memo-->'; exit;
	}
	//특정 회원의 이메일 주소가 입력된 경우, 회원 유무 검사
	if(trim($receiver_id)!=""){
		$mysql->select("
			SELECT me_idno
			FROM toony_member_list 
			WHERE me_id='$receiver_id' AND me_drop_regdate IS NULL
		");
		if($mysql->numRows()<1){
			echo '<!--error::not_member-->'; exit;
		}
		$receiver_idno = $mysql->fetch("me_idno");
	}else{
		$receiver_idno = "";
	}
	//수신 회원 범위 유무성 검사
	if(trim($receiver_id)==""){
		if($min_level=="none"){
			echo '<!--error::null_min_receiver-->'; exit;
		}
		if($max_level=="none"){
			echo '<!--error::null_max_receiver-->'; exit;
		}
		if($min_level<$max_level){
			echo '<!--error::null_not_receiver_selected-->'; exit;
		}
	}
	
	/*
	수신 회원 범위에 따른 DB 조건문 생성
	*/
	if(trim($receiver_id)!=""){
		$mail_query_where = "
			me_id='$receiver_id' AND me_drop_regdate IS NULL
		";
	}else{
		$mail_query_where = "
			(me_level<=$min_level AND me_level>=$max_level) AND me_drop_regdate IS NULL
		";
	}
	
	/*
	DB저장
	*/
	$mysql->query("
		INSERT INTO toony_admin_mailling
		(me_idno,min_level,max_level,subject,memo,regdate)
		VALUES
		('$receiver_idno','$min_level','$max_level','$subject','$memo',now())
	");
	
	/*
	메일 발송
	*/
	$mysql->select("
		SELECT me_id,me_nick
		FROM toony_member_list
		WHERE $mail_query_where
		ORDER BY me_regdate DESC
	");
	if($mysql->numRows()>0){
		$sendCount = 0;
		do{
			$mailSender->func_mail_sender();
			$mailSender->func_mail_sender->temp = "mailling";
			$mailSender->func_mail_sender->t_email = $mysql->fetch("me_id");
			$mailSender->func_mail_sender->t_name = $mysql->fetch("me_nick");
			$mailSender->func_mail_sender->subject = $subject;
			$mailSender->func_mail_sender->memo = str_replace('\"','"',stripslashes($memo));
			$mailSender->func_mail_sender_get();
			$sendCount++;
		}while($mysql->nextRec());
	}else{
		echo '<!--error::null_receiver_member-->';
		exit;
	}
	
	/*
	완료 후 리턴
	*/
	echo $sendCount;
?>