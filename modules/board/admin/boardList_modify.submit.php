<?php
	include "../../../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("POST","type,board_id,skin,name,use_list,use_comment,use_likes,use_reply,use_category,category,use_file1,use_file2,file_limit,list_limit,length_limit,array_level,write_level,secret_level,comment_level,delete_level,read_level,controll_level,reply_level,write_point,read_point,top_file,top_source,bottom_file,bottom_source,thumb_width,thumb_height,article_length");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/**************************************************
	추가 모드인 경우
	**************************************************/
	if($type=="new"){
		/*
		검사
		*/
		$mysql->select("
			SELECT *
			FROM toony_module_board_config
			WHERE board_id='$board_id'
		");
		if($mysql->numRows()>0){
			echo '<!--error::have_board_id-->'; exit;
		}
		if(trim($board_id)==""){ echo '<!--error::null_board_id-->'; exit; }
		$lib->func_method_param_check("idx",$board_id,"<!--error::not_board_id-->");
		if(trim($name)==""){ echo '<!--error::null_name-->'; exit; }
		if(trim($list_limit)==""){ echo '<!--error::null_list_limit-->'; exit; }
		$lib->func_method_param_check("number",$list_limit,"<!--error::not_list_limit-->");
		if(trim($length_limit)==""){ echo '<!--error::null_length_limit-->'; exit; }
		$lib->func_method_param_check("number",$length_limit,"<!--error::not_length_limit-->");
		if(trim($file_limit)==""){ echo '<!--error::null_file_limit-->'; exit; }
		$lib->func_method_param_check("number",$file_limit,"<!--error::not_file_limit-->");
		if(trim($article_length)==""){ echo '<!--error::null_article_length-->'; exit; }
		$lib->func_method_param_check("number",$article_length,"<!--error::not_article_length-->");
		if(trim($thumb_width)==""){ echo '<!--error::null_thumb_width-->'; exit; }
		$lib->func_method_param_check("number",$thumb_width,"<!--error::not_thumb_width-->");
		if(trim($thumb_height)==""){ echo '<!--error::null_thumb_height-->'; exit; }
		$lib->func_method_param_check("number",$thumb_height,"<!--error::not_thumb_height-->");
		
		/*
		DB입력
		*/
		include_once __DIR_PATH__."modules/board/install/board_create.php";
		$mysql->query($db_toony_module_board_config_insert); //게시판 정보 테이블에 정보 기록
		$mysql->query($db_toony_module_board_create_board); //게시판 테이블 생성
		$mysql->query($db_toony_module_board_create_board_comment); //게시판 덧글 테이블 생성
		/*
		완료 후 리턴
		*/
		echo '<!--success::1-->';
	}
	
	/**************************************************
	수정 모드인 경우
	**************************************************/
	if($type=="modify"){
		/*
		검사
		*/
		if(trim($name)==""){ echo '<!--error::null_name-->'; exit; }
		if(trim($list_limit)==""){ echo '<!--error::null_list_limit-->'; exit; }
		$lib->func_method_param_check("number",$list_limit,"<!--error::not_list_limit-->");
		if(trim($length_limit)==""){ echo '<!--error::null_length_limit-->'; exit; }
		$lib->func_method_param_check("number",$length_limit,"<!--error::not_length_limit-->");
		if(trim($file_limit)==""){ echo '<!--error::null_file_limit-->'; exit; }
		$lib->func_method_param_check("number",$file_limit,"<!--error::not_file_limit-->");
		if(trim($article_length)==""){ echo '<!--error::null_article_length-->'; exit; }
		$lib->func_method_param_check("number",$article_length,"<!--error::not_article_length-->");
		if(trim($thumb_width)==""){ echo '<!--error::null_thumb_width-->'; exit; }
		$lib->func_method_param_check("number",$thumb_width,"<!--error::not_thumb_width-->");
		if(trim($thumb_height)==""){ echo '<!--error::null_thumb_height-->'; exit; }
		$lib->func_method_param_check("number",$thumb_height,"<!--error::not_thumb_height-->");
		
		/*
		DB수정
		*/
		$mysql->query("
			UPDATE toony_module_board_config
			SET skin='$skin',name='$name',use_list='$use_list',use_category='$use_category',category='$category',use_comment='$use_comment',use_likes='$use_likes',use_reply='$use_reply',use_file1='$use_file1',use_file2='$use_file2',file_limit='$file_limit',list_limit='$list_limit',length_limit='$length_limit',array_level='$array_level',write_level='$write_level',secret_level='$secret_level',comment_level='$comment_level',delete_level='$delete_level',read_level='$read_level',controll_level='$controll_level',reply_level='$reply_level',write_point='$write_point',read_point='$read_point',top_file='$top_file',top_source='$top_source',bottom_file='$bottom_file',bottom_source='$bottom_source',thumb_width='$thumb_width',thumb_height='$thumb_height',article_length='$article_length'
			WHERE board_id='$board_id'
		");
		
		/*
		완료 후 리턴
		*/
		echo '<!--success::2-->';
	
	/**************************************************
	삭제 모드인 경우
	**************************************************/
	}else if($type=="delete"){
		/*
		검사
		*/
		$mysql->select("
			SELECT board_id
			FROM toony_module_board_config
			WHERE board_id='$board_id';
		");
		if($mysql->numRows()<1){
			echo '<!--error::not_board-->'; exit;
		}
		
		/*
		DB삭제
		*/
		$mysql->query("
			DROP TABLE toony_module_board_data_$board_id
		");
		$mysql->query("
			DROP TABLE toony_module_board_comment_$board_id
		");
		$mysql->query("
			DELETE FROM toony_module_board_config
			WHERE board_id='$board_id'
		");
		
		/*
		첨부파일 모두 삭제
		*/
		$path = __DIR_PATH__."modules/board/upload/".$board_id;
		if(is_dir($path)){
			$arr = array();
			$dir = opendir($path);
			while($file = readdir($dir)){
				if($file=='.'||$file=='..'){
					continue;
				}else{
					unlink($path."/".$file);
				}
			}
			closedir($dir);
			rmdir($path);
		}
		
		/*
		완료 후 리턴
		*/
		echo '<!--success::3-->';
	}
	
?>