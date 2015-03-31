<?php
	include_once "functions.inc.php";
	$functions = new functions();
	
	if($functions->file_check("../include/mysql.info.php")==TRUE){
		@include "../include/engine.inc.php";
		$connect = @mysql_connect(__HOST__,__DB_USER__,__DB_PASS__);
		mysql_select_db(__DB_NAME__,$connect);
	}

	if($functions->file_permission("../include/")==FALSE || $functions->file_permission("../upload/sessionCookies/")==FALSE || $functions->file_permission("../upload/siteInformations/")==FALSE || $functions->file_permission("../upload/smartEditor/")==FALSE){
		$functions->error_alert_location("1단계가 진행되지 않았습니다.","index.php");
	}
	if($functions->file_check("../include/path.info.php")==FALSE){
		$functions->error_alert_location("1단계가 진행되지 않았습니다.","index.php");
	}
	if($functions->file_check("../include/mysql.info.php")==FALSE){
		$functions->error_alert_location("2단계가 진행되지 않았습니다.","index.php");
	}

	if(!mysql_query("select * from toony_admin_siteconfig")){
		$functions->error_alert_location("DB가 정상적으로 설치되지 않았습니다.","step2.php");
	}else if(mysql_num_rows(mysql_query("select * from toony_member_list",$connect))<1 && !$_POST['id']){
		$functions->error_alert_location("3단계가 진행되지 않았습니다.","step3.php");
	}
	
	$method = new methodController();
	$method->method_param("POST","id,password,password02,name");
	
	if(getenv("REQUEST_METHOD")=="POST"){
		mb_internal_encoding('UTF-8');
		if(trim($id)==""){
			$functions->error_alert_back("아이디를 입력해 주세요");
		}
		$filter = "/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/";
		if(!preg_match($filter,$id)){
			$functions->error_alert_back("아이디가 올바르지 않습니다.");
		}
		if(trim($password)==""){
			$functions->error_alert_back("비밀번호를 입력해 주세요.");
		}
		if(trim($password02)==""){
			$functions->error_alert_back("비밀번호 확인을 입력해 주세요.");
		}
		if($password!=$password02){
			$functions->error_alert_back("비밀번호와 비밀번호 확인이 일치하지 않습니다.");
		}
		if(mb_strlen($password)<5 || mb_strlen($password)>30){
			$functions->error_alert_back("비밀번호가 올바르지 않습니다.");
		}
		if(trim($name)==""){
			$functions->error_alert_back("이름을 입력해 주세요.");
		}
		$filter = "/^[a-zA-Z0-9가-힣]+$/";
		if(!preg_match($filter,$name)){
			$functions->error_alert_back("이름이 올바르지 않습니다.");
		}
		if(mysql_num_rows(mysql_query("select * from toony_member_list where me_admin='Y'",$connect))<1){
			mysql_query("set names UTF8",$connect);
			mysql_query("
				insert into toony_member_list
				(me_admin,me_id,me_password,me_nick,me_level,me_regdate,me_idCheck)
				values
				('Y','$id',password('$password'),'$name','1',now(),'Y')
			",$connect);
			
			$file = @fopen("../upload/siteInformations/installed.info.txt","w");
			@fwrite($file,$password);
			@fclose($file);
			
		}else{
			mysql_query("set names UTF8",$connect);
			mysql_query("
				update toony_member_list SET
				me_id='$id',me_password=password('$password'),me_nick='$name'
				where me_admin='Y'
			",$connect);
			
			@unlink("../upload/siteInformations/installed.info.txt");
			$file = @fopen("../upload/siteInformations/installed.info.txt","w");
			@fwrite($file,$password);
			@fclose($file);
			
		}
	}else{
		$functions->error_alert_location("정상적으로 접근 바랍니다.","index.php");	
	}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>투니페이퍼 투니툴 - 설치하기</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../library/js/jquery-1.7.1.js"></script>
<script type="text/javascript" src="../library/js/jquery-ui.js"></script>
<script type="text/javascript" src="../library/js/ghost_html5.js"></script>
<script type="text/javascript" src="../library/js/respond.min.js"></script>
</head>
<body>
<header>
	<img src="images/title.jpg" alt="투니툴 코어 설치" />
</header>
<form name="step2Form" action="step3.php" method="post">
<article>
	<div class="inner">
		<div class="t">
			<strong>완료</strong>설치를 모두 완료 하였습니다.
		</div>
		<div class="c">
			<span class="stitle">
				투니툴 설치를 모두 완료 하였습니다.<br />
				아래 링크를 클릭하여 관리모드로 접속하여 투니툴 설정을 해주시기 바랍니다.<br />
				이용해 주셔서 감사 합니다.
			</span>
			<span class="tb">
				<strong>관리모드 바로가기</strong><a href="<?=__URL_PATH__."admin/"?>" style="padding-left:10px;"><?=__URL_PATH__."admin/"?></a>
			</span>
		</div>
	</div>
	
</article>
</form>
</body>
</html>
