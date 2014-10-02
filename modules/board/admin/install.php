<?php
	$mysql = new mysqlConnection();
	$lib = new libraryClass();
	$tpl = new skinController();
	$method = new methodController();
	
	$method->method_param("POST","submitVal");
	
	/*
	모듈이 설치되어 있는지 검사
	*/
	if($mysql->is_table("toony_module_board_config")){
		$lib->error_location("?m=board&p=boardList","A");
	}
	
	/*
	POST 변수가 전달된 경우 모듈을 설치
	*/
	if($submitVal){
		include __DIR_PATH__."modules/board/install/schema.php";
		$mysql->query($db_toony_board_like);
		$mysql->query($db_toony_board_config);
		$mysql->query($db_insert_toony_board_config);
		$mysql->query($db_toony_module_board_data_notice);
		$mysql->query($db_toony_module_board_comment_notice);
		$mysql->query($db_toony_module_board_data_notice_value);
		$mysql->query($db_toony_module_board_data_webzine);
		$mysql->query($db_toony_module_board_comment_webzine);
		$mysql->query($db_toony_module_board_data_webzine_value);
		$mysql->query($db_toony_module_board_data_gallery);
		$mysql->query($db_toony_module_board_comment_gallery);
		$mysql->query($db_toony_module_board_data_gallery_value);
		$lib->error_alert_location("설치가 완료 되었습니다.","?m=board&p=boardList","A");
	}
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("modules/board/admin/_tpl/install.html");
	
	/*
	특정 디텍토리 퍼미션 검사
	*/
	function permission_check($file){
		$open = @is_writable($file);
		if(!$open){
			return "N";
		}else{
			return "Y";
		}
	}
	function permission_txt($val){
		if($val=="Y"){
			return "<span style='color:blue;font-size:11px;letter-spacing:-1px;padding-left:10px;'>변경 완료됨</span>";
		}else{
			return "<span style='color:red;font-size:11px;letter-spacing:-1px;padding-left:10px;'>퍼미션 변경되지 않음</span>";
		}
	}
	
	/*
	템플릿 치환
	*/
	if(permission_check(__DIR_PATH__."modules/board/upload")=="Y"){
		$tpl->skin_modeling_hideArea("[{installBtn_start}]","[{installBtn_end}]","show");
		$tpl->skin_modeling_hideArea("[{recheckBtn_start}]","[{recheckBtn_end}]","hide");
	}else{
		$tpl->skin_modeling_hideArea("[{installBtn_start}]","[{installBtn_end}]","hide");
		$tpl->skin_modeling_hideArea("[{recheckBtn_start}]","[{recheckBtn_end}]","show");
	}
	$tpl->skin_modeling("[permissionCheck_upload]",permission_txt(permission_check(__DIR_PATH__."modules/board/upload")));
	echo $tpl->skin_echo();
?>