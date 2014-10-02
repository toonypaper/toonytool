<?php
	$mysql = new mysqlConnection();
	$lib = new libraryClass();
	
	/*
	모듈이 설치되어 있는지 검사
	*/
	if(!$mysql->is_table("toony_module_board_config")){
		$lib->error_location(__URL_PATH__."admin/?m=board&p=install","A");
	}
?>