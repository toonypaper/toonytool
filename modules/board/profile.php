<?php
	/*
	PC버전으로 출력함
	*/
	include "../../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	include __DIR_PATH__."include/outModules.inc.php";
	
	$method = new methodController();
	$lib = new libraryClass();
	$tpl = new skinController();
	$mysql = new mysqlConnection();
	
	$method->method_param("GET","me_idno,article");
?>
<!DOCTYPE HTML>
<html>
<head>
<?php include_once __DIR_PATH__."include/head_script.php";?>
</head>
<body style="background-color:#F5F5F5;">
<?php
	/*
	회원 기본정보 로드
	*/
	$mysql->select("
		SELECT *
		FROM toony_member_list
		WHERE me_idno='$me_idno' AND me_drop_regdate IS NULL
	");
	$mysql->fetchArray("me_nick,me_sex,me_id,me_level,me_point,me_regdate,me_login_regdate,me_login_ip");
	$array = $mysql->array;
	
	/*
	검사
	*/
	if($mysql->numRows()<1){
		$lib->error_alert_close("회원 정보가 존재하지 않거나 탈퇴한 회원입니다.","A");
	}
	if($member['me_level']>9){
		$lib->error_alert_close("회원 정보를 볼 권한이 없습니다.","A");
	}
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("modules/board/_tpl/{$viewDir}profile.html");
	
	/*
	템플릿 함수
	*/
	function sex_func(){
		global $array;
		if($array['me_sex']=="M"){
			return "남자";
		}else{
			return "여자";
		}
	}
	
	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[nick]",$array['me_nick']);
	$tpl->skin_modeling("[sex]",sex_func());
	$tpl->skin_modeling("[id]",$array['me_id']);
	$tpl->skin_modeling("[level]",$array['me_level']);
	$tpl->skin_modeling("[member_type]",$member_type_var[$array['me_level']]);
	$tpl->skin_modeling("[point]",number_format($array['me_point']));
	$tpl->skin_modeling("[regdate]",$array['me_regdate']);
	$tpl->skin_modeling("[login_regdate]",$array['me_login_regdate']);
	$tpl->skin_modeling("[login_ip]",$array['me_login_ip']);
	$tpl->skin_modeling("[article]",$article);
	echo $tpl->skin_echo();
?>
</body>
</html>