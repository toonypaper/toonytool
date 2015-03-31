<?php
	include "../../../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("POST","board_id,parent_id");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	검사
	*/
	if(trim($board_id)==""){
		echo "<!--error::null_board_id-->";
	}
	if(trim($parent_id)==""){
		echo "<!--error::null_parent_id-->";
	}
	
	/*
	보낼 게시판의 설정값 가져옴
	*/
	$mysql->select("
		SELECT *
		FROM toony_module_board_config
		WHERE board_id='$parent_id'
	");
	$mysql->fetchArray("skin,use_list,use_secret,use_comment,use_likes,use_reply,use_file1,use_file2,use_category,category,file_limit,list_limit,length_limit,array_level,write_level,secret_level,comment_level,delete_level,read_level,controll_level,reply_level,write_point,read_point,top_file,top_source,bottom_file,bottom_source,thumb_width,thumb_height,articleIMG_width,articleIMG_height,article_length,ico_file,ico_mobile,ico_secret,ico_secret_def,ico_new,ico_new_def,ico_hot,ico_hot_def");
	$p_array = $mysql->array;
	
	/*
	받을 게시판의 설정값 가져옴
	*/
	$mysql->query("
		UPDATE toony_module_board_config
		SET
		skin='{$p_array['skin']}',
		use_list='{$p_array['use_list']}',
		use_secret='{$p_array['use_secret']}',
		use_comment='{$p_array['use_comment']}',
		use_likes='{$p_array['use_likes']}',
		use_reply='{$p_array['use_reply']}',
		use_file1='{$p_array['use_file1']}',
		use_file2='{$p_array['use_file2']}',
		use_category='{$p_array['use_category']}',
		category='{$p_array['category']}',
		file_limit='{$p_array['file_limit']}',
		list_limit='{$p_array['list_limit']}',
		length_limit='{$p_array['length_limit']}',
		array_level='{$p_array['array_level']}',
		write_level='{$p_array['write_level']}',
		secret_level='{$p_array['secret_level']}',
		comment_level='{$p_array['comment_level']}',
		delete_level='{$p_array['delete_level']}',
		read_level='{$p_array['read_level']}',
		controll_level='{$p_array['controll_level']}',
		reply_level='{$p_array['reply_level']}',
		write_point='{$p_array['write_point']}',
		read_point='{$p_array['read_point']}',
		top_file='{$p_array['top_file']}',
		top_source='{$p_array['top_source']}',
		bottom_file='{$p_array['bottom_file']}',
		bottom_source='{$p_array['bottom_source']}',
		thumb_width='{$p_array['thumb_width']}',
		thumb_height='{$p_array['thumb_height']}',
		articleIMG_width='{$p_array['articleIMG_width']}',
		articleIMG_height='{$p_array['articleIMG_height']}',
		article_length='{$p_array['article_length']}',
		ico_file='{$p_array['ico_file']}',
		ico_mobile='{$p_array['ico_mobile']}',
		ico_secret='{$p_array['ico_secret']}',
		ico_secret_def='{$p_array['ico_secret_def']}',
		ico_new='{$p_array['ico_new']}',
		ico_new_def='{$p_array['ico_new_def']}',
		ico_hot='{$p_array['ico_hot']}',
		ico_hot_def='{$p_array['ico_hot_def']}'
		WHERE board_id='$board_id'
	");
	
	/*
	완료 후 리턴
	*/
	echo "<!--success::1-->";
?>