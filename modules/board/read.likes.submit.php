<?php
	include "../../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	$method->method_param("POST","board_id,read_idno,mode");
	
	/*
	게시물 설정 정보 로드
	*/
	$mysql->select("
		SELECT *
		FROM toony_module_board_config
		WHERE board_id='$board_id'
	");
	$mysql->fetchArray("use_likes");
	$c_array = $mysql->array;
	
	/*
	검사
	*/
	if($c_array['use_likes']=="N"){ echo '추천 기능 비활성 중입니다.'; exit; }
	if($member['me_level']>9){ echo '<!--error::not_permissions-->'; exit; }
	
	/*
	이미 추천.비추천 했는지 검사
	*/
	$mysql->select("
		SELECT *
		FROM toony_module_board_like
		WHERE board_id='$board_id' AND read_idno='$read_idno' AND me_idno='{$member['me_idno']}'
	");
	if($mysql->numRows()>0){
		echo '<!--error::have_likes-->'; exit;
	}
	
	/*
	추천/비추천 처리
	*/
	if($mode=="likes"){
		$likes = 1; $unlikes = 0;
		$return_where = "AND likes>0";
	}else{
		$likes = 0; $unlikes = 1;
		$return_where = "AND unlikes>0";
	}
	$mysql->query("
		INSERT INTO toony_module_board_like
		(board_id,read_idno,me_idno,likes,unlikes,regdate)
		VALUES
		('$board_id','$read_idno','{$member['me_idno']}','$likes','$unlikes',now())
	");
	
	/*
	추천/비추천 완료 후 추천/비추천 카운트를 리턴
	*/
	$mysql->select("
		SELECT
		COUNT(*) totalCount
		FROM toony_module_board_like
		WHERE board_id='$board_id' AND read_idno='$read_idno' $return_where
	");
	echo $mysql->fetch("totalCount");
	
?>