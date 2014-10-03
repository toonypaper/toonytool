<?php
	/*
	게시판 정보 기록
	*/
	$db_toony_module_board_config_insert = "
		insert into toony_module_board_config
		(
			board_id,skin,name,use_list,use_comment,use_category,category,use_reply,use_file1,use_file2,use_likes,file_limit,list_limit,length_limit,
			array_level,write_level,secret_level,comment_level,delete_level,read_level,controll_level,reply_level,write_point,read_point,thumb_width,thumb_height,article_length,regdate
		)
		values
		(
			'$board_id','$skin','$name','$use_list','$use_comment','$use_category','$category','$use_reply','$use_file1','$use_file2','$use_likes','$file_limit','$list_limit','$length_limit',
			'$array_level','$write_level','$secret_level','$comment_level','$delete_level','$read_level','$controll_level','$reply_level','$write_point','$read_point','$thumb_width','$thumb_height','$article_length',now()
		)
	";
	/*
	게시판 테이블 생성
	*/
	$db_toony_module_board_create_board = "
		create table toony_module_board_data_$board_id(
			idno int(11) auto_increment,
			category varchar(255) default NULL,
			ln int(11) default 0,
			rn int(11) default 0,
			me_idno int(11) default 0,
			writer varchar(255) default NULL,
			password text,
			email varchar(255),
			ment text,
			subject varchar(255),
			file1 text default NULL,
			file1_cnt int(11) default 0,
			file2 text default NULL,
			file2_cnt int(11) default 0,
			link1 text default NULL,
			link2 text default NULL,
			use_secret char(1) default 'N',
			use_notice char(1) default 'N',
			use_html char(1) default 'Y',
			use_email char(1) default 'Y',
			view int(11) default 0,
			ip varchar(255),
			regdate datetime,
			td_1 text default NULL,
			td_2 text default NULL,
			td_3 text default NULL,
			td_4 text default NULL,
			td_5 text default NULL,
			primary key(idno)
		)engine=innodb default charset=utf8
	";
	/*
	게시판 댓글 테이블 생성
	*/
	$db_toony_module_board_create_board_comment = "
		create table toony_module_board_comment_$board_id(
			idno int(11) auto_increment,
			ln int(11) default 0,
			rn int(11) default 0,
			bo_idno int(11) default 0,
			me_idno int(11) default NULL,
			writer varchar(255) default NULL,
			comment text,
			ip varchar(255),
			regdate datetime,
			tr_1 text default NULL,
			tr_2 text default NULL,
			tr_3 text default NULL,
			tr_4 text default NULL,
			tr_5 text default NULL,
			primary key(idno)
		)engine=innodb default charset=utf8
	";
?>