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
	
	$method->method_param("GET","cnum,article,board_id,where,keyword,page,category");
?>
<!DOCTYPE HTML>
<html>
<head>
<?php include_once __DIR_PATH__."include/head_script.php";?>
</head>
<body style="background-color:#F5F5F5;">
<?php
	/*
	선택한 게시물을 쪼갬
	*/
	$cnum = str_replace("on,","",$cnum);
	$cnum_ex = explode(",",$cnum);
	
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
	if(sizeof($cnum_ex)<1){
		$lib->error_alert_close("선택된 게시물이 없습니다.","A");
	}
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("modules/board/_tpl/{$viewDir}controll.html");
	
	/*
	템플릿 함수
	*/
	function board_list_options(){
		global $mysql;
		$mysql->select("
			SELECT *
			FROM toony_module_board_config
			ORDER BY name ASC
		");
		do{
			$mysql->fetchArray("board_id,name");
			$array = $mysql->array;
			$option .= "<option value=\"".$array[board_id]."\">".$array[name]."(".$array['board_id'].")</option>";
		}while($mysql->nextRec());
		return $option;
	}
	
	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[board_id]",$board_id);
	$tpl->skin_modeling("[article]",$article);
	$tpl->skin_modeling("[keyword]",$keyword);
	$tpl->skin_modeling("[where]",$where);
	$tpl->skin_modeling("[page]",$page);
	$tpl->skin_modeling("[category]",$category);
	$tpl->skin_modeling("[select_count]",sizeof($cnum_ex));
	$tpl->skin_modeling("[cnum]",$cnum);
	$tpl->skin_modeling("[board_list_options]",board_list_options());
	echo $tpl->skin_echo();
?>
</body>
</html>