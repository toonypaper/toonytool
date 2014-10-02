<?php
	//index가 진행되어 있지 않으면 첫화면으로 이동
	function permission_check($file){
		$open = @is_writable($file);
		if(!$open){
			return "N";
		}else{
			return "Y";
		}
	}
	if(permission_check("../include/")=="N"||permission_check("../upload/")=="N"){
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script type="text/javascript">document.location.href = "index.php";</script>'; exit;
	//Step1이 진행되어 있지 않으면 첫 화면으로 이동
	}else if(!is_file("../include/path.info.php")){
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script type="text/javascript">document.location.href = "index.php";</script>'; exit;
	//Step2가 진행되어 있지 않으면 첫화면으로 이동
	}else if(!is_file("../include/mysql.info.php")&&!$_POST['host']){
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script type="text/javascript">document.location.href = "index.php";</script>'; exit;
	}
	//Step2가 진행되어 있는 경우 Mysql 정보를 로드
	if(is_file("../include/mysql.info.php")){
		include "../include/mysql.info.php";
		$host = __HOST__;
		$db = __DB_NAME__;
		$id = __DB_USER__;
		$password = __DB_PASS__;
	//변수 처리
	}else{
		$host = $_POST['host'];
		$db = $_POST['db'];
		$id = $_POST['id'];
		$password = $_POST['password'];
	}
	//Step3이 진행되어 있는 경우 Step4로 이동
	$connect = @mysql_connect($host,$id,$password);
	mysql_select_db($db,$connect);
	if(mysql_query("select * from toony_member_list",$connect)&&mysql_num_rows(mysql_query("select * from toony_member_list",$connect))>0){
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script type="text/javascript">document.location.href = "step4.php";</script>'; exit;
	}
	//입력값 확인
	if(trim($host)==""){
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script type="text/javascript">alert("호스트를 입력 하세요.");history.back();</script>'; exit;
	}
	if(trim($db)==""){
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script type="text/javascript">alert("DB 이름을 입력 하세요.");history.back();</script>'; exit;
	}
	if(trim($id)==""){
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script type="text/javascript">alert("아이디를 입력 하세요.");history.back();</script>'; exit;
	}
	if(trim($password)==""){
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script type="text/javascript">alert("패스워드를 입력 하세요.");history.back();</script>'; exit;
	}
	//경로 정보 설정파일 로드
	include "../include/path.info.php";
	//DB 연결
	if(!$connect = @mysql_connect($host,$id,$password)){
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script type="text/javascript">alert("Mysql 접속에 실패 하였습니다.\n입력한 정보를 다시 한 번 확인해 주세요.");history.back();</script>'; exit;
	}
	if(!mysql_select_db($db,$connect)){
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script type="text/javascript">alert("DB 연결에 실패 하였습니다.\n입력한 정보를 다시 한 번 확인해 주세요.");history.back();</script>'; exit;
	}
	//mysql.info.php 파일 생성
	$file = @fopen("../include/mysql.info.php","w");
	@fwrite($file,"<?php\n    define(\"__HOST__\",\"".$host."\");\n    define(\"__DB_NAME__\",\"".$db."\");\n    define(\"__DB_USER__\",\"".$id."\");\n    define(\"__DB_PASS__\",\"".$password."\");\n?>");
	@fclose($file);
	//DB에 Table 생성
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
	<img src="images/title.jpg" alt="투니툴 엔진 설치" />
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
