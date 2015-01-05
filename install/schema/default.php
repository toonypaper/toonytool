<?php
	/*
	사이트 기본 설정 테이블
	*/
	$db_toony_admin_siteconfig = "
		create table toony_admin_siteconfig(
			ad_site_name varchar(255),
			ad_site_url text,
			ad_msite_url text,
			ad_use_msite char(1) default 'Y',
			ad_use_www char(1) default 'N',
			ad_site_title varchar(255),
			ad_email varchar(255),
			ad_phone varchar(255),
			ad_pavicon text,
			ad_logo text,
			ad_member_type text,
			ad_site_layout varchar(255) default 'defaultSkin',
			ad_msite_layout varchar(255) default 'defaultSkin',
			ad_site_main text,
			ad_msite_main text,
			ad_site_jsmain text,
			ad_msite_jsmain text
		)engine=innodb default charset=utf8
	";
	$db_insert_toony_admin_siteconfig_ad_site_main = "<LI class=latest itemtype=\"latest\" itemclass=\"c2014912213762\" data-col=\"1\" data-row=\"1\" data-sizex=\"5\" data-sizey=\"7\"><DIV class=_tb>&lt;div class=\"<SPAN>c2014912213762</SPAN>\"&gt;</DIV><A class=_d href=\"#\"></A><SPAN class=_title>게시판 모듈 최근게시물</SPAN><SPAN class=_article>공지사항 (notice)</SPAN><SPAN class=_itemBoxSize>490 x 260</SPAN> <DIV style=\"DISPLAY: none\" class=_itemData>notice|c2014912213762|notice|toony_list|7|30|25|180|100|0|1</DIV></LI><LI class=latest itemtype=\"latest\" itemclass=\"c20149122137144\" data-col=\"6\" data-row=\"1\" data-sizex=\"5\" data-sizey=\"7\"><DIV class=_tb>&lt;div class=\"<SPAN>c20149122137144</SPAN>\"&gt;</DIV><A class=_d href=\"#\"></A><SPAN class=_title>게시판 모듈 최근게시물</SPAN><SPAN class=_article>웹진 게시판 (webzine)</SPAN><SPAN class=_itemBoxSize>490 x 260</SPAN> <DIV style=\"DISPLAY: none\" class=_itemData>webzine|c20149122137144|webzine|toony_webzine|2|10|25|180|110|0|2</DIV></LI><LI class=latest itemtype=\"latest\" itemclass=\"c2014912213835\" data-col=\"1\" data-row=\"8\" data-sizex=\"10\" data-sizey=\"7\"><DIV class=_tb>&lt;div class=\"<SPAN>c2014912213835</SPAN>\"&gt;</DIV><A class=_d href=\"#\"></A><SPAN class=_title>게시판 모듈 최근게시물</SPAN><SPAN class=_article>포토갤러리 (gallery)</SPAN><SPAN class=_itemBoxSize>1000 x 260</SPAN> <DIV style=\"DISPLAY: none\" class=_itemData>gallery|c2014912213835|gallery|toony_gallery|6|10|25|150|150|0|1</DIV></LI>";
	$db_insert_toony_admin_siteconfig_ad_msite_main = "<LI class=\"latest player-revert\" itemtype=\"latest\" itemclass=\"c2014913022299\" data-col=\"1\" data-row=\"1\" data-sizex=\"1\" data-sizey=\"2\"><DIV class=_tb>&lt;div class=\"<SPAN>c2014913022299</SPAN>\"&gt;</DIV><A class=_d href=\"#\"></A><SPAN class=_title>게시판 모듈 최근게시물</SPAN><SPAN class=_article>공지사항 (notice)</SPAN> <DIV style=\"DISPLAY: none\" class=_itemData>notice|c2014913022299|notice|toony_list|5|20|25|180|180|0|1</DIV></LI><LI class=latest itemtype=\"latest\" itemclass=\"c2014913934508\" data-col=\"1\" data-row=\"3\" data-sizex=\"1\" data-sizey=\"2\"><DIV class=_tb>&lt;div class=\"<SPAN>c2014913934508</SPAN>\"&gt;</DIV><A class=_d href=\"#\"></A><SPAN class=_title>게시판 모듈 최근게시물</SPAN><SPAN class=_article>웹진 게시판 (webzine)</SPAN> <DIV style=\"DISPLAY: none\" class=_itemData>webzine|c2014913934508|webzine|toony_webzine|1|20|50|280|120|0|2</DIV></LI><LI class=latest itemtype=\"latest\" itemclass=\"c2014913108298\" data-col=\"1\" data-row=\"5\" data-sizex=\"1\" data-sizey=\"2\"><DIV class=_tb>&lt;div class=\"<SPAN>c2014913108298</SPAN>\"&gt;</DIV><A class=_d href=\"#\"></A><SPAN class=_title>게시판 모듈 최근게시물</SPAN><SPAN class=_article>포토갤러리 (gallery)</SPAN> <DIV style=\"DISPLAY: none\" class=_itemData>gallery|c2014913108298|gallery|toony_gallery|4|10|25|120|120|0|1</DIV></LI>";
	$db_insert_toony_admin_siteconfig_ad_site_jsmain = "#latest|notice|c2014912213762|notice|toony_list|7|30|25|180|100|0|1|1|1|5|7#latest|webzine|c20149122137144|webzine|toony_webzine|2|10|25|180|110|0|2|6|1|5|7#latest|gallery|c2014912213835|gallery|toony_gallery|6|10|25|150|150|0|1|1|8|10|7";
	$db_insert_toony_admin_siteconfig_ad_msite_jsmain = "#latest|notice|c2014913022299|notice|toony_list|5|20|25|180|180|0|1|1|1|1|2#latest|webzine|c2014913934508|webzine|toony_webzine|1|20|50|280|120|0|2|1|3|1|2#latest|gallery|c2014913108298|gallery|toony_gallery|4|10|25|120|120|0|1|1|5|1|2";	$db_insert_toony_admin_siteconfig = "
		insert into toony_admin_siteconfig
		(ad_site_name,ad_site_url,ad_msite_url,ad_site_title,ad_email,ad_phone,ad_member_type,ad_site_layout,ad_msite_layout,ad_site_main,ad_msite_main,ad_site_jsmain,ad_msite_jsmain)
		values
		('투니툴 홈페이지','".__URL_PATH__."','".__URL_PATH__."m/','투니툴 홈페이지','admin@admin.com','02-0202-0202','최고관리자,관리자,관리자,정회원,정회원,정회원,정회원,정회원,일반회원','defaultSkin','defaultSkin','$db_insert_toony_admin_siteconfig_ad_site_main','$db_insert_toony_admin_siteconfig_ad_msite_main','$db_insert_toony_admin_siteconfig_ad_site_jsmain','$db_insert_toony_admin_siteconfig_ad_msite_jsmain')
	";
	
	/*
	회원 접속자수 기록 테이블
	*/
	$db_toony_admin_counter = "
		create table toony_admin_counter(
			idno int(11) auto_increment,
			me_idno int(11) default 0,
			me_id varchar(255) null default null,
			ip varchar(255),
			regdate datetime,
			primary key(idno)
		)engine=innodb default charset=utf8
	";
	
	/*
	현재 접속자 기록 테이블
	*/
	$db_toony_admin_member_online = "
		create table toony_admin_member_online(
			idno int(11) auto_increment,
			me_idno int(11) default 0,
			guest_ip varchar(255),
			visitdate datetime,
			primary key(idno)
		)engine=innodb default charset=utf8
	";
	
	/*
	회원 접속경로 기록 테이블
	*/
	$db_toony_admin_referer = "
		create table toony_admin_referer(
			idno int(11) auto_increment,
			referer_sub text not null,
			referer text not null,
			regdate datetime,
			primary key(idno)
		)engine=innodb default charset=utf8
	";
	
	/*
	IP 차단 기록 테이블
	*/
	$db_toony_admin_security_ip = "
		create table toony_admin_security_ip(
			idno int(11) auto_increment,
			ip varchar(255) not null,
			memo text not null,
			regdate datetime,
			primary key(idno)
		)engine=innodb default charset=utf8
	";
	
	/*
	회원차단 기록 테이블
	*/
	$db_toony_admin_security_member = "
		create table toony_admin_security_member(
			idno int(11) auto_increment,
			me_idno int(11) not null default 0,
			me_id varchar(255) not null,
			memo text not null,
			regdate datetime,
			primary key(idno)
		)engine=innodb default charset=utf8
	";
	
	/*
	팝업 설정 테이블
	*/
	$db_toony_admin_popupconfig = "
		create table toony_admin_popupconfig(
			img text default NULL,
			name varchar(255) not null,
			memo text,
			void_use char(1) default 'N',
			void_link char(1) default 'N',
			link text not null,
			bleft int(11),
			btop int(11),
			target varchar(255) default '_self',
			start_level int(11) default 10,
			end_level int(11) default 1,
			pop_article varchar(255) default 'main',
			pop_article_txt text default NULL,
			regdate datetime
		)engine=innodb default charset=utf8
	";
	
	/*
	페이지 테이블
	*/
	$db_toony_page_list = "
		create table toony_page_list(
			idno int(11) auto_increment,
			vtype char(1) default 'p',
			name varchar(255),
			scriptCode text,
			memo text,
			source text,
			level int(2) default '10',
			regdate datetime,
			primary key(idno)
		)engine=innodb default charset=utf8
	";
	$source_page_list = '
		<BR style="DISPLAY: none">
		<STYLE>
		.services{ margin:0; padding:0; list-style-type:none; width:100%; }
		.services > li{ padding:10px 0; }
		.services > li > strong{ display:block; padding-bottom:10px; font-size:16px; font-family:MalgunGothic; }
		</STYLE>
		<UL class=services>
		<LI>
		<STRONG>
			클릭 한 번으로 홈페이지+모바일페이지 완성 
		</STRONG>
		<SPAN>
			클릭 한 번으로 강력한 기능의 홈페이지와 모바일페이지를 완성할 수 있습니다. 
		</SPAN>
		</LI>
		<LI>
		<STRONG>
			Drag &amp; Drop 으로 디자인하는 메인, 서브 페이지 
		</STRONG>
		<SPAN>
			마우스 Drag &amp; Drop 만으로 디자인을 쉽게 완성할 수 있는 GUI기반의 디자인 기능이 탑재되어 있습니다. 
		</SPAN>
		</LI>
		<LI>
		<STRONG>
			클릭 한 번으로 변경되는 레이아웃 디자인 
		</STRONG>
		<SPAN>
			투니툴에서 제공하는 다양한 사용자 스킨을 클릭 한번으로 쉽게 적용 가능합니다. 
		</SPAN>
		</LI>
		<LI>
		<STRONG>
			메뉴(사이트맵) 관리 기능 
		</STRONG>
		<SPAN>
			관리페이지를 통해 원하는대로 사이트 메뉴 구성을 변경 가능합니다. 
		</SPAN>
		</LI>
		<LI>
		<STRONG>
			기간별 접속자 통계 기능 
		</STRONG>
		<SPAN>
			기간별로 방문한 방문자의 정보를 확인하고, 그래프를 통한 분석이 가능합니다. 
		</SPAN>
		</LI>
		<LI>
		<STRONG>
			강력한 회원 관리 기능 
		</STRONG>
		<SPAN>
			강력한 투니툴의 회원관리 기능을 통해 가입 회원을 세부 관리 가능합니다. 
		</SPAN>
		</LI>
		<LI>
		<STRONG>
			고객 문의, 메일 발송 시스템 
		</STRONG>
		<SPAN>
			1:1 고객 문의 접수 및 답변 기능이 탑재되어 있으며, 모든 회원에게 뉴스레터 전체 메일 발송이 가능합니다. 
		</SPAN>
		</LI>
		<LI>
		<STRONG>
			팝업 관리 기능 
		</STRONG>
		<SPAN>
			메인화면 또는 모든 페이지에 팝업창을 노출 하거나, 회원 등급별로 차등을 두어 팝업을 노출할 수 있습니다. 
		</SPAN>
		</LI>
		<LI>
		<STRONG>
			서브페이지 디자인 및 메뉴 연결 기능 
		</STRONG>
		<SPAN>
			새로운 서브 페이지를 직접 디자인 하고 특정 메뉴에 연결하여 사이트를 구성할 수 있습니다. 
		</SPAN>
		</LI>
		<LI>
		<STRONG>
			다중 게시판 무제한 생성 
		</STRONG>
		<SPAN>
			투니툴에 기본 탑재된 게시판 모듈을 통해 강력한 기능의 게시판을 무한 생성할 수 있습니다. 
		</SPAN>
		</LI>
		<LI>
		<STRONG>
			다양한 사용자 스킨 
		</STRONG>
		<SPAN>
			투니페이퍼에서 제공하는 다양한 사용자 스킨으로 레이아웃 및 게시판등의 모듈 디자인을 클릭 한 번으로 쉽게 변경 가능합니다. 
		</SPAN>
		</LI>
		<LI>
		<STRONG>
			손 쉬운 추가 모듈 설치로 사이트 기능 확장 
		</STRONG>
		<SPAN>
			투니페이퍼에서 제공하는 사용자 모듈을 추가하여 보다 쉽게 홈페이지/모바일페이지에 다양한 기능을 확장할 수 있습니다. 
		</SPAN>
		</LI>
		</UL>
	';
	$db_insert_toony_page_list = "
		insert into toony_page_list
		(vtype,name,memo,source,regdate)
		values
		('p','greetings','투니툴 주요기능 안내','$source_page_list',now())
		,('m','greetings','투니툴 주요기능 안내(모바일)','$source_page_list',now())
	";
	
	/*
	메일링 템플릿 테이블
	*/
	$db_toony_admin_mailling_template = "
		create table toony_admin_mailling_template(
			type varchar(255),
			source text,
			regdate datetime
		)engine=innodb default charset=utf8
	";
	$source_account = '
		<TABLE border=0 cellSpacing=0 cellPadding=0 width=564 align=center>
		<TBODY>
		<TR>
		<TD style="TEXT-ALIGN: left; PADDING-BOTTOM: 10px"><IMG src="'.__URL_PATH__.'admin/images/siteDefaultInfo_logo.jpg"></TD></TR></TBODY></TABLE>
		<TABLE style="BORDER-BOTTOM: #ebebeb 1px solid; BORDER-LEFT: #ebebeb 1px solid; BORDER-TOP: #ebebeb 1px solid; BORDER-RIGHT: #ebebeb 1px solid" border=0 cellSpacing=0 cellPadding=0 width=564 align=center>
		<TBODY>
		<TR>
		<TD><IMG src="'.__URL_PATH__.'admin/images/mailling_bg.jpg"></TD></TR>
		<TR>
		<TD style="TEXT-ALIGN: center; LINE-HEIGHT: 19px; FONT-FAMILY: dotum; COLOR: #333; FONT-SIZE: 12px"><STRONG>{{name}}</STRONG> 회원님, <STRONG>{{site_name}}</STRONG> 홈페이지에 회원가입 해주셔서 감사 드립니다.<BR>저희 <STRONG>{{site_name}}</STRONG> 홈페이지는 이메일 인증한 회원에 한하여 서비스를 제공합니다.<BR>회원가입시 입력하신 이메일 주소를 인증해 주시기 바랍니다.<BR>아래 URL을 클릭하여 이메일 인증을 수행해 주시기 바랍니다.<BR>감사합니다. <BR>
		<DIV style="BORDER-BOTTOM: #dedede 1px solid; BORDER-LEFT: #dedede 1px solid; PADDING-BOTTOM: 10px; MARGIN: 20px; PADDING-LEFT: 10px; PADDING-RIGHT: 10px; COLOR: #0075c8; BORDER-TOP: #dedede 1px solid; BORDER-RIGHT: #dedede 1px solid; TEXT-DECORATION: underline; PADDING-TOP: 10px">{{check_url}} </DIV><BR><A href="'.__URL_PATH__.'" target=_blank><IMG border=0 src="'.__URL_PATH__.'admin/images/mailling_gotoHomepage.jpg"></A> </TD></TR>
		<TR>
		<TD style="TEXT-ALIGN: center; PADDING-BOTTOM: 10px; PADDING-LEFT: 10px; PADDING-RIGHT: 10px; FONT-FAMILY: dotum; COLOR: #999; FONT-SIZE: 10px; PADDING-TOP: 30px">Copyright <STRONG>{{site_name}}</STRONG> All Right Reserved. </TD></TR></TBODY></TABLE>
	';
	$source_mailling = '
		<TABLE border=0 cellSpacing=0 cellPadding=0 width=564 align=center>
		<TBODY>
		<TR>
		<TD style="TEXT-ALIGN: left; PADDING-BOTTOM: 10px"><IMG src="'.__URL_PATH__.'admin/images/siteDefaultInfo_logo.jpg"></TD></TR></TBODY></TABLE>
		<TABLE style="BORDER-BOTTOM: #ebebeb 1px solid; BORDER-LEFT: #ebebeb 1px solid; BORDER-TOP: #ebebeb 1px solid; BORDER-RIGHT: #ebebeb 1px solid" border=0 cellSpacing=0 cellPadding=0 width=564 align=center>
		<TBODY>
		<TR>
		<TD><IMG src="'.__URL_PATH__.'admin/images/mailling_bg.jpg"></TD></TR>
		<TR>
		<TD style="TEXT-ALIGN: center; LINE-HEIGHT: 19px; FONT-FAMILY: dotum; COLOR: #333; FONT-SIZE: 12px">
		<P>{{memo}}<BR><BR><A href="'.__URL_PATH__.'" target=_blank><IMG border=0 src="'.__URL_PATH__.'admin/images/mailling_gotoHomepage.jpg"></A>&nbsp;</P></TD></TR>
		<TR>
		<TD style="TEXT-ALIGN: center; PADDING-BOTTOM: 10px; PADDING-LEFT: 10px; PADDING-RIGHT: 10px; FONT-FAMILY: dotum; COLOR: #999; FONT-SIZE: 10px; PADDING-TOP: 30px">Copyright <STRONG>{{site_name}}</STRONG> All Right Reserved. </TD></TR></TBODY></TABLE>
	';
	$source_password = '
		<TABLE border=0 cellSpacing=0 cellPadding=0 width=564 align=center>
		<TBODY>
		<TR>
		<TD style="TEXT-ALIGN: left; PADDING-BOTTOM: 10px"><IMG src="'.__URL_PATH__.'admin/images/siteDefaultInfo_logo.jpg"></TD></TR></TBODY></TABLE>
		<TABLE style="BORDER-BOTTOM: #ebebeb 1px solid; BORDER-LEFT: #ebebeb 1px solid; BORDER-TOP: #ebebeb 1px solid; BORDER-RIGHT: #ebebeb 1px solid" border=0 cellSpacing=0 cellPadding=0 width=564 align=center>
		<TBODY>
		<TR>
		<TD><IMG src="'.__URL_PATH__.'admin/images/mailling_bg.jpg"></TD></TR>
		<TR>
		<TD style="TEXT-ALIGN: center; LINE-HEIGHT: 19px; FONT-FAMILY: dotum; COLOR: #333; FONT-SIZE: 12px"><STRONG>{{name}}</STRONG> 회원님, <STRONG>{{site_name}}</STRONG> 홈페이지 로그인을 위한 <BR>임시 비밀번호 발급을 신청 하셨습니다. <BR>아래 임시 비밀번호를 복사하여 로그인 하신 뒤 비밀번호를 변경 하십시오. <BR>
		<DIV style="BORDER-BOTTOM: #dedede 1px solid; BORDER-LEFT: #dedede 1px solid; PADDING-BOTTOM: 10px; MARGIN: 20px; PADDING-LEFT: 10px; PADDING-RIGHT: 10px; COLOR: #0075c8; BORDER-TOP: #dedede 1px solid; BORDER-RIGHT: #dedede 1px solid; TEXT-DECORATION: underline; PADDING-TOP: 10px">{{password}} </DIV><BR><A href="'.__URL_PATH__.'" target=_blank><IMG border=0 src="'.__URL_PATH__.'admin/images/mailling_gotoHomepage.jpg"></A> </TD></TR>
		<TR>
		<TD style="TEXT-ALIGN: center; PADDING-BOTTOM: 10px; PADDING-LEFT: 10px; PADDING-RIGHT: 10px; FONT-FAMILY: dotum; COLOR: #999; FONT-SIZE: 10px; PADDING-TOP: 30px">Copyright <STRONG>{{site_name}}</STRONG> All Right Reserved. </TD></TR></TBODY></TABLE>
	';
	$db_insert_toony_admin_mailling_template = "
		insert into toony_admin_mailling_template
		(type,source,regdate)
		values
		('account','$source_account',now())
		,('mailling','$source_mailling',now())
		,('password','$source_password',now());
	";
	
	/*
	메일링 발송 이력 DB
	*/
	$db_toony_admin_mailling = "
		create table toony_admin_mailling(
			idno int(11) auto_increment,
			me_idno int(11) default NULL,
			min_level int(11) default NULL,
			max_level int(11) default NULL,
			subject text,
			memo text,
			regdate datetime,
			primary key(idno)
		)engine=innodb default charset=utf8;
	";
	
	/*
	메뉴 설정 DB
	*/
	$db_toony_admin_menuInfo = "
		create table toony_admin_menuInfo(
			idno int(11) auto_increment,
			name text,
			callName varchar(255),
			vtype char(1) default 'p',
			class int(11),
			depth int(1),
			parent int(11) default 0,
			zindex int(11),
			forward varchar(255),
			href char(2) default 'pm',
			link text,
			linkDoc text,
			title_img text default NULL,
			img text default NULL,
			img2 text default NULL,
			regdate datetime,
			drop_regdate datetime null default null,
			lockMenu char(1) default 'N',
			useMenu char(1) default 'Y',
			useMenu_side char(1) default 'Y',
			primary key(idno)
		)engine=innodb default charset=utf8;
	";
	$db_insert_toony_admin_menuInfo = "
		insert into toony_admin_menuInfo
		(vtype,callName,name,class,depth,zindex,link,linkDoc,forward,href,regdate,useMenu,lockMenu)
		values
		('p','main','메인화면','1','1','1','?p=main','','','pm',now(),'Y','Y')
		,('p','introduce','투니툴 소개','2','1','2','','','greetings','fm',now(),'Y','N')
		,('p','greetings','투니툴 주요기능 안내','2','2','3','?m=page&p=greetings','','','pm',now(),'Y','N')
		,('p','customer','고객지원','4','1','4','','','contactUs','fm',now(),'Y','Y')
		,('p','contactUs','1:1문의하기','4','2','5','','contactUs','','mp',now(),'Y','Y')
		,('p','notice','공지사항','4','2','6','?m=board&board_id=notice','','','pm',now(),'Y','N')
		,('p','webzine','웹진 게시판','4','2','7','?m=board&board_id=webzine','','','pm',now(),'Y','N')
		,('p','gallery','포토갤러리','4','2','8','?m=board&board_id=gallery','','','pm',now(),'Y','N')
		,('p','members','회원','9','1','9','','','login','fm',now(),'N','Y')
		,('p','login','회원 로그인','9','2','10','','login','','mp',now(),'N','Y')
		,('p','findPassword','비밀번호 찾기','9','2','11','','findPassword','','mp',now(),'N','Y')
		,('p','account','신규 회원가입','9','2','12','','account','','mp',now(),'N','Y')
		,('p','mypage','마이페이지','13','1','13','','','myInformation','fm',now(),'N','Y')
		,('p','myInformation','개인정보 변경','13','2','14','','myInformation','','mp',now(),'N','Y')
		,('p','myPoint','나의 포인트 내역','13','2','15','','myPoint','','mp',now(),'N','Y')
		,('p','search','통합검색','16','1','16','','','search_board','fm',now(),'N','Y')
		,('p','search_board','전체 게시판 검색','16','2','17','','search_board','','mp',now(),'N','Y')
		
		,('m','main','메인화면','18','1','1','?p=main','','','pm',now(),'Y','Y')
		,('m','introduce','투니툴 소개','19','1','2','','','greetings','fm',now(),'Y','N')
		,('m','greetings','투니툴 주요기능 안내','19','2','3','?m=page&p=greetings','','','pm',now(),'Y','N')
		,('m','customer','고객지원','21','1','4','','','contactUs','fm',now(),'Y','Y')
		,('m','contactUs','1:1문의하기','21','2','5','','contactUs','','mp',now(),'Y','Y')
		,('m','notice','공지사항','21','2','6','?m=board&board_id=notice','','','pm',now(),'Y','N')
		,('m','webzine','웹진 게시판','21','2','7','?m=board&board_id=webzine','','','pm',now(),'Y','N')
		,('m','gallery','포토갤러리','21','2','8','?m=board&board_id=gallery','','','pm',now(),'Y','N')
		,('m','members','회원','26','1','9','','','login','fm',now(),'N','Y')
		,('m','login','회원 로그인','26','2','10','','login','','mp',now(),'N','Y')
		,('m','findPassword','비밀번호 찾기','26','2','11','','findPassword','','mp',now(),'N','Y')
		,('m','account','신규 회원가입','26','2','12','','account','','mp',now(),'N','Y')
		,('m','mypage','마이페이지','30','1','13','','','myInformation','fm',now(),'N','Y')
		,('m','myInformation','개인정보 변경','30','2','14','','myInformation','','mp',now(),'N','Y')
		,('m','myPoint','나의 포인트 내역','30','2','15','','myPoint','','mp',now(),'N','Y')
		,('m','search','통합검색','33','1','16','','','search_board','fm',now(),'N','Y')
		,('m','search_board','전체 게시판 검색','33','2','17','','search_board','','mp',now(),'N','Y');
	";
	
	/*
	회원 테이블
	*/
	$db_toony_member_list = "
		create table toony_member_list(
			me_admin char(1) default 'N',
			me_idno int(11) auto_increment,
			me_id char(255) not null,
			me_password text not null,
			me_nick varchar(255),
			me_level int(11) default 9,
			me_sex char(1) default 'M',
			me_phone varchar(255),
			me_telephone varchar(255),
			me_regdate datetime,
			me_login_regdate datetime,
			me_login_ip varchar(255),
			me_point int(11) default 0,
			me_idCheck char(1) default 'N',
			me_drop_regdate datetime null default null,
			primary key(me_idno)
		)engine=innodb default charset=utf8
	";
	
	/*
	회원 아이디 체크 여부 기록 테이블
	*/
	$db_toony_member_idCheck = "
		create table toony_member_idCheck(
			me_idno int(11) not null,
			ric_idno int(11) auto_increment,
			ric_code text,
			ric_check char(1) default 'N',
			ric_regdate datetime,
			primary key(ric_idno)
		)engine=innodb default charset=utf8
	";
	
	
	/*
	회원 포인트 현황 기록 테이블
	*/
	$db_toony_member_point = "
		create table toony_member_point(
			idno int(11) auto_increment,
			me_idno int(11) not null,
			point_in int(11),
			point_out int(11),
			memo text,
			regdate datetime,
			primary key(idno)
		)engine=innodb default charset=utf8
	";
	
	/*
	본문 기본 스타일 테이블
	*/
	$db_toony_admin_design_bodyStyle = "
		create table toony_admin_design_bodyStyle(
			vtype char(1) default 'p',
			body_bgColor varchar(255),
			body_txtColor varchar(255),
			body_txtSize varchar(255),
			link_txtColor varchar(255),
			link_hoverColor varchar(255),
			link_activeColor varchar(255),
			link_visitedColor varchar(255),
			link_txtSize varchar(255),
			input_txtColor varchar(255),
			input_txtSize varchar(255),
			usedefault char(1) default 'Y'
		)engine=innodb default charset=utf8
	";
	$db_insert_toony_admin_design_bodyStyle = "
		insert into toony_admin_design_bodyStyle
		(vtype,body_bgColor,body_txtColor,body_txtSize,link_txtColor,link_hoverColor,link_activeColor,link_visitedColor,link_txtSize,input_txtColor,input_txtSize,usedefault)
		values
		('p','#FFFFFF','#343434','12','#666666','#666666','#666666','#000000','12','#343434','12','Y')
		,('m','#F1F1F1','#343434','14','#666666','#666666','#666666','#999999','14','#343434','14','Y');
	";
	
	/*
	메인 비쥬얼 관리 테이블
	*/
	$db_toony_admin_design_mainVisual = "
		create table toony_admin_design_mainVisual(
			vtype char(1) default 'p',
			scriptCode text,
			sourceCode text
		)engine=innodb default charset=utf8
	";
	$db_insert_toony_admin_design_mainVisual = "
		insert into toony_admin_design_mainVisual
		(vtype,scriptCode,sourceCode)
		values
		('p','','<img src=\"".__URL_PATH__."/admin/images/mainVisual_image.jpg\" />')
		,('m','','<img src=\"".__URL_PATH__."/admin/images/mainVisual_m_image.jpg\" style=\"width:100%;\" />');
	";
	
	/*
	카피라이터(푸터) 관리 테이블
	*/
	$db_toony_admin_design_footer = "
		create table toony_admin_design_footer(
			vtype char(1) default 'p',
			scriptCode text,
			sourceCode text
		)engine=innodb default charset=utf8
	";
	$source = '
		<ADDRESS style="TEXT-ALIGN: center; FONT-STYLE: normal; MARGIN-TOP: 30px; WIDTH: 100%; BORDER-TOP: #e0e0e0 1px solid; PADDING-TOP: 20px"><SPAN style="DISPLAY: block; COLOR: #878787; FONT-SIZE: 11px">Copyright (C) ToonyPaper All Right Reserved.</SPAN> <SPAN style="DISPLAY: block; COLOR: #878787; FONT-SIZE: 11px"><STRONG>회사명</STRONG> 투니페이퍼&nbsp;&nbsp;&nbsp;<STRONG>대표자</STRONG> 홍길동&nbsp;&nbsp;&nbsp;<STRONG>연락처</STRONG> 02-2220-2220</SPAN></ADDRESS>
	';
	$source_mobile = '
		<ADDRESS style="TEXT-ALIGN: center; FONT-STYLE: normal; MARGIN-TOP: 30px; WIDTH: 100%; BORDER-TOP: #e0e0e0 1px solid; PADDING-TOP: 20px"><SPAN style="DISPLAY: block; COLOR: #878787; FONT-SIZE: 11px">Copyright (C) ToonyPaper All Right Reserved.</SPAN> <SPAN style="DISPLAY: block; COLOR: #878787; FONT-SIZE: 11px"><STRONG>회사명</STRONG> 투니페이퍼&nbsp;&nbsp;&nbsp;<STRONG>대표자</STRONG> 홍길동&nbsp;&nbsp;&nbsp;<STRONG>연락처</STRONG> 02-2220-2220</SPAN></ADDRESS>
	';
	$db_insert_db_toony_admin_design_footer = "
		insert into toony_admin_design_footer
		(vtype,scriptCode,sourceCode)
		values
		('p','','$source')
		,('m','','$source_mobile');
	";
	
	/*
	고객센터 문의 내역 테이블
	*/
	$db_toony_customer_qna = "
		create table toony_customer_qna(
			idno int(11) auto_increment,
			me_idno int(11) default '0',
			re_idno int(11) default '0',
			memo text,
			cst_name varchar(255),
			cst_email text,
			cst_phone text,
			regdate datetime,
			primary key(idno)
		)engine=innodb default charset=utf8
	";
	
?>