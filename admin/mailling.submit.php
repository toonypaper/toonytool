<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$mailSender = new mailSender();
	$validator = new validator();
	
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	$method->method_param("POST","min_level,max_level,receiver_id,subject,memo");
	$validator->validt_tags("memo",1,"");
	/*
	검사
	*/
	$validator->validt_null("subject","");
	$validator->validt_null("memo","");
	//특정 회원의 이메일 주소가 입력된 경우, 회원 유무 검사
	if(trim($receiver_id)!=""){
		$mysql->select("
			SELECT me_idno
			FROM toony_member_list 
			WHERE me_id='$receiver_id' AND me_drop_regdate IS NULL
		");
		if($mysql->numRows()<1){
			$validator->validt_diserror("receiver_id","존재하지 않는 회원 아이디 입니다.");
		}
		$receiver_idno = $mysql->fetch("me_idno");
	}else{
		$receiver_idno = "";
	}
	//수신 회원 범위 유효성 검사
	if(trim($receiver_id)==""){
		if($min_level=="none"){
			$validator->validt_diserror("min_receiver","최하 수신 범위를 선택 하세요.");
		}
		if($max_level=="none"){
			$validator->validt_diserror("max_receiver","최대 수신 범위를 선택 하세요.");
		}
		if($min_level<$max_level){
			$validator->validt_diserror("max_receiver","최대 수신 범위가 최소 수신 범위보다 낮을 수 없습니다.");
		}
	}
	//본문에 사용금지 태그가 있는지 검사
	
	
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
			$mailSender->template = "mailling";
			$mailSender->t_email = $mysql->fetch("me_id");
			$mailSender->t_name = $mysql->fetch("me_nick");
			$mailSender->subject = $subject;
			$mailSender->memo = str_replace('\"','"',stripslashes($memo));
			$mailSender->mail_send();
			$sendCount++;
		}while($mysql->nextRec());
	}else{
		$validator->validt_diserror("","수신할 회원이 한명도 없습니다.");
		exit;
	}
	
	/*
	완료 후 리턴
	*/
	$validator->validt_success($sendCount."명에게 성공적으로 발송 되었습니다.","admin/?p=mailling");
?>