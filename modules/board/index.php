<?php
	include_once __DIR_PATH__."include/pageJustice.inc.php";
	
	$method = new methodController();
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$paging = new pagingClass();
	$header = new skinController();
	$notice_loop = new skinController();
	$array_loop = new skinController();
	$footer = new skinController();
	
	$method->method_param("GET","where,keyword,page,read,category");
	
	/*
	게시판 설정 정보 로드
	*/
	$mysql->select("
		SELECT *
		FROM toony_module_board_config 
		WHERE board_id='$board_id'
	");
	$mysql->fetchArray("board_id,name,use_list,use_comment,use_category,category,use_likes,use_reply,use_file1,use_file2,void_html,file_limit,list_limit,length_limit,array_level,write_level,secret_level,comment_level,delete_level,read_level,controll_level,reply_level,regdate,skin,top_file,bottom_file,tc_1,tc_2,tc_3,tc_4,tc_5");
	$c_array = $mysql->array;
	$mysql->htmlspecialchars = 0;
	$mysql->nl2br = 0;
	$mysql->fetchArray("top_source,bottom_source");
	$c_array = $mysql->array;
	
	/*
	검사
	*/
	if(!$board_id){
		$lib->error_alert_location("게시판이 지정되지 않았습니다.",__URL_PATH__.$viewDir,"A");
	}
	if($mysql->numRows()<1){
		$lib->error_alert_location("존재하지 않는 게시판 입니다.",__URL_PATH__.$viewDir,"A");
	}
	$lib->func_page_level(__URL_PATH__.$viewDir."?article=login&redirect=".urlencode("?article={$article}&board_id={$board_id}&where={$where}&keyword={$keyword}"),$c_array['array_level']);
	
	/*
	상단 파일&소스코드 출력
	*/
	if(!$read_true){
		if($c_array['top_file']){
			include $c_array['top_file'];
		}
		echo $c_array['top_source'];
	}
	
	/*
	카테고리 검색 처리
	*/
	$category = urldecode($category);
	if($category!=""){
		$search = "and A.category='".$category."'";	
	}else{
		$search = "";	
	}
	
	/*
	검색 키워드 처리
	*/
	$keyword = htmlspecialchars(urlencode($keyword));
	if($keyword!=""){
		$keyword = urldecode($keyword);
		if($where=="subject"){
			$search .= "and A.subject like '%".$keyword."%'";
		}else if($where=="ment"){
			$search .= "and A.ment like '%".$keyword."%'";
		}else if($where=="writer"){
			$search .= "and A.writer like '%".$keyword."%'";
		}
	}else{
		$search .= "";
	}
	switch($where){
		case "subject" :
			$ss = "selected";
			break;
		case "ment" :
			$sm = "selected";
			break;
		case "writer" :
			$sw = "selected";
			break;
	}
	
	
	/*
	스킨 템플릿 로드
	*/
	//header
	$header->skin_file_path("modules/board/skin/{$c_array['skin']}/{$viewDir}index.html");
	$header->skin_loop_header("[{notice_loop_start}]");
	//notice array
	$notice_loop->skin_file_path("modules/board/skin/{$c_array['skin']}/{$viewDir}index.html");
	$notice_loop->skin_loop_array("[{notice_loop_start}]","[{notice_loop_end}]");
	//array
	$array_loop->skin_file_path("modules/board/skin/{$c_array['skin']}/{$viewDir}index.html");
	$array_loop->skin_loop_array("[{array_loop_start}]","[{array_loop_end}]");
	//footer
	$footer->skin_file_path("modules/board/skin/{$c_array['skin']}/{$viewDir}index.html");
	$footer->skin_loop_footer("[{array_loop_end}]");
	
	/*
	템플릿 함수
	*/
	//관리자 버튼 출력
	function bbs_controll_btn(){
		global $member,$c_array, $board_id, $article, $where, $keyword, $page, $category;
		if($member['me_level']<=$c_array['controll_level']){
			return "<input type=\"button\" class=\"__button_cancel\" value=\"관리\" id=\"array_controll_btn\" article=\"{$article}\" board_id=\"{$board_id}\" where=\"{$where}\" keyword=\"{$keyword}\" page=\"{$page}\" category=\"{$category}\" />";
		}else{
			return "";
		}
	}
	//관리자 체크박스 출력
	function array_subject_controll_chk(){
		global $array;
		return "<input type=\"checkbox\" name=\"cnum[]\" value=\"".$array['idno']."\" style=\"border:0;\">";
	}
	//제목 출력
	function bbs_subject_title(){
		global $article,$lib;
		global $board_id, $page, $where, $keyword, $c_array, $array, $lib, $category;
		return array_subject_reply()."<a href=\"{$viewDir}?article={$article}&category=".urlencode($category)."&p=read&read=".$array['idno']."&page=".$page."&where=".$where."&keyword=".$keyword."\">".$lib->func_length_limit($array['subject'],0,$c_array['length_limit'])."</a>";
	}
	//내용 출력
	function bbs_ment_title(){
		global $article,$lib;
		global $board_id, $page, $where, $keyword, $c_array, $array, $lib;
		return "<a href=\"{$viewDir}?article={$article}&category=".urlencode($category)."&p=read&read=".$array['idno']."&page=".$page."&where=".$where."&keyword=".$keyword."\" class=\"webzine_ment\">".$lib->func_length_limit(strip_tags($lib->htmldecode($array['ment'])),0,90)."</a>";
	}
	//모바일 작성 아이콘 출력
	function array_subject_mobile(){
		global $array;
		if($array['use_html']=="N"){
			return "<img src=\"".__URL_PATH__."modules/board/images/array_list_mobile.gif\" align=\"middle\" style=\"margin:0 0 0 3px;\" title=\"모바일에서 작성\" alt=\"모바일에서 작성\" />";
		}else{
			return "";
		}
	}
	//파일 아이콘 출력
	function array_subject_file(){
		global $array,$url_path;
		$file1_type = strtolower(array_pop(explode(".",$array['file1'])));
		$file2_type = strtolower(array_pop(explode(".",$array['file2'])));
		if($file1_type=="gif"||$file1_type=="jpg"||$file1_type=="bmp"||$file1_type=="png"){
			return "<img src=\"".__URL_PATH__."modules/board/images/array_list_img.gif\" align=\"middle\" style=\"margin:0 0 0 3px;\" title=\"그림파일\" alt=\"그림파일\" />";
		}else if($array['file1']!=""){
			return "<img src=\"".__URL_PATH__."modules/board/images/array_list_file.gif\" align=\"middle\" style=\"margin:0 0 0 3px;\" title=\"파일\" alt=\"파일\" />";
		}
		if($file2_type=="gif"||$file2_type=="jpg"||$file2_type=="bmp"||$file2_type=="png"){
			return "<img src=\"".__URL_PATH__."modules/board/images/array_list_img.gif\" align=\"middle\" style=\"margin:0 0 0 3px;\" title=\"그림파일\" alt=\"그림파일\" />";
		}else if($array['file2']!=""){
			return "<img src=\"".__URL_PATH__."modules/board/images/array_list_file.gif\" align=\"middle\" style=\"margin:0 0 0 3px;\" title=\"파일\" alt=\"파일\" />";
		}
	}
	//답글 아이콘 출력
	function array_subject_reply(){
		global $array;
		if($array['rn']>0){
			for($s=1;$s<=$array['rn'];$s++){
				$space .= "&nbsp;&nbsp;";
			}
			return $space."<img src=\"".__URL_PATH__."modules/board/images/array_list_reply.gif\" align=\"absmiddle\" title=\"답변글\" alt=\"답변글\" />&nbsp;";
		}
	}
	//비밀글 아이콘 출력
	function array_subject_secret(){
		global $array;
		if($array['use_secret']=="Y"){
			return "<img src=\"".__URL_PATH__."modules/board/images/array_list_secret.gif\" title=\"비밀글\" alt=\"비밀글\" style=\"padding-right:5px;\" />";
		}
	}
	//댓글 갯수 출력
	function array_subject_comment(){
		global $array, $c_array;
		if($array['cmtnum']>=1&&$c_array['use_comment']=="Y"){
			return number_format($array['cmtnum']);
		}
	}
	//작성 버튼 출력
	function bbs_write_btn(){
		global $member, $c_array, $board, $page, $where, $keyword, $article, $viewDir, $category;
		if($member['me_level']<=$c_array['write_level']){
			return "<input type=\"button\" class=\"__button_submit\" value=\"글 작성\" onclick=\"document.location.href='".__URL_PATH__."{$viewDir}?article={$article}&category=".urlencode($category)."&p=write&page={$page}&where={$where}&keyword=".urlencode($keyword)."';\" />";
		}
	}
	//게시물 번호 출력
	function bbs_number($num){
		global $read,$array;
		if($read==$array['idno']){
			return ">";
		}else{
			return $num;
		}
	}
	//상단 설정 버튼 출력
	function bbs_setting_btn(){
		global $c_array, $member, $board_id, $_SERVER;
		if($member['me_level']==1||$member['me_admin']=="Y"){
			return "
				<input type=\"button\" class=\"__button_small_gray\" value=\"게시판 설정\" onclick=\"document.location.href='".__URL_PATH__."admin/?m=board&p=boardList_modify&type=modify&act={$board_id}';\" />
			";
		}else{
			return "";
		}
	}
	//회원 이름 출력
	function bbs_me_nick(){
		global $array,$article;
		if($array['me_idno']!=0){
			return "<a href=\"#\" member_profile=\"{$array['me_idno']}\" article=\"{$article}\">{$array['writer']}</a>";
		}else{
			return $array['writer'];
		}
	}
	//카테고리 출력
	function bbs_category(){
		global $c_array,$category;
		if($c_array[use_category]=="Y"){
			$cat_exp = explode("|",$c_array[category]);
			$cat_op = "<select name=\"category\">";
			$cat_op .= "<option value=\"all\">카테고리 전체</option>";
			for($i=0;$i<sizeOf($cat_exp);$i++){
				if($category==$cat_exp[$i]){
					$selected = "selected";	
				}else{
					$selected = "";	
				}
				$cat_op .= "<option value=\"{$cat_exp[$i]}\" {$selected}>{$cat_exp[$i]}</option>";	
			}
			$cat_op .= "</select>";
			return $cat_op;
		}
	}
	//카테고리명 출력
	function array_category_name(){
		global $c_array,$array;
		if($c_array['use_category']=="Y"&&$array['category']!=""&&$array['use_notice']!="Y"){
			return "[".$array['category']."]";	
		}else{
			return "";
		}
	}
	//썸네일 출력
	function thumbnail_func(){
		global $array,$lib,$board_id,$page,$where,$keyword,$article,$category;
		//본문내 첫번째 이미지 태그를 추출
		preg_match("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $lib->htmldecode($array['ment']), $match);
		//썸네일의 파일 타입을 추출
		$file1_type = strtolower(array_pop(explode(".",$array['file1'])));
		$file2_type = strtolower(array_pop(explode(".",$array['file2'])));
		//조건에 따라 썸네일 HTML코드 리턴
		if($file1_type=="gif"||$file1_type=="jpg"||$file1_type=="bmp"||$file1_type=="png"){
			$thumb = "<a href=\"{$viewDir}?article={$article}&category=".urlencode($category)."&p=read&read=".$array['idno']."&page=".$page."&where=".$where."&keyword=".$keyword."\">".$lib->func_img_resize("modules/board/upload/".$board_id."/",$array['file1'],120,80,0,0)."</a>";
		}else if($file2_type=='gif'||$file2_type=='jpg'||$file2_type=='bmp'||$file2_type=='png'){
			$thumb = "<a href=\"{$viewDir}?article={$article}&category=".urlencode($category)."&p=read&read=".$array['idno']."&page=".$page."&where=".$where."&keyword=".$keyword."\">".$lib->func_img_resize("modules/board/upload/".$board_id."/",$array['file2'],120,80,0,0)."</a>";
		}else if($match['0']){
			$thumb = "<a href=\"{$viewDir}?article={$article}&category=".urlencode($category)."&p=read&read=".$array['idno']."&page=".$page."&where=".$where."&keyword=".$keyword."\"><img src=\"{$match[1]}\" width=\"120\" height=\"80\" /></a>";
		}else{
			$thumb = "
				<a href='{$viewDir}?article={$article}&category=".urlencode($category)."&p=read&read=".$array['idno']."&page=".$page."&where=".$where."&keyword=".$keyword."'><img src=\"".__URL_PATH__."images/blank_thumbnail.jpg\" width=\"120\" height=\"80\" /></a>
			";
		}
		return $thumb;
	}
	
	/*
	스킨 CSS로드
	*/
	echo "\n<link href=\"".__URL_PATH__."modules/board/skin/{$c_array['skin']}/{$viewDir}style.css\" rel=\"stylesheet\" type=\"text/css\" />";
	
	/*
	템플릿 치환
	*/
	//header
	$header->skin_modeling("[board_id_value]",$board_id);
	$header->skin_modeling("[page_value]",$page);
	$header->skin_modeling("[article_value]",$article);
	$header->skin_modeling("[category_value]",$category);
	$header->skin_modeling("[bbs_setting]",bbs_setting_btn());
	$header->skin_modeling("[bbs_category]",bbs_category());
	$header->skin_modeling("[board_title]",$c_array[name]);
	if($member['me_level']<=$c_array['controll_level']||$member['me_admin']=="Y"){
		$header->skin_modeling_hideArea("[{controll_chk_start}]","[{controll_chk_end}]","show");
	}else{
		$header->skin_modeling_hideArea("[{controll_chk_start}]","[{controll_chk_end}]","hide");
	}
	if($c_array['use_likes']=="Y"){
		$header->skin_modeling_hideArea("[{likes_start}]","[{likes_end}]","show");
	}else{
		$header->skin_modeling_hideArea("[{likes_start}]","[{likes_end}]","hide");
	}
	echo $header->skin_echo();
	//notice array
	$paging_query = "
		SELECT *,
		(
			SELECT COUNT(*)
			FROM toony_module_board_comment_$board_id
			WHERE bo_idno=A.idno
		) cmtnum,
		(
			SELECT COUNT(*)
			FROM toony_module_board_like
			WHERE board_id='$board_id' AND read_idno=A.idno AND likes>0
		) likes_count,
		(
			SELECT COUNT(*)
			FROM toony_module_board_like
			WHERE board_id='$board_id' AND read_idno=A.idno AND unlikes>0
		) unlikes_count
		FROM toony_module_board_data_$board_id A
		WHERE A.use_notice='Y'
		ORDER BY A.idno DESC
	";
	$mysql->select($paging_query);
	$array_total = $mysql->numRows();
	if($array_total>0){
		do{
			$mysql->htmlspecialchars = 1;
			$mysql->fetchArray("category,writer,subject,name,ment,me_nick,idno,me_idno,regdate,view,vote,cmtnum,use_notice,use_secret,use_html,file1,file2,rn,likes_count,unlikes_count,td_1,td_2,td_3,td_4,td_5");
			$array = $mysql->array;
			$notice_loop->skin_modeling("[controll_chk]",array_subject_controll_chk());
			$notice_loop->skin_modeling("[subject]",bbs_subject_title());
			$notice_loop->skin_modeling("[hit]",number_format((int)$array['view']));
			$notice_loop->skin_modeling("[write_date]",date("y.m.d",strtotime($array['regdate'])));
			$notice_loop->skin_modeling("[write_datetime]",date("y.m.d H:i:s",strtotime($array['regdate'])));
			$notice_loop->skin_modeling("[writer]",bbs_me_nick());
			$notice_loop->skin_modeling("[comment_num]",array_subject_comment());
			$notice_loop->skin_modeling("[file_ico]",array_subject_file());
			$notice_loop->skin_modeling("[secret_ico]",array_subject_secret());
			$notice_loop->skin_modeling("[mobile_ico]",array_subject_mobile());
			$notice_loop->skin_modeling("[likes]",$array['likes_count']);
			$notice_loop->skin_modeling("[unlikes]",$array['unlikes_count']);
			$notice_loop->skin_modeling("[category_name]",array_category_name());
			if($member['me_level']<=$c_array['controll_level']||$member['me_admin']=="Y"){
				$notice_loop->skin_modeling_hideArea("[{controll_chk_start}]","[{controll_chk_end}]","show");
			}else{
				$notice_loop->skin_modeling_hideArea("[{controll_chk_start}]","[{controll_chk_end}]","hide");
			}
			if($c_array['use_likes']=="Y"){
				$notice_loop->skin_modeling_hideArea("[{likes_start}]","[{likes_end}]","show");
			}else{
				$notice_loop->skin_modeling_hideArea("[{likes_start}]","[{likes_end}]","hide");
			}
			echo $notice_loop->skin_echo();
		}while($mysql->nextRec());
	}
	//array
	$paging_query = "
		SELECT *,
		(
			SELECT COUNT(*)
			FROM toony_module_board_comment_$board_id 
			WHERE bo_idno=A.idno
		) cmtnum,
		(
			SELECT COUNT(*)
			FROM toony_module_board_like
			WHERE board_id='$board_id' AND read_idno=A.idno AND likes>0
		) likes_count,
		(
			SELECT COUNT(*)
			FROM toony_module_board_like
			WHERE board_id='$board_id' AND read_idno=A.idno AND unlikes>0
		) unlikes_count
		FROM toony_module_board_data_$board_id A
		WHERE A.use_notice='N' $search
		ORDER BY A.ln DESC, A.rn ASC, A.regdate DESC
	";
	$mysql->select($paging_query);
	$paging_query_no = $mysql->numRows();
	$paging->page_param($page);
	$total_num = $paging->setTotal($paging_query_no);
	$paging->setListPerPage($c_array['list_limit']);
	$sql = $paging->getPaggingQuery($paging_query);
	$mysql->select($sql);
	$array_total = $mysql->numRows();
	if($array_total>0){
		$j = 1;
		do{
			$mysql->htmlspecialchars = 1;
			$mysql->fetchArray("category,writer,subject,name,ment,me_idno,idno,regdate,view,vote,cmtnum,use_notice,use_secret,use_html,file1,file2,rn,likes_count,unlikes_count,td_1,td_2,td_3,td_4,td_5");
			$array = $mysql->array;
			$array_loop->skin_modeling("[controll_chk]",array_subject_controll_chk());
			$array_loop->skin_modeling("[subject]",bbs_subject_title());
			$array_loop->skin_modeling("[ment]",bbs_ment_title());
			$array_loop->skin_modeling("[number]",bbs_number($paging->getNo($i)));$i++;
			$array_loop->skin_modeling("[hit]",number_format((int)$array['view']));
			$array_loop->skin_modeling("[write_date]",date("y.m.d",strtotime($array['regdate'])));
			$array_loop->skin_modeling("[write_datetime]",date("y.m.d H:i:s",strtotime($array['regdate'])));
			$array_loop->skin_modeling("[writer]",bbs_me_nick());
			$array_loop->skin_modeling("[thumbnail]",thumbnail_func());
			$array_loop->skin_modeling("[comment_num]",array_subject_comment());
			$array_loop->skin_modeling("[file_ico]",array_subject_file());
			$array_loop->skin_modeling("[secret_ico]",array_subject_secret());
			$array_loop->skin_modeling("[mobile_ico]",array_subject_mobile());
			$array_loop->skin_modeling("[likes]",$array['likes_count']);
			$array_loop->skin_modeling("[unlikes]",$array['unlikes_count']);
			$array_loop->skin_modeling("[category_name]",array_category_name());
			if($member['me_level']<=$c_array['controll_level']||$member['me_admin']=="Y"){
				$array_loop->skin_modeling_hideArea("[{controll_chk_start}]","[{controll_chk_end}]","show");
			}else{
				$array_loop->skin_modeling_hideArea("[{controll_chk_start}]","[{controll_chk_end}]","hide");
			}
			if($j==1){
				$array_loop->skin_modeling_hideArea("[{array_first_item_start}]","[{array_first_item_end}]","show");
				$array_loop->skin_modeling_hideArea("[{array_last_item_start}]","[{array_last_item_end}]","hide");
			}else if($j==$array_total){
				$array_loop->skin_modeling_hideArea("[{array_first_item_start}]","[{array_first_item_end}]","hide");
				$array_loop->skin_modeling_hideArea("[{array_last_item_start}]","[{array_last_item_end}]","show");
			}else{
				$array_loop->skin_modeling_hideArea("[{array_first_item_start}]","[{array_first_item_end}]","hide");
				$array_loop->skin_modeling_hideArea("[{array_last_item_start}]","[{array_last_item_end}]","hide");
			}
			if($c_array['use_likes']=="Y"){
				$array_loop->skin_modeling_hideArea("[{likes_start}]","[{likes_end}]","show");
			}else{
				$array_loop->skin_modeling_hideArea("[{likes_start}]","[{likes_end}]","hide");
			}
			echo $array_loop->skin_echo();
			$j++;
		}while($mysql->nextRec());
	}
	//footer
	if($array_total>0){
		$footer->skin_modeling_hideArea("[{array_not_loop_start}]","[{array_not_loop_end}]","hide");
	}else{
		$footer->skin_modeling_hideArea("[{array_not_loop_start}]","[{array_not_loop_end}]","show");
	}
	$footer->skin_modeling("[ss_selected]",$ss);
	$footer->skin_modeling("[sm_selected]",$sm);
	$footer->skin_modeling("[sw_selected]",$sw);
	$footer->skin_modeling("[keyword_value]",$keyword);
	$footer->skin_modeling("[write_btn]",bbs_write_btn());
	$footer->skin_modeling("[paging_area]",$paging->Show(__URL_PATH__."{$viewDir}?article={$article}&category={$category}&where={$where}&keyword={$keyword}"));
	$footer->skin_modeling("[controll_btn]",bbs_controll_btn());
	$footer->skin_modeling("[search_btn]","<input type=\"submit\" class=\"__button_small\" value=\"검색\" />");
	$footer->skin_modeling("[search_cancel_btn]","<input type=\"button\" class=\"__button_small_gray\" value=\"취소\" onclick=\"document.location.href='".__URL_PATH__."{$viewDir}?article={$article}';\"; />");
	echo $footer->skin_echo();
	
	/*
	하단 파일&소스코드 출력
	*/
	if(!$read_true){
		echo $c_array['bottom_source'];
		if($c_array['bottom_file']){
			include $c_array['bottom_file'];
		}
	}
	
?>