<?php
	include_once "functions.inc.php";
	$functions = new functions();
	
	if($functions->file_permission("../include/")==FALSE || $functions->file_permission("../upload/sessionCookies/")==FALSE || $functions->file_permission("../upload/siteInformations/")==FALSE || $functions->file_permission("../upload/smartEditor/")==FALSE){
		$functions->error_alert_location("1단계가 진행되지 않았습니다.","index.php");
	}
	if($functions->file_check("../include/path.info.php")==FALSE){
		$functions->error_alert_location("1단계가 진행되지 않았습니다.","index.php");
	}
	if($functions->file_check("../include/mysql.info.php")){
		include "../include/mysql.info.php";
		$host = __HOST__;
		$db = __DB_NAME__;
		$id = __DB_USER__;
		$password = __DB_PASS__;
	}else{
		$host = $_POST['host'];
		$db = $_POST['db'];
		$id = $_POST['id'];
		$password = $_POST['password'];
	}
	
	$connect = @mysql_connect($host,$id,$password);
	mysql_select_db($db,$connect);
	if(mysql_query("select * from toony_member_list",$connect) && mysql_num_rows(mysql_query("select * from toony_member_list",$connect))>0){
		$functions->error_alert_location("이미 3단계가 진행 되었습니다.","step4.php");
	}
	
	if(trim($host)==""){
		$functions->error_alert_back("호스트를 입력 하세요.");
	}
	if(trim($db)==""){
		$functions->error_alert_back("DB 이름을 입력 하세요.");
	}
	if(trim($id)==""){
		$functions->error_alert_back("아이디를 입력 하세요.");
	}
	if(trim($password)==""){
		$functions->error_alert_back("패스워드를 입력 하세요.");
	}
	
	include "../include/path.info.php";
	
	if(!$connect = @mysql_connect($host,$id,$password)){
		$functions->error_alert_back("Mysql 접속에 실패 하였습니다. 입력한 정보를 다시 한 번 확인해 주세요.");
	}
	if(!mysql_select_db($db,$connect)){
		$functions->error_alert_back("DB 연결에 실패 하였습니다. 입력한 정보를 다시 한 번 확인해 주세요.");
	}
	
	$file = @fopen("../include/mysql.info.php","w");
	@fwrite($file,"<?php\n    define(\"__HOST__\",\"".$host."\");\n    define(\"__DB_NAME__\",\"".$db."\");\n    define(\"__DB_USER__\",\"".$id."\");\n    define(\"__DB_PASS__\",\"".$password."\");\n?>");
	@fclose($file);
	
	if(!mysql_query("select * from toony_admin_siteconfig")){
		include "./schema/default.php";
		mysql_query('set names UTF8',$connect);
		mysql_query($db_toony_admin_siteconfig,$connect);
		mysql_query($db_insert_toony_admin_siteconfig,$connect);
		mysql_query($db_toony_admin_counter,$connect);
		mysql_query($db_toony_admin_member_online,$connect);
		mysql_query($db_toony_admin_referer,$connect);
		mysql_query($db_toony_admin_security_ip,$connect);
		mysql_query($db_toony_admin_security_member,$connect);
		mysql_query($db_toony_admin_popupconfig,$connect);
		mysql_query($db_toony_page_list,$connect);
		mysql_query($db_insert_toony_page_list,$connect);
		mysql_query($db_toony_admin_mailling_template,$connect);
		mysql_query($db_insert_toony_admin_mailling_template,$connect);
		mysql_query($db_toony_admin_mailling,$connect);
		mysql_query($db_toony_admin_menuInfo,$connect);
		mysql_query($db_insert_toony_admin_menuInfo,$connect);
		mysql_query($db_toony_member_list,$connect);
		mysql_query($db_toony_member_idCheck,$connect);
		mysql_query($db_toony_member_point,$connect);
		mysql_query($db_toony_admin_design_bodyStyle,$connect);
		mysql_query($db_insert_toony_admin_design_bodyStyle,$connect);
		mysql_query($db_toony_admin_design_mainVisual,$connect);
		mysql_query($db_insert_toony_admin_design_mainVisual,$connect);
		mysql_query($db_toony_admin_design_footer,$connect);
		mysql_query($db_insert_db_toony_admin_design_footer,$connect);
		mysql_query($db_toony_customer_qna,$connect);
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
<form name="step3Form" action="step4.php" method="post">
<article>
	<div class="inner">
		<div class="t">
			<strong>3단계</strong>최고 관리자 정보를 설정 합니다.
		</div>
		<div class="c">
			<span class="stitle">
				투니툴을 관리하는 최고 권한 운영자의 정보를 설정 합니다.<br />
				홈페이지 운영에 중요한 정보이므로 정확한 정보를 입력 하십시오.
			</span>
			<table cellpadding="0" cellspacing="0">
				<tr>
					<th>아이디</th>
					<td>
						<input type="text" name="id" value="ex) toonypaper@toonypaper.com" onFocus="this.value='';" />
						<span class="__span_sment" style="display:block; padding:0; padding-top:2px;">아이디는 이메일 형태입니다.</span>
					</td>
				</tr>
				<tr>
					<th>
						비밀번호</th>
					<td>
						<input type="password" name="password" value="" />
						<span class="__span_sment" style="display:block; padding:0; padding-top:2px;">5~30자로 입력 하세요.</span>
					</td>
				</tr>
				<tr>
					<th style="line-height:14px;">비밀번호<br />확인</th>
					<td>
						<input type="password" name="password02" value="" />
						<span class="__span_sment" style="display:block; padding:0; padding-top:2px;">위 비밀번호를 한번 더 입력 하세요.</span>
					</td>
				</tr>
				<tr>
					<th>이름</th>
					<td><input type="text" name="name" value="" /></td>
				</tr>
			</table>
		</div>
	</div>
	
</article>
<footer>
	<input type="submit" class="__button_submit" value="다음 단계로" />
</footer>
</form>
</body>
</html>
