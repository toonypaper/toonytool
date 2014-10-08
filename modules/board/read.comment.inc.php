<?php
	include "../../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$tpl = new skinController();
	$tpl_list = new skinController();
	$method = new methodController();
	$mysql = new mysqlConnection();
	
	
	$method->method_param("GET","board_id,read,viewDir,viewType");
	
	/*
	게시물 기본 정보 로드
	*/
	$mysql->select("
		SELECT *,
		(
			SELECT COUNT(*)
			FROM toony_module_board_comment_$board_id
			WHERE bo_idno=$read
		) cmtnum
		FROM toony_module_board_config
		WHERE board_id='$board_id'
	");
	$mysql->fetchArray("cmtnum,skin,title,use_comment,use_list,use_reply,use_file1,use_file2,use_vote,void_html,file_limit,list_limit,length_limit,array_level,write_level,secret_level,comment_level,delete_level,read_level,reply_level,controll_level,tc_1,tc_2,tc_3,tc_4,tc_5");
	$c_array = $mysql->array;
	
	/*
	스킨 템플릿 로드
	*/
	//Read Comment
	$tpl->skin_file_path("modules/board/skin/{$c_array['skin']}/{$viewDir}read.comment.inc.html");
	$tpl_list->skin_file_path("modules/board/skin/{$c_array['skin']}/{$viewDir}read.comment_list.inc.html");
	$tpl_list->skin_loop_array("[{read_comment_loop_start}]","[{read_comment_loop_end}]");
	
	/*
	댓글 로드
	*/
	$mysql->select("
		SELECT *
		FROM toony_module_board_comment_$board_id
		WHERE bo_idno=$read
		ORDER BY ln ASC, rn ASC, regdate ASC
	");
	
	/*
	상단 Form 출력
	*/
	echo "
		<form name=\"read_comment_form\" id=\"read_comment_form\">
		<input type=\"hidden\" name=\"mode\" value=\"1\" />
		<input type=\"hidden\" name=\"board_id\" value=\"".$board_id."\" />
		<input type=\"hidden\" name=\"read\" value=\"".$read."\" />
		<input type=\"hidden\" name=\"cidno\" value=\"\" />";
	if(isset($__toony_member_idno)){
		echo "<input type=\"hidden\" name=\"type\" value=\"2\" />";
	}else{
		echo "<input type=\"hidden\" name=\"type\" value=\"1\" />";
	}
	
	/*
	템플릿 함수
	*/
	
	//삭제 버튼 출력
	function comment_del_btn(){
		global $carray, $member, $c_array;
		if(($carray['me_idno']==$member['me_idno']&&$carray['me_idno']!="")||$member['me_level']<$c_array['controll_level']){
			return "<img src=\"".__URL_PATH__."modules/board/images/btn_comment_delete.jpg\" id=\"comment_del_img\" name=\"".$carray['idno']."\" alt=\"댓글 삭제\" title=\"댓글 삭제\" style=\"cursor:pointer; margin-left:10px;\" />";
		}	
	}
	
	//수정 버튼 출력
	function comment_modify_btn(){
		global $carray, $member, $c_array;
		if(($carray['me_idno']==$member['me_idno']&&$carray['me_idno']!="")||($member['me_level']<$c_array['controll_level'])){
			return "<img src=\"".__URL_PATH__."modules/board/images/btn_comment_modify.jpg\" id=\"comment_modify_img\" name=\"comment_modify_".$carray['idno']."\" name=\"".$carray['idno']."\" alt=\"댓글 수정\" title=\"댓글 수정\" style=\"cursor:pointer; margin-left:10px;\" />";
		}	
	}
	
	//대댓글 버튼 출력
	function comment_reply_btn(){
		global $carray, $member, $c_array;
		if($member['me_level']<=$c_array['comment_level']){
			return "<img src=\"".__URL_PATH__."modules/board/images/btn_reply_comment.gif\" id=\"comment_reply_img\" name=\"comment_reply_".$carray['idno']."\" name=\"".$carray['idno']."\" alt=\"대댓글 달기\" title=\"대댓글 달기\" style=\"cursor:pointer; margin-left:10px;\" />";
		}else{
			return "";
		}
	}
	
	//수정 댓글상자 출력
	function comment_comment_div(){
		global $carray;
		return "<div class=\"comment_modify\" name=\"comment_modify_".$carray['idno']."\">".$carray['comment']."</div>";
	}
	
	//대댓글인 경우 들여쓰기 클래스 부여
	function reply_comment_depthClass(){
		global $carray;
		if($carray['rn']>0){
			return "reply_comment_depthClass".$carray['rn'];
		}else{
			return "";
		}
	}
	
	//회원 이름 출력
	function bbs_me_nick(){
		global $carray,$article,$viewType;
		if($viewType=="p"&&$carray['me_idno']!=0){
			return "<a href=\"#\" member_profile=\"{$carray['me_idno']}\" article=\"{$article}\">{$carray['writer']}</a>";
		}else{
			return $carray['writer'];
		}
	}

	/*
	템플릿 치환
	*/
	if($c_array['use_comment']=="Y"){
		
		if($member['me_level']<=$c_array['comment_level']){
			$tpl->skin_modeling_hideArea("[{comment_start}]","[{comment_end}]","show");
			if(!isset($__toony_member_idno)){
				$tpl->skin_modeling_hideArea("[{guest_input_start}]","[{guest_input_end}]","show");
			}else{
				$tpl->skin_modeling_hideArea("[{guest_input_start}]","[{guest_input_end}]","hide");
			}
			$tpl->skin_modeling("[nick]",$member['me_nick']);
		}else{
			$tpl->skin_modeling_hideArea("[{comment_start}]","[{comment_end}]","hide");
		}
		if($mysql->numRows()<1){
			$tpl->skin_modeling_hideArea("[{not_comment_start}]","[{not_comment_end}]","show");
		}else{
			$tpl->skin_modeling_hideArea("[{not_comment_start}]","[{not_comment_end}]","hide");
		}
		$tpl->skin_modeling("[comment_num]",$c_array['cmtnum']);
		echo $tpl->skin_echo();
		
		if($mysql->numRows()>0){
			do{
				$mysql->htmlspecialchars = 1;
				$mysql->nl2br = 1;
				$mysql->fetchArray("ln,rn,idno,bo_idno,me_idno,writer,regdate,tr_1,tr_2,tr_3,tr_4,tr_5");
				$carray = $mysql->array;
				$mysql->htmlspecialchars = 0;
				$mysql->nl2br = 1;
				$carray['comment'] = $mysql->fetch("comment");
				$tpl_list->skin_modeling("[reply_comment_depthClass]",reply_comment_depthClass());
				$tpl_list->skin_modeling("[writer]",bbs_me_nick());
				$tpl_list->skin_modeling("[comment]",comment_comment_div());
				$tpl_list->skin_modeling("[del_btn]",comment_del_btn());
				$tpl_list->skin_modeling("[modify_btn]",comment_modify_btn());
				$tpl_list->skin_modeling("[reply_btn]",comment_reply_btn());
				$tpl_list->skin_modeling("[date]",date("Y.m.d",strtotime($carray['regdate'])));
				$tpl_list->skin_modeling("[datetime]",date("y.m.d H:i:s",strtotime($carray['regdate'])));
				if(!isset($__toony_member_idno)){
					$tpl_list->skin_modeling_hideArea("[{guest_input_start}]","[{guest_input_end}]","show");
				}else{
					$tpl_list->skin_modeling_hideArea("[{guest_input_start}]","[{guest_input_end}]","hide");
				}
				echo $tpl_list->skin_echo();
			}while($mysql->nextRec());
		}
	}

	/*
	하단 Form 출력
	*/
	echo "
	</form>
	";
	
?>