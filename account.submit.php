<?php
	include "include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$method = new methodController();
	$mysql = new mysqlConnection();
	$mailSender = new mailSender();
	
	$method->method_param("POST","chk_agreement,chk_private,id,password,password02,nick,sex,phone,telephone");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	검사
	*/
	if($chk_agreement!="checked"){ echo '<!--error::null_agreement-->'; exit; }
	if($chk_private!="checked"){ echo '<!--error::null_private-->'; exit; }
	if($member['me_level']<10){ echo '<!--error::logged-->'; exit; }
	if(trim($id)==""){
		echo '<!--error::null_id-->'; exit;
	}
	$lib->func_method_param_check("id",$id,"<!--error::not_id-->");
	if(trim($password)==""){
		echo '<!--error::null_password-->'; exit;
	}
	if($password!=$password02){
		echo '<!--error::not_samePassword-->'; exit;
	}
	$lib->func_method_param_check("password",$password,"<!--error::not_password-->");
	$password_val = "password('$password')";
	if(trim($nick)==""){
		echo '<!--error::null_nick-->'; exit;
	}
	$lib->func_method_param_check("nick",$nick,"<!--error::not_nick-->");
	$lib->func_method_param_check("phone",$phone,"<!--error::not_phone-->");
	$lib->func_method_param_check("telephone",$telephone,"<!--error::not_telephone-->");
	
	/*
	이미 존재하는 아이디인지 검사
	*/
	$mysql->select("
		SELECT *
		FROM toony_member_list
		WHERE me_id='$id' AND me_drop_regdate IS NULL
	");
	//아이디가 존재하는 경우
	if($mysql->numRows()>0){
		echo '<!--error::have_id-->'; exit;
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
	$mailSender->func_mail_sender();
	$mailSender->func_mail_sender->temp = "account";
	$mailSender->func_mail_sender->t_email = $id;
	$mailSender->func_mail_sender->t_name = $nick;
	$mailSender->func_mail_sender->subject = "{$nick}님, {$site_config['ad_site_name']} 이메일 인증을 해주세요.";
	$idCheckCode = md5(date("YmdHis").$id);
	$idCheckUrl = __URL_PATH__."?article=account&p=account.idCheck&code=".$idCheckCode;
	$mailSender->func_mail_sender->account_check_url = "<a href=\"{$idCheckUrl}\" target=\"_blank\">".$idCheckUrl."</a>";
	$mailSender->func_mail_sender_get();
	$sendCount++;
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
	echo '<!--success::1-->';
?>