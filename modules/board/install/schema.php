<?php
	/*
	좋아요 기록 테이블
	*/
	$db_toony_board_like = "
		create table toony_module_board_like(
			idno int(11) auto_increment,
			board_id varchar(255),
			read_idno int(11),
			me_idno int(11),
			likes int(11) default 0,
			unlikes int(11) default 0,
			regdate datetime,
			primary key(idno)
		)engine=innodb default charset=utf8
	";
	
	/*
	게시판 설정 테이블
	*/
	$db_toony_board_config = "
		create table toony_module_board_config(
			board_id varchar(255),
			skin varchar(255) default 'toony_default',
			name varchar(255),
			use_list char(3) default 'Y|N',
			use_secret char(1) default 'Y',
			use_comment char(1) default 'Y',
			use_likes char(1) default 'Y',
			use_reply char(1) default 'Y',
			use_file1 char(1) default 'Y',
			use_file2 char(1) default 'N',
			use_category char(1) default 'N',
			category text default NULL,
			file_limit int(50) default 5242880,
			list_limit varchar(50) default '15|5',
			length_limit varchar(50) default '50|30',
			array_level int(11) default 10,
			write_level int(11) default 9,
			secret_level int(11) default 1,
			comment_level int(11) default 9,
			delete_level int(11) default 9,
			read_level int(11) default 10,
			controll_level int(11) default 3,
			reply_level int(11) default 9,
			write_point int(11) default 10,
			read_point int(11) default 0,
			top_file text default NULL,
			top_source text default NULL,
			bottom_file text default NULL,
			bottom_source text default NULL,
			thumb_width varchar(50) default '120|100',
			thumb_height varchar(50) default '80|100',
			articleIMG_width varchar(50) default '600|250',
			articleIMG_height varchar(50) default '600|250',
			article_length varchar(50) default '90|50',
			regdate datetime,
			tc_1 text default NULL,
			tc_2 text default NULL,
			tc_3 text default NULL,
			tc_4 text default NULL,
			tc_5 text default NULL
		)engine=innodb default charset=utf8
	";
	$db_insert_toony_board_config = "
		insert into toony_module_board_config
		(skin,board_id,name,thumb_width,thumb_height,regdate)
		values
		('toony_default','notice','공지사항','120|100','80|100',now())
		,('toony_webzine','webzine','웹진 게시판','120|100','80|100',now())
		,('toony_gallery','gallery','포토갤러리','200|250','120|150',now());
	";
	
	/*
	기본(공지사항) 게시판 테이블 생성
	*/
	$db_toony_module_board_data_notice = "
		create table toony_module_board_data_notice(
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
	$db_toony_module_board_comment_notice = "
		create table toony_module_board_comment_notice(
			idno int(11) auto_increment,
			ln int(11) default 0,
			rn int(11) default 0,
			bo_idno int(11),
			me_idno int(11) default 0,
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
	$db_toony_module_board_data_notice_value = "
		insert into toony_module_board_data_notice
		(me_idno,ln,rn,writer,ment,subject,regdate)
		values
		(1,1000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,2000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,3000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,4000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,5000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,6000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,7000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,8000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,9000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,1000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now());
	";
	
	/*
	기본(웹진 게시판) 게시판 테이블 생성
	*/
	$db_toony_module_board_data_webzine = "
		create table toony_module_board_data_webzine(
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
	$db_toony_module_board_comment_webzine = "
		create table toony_module_board_comment_webzine(
			idno int(11) auto_increment,
			ln int(11) default 0,
			rn int(11) default 0,
			bo_idno int(11),
			me_idno int(11) default 0,
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
	$db_toony_module_board_data_webzine_value = "
		insert into toony_module_board_data_webzine
		(me_idno,ln,rn,writer,ment,subject,regdate)
		values
		(1,1000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,2000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,3000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,4000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,5000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,6000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,7000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,8000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,9000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,1000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now());
	";
	
	/*
	기본(포토갤러리) 게시판 테이블 생성
	*/
	$db_toony_module_board_data_gallery = "
		create table toony_module_board_data_gallery(
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
	$db_toony_module_board_comment_gallery = "
		create table toony_module_board_comment_gallery(
			idno int(11) auto_increment,
			ln int(11) default 0,
			rn int(11) default 0,
			bo_idno int(11),
			me_idno int(11) default 0,
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
	$db_toony_module_board_data_gallery_value = "
		insert into toony_module_board_data_gallery
		(me_idno,ln,rn,writer,ment,subject,regdate)
		values
		(1,1000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,2000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,3000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,4000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,5000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,6000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,7000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,8000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,9000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now())
		,(1,1000,0,'테스트','<P>이 글은 설치 후 기능 미리보기를 위한 기본 게시물입니다.&nbsp; <BR>홈페이지 개발 완료 후 이 게시물은 삭제 하시면 됩니다. </P>','설치 후 기본 게시물입니다.',now());
	";
?>