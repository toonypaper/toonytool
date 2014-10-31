<?php
	include_once __DIR_PATH__."include/pageJustice.inc.php";
	
	$tpl = new skinController();
	$method = new methodController();
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$read_true_3 = new skinController();
	$skin_read = new skinController();
	$session = new sessionController();
	
	$method->method_param("GET","read,page,where,keyword,category");
	$method->method_param("POST","s_password");
	
	/*
	세션 로드
	*/
	$__toony_board_view = $session->session_selector('__toony_board_view_'.$board_id.'_'.$read); //조회수 세션
	
	/*
	패스워드가 submit된 경우
	*/
	if($s_password!=""){
		$method->method_param("POST","s_board_id,s_mode,s_read,s_password,s_page,where,keyword");
		$board_id=$s_board_id;
		$read=$s_read;
		$mode=$s_mode;
		$page=$s_page;
	}
	
	/*
	게시물 설정 정보 로드
	*/
	$mysql->select("
		SELECT *
		FROM toony_module_board_config
		WHERE board_id='$board_id'
	");
	$mysql->fetchArray("write_point,read_point,skin,name,use_category,use_comment,use_list,use_likes,use_reply,use_file1,use_file2,file_limit,list_limit,length_limit,array_level,write_level,secret_level,comment_level,delete_level,read_level,reply_level,controll_level,top_file,bottom_file,tc_1,tc_2,tc_3,tc_4,tc_5");
	$c_array = $mysql->array;
	$mysql->htmlspecialchars = 0;
	$mysql->nl2br = 0;
	$mysql->fetchArray("top_source,bottom_source");
	$c_array = $mysql->array;
	
	/*
	게시물 정보 로드
	*/
	$mysql->select("
		SELECT
		(
			SELECT COUNT(*)
			FROM toony_module_board_like
			WHERE board_id='$board_id' AND read_idno='$read' AND likes>0
		) likes_count,
		(
			SELECT COUNT(*)
			FROM toony_module_board_like
			WHERE board_id='$board_id' AND read_idno='$read' AND unlikes>0
		) unlikes_count,
		A.*
		FROM toony_module_board_data_$board_id A
		WHERE A.idno='$read'
	");
	if($mysql->numRows()<1){
		$lib->error_alert_location("해당 게시물이 존재하지 않습니다.",__URL_PATH__.$viewDir,"A");
	}
	$mysql->htmlspecialchars = 1;
	$mysql->nl2br = 1;
	$mysql->fetchArray("rn,ln,category,regdate,subject,writer,me_idno,cmtnum,view,vote,password,use_secret,use_notice,use_html,me_idno,file1,file1_cnt,file2,file2_cnt,likes_count,unlikes_count,td_1,td_2,td_3,td_4,td_5");
	$array = $mysql->array;
	$mysql->htmlspecialchars = 0;
	$mysql->nl2br = 0;
	$mysql->fetchArray("ment");
	$array = $mysql->array;
	
	/*
	게시물이 답글이고, 회원에 대한 답글인 경우 부모글의 정보를 로드
	*/
	if($array['rn']>0&&$array['password']==""){
		$mysql->select("
			SELECT *
			FROM toony_module_board_data_$board_id
			WHERE ln>{$array['ln']} AND rn={$array['rn']}-1
			ORDER BY ln ASC
			LIMIT 1
		");
		$mysql->fetchArray("me_idno");
		$parent_array = $mysql->array;
	}
	
	/*
	스킨 CSS로드
	*/
	echo "\n<link href=\"".__URL_PATH__."modules/board/skin/{$c_array['skin']}/{$viewDir}style.css\" rel=\"stylesheet\" type=\"text/css\" />";
	
	/*
	상단 파일&소스코드 출력
	*/
	if($c_array['top_file']){
		include $c_array['top_file'];
	}
	echo $c_array['top_source'];
	
	/*
	패스워드가 submit된 경우(비밀글) 패스워드가 일치 하는지 검사
	*/
	if($s_password!=""){
		if($array['password']==$s_password){
			$read_true = 1;
		}else{
			$read_true = 3;
			$lib->error_alert_back("비밀번호가 일치하지 않습니다.","A");
		}
	}
	/*
	패스워드 submit이 아닌 경우, 글 읽기 권한이 있는지 검사
	*/
	if(!$s_password){
		//비밀글인 경우
		if($array['use_secret']=="Y"){
			//관리자 레벨 이거나, 비밀글 읽기 권한이 있는 경우 글을 보임
			if($member['me_level']<=$c_array['controll_level']||$member['me_level']<=$c_array['secret_level']){
				$read_true = 1;	
			//그 외
			}else{
				//비회원의 글이고 로그인 되지 않은 경우 패스워드 폼을 보임
				if($array['me_idno']==0&&!isset($__toony_member_idno)){
					$read_true = 3;
				//글이 답글이고, 비밀번호가 저장되어 있는 경우(비회원 글에 대한 답변) 패스워드 폼을 보임
				}else if($array['rn']>0&&$array['password']!=""&&!isset($__toony_member_idno)){
					$read_true = 3;
				//글이 답글이고, 자신의 글에 대한 답글인 경우 글을 보임
				}else if($array['rn']>0&&$parent_array['me_idno']==$member['me_idno']){
					$read_true = 1;
				//자신의 글인 경우 글을 보임
				}else if($array['me_idno']==$member['me_idno']){
					$read_true = 1;
				//그 외 아무 권한 없음
				}else{
					$read_true = 0;	
				}
			}
		//비밀글이 아닌 경우
		}else if($array['use_secret']=="N"){
			//글 읽기 권한이 있는 경우 글을 보임
			if($member['me_level']<=$c_array['read_level']){
				$read_true = 1;	
			//그 외
			}else{
				//공지글인 경우 글을 보임
				if($array['use_notice']=="Y"){
					$read_true = 1;
				//로그인 되어있지 않은 경우 패스워드 폼을 보임
				}else if($array['me_idno']==0&&!isset($__toony_member_idno)){
					$read_true = 3;	
				//그 외 아무 권한 없음
				}else{
					$read_true = 0;	
				}
			}
		}
	}
	
	/*
	회원인 경우 포인트 부여/차감
	*/
	if(isset($__toony_member_idno)&&!isset($__toony_board_view)&&$array['me_idno']!=$member['me_idno']){
		if($c_array['read_point']<0&&$c_array['read_point']!=0){
			if($member['me_point']<=0){
				$lib->error_alert_back("포인트가 부족하여 글을 조회할 수 없습니다.","A");
			}
			$point = 0-$c_array['read_point'];
			$lib->func_member_point_add($member['me_idno'],"out",$point,"게시판 글 조회 ({$c_array['name']})");
		}else if($c_array['read_point']!=0){
			$lib->func_member_point_add($member['me_idno'],"in",$c_array['read_point'],"게시판 글 조회 ({$c_array['name']})");
		}
	}
	/*
	조회수 +1 시킴
	*/
	if(!isset($__toony_board_view)){
		$mysql->query("
			UPDATE toony_module_board_data_$board_id
			SET view=view+1
			WHERE idno=$read
		");
		//중복 방지를 위해 조회수 세션을 생성
		$session->session_register('__toony_board_view_'.$board_id.'_'.$read,$read);
	}
	
	/*
	스킨 템플릿 로드
	*/
	//패스워드 입력 폼
	$read_true_3->skin_file_path("modules/board/skin/{$c_array['skin']}/{$viewDir}read.html");
	$read_true_3->skin_loop_array("[{read_password_start}]","[{read_password_end}]");
	//글 읽기 페이지
	$skin_read->skin_file_path("modules/board/skin/{$c_array['skin']}/{$viewDir}read.html");
	
	/*
	템플릿 함수
	*/
	//비밀글 아이콘 출력
	function read_secret_ico(){
		global $array,$c_array;
		if($array['use_secret']=="Y"){
			return "<img src=\"".__URL_PATH__."modules/board/images/array_list_secret.gif\" alt=\"비밀글\" style=\"padding-right:5px;\" />";
		}
	}
	//모바일 작성 아이콘 출력
	function read_mobile_ico(){
		global $array;
		if($array['use_html']=="N"){
			return "<img src=\"".__URL_PATH__."modules/board/images/array_list_mobile.gif\" align=\"middle\" style=\"margin:0 0 0 3px;\" title=\"모바일에서 작성\" alt=\"모바일에서 작성\" />";
		}else{
			return "";
		}
	}
	//첨부파일명 및 용량(Byte) 출력
	function read_file_text($cnt_field,$file){
		global $board_id, $lib;
		if($file){
			return "<a href=\"".__URL_PATH__."modules/board/file_download.php?board_id=".$board_id."&file=".urlencode($file)."\">".$file."</a>&nbsp;<span style=\"font-size:11px; color:#999; padding-left:10px;\">(".$lib->func_file_size(__DIR_PATH__."modules/board/upload/".$board_id."/".$file,"K").")</span><span style=\"font-size:11px; color:#999; padding-left:10px;\">".number_format($cnt_field)."회 다운로드</span>";
		}else{
			return "";
		}
	}
	//삭제 버튼 출력
	function delete_true_func(){
		global $member,$c_array,$array,$member_idno;
		if($member['me_level']<=$c_array['controll_level']){
			return 1;	
		}else{
			if($array['me_idno']=="0"&&!isset($__toony_member_idno)&&$member['me_level']<=$c_array['delete_level']){
				return 3;
			}else if($array['me_idno']==$member['me_idno']&&$member['me_level']<=$c_array['delete_level']){
				return 1;
			}else{
				return 0;
			}
		}
	}
	function read_delete_btn(){
		global $c_array,$article;
		if(delete_true_func()==1||delete_true_func()==3){
			return "<input type=\"button\" id=\"read_delete_btn\" class=\"__button_cancel\" value=\"삭제\" />";
		}
	}
	//수정 버튼 출력
	function write_true_func(){
		global $member,$c_array,$array,$member_idno,$viewType;
		//일반적인 경우
		if($member['me_level']<=$c_array['controll_level']){
			$returnVar = 1;	
		}else{
			if($array['me_idno']=="0"&&!isset($__toony_member_idno)&&$c_array['write_level']==10){
				$returnVar = 3;
			}else if($array['me_idno']==$member['me_idno']&&$member['me_level']<=$c_array['write_level']){
				$returnVar = 1;
			}else{
				$returnVar = 0;
			}
		}
		//모바일에서 작성한 글을 PC에서 보는 경우 / PC에서 작성한 글을 모바일에서 보는 경우는 출력 안함
		if($array['use_html']=="Y"&&$viewType=="m"||$array['use_html']=="N"&&$viewType=="p"){
			$returnVar = 0;
		}
		
		return $returnVar;
	}
	function read_modify_btn(){
		global $board, $read, $page, $where, $keyword, $c_array, $article, $viewDir, $category;
		if(write_true_func()==1||write_true_func()==3){
			return "<input type=\"button\" class=\"__button_submit\" value=\"수정\" onclick=\"document.location.href='".__URL_PATH__."{$viewDir}?article={$article}&p=write&mode=modify&category=".urlencode($category)."&read={$read}&page={$page}&where={$where}&keyword={$keyword}';\" />";
		}
	}
	//답글 버튼 출력
	function reply_true_func(){
		global $member,$c_array,$array,$member_idno,$article;
		if(($member['me_level']>$c_array['write_level']&&$member['me_level']>$c_array['controll_level'])||$array['use_notice']=="Y"||$c_array['use_reply']=="N"||$member['me_level']>$c_array['reply_level']){
			return 0;
		}else{
			return 1;
		}
	}
	function read_reply_btn(){
		global $board, $read, $page, $where, $keyword, $c_array, $article, $viewDir, $category;
		if(reply_true_func()==1){
			return "<input type=\"button\" class=\"__button_submit\" value=\"답글작성\" onclick=\"document.location.href='".__URL_PATH__."{$viewDir}?article={$article}&p=write&mode=reply&category=".urlencode($category)."&read={$read}&page={$page}&where={$where}&keyword={$keyword}';\" />";
		}
	}
	//리스트 버튼 출력
	function read_array_btn(){
		global $board, $idno, $page, $where, $keyword, $c_array, $article, $viewDir, $category, $viewDir;
		return "<input type=\"button\" class=\"__button_cancel\" value=\"리스트\" onclick=\"document.location.href='".__URL_PATH__."{$viewDir}?article={$article}&category=".urlencode($category)."&read={$idno}&page={$page}&where={$where}&keyword={$keyword}';\" />";
	}
	
	//첨부 이미지 출력
	function img_func($img){
		global $lib,$board_id,$viewType;
		if(strtolower(array_pop(explode(".",$img)))=='gif'||strtolower(array_pop(explode(".",$img)))=='jpg'||strtolower(array_pop(explode(".",$img)))=='bmp'||strtolower(array_pop(explode(".",$img)))=='png'){
			return "<img src=\"".__URL_PATH__."modules/board/upload/".$board_id."/".$img."\" /><br /><br />";
		}else{
			return "";
		}
	}
	
	//내용 출력
	function memo_output_func(){
		global $array,$lib;
		if($array['use_html']=="Y"){
			return $array['ment'];
		}else{
			return htmlspecialchars(nl2br($array['ment']));
		}
	}
	//카테고리명 출력
	function read_category_name(){
		global $c_array,$array;
		if($c_array['use_category']=="Y"&&$array['category']!=""&&$array['use_notice']!="Y"){
			return $array['category'];	
		}else{
			return "";
		}
	}
	//회원 이름 출력
	function read_bbs_me_nick(){
		global $array,$article, $viewType;
		if($viewType=="p"&&$array['me_idno']!=0){
			return "<a href=\"#\" member_profile=\"{$array['me_idno']}\" article=\"{$article}\">{$array['writer']}</a>";
		}else{
			return $array['writer'];
		}
	}
	
	/*
	템플릿 치환
	*/
	//패스워드 입력 폼 출력
	if($read_true==3){
		$read_true_3->skin_modeling("[/boardskinDir/]",__URL_PATH__."modules/board/skin/".$c_array['skin']."/".$viewDir);
		$read_true_3->skin_modeling("[board_id_value]",$board_id);
		$read_true_3->skin_modeling("[read_value]",$read);
		$read_true_3->skin_modeling("[page_value]",$page);
		$read_true_3->skin_modeling("[where_value]",$where);
		$read_true_3->skin_modeling("[keyword_value]",$keyword);
		$read_true_3->skin_modeling("[article_value]",$article);
		$read_true_3->skin_modeling("[category_value]",$category);
		echo $read_true_3->skin_echo();
	//일반 글 보기 출력
	}else if($read_true==1){
		
		include_once __DIR_PATH__."modules/board/skin/".$c_array['skin']."/plugins/read.inc.php";
		$skin_read->skin_modeling("[/boardskinDir/]",__URL_PATH__."modules/board/skin/".$c_array['skin']."/".$viewDir);
		$skin_read->skin_modeling_hideArea("[{read_password_start}]","[{read_password_end}]","hide");
		if($array['file1']){
			$skin_read->skin_modeling_hideArea("[{read_file1_start}]","[{read_file1_end}]","show");
		}else{
			$skin_read->skin_modeling_hideArea("[{read_file1_start}]","[{read_file1_end}]","hide");
		}
		if($array['file2']){
			$skin_read->skin_modeling_hideArea("[{read_file2_start}]","[{read_file2_end}]","show");
		}else{
			$skin_read->skin_modeling_hideArea("[{read_file2_start}]","[{read_file2_end}]","hide");
		}
		if($c_array['use_comment']=="Y"){
			$skin_read->skin_modeling_hideArea("[{read_comment_start}]","[{read_comment_end}]","show");
		}else{
			$skin_read->skin_modeling_hideArea("[{read_comment_start}]","[{read_comment_end}]","hide");
		}
		if($c_array['use_likes']=="Y"){
			$skin_read->skin_modeling_hideArea("[{read_likes_start}]","[{read_likes_end}]","show");
		}else{
			$skin_read->skin_modeling_hideArea("[{read_likes_start}]","[{read_likes_end}]","hide");
		}
		$skin_read->skin_modeling("[article_value]",$article);
		$skin_read->skin_modeling("[category_value]",$category);
		$skin_read->skin_modeling("[board_id_value]",$board_id);
		$skin_read->skin_modeling("[read_value]",$read);
		$skin_read->skin_modeling("[page_value]",$page);
		$skin_read->skin_modeling("[where_value]",$where);
		$skin_read->skin_modeling("[keyword_value]",$keyword);
		$skin_read->skin_modeling("[secret_ico]",read_secret_ico());
		$skin_read->skin_modeling("[mobile_ico]",read_mobile_ico());
		$skin_read->skin_modeling("[subject]",$array['subject']);
		$skin_read->skin_modeling("[hit]",number_format((int)$array['view']));
		$skin_read->skin_modeling("[date]",date("Y-m-d",strtotime($array['regdate'])));
		$skin_read->skin_modeling("[datetime]",$array['regdate']);
		$skin_read->skin_modeling("[writer]",read_bbs_me_nick());
		$skin_read->skin_modeling("[memo]","<div class=\"smartOutput\">".memo_output_func()."</div>");
		$skin_read->skin_modeling("[img1]",img_func($array['file1']));
		$skin_read->skin_modeling("[img2]",img_func($array['file2']));
		$skin_read->skin_modeling("[file1]",read_file_text($array['file1_cnt'],$array['file1']));
		$skin_read->skin_modeling("[file2]",read_file_text($array['file2_cnt'],$array['file2']));
		$skin_read->skin_modeling("[likes]",$array['likes_count']);
		$skin_read->skin_modeling("[unlikes]",$array['unlikes_count']);
		$skin_read->skin_modeling("[category]",read_category_name());
		$skin_read->skin_modeling("[delete_btn]",read_delete_btn());
		$skin_read->skin_modeling("[modify_btn]",read_modify_btn());
		$skin_read->skin_modeling("[reply_btn]",read_reply_btn());
		$skin_read->skin_modeling("[array_btn]",read_array_btn());
		$skin_read->skin_modeling("[comment_area]","<div id=\"read_comment_area\"></div>");
		$skin_read->skin_modeling("[td_1]",$array['td_1']);
		$skin_read->skin_modeling("[td_2]",$array['td_2']);
		$skin_read->skin_modeling("[td_3]",$array['td_3']);
		$skin_read->skin_modeling("[td_4]",$array['td_4']);
		$skin_read->skin_modeling("[td_5]",$array['td_5']);
		
		echo $skin_read->skin_echo();
	}else if($read_true==0){
		switch($array['use_secret']){
			case "N" :
				$lib->func_page_level(__URL_PATH__.$viewDir."?article=login&redirect=".urlencode("?article={$article}&p=read&read={$read}&board_id={$board_id}&where={$where}&keyword={$keyword}"),$c_array[read_level]);
				break;
			case "Y" :
				$lib->error_alert_back("접근 권한이 없습니다.","A");
				break;
		}
	}
	
	/*
	하단 리스트 출력 기능을 이용하는 경우 하단에 리스트 노출
	*/
	if($c_array['use_list']=="Y"&&$read_true!=3){
		include __DIR_PATH__."modules/board/index.php";
	}
	
	/*
	하단 파일&소스코드 출력
	*/
	echo $c_array['bottom_source'];
	if($c_array['bottom_file']){
		include $c_array['bottom_file'];
	}
	
?>