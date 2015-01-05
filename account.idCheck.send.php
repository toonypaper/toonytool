<?php
	include "include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$method = new methodController();
	$mysql = new mysqlConnection();
	$mailSender = new mailSender();
	$validator = new validator();
	
	$method->method_param("POST","id,nick");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	회원 정보를 불러옴
	*/
	$mysql->select("
		SELECT me_nick,me_idno
		FROM toony_member_list
		WHERE me_id='$id' AND me_drop_regdate IS NULL
	");
	$member['me_nick'] = $mysql->fetch("me_nick");
	$member['me_idno'] = $mysql->fetch("me_idno");
	
	/*
	인증 메일 발송
	*/
	$idCheckCode = md5(date("YmdHis").$id);
	$idCheckUrl = __URL_PATH__."?article=account&p=account.idCheck&code=".$idCheckCode."&keepViewType=true";
	$mailSender->account_check_url = "<a href=\"{$idCheckUrl}\" target=\"_blank\">".$idCheckUrl."</a>";
	$mailSender->template = "account";
	$mailSender->t_email = $id;
	$mailSender->t_name = $member['me_nick'];
	$mailSender->subject = "{$member['me_nick']}님, {$site_config['ad_site_name']} 이메일 인증을 해주세요.";
	$mailSender->mail_send();
	
	/*
	인증 메일 발송 이력 DB 기록
	*/
	$mysql->query("
		INSERT INTO toony_member_idCheck
		(me_idno,ric_code,ric_regdate)
		VALUES
		('{$member['me_idno']}','$idCheckCode',now())
	");
	
	/*
	완료 후 리턴
	*/
	$validator->validt_success("인증 메일이 재발송 되었습니다.\n\n메일을 확인하여 인증을 완료해 주세요.","?article=login");
?>