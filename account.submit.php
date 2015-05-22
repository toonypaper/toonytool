<?php
	include "include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$method = new methodController();
	$mysql = new mysqlConnection();
	$mailSender = new mailSender();
	$validator = new validator();
	
	$method->method_param("POST","chk_agreement,chk_private,id,password,password02,nick,sex,phone,telephone");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	검사
	*/
	if($member['me_level']<10){
		$validator->validt_diserror("","이미 회원가입이 되어 있습니다.");
	}
	$validator->validt_checked("chk_agreement","이용약관에 동의해야 합니다.");
	$validator->validt_checked("chk_private","개인정보취급방침에 동의해야 합니다.");
	$validator->validt_email("id",1,"");
	$validator->validt_password("password",1,"");
	if($password!=$password02){
		$validator->validt_diserror("password02","");
	}
	$validator->validt_nick("nick",1,"");
	$validator->validt_phone("phone",0,"");
	$validator->validt_phone("telephone",0,"");
	$password_val = "password('$password')";
	
	/*
	이미 존재하는 아이디인지 검사
	*/
	$mysql->select("
		SELECT *
		FROM toony_member_list
		WHERE me_id='$id' AND me_drop_regdate IS NULL
	");
	if($mysql->numRows()>0){
		$validator->validt_diserror("id","이미 존재하는 아이디입니다.");
	}
	
	/*
	DB 기록
	*/
	$mysql->query("
		INSERT INTO toony_member_list
		(me_id,me_password,me_nick,me_sex,me_phone,me_telephone,me_regdate)
		VALUES
		('$id',$password_val,'$nick','$sex','$phone','$telephone',now())
	");
	
	/*
	회원 코드를 가져옴
	*/
	$mysql->select("
		SELECT me_idno
		FROM toony_member_list
		WHERE me_id='$id' AND me_password=password('$password') AND me_drop_regdate IS NULL
	");
	$member['me_idno'] = $mysql->fetch("me_idno");
	
	/*
	가입 이력이 없는 새로운 가입인 경우 아이디 인증 메일 발송
	*/
	//인증 메일 발송
	$idCheckCode = md5(date("YmdHis").$id);
	$idCheckUrl = __URL_PATH__."?article=account&p=account.idCheck&code=".$idCheckCode."&keepViewType=true";
	$mailSender->template = "account";
	$mailSender->t_email = $id;
	$mailSender->t_name = $nick;
	$mailSender->subject = "{$nick}님, {$site_config['ad_site_name']} 이메일 인증을 해주세요.";
	$mailSender->account_check_url = "<a href=\"{$idCheckUrl}\" target=\"_blank\">".$idCheckUrl."</a>";
	$mailSender->mail_send();
	//인증 메일 발송 이력 DB 기록
	$mysql->query("
		INSERT INTO toony_member_idCheck
		(me_idno,ric_code,ric_regdate)
		VALUES
		('{$member['me_idno']}','$idCheckCode',now())
	");
	
	/*
	완료 후 리턴
	*/
	$validator->validt_success("이메일로 발송된 메일을 확인해 주시면 회원가입이 완료됩니다.\n\n가입해 주셔서 감사합니다.","?article=main");
?>