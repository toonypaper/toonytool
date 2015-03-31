<?php
	include_once __DIR_PATH__."include/pageJustice.inc.php";
	
	$tpl = new skinController();
	$write_password = new skinController();
	$skin_write = new skinController();
	$method = new methodController();
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$session = new sessionController();
	
	$method->method_param("GET","mode,read,page,where,keyword,category");
	$method->method_param("POST","s_password");
	$session->session_selector("__toony_member_idno");
	
	/*
	패스워드가 submit 된 경우 GET변수를 POST변수로 변환
	*/
	if($s_password!=""){
		$method->method_param("POST","s_board_id,s_mode,s_read,s_password,s_page");
		$board_id=$s_board_id;
		$read=$s_read;
		$mode=$s_mode;
		$page=$s_page;
	}
	if(isset($HTTP_POST_VARS['keyword'])){
		$method->method_param("POST","where,keyword");
	}
	
	/*
	게시물 설정 정보 로드
	*/
	$mysql->select("
		SELECT *
		FROM toony_module_board_config
		WHERE board_id='$board_id'
	");
	$mysql->fetchArray("skin,title,use_comment,use_list,use_secret,use_category,category,use_reply,use_file1,use_file2,void_html,file_limit,list_limit,length_limit,array_level,write_level,secret_level,comment_level,delete_level,read_level,reply_level,controll_level,top_file,bottom_file,ico_secret_def,tc_1,tc_2,tc_3,tc_4,tc_5");
	$c_array = $mysql->array;
	$mysql->htmlspecialchars = 0;
	$mysql->nl2br = 0;
	$mysql->fetchArray("top_source,bottom_source");
	$c_array = $mysql->array;
	
	/*
	수정 혹은 답글 모드인 경우 원본글 정보를 로드
	*/
	if($mode=="modify"||$mode=="reply"){
		$mysql->select("
			SELECT A.*,CEIL(A.ln) ceil_ln,
			(
				SELECT COUNT(*)
				FROM toony_module_board_data_$board_id
				WHERE ln<=((ceil_ln/1000)*1000) AND ln>((ceil_ln/1000)*1000)-1000 AND rn>0
			) reply_count
			FROM toony_module_board_data_$board_id A
			WHERE A.idno='$read'
		");
		$mysql->htmlspecialchars = 0;
		$mysql->nl2br = 0;
		$mysql->fetchArray("idno,reply_count,me_idno,subject,writer,category,me_nick,view,vote,password,email,ment,use_notice,use_secret,use_email,use_html,me_idno,file1,file2,rn,td_1,td_2,td_3,td_4,td_5");
		$array = $mysql->array;
		$array['subject'] = htmlspecialchars($array['subject']);
		$array['writer'] = htmlspecialchars($array['writer']);
		$array['me_nick'] = htmlspecialchars($array['me_nick']);
		$array['password'] = htmlspecialchars($array['password']);
		$array['ment'] = htmlspecialchars($array['ment']);
		if(!$array['idno']){
			$lib->error_alert_back("존재하지 않는 글입니다.","A");
		}
		if($mode=="reply"){
			if($viewType=="p"&&$array['use_html']=="Y"){
				$array['ment'] = "<br /><br /><br />-------------원본글-------------<br /><br />".$array['ment'];
			}else if($viewType=="m"&&$array['use_html']=="N"){
				$array['ment'] = "\n\n\n-------------원본글-------------\n\n".$array['ment'];
			}else{
				$array['ment'] = "";
			}
		}
	}else{
		$array = NULL;
	}
	
	/*
	검사
	*/
	if(!$board_id){
		$lib->error_alert_back("게시판이 지정되지 않았습니다.","A");
	}
	if($member['me_level']>$c_array['write_level']&&$member['me_level']>$c_array['controll_level']){
		$lib->error_alert_back("글 작성 권한이 없습니다.","A");
	}
	if($mode=="reply"&&$c_array['use_reply']=="N"){
		$lib->error_alert_back("답변글을 등록할 수 없습니다.","A");
	}
	//수정모드인 경우 검사
	if($mode=="modify"){
		if($member['me_level']<=$c_array['controll_level']){
			$write_true = 1;	
		}else{
			if($array['me_idno']=="0"&&!isset($__toony_member_idno)){
				$write_true = 3;
			}else if($array['me_idno']==$member['me_idno']&&$member['me_level']<=$c_array['write_level']){
				$write_true = 1;
			}else{
				$wtire_true = 0;	
			}
		}
		if($write_true==0){
			$lib->error_alert_back("수정 권한이 없습니다.","A");
		}
	}
	//답글 모드인 경우
	if($mode=="reply"){
		if(($member['me_level']>$c_array['write_level']&&$member['me_level']>$c_array['controll_level'])||$member['me_level']>$c_array['reply_level']){
			$lib->error_alert_back("답글 작성 권한이 없습니다.","A");
		}
		if($array['use_notice']=="Y"){
			$lib->error_alert_back("공지글에는 답글을 달 수 없습니다.","A");
		}
	}
	
	/*
	패스워드가 submit된 경우 검사
	*/
	if($s_password!=""){
		if($array['password']==$s_password){
			$write_password_true = 1;
		}else{
			$lib->error_alert_back("비밀번호가 일치하지 않습니다.","A");
		}
	}
	
	/*
	스킨 CSS로드
	*/
	echo "\n<link href=\"".__URL_PATH__."modules/board/skin/{$c_array['skin']}/{$viewDir}style.css\" rel=\"stylesheet\" type=\"text/css\" />";
	
	/*
	상단 파일&소스코드 출력
	*/
	$top_file_ex = explode("{||||||||||}",$c_array['top_file']);
	$top_source_ex = explode("{||||||||||}",$c_array['top_source']);
	if($viewType=="p"){
		$ex_slt = 0;
	}else{
		$ex_slt = 1;
	}
	if($top_file_ex[$ex_slt]){
		include $top_file_ex[$ex_slt];
	}
	echo $top_source_ex[$ex_slt];
	
	/*
	스킨 템플릿 로드
	*/
	$tpl->skin_file_path("modules/board/skin/{$c_array['skin']}/{$viewDir}write.html");
	$write_password->skin_html_load($tpl->skin);
	$skin_write->skin_html_load($tpl->skin);
	
	/*
	템플릿 함수
	*/
	//Type 변수 값
	function type_act(){
		global $__toony_member_idno,$mode;
		if((isset($__toony_member_idno)&&!$mode)||(isset($__toony_member_idno)&&$mode=="modify")||(isset($__toony_member_idno)&&$mode=="reply")){
			return "2";
		}else{
			return "1";
		}
	}
	//카테고리 출력
	function bbs_category(){
		global $c_array,$array,$category;
		$cat_exp = explode("|",$c_array['category']);
		$cat_op = "<select name=\"category\" class=\"_category_select\">";
		for($i=0;$i<sizeOf($cat_exp);$i++){
			if(urldecode($cat_exp[$i])==$array['category']||urldecode($cat_exp[$i])==$category){
				$selected = "selected";	
			}else{
				$selected = "";	
			}
			$cat_op .= "<option value=\"{$cat_exp[$i]}\" {$selected}>{$cat_exp[$i]}</option>";	
		}
		$cat_op .= "</select>";
		return $cat_op;
	}
	//파일명 출력
	function write_file_ed($num){
		global $array,$mode;
		if($mode!="reply"){
			return $array['file'.$num];
		}
	}
	//공지글 옵션 출력
	function write_option_notice(){
		global $member, $c_array, $array,$mode;
		if($member['me_level']==1||$member['me_level']<=$c_array['controll_level']){
			//이미 공지글인 경우
			if($array['use_notice']=="Y"){
				return "<label><input type=\"checkbox\" name=\"use_notice\" id=\"use_notice\" checked />공지사항</label>";
			//이미 공지글이 아닌경우
			}else{
				//답글인 경우
				if($array['rn']>0||$mode=="reply"){
					return "";
				//일반글인 경우
				}else{
					return "<label><input type=\"checkbox\" name=\"use_notice\" id=\"use_notice\" />공지사항</label>";
				}
			}
		}else{
			return "";
		}
	}
	//비밀글 옵션 출력
	function write_option_secret(){
		global $c_array,$array;
		if($c_array['use_secret']=="Y"&&($array['use_secret']=="Y"||$c_array['ico_secret_def']=='Y')){
			return "<label><input type=\"checkbox\" name=\"use_secret\" id=\"use_secret\" checked />비밀글</label>";
		}else if($c_array['use_secret']=="Y"){
			return "<label><input type=\"checkbox\" name=\"use_secret\" id=\"use_secret\" />비밀글</label>";
		}else{
			return "";
		}
	}
	//답변메일 옵션 출력
	function write_option_email(){
		global $array;
		if($array['use_email']=="N"){
			return "<label class=\"_use_secret_label\" style=\"display:none;\"><input type=\"checkbox\" name=\"use_email\" id=\"use_email\" />답변 글이 달리면 메일로 알림 받음</label>";
		}else{
			return "<label class=\"_use_secret_label\" style=\"display:none;\"><input type=\"checkbox\" name=\"use_email\" id=\"use_email\" checked />답변 글이 달리면 메일로 알림 받음</label>";
		}
	}
	//submit버튼 출력
	function write_submit_btn(){
		global $c_array;
		return "<input type=\"button\" class=\"write_submit_btn __button_submit\" value=\"작성완료\" />";
	}
	//취소 버튼 출력
	function write_return_btn(){
		global $board, $page, $where, $keyword, $c_array, $article, $viewDir;
		return "<input type=\"button\" class=\"__button_cancel\" value=\"취소\" onclick=\"document.location.href='".__URL_PATH__."{$viewDir}?article={$article}&page={$page}&where={$where}&keyword=".urlencode($keyword)."';\" />";
	}
	//글쓰기 타이틀 출력
	function write_title_echo(){
		global $mode;
		if($mode=="modify"){ return "글 수정하기"; }else if($mode=="reply"){ return "답글 작성하기"; }else{ return "새글 작성하기"; }
	}
	
	/*
	템플릿 치환
	*/
	//패스워드 입력 폼
	if($mode=="modify"&&$member['me_level']==10&&$write_password_true!=1){
		$write_password->skin_modeling("[/boardskinDir/]",__URL_PATH__."modules/board/skin/".$c_array['skin']."/".$viewDir);
		$write_password->skin_loop_array("[{write_password_form_start}]","[{write_password_form_end}]");
		$write_password->skin_modeling("[board_id_value]",$board_id);
		$write_password->skin_modeling("[mode_value]",$mode);
		$write_password->skin_modeling("[read_value]",$read);
		$write_password->skin_modeling("[page_value]",$page);
		$write_password->skin_modeling("[where_value]",$where);
		$write_password->skin_modeling("[keyword_value]",$keyword);
		$write_password->skin_modeling("[article_value]",$article);
		$write_password->skin_modeling("[category_value]",$category);
		echo $write_password->skin_echo();		
	//글쓰기 폼
	}else{
		
		include_once __DIR_PATH__."modules/board/skin/".$c_array['skin']."/plugins/write.inc.php";
		$skin_write->skin_modeling("[/boardskinDir/]",__URL_PATH__."modules/board/skin/".$c_array['skin']."/".$viewDir);
		$skin_write->skin_modeling_hideArea("[{write_password_form_start}]","[{write_password_form_end}]","hide");
		if(!isset($__toony_member_idno)||($mode=="modify"&&$array['me_idno']=="0")){
			$skin_write->skin_modeling_hideArea("[{write_writer_start}]","[{write_writer_end}]","show");
		}else{
			$skin_write->skin_modeling_hideArea("[{write_writer_start}]","[{write_writer_end}]","hide");
		}
		if(!isset($__toony_member_idno)||($mode=="modify"&&$array['me_idno']=="0")){
			$skin_write->skin_modeling_hideArea("[{write_password_start}]","[{write_password_end}]","show");
			$skin_write->skin_modeling_hideArea("[{write_email_start}]","[{write_email_end}]","show");
		}else{
			$skin_write->skin_modeling_hideArea("[{write_password_start}]","[{write_password_end}]","hide");
			$skin_write->skin_modeling_hideArea("[{write_email_start}]","[{write_email_end}]","hide");
		}
		if(!isset($__toony_member_idno)){
			$skin_write->skin_modeling_hideArea("[{write_capcha_start}]","[{write_capcha_end}]","show");
			$skin_write->skin_modeling("[capcha_img]","<img id=\"zsfImg\" src=\"".__URL_PATH__."capcha/zmSpamFree.php?zsfimg\" alt=\"코드를 바꾸시려면 여기를 클릭해 주세요.\" title=\"코드를 바꾸시려면 여기를 클릭해 주세요.\" style=\"cursor:pointer\" onclick=\"this.src='".__URL_PATH__."capcha/zmSpamFree.php?re&amp;zsfimg='+new Date().getTime()\" />");
		}else{
			$skin_write->skin_modeling_hideArea("[{write_capcha_start}]","[{write_capcha_end}]","hide");
		}
		if($c_array['use_file1']=="Y"){
			$skin_write->skin_modeling_hideArea("[{write_file1_start}]","[{write_file1_end}]","show");
		}else{
			$skin_write->skin_modeling_hideArea("[{write_file1_start}]","[{write_file1_end}]","hide");
		}
		if($c_array['use_file2']=="Y"){
			$skin_write->skin_modeling_hideArea("[{write_file2_start}]","[{write_file2_end}]","show");
		}else{
			$skin_write->skin_modeling_hideArea("[{write_file2_start}]","[{write_file2_end}]","hide");
		}
		if($array['file1']!=""&&$mode!="reply"){
			$skin_write->skin_modeling_hideArea("[{write_file1_name_start}]","[{write_file1_name_end}]","show");
		}else{
			$skin_write->skin_modeling_hideArea("[{write_file1_name_start}]","[{write_file1_name_end}]","hide");
		}
		if($array['file2']!=""&&$mode!="reply"){
			$skin_write->skin_modeling_hideArea("[{write_file2_name_start}]","[{write_file2_name_end}]","show");
		}else{
			$skin_write->skin_modeling_hideArea("[{write_file2_name_start}]","[{write_file2_name_end}]","hide");
		}
		if($c_array['use_category']=="Y"&&$mode!="reply"&&$array['rn']==0&&$array['reply_count']<1){
			$skin_write->skin_modeling_hideArea("[{write_category_start}]","[{write_category_end}]","show");
		}else{
			$skin_write->skin_modeling_hideArea("[{write_category_start}]","[{write_category_end}]","hide");
		}
		$skin_write->skin_modeling("[board_id_value]",$board_id);
		$skin_write->skin_modeling("[mode_value]",$mode);
		$skin_write->skin_modeling("[read_value]",$read);
		$skin_write->skin_modeling("[viewDir_value]",$viewDir);
		$skin_write->skin_modeling("[page_value]",$page);
		$skin_write->skin_modeling("[where_value]",$where);
		$skin_write->skin_modeling("[keyword_value]",$keyword);
		$skin_write->skin_modeling("[article_value]",$article);
		$skin_write->skin_modeling("[category_value]",$category);
		$skin_write->skin_modeling("[type_value]",type_act());
		$skin_write->skin_modeling("[writer]",$array['writer']);
		$skin_write->skin_modeling("[write_title]",write_title_echo());
		$skin_write->skin_modeling("[option_notice]",write_option_notice());
		$skin_write->skin_modeling("[option_secret]",write_option_secret());
		$skin_write->skin_modeling("[option_email]",write_option_email());
		$skin_write->skin_modeling("[category_selectbox]",bbs_category());
		$skin_write->skin_modeling("[subject]",$array['subject']);
		if(!isset($__toony_member_idno)&&$mode=="modify"){
			$skin_write->skin_modeling("[password]",$array['password']);
			$skin_write->skin_modeling("[email]",$array['email']);
		}else if(!isset($__toony_member_idno)){
			$skin_write->skin_modeling("[password]","");
			$skin_write->skin_modeling("[email]","");
		}else{
			$skin_write->skin_modeling("[password]",$array['password']);
			$skin_write->skin_modeling("[email]",$array['email']);
		}
		$skin_write->skin_modeling("[memo]",$array['ment']);
		$skin_write->skin_modeling("[file1_name]",write_file_ed(1));
		$skin_write->skin_modeling("[file2_name]",write_file_ed(2));
		$skin_write->skin_modeling("[write_file_byte]","(MAX ".($c_array['file_limit']/1024/1024)."M)");
		$skin_write->skin_modeling("[submit_btn]",write_submit_btn());
		$skin_write->skin_modeling("[return_btn]",write_return_btn());
		$skin_write->skin_modeling("[td_1]",$array['td_1']);
		$skin_write->skin_modeling("[td_2]",$array['td_2']);
		$skin_write->skin_modeling("[td_3]",$array['td_3']);
		$skin_write->skin_modeling("[td_4]",$array['td_4']);
		$skin_write->skin_modeling("[td_5]",$array['td_5']);
		
		echo $skin_write->skin_echo();
	}

	/*
	하단 파일&소스코드 출력
	*/
	$bottom_file_ex = explode("{||||||||||}",$c_array['bottom_file']);
	$bottom_source_ex = explode("{||||||||||}",$c_array['bottom_source']);
	if($viewType=="p"){
		$ex_slt = 0;
	}else{
		$ex_slt = 1;
	}
	echo $bottom_source_ex[$ex_slt];
	if($bottom_file_ex[$ex_slt]){
		include $bottom_file_ex[$ex_slt];
	}
	
?>