<?php
	include "../../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	include __DIR_PATH__."capcha/zmSpamFree.php";
	
	$method = new methodController();
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$session = new sessionController();
	$mailSender = new mailSender();
	$fileUploader = new fileUploader();
	$validator = new validator();
	
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	$method->method_param("POST","article,category,category_ed,board_id,writer,subject,use_secret,use_notice,use_html,use_email,ment,password,email,file1_ed,file2_ed,file1_del,file2_del,read,mode,type,page,where,keyword,capcha,td_1,td_2,td_3,td_4,td_5");
	$method->method_param("FILE","file1,file2");
	
	/*
	게시물 설정 정보 로드
	*/
	$mysql->select("
		SELECT *
		FROM toony_module_board_config
		WHERE board_id='$board_id'
	");
	$mysql->fetchArray("write_point,read_point,viewType,name,use_comment,use_list,use_reply,use_file1,use_file2,use_vote,void_html,file_limit,list_limit,length_limit,array_level,write_level,secret_level,comment_level,delete_level,read_level,reply_level,controll_level,tc_1,tc_2,tc_3,tc_4,tc_5");
	$c_array = $mysql->array;
	$mysql->htmlspecialchars = 0;
	$mysql->nl2br = 0;
	$c_array['point_board_name'] = $mysql->fetch("name");
	
	/*
	수정/답글 모드인 경우 원본 글 가져옴
	*/
	if($mode=="modify"||$mode=="reply"){
		$mysql->select("
			SELECT *
			FROM toony_module_board_data_$board_id
			WHERE idno=$read
		");
		$mysql->fetchArray("ln,category,writer,me_idno,idno,use_notice,use_html,use_secret,password,use_email,email,td_1,td_2,td_3,td_4,td_5");
		$wquery = $mysql->array;
	}
	
	/*
	옵션값 처리
	*/
	if($use_notice==true){
		$use_notice = "Y";
	}else{
		$use_notice = "N";
	}
	if($use_secret==true){
		$use_secret = "Y";
	}else{
		$use_secret = "N";
	}
	if($use_email==true){
		$use_email = "Y";
	}else{
		$use_email = "N";
	}
	if($use_notice=="Y"){
		$use_email = "N";
	}
	
	/*
	수정모드인 경우 여분필드 처리
	(입력폼에서 전해 받은 값이 없으면 DB 값을 가져와 변수 값을 채움)
	*/
	if($mode=="modify"){
		if(!$td_1){
			$td_1 = $wquery['td_1'];
		}
		if(!$td_2){
			$td_2 = $wquery['td_2'];
		}
		if(!$td_3){
			$td_3 = $wquery['td_3'];
		}
		if(!$td_4){
			$td_4 = $wquery['td_4'];
		}
		if(!$td_5){
			$td_5 = $wquery['td_5'];
		}
	}
	
	/*
	검사
	*/
	if(!$type||$type>2||$type<1){
		$validator->validt_diserror("","변수 값이 지정 되지 않았습니다.");
	}
	$validator->validt_null("subject","");
	$validator->validt_strLen("ment",30,"",1,"내용은 30자 이상 입력해야 합니다.");
	if($type==1){
		$validator->validt_nick("writer",1,"");
		$validator->validt_password("password",1,"");
		if($use_email=="Y"||$email!=""){
			$validator->validt_email("email",1,"");
		}
		if(zsfCheck($capcha,"")!=true){
			$validator->validt_diserror("capcha","NOT_CAPCHA");
		}
	}
	if($file1['size']>0&&$file2['size']>0&&$file1['name']==$file2['name']){
		$validator->validt_diserror("","동일한 파일을 2개 이상 업로드 할 수 없습니다.");
	}
	$validator->validt_tags("ment",1,"");
	
	//수정모드인 경우 검사
	if($mode=="modify"&&$type==2&&isset($__toony_member_idno)&&$wquery['me_idno']==0){
		$validator->validt_nick("writer",1,"");
		$validator->validt_password("password",1,"");
		if($use_email=="Y"||$email!=""){
			$validator->validt_email("email",1,"");
		}
	}
	//글 작성인 경우, 이미 같은 내용의 글이 존재하는지 검사
	if($mode==""||$mode=="reply"){
		$mysql->select("
			SELECT ment FROM toony_module_board_data_$board_id
			WHERE ment='$ment'
		");
		if($mysql->numRows()>0){
			$validator->validt_diserror("ment","이미 같은 내용의 글이 존재합니다.");
		}
	}
	
	/*
	글 작성 포인트 부여/차감
	*/
	if($mode==""||$mode=="reply"){
		if($c_array['write_point']<0&&$c_array['write_point']!=0){
			if($member['me_point']<=0){
				$lib->error_alert_back("포인트가 부족하여 글을 작성할 수 없습니다.","A");
			}
			$point = 0-$c_array['write_point'];
			$lib->func_member_point_add($member['me_idno'],"out",$point,"게시판 글 작성 ({$c_array['point_board_name']})");
		}else if($c_array['write_point']!=0){
			$lib->func_member_point_add($member['me_idno'],"in",$c_array['write_point'],"게시판 글 작성 ({$c_array['point_board_name']})");
		}
	}
	
	/*
	첨부파일 저장
	*/
	$fileUploader->savePath = __DIR_PATH__."modules/board/upload/".$board_id."/";
	//파일1 저장
	$file1_name = "";
	if($file1['size']>0&&$c_array['use_file1']){
		$fileUploader->saveFile = $file1;
		//경로 및 파일 검사
		$fileUploader->filePathCheck();
		if($fileUploader->fileNameCheck()==true){
			$validator->validt_diserror("file1","첨부 불가능한 확장자입니다.");
		}
		if($fileUploader->fileByteCheck($c_array['file_limit'])==false){
			$validator->validt_diserror("file1","허용 파일 용량 초과");
		}
		//파일저장
		$file1_name = date("ymdhis",mktime())."_".$file1['name'];
		$file1_name = str_replace(" ","_",$file1_name);
		if($fileUploader->fileUpload($file1_name)==false){
			$validator->validt_diserror("file1","첨부파일1 업로드 실패");
		}
	}
	//파일2 저장
	$file2_name = "";
	if($file2['size']>0&&$c_array['use_file2']){
		$fileUploader->saveFile = $file2;
		//경로 및 파일 검사
		$fileUploader->filePathCheck();
		if($fileUploader->fileNameCheck()==true){
			$validator->validt_diserror("file2","첨부 불가능한 확장자입니다.");
		}
		if($fileUploader->fileByteCheck($c_array['file_limit'])==false){
			$validator->validt_diserror("file2","허용 파일 용량 초과");
		}
		//파일저장
		$file2_name = date("ymdhis",mktime())."_".$file2['name'];
		$file2_name = str_replace(" ","_",$file2_name);
		if($fileUploader->fileUpload($file2_name)==false){
			$validator->validt_diserror("file2","첨부파일2 업로드 실패.");
		}
	}
	//수정모드인 경우 파일 삭제
	if($mode=="modify"){
		if($file1_del==true){
			$fileUploader->fileDelete($file1_ed);
		}
		if($file2_del==true){
			$fileUploader->fileDelete($file2_ed);
		}
		if($file1_ed!=""&&!$file1['tmp_name']&&$file1_del==false){
			$file1_name=$file1_ed;
		}
		if($file2_ed!=""&&!$file2['tmp_name']&&$file2_del==false){
			$file2_name=$file2_ed;
		}
		if($file1['size']>0&&$file1_ed&&$file1_del!=true){
			$fileUploader->fileDelete($file1_ed);
		}
		if($file2['size']>0&&$file2_ed&&$file2_del!=true){
			$fileUploader->fileDelete($file2_ed);
		}
	}
	
	/**************************************************
	새로운 글쓰기인 경우
	**************************************************/
	if(!$mode){
		//ln값 처리
		$mysql->select("
			SELECT MAX(ln)+1000 AS ln_max
			FROM toony_module_board_data_$board_id
		");
		$ln_array['ln_max'] = $mysql->fetch("ln_max");
		if(!$ln_array['ln_max']) $ln_array['ln_max']=1000;
		$ln_array['ln_max'] = ceil($ln_array['ln_max']/1000)*1000;
		//회원인 경우 회원 아이디를 이메일 주소로 기록
		if($type==2&&isset($__toony_member_idno)){
			$email = $member['me_id'];	
		}
		//회원인 경우 회원 이름을 작성자에 기록
		if($type==2&&isset($__toony_member_idno)){
			$writer = $member['me_nick'];
		}
		//DB 기록
		$mysql->query("
			INSERT INTO toony_module_board_data_$board_id
			(category,me_idno,writer,password,email,ment,subject,file1,file2,use_secret,use_notice,use_html,use_email,ip,regdate,ln,td_1,td_2,td_3,td_4,td_5) 
			VALUES
			('$category','{$member['me_idno']}','$writer','$password','$email','$ment','$subject','$file1_name','$file2_name','$use_secret','$use_notice','$use_html','$use_email','{$_SERVER['REMOTE_ADDR']}',now(),'{$ln_array['ln_max']}','$td_1','$td_2','$td_3','$td_4','$td_5')
		");
		//작성된 글을 다시 로드해옴
		$mysql->select("
			SELECT idno 
			FROM toony_module_board_data_$board_id
			WHERE writer='$writer' AND subject='$subject' AND ment='$ment'
		");
		//조회수 세션 등록
		$session->session_register('__toony_board_view_'.$mysql->fetch("idno"),$mysql->fetch("idno"));
		//완료 후 리턴
		$validator->validt_success("","?article={$article}&category=".urlencode($category_ed)."&p=read&read={$mysql->fetch("idno")}");
	}
	
	/**************************************************
	글 수정인 경우
	**************************************************/
	if($mode=="modify"){
		//작성자 처리
		if($wquery['me_idno']==$__toony_member_idno&&$type==2){
			$writer = $member['me_nick'];
		}else if($wquery['me_idno']!=0&&$type==2){
			$writer = $wquery['writer'];
		}else{
			$writer = $writer;
		}
		//이메일&비밀번호 처리
		if($type==2&&$wquery['me_idno']!=0){
			$email = $wquery['email'];
			$password = $wquery['password'];
		}else{
			$email = $email;
			$password = $password;
		}
		//DB 변경
		$mysql->query("
			UPDATE toony_module_board_data_$board_id
			SET category='$category',writer='$writer',password='$password',email='$email',ment='$ment',subject='$subject',file1='$file1_name',file2='$file2_name',use_secret='$use_secret',use_notice='$use_notice',use_email='$use_email',use_html='$use_html',ip='{$_SERVER['REMOTE_ADDR']}',td_1='$td_1',td_2='$td_2',td_3='$td_3',td_4='$td_4',td_5='$td_5'
			WHERE idno='$read'
		");
		//조회수 세션 등록
		$session->session_register('__toony_board_view_'.$mysql->fetch("idno"),$mysql->fetch("idno"));
		//완료 후 리턴
		$validator->validt_success("","?article={$article}&category=".urlencode($category_ed)."&p=read&read={$mysql->fetch("idno")}&page={$page}&where={$where}&keyword={$keyword}");
	}
	
	/**************************************************
	답글 작성인 경우
	**************************************************/
	if($mode=="reply"){
		//ln값 처리
		$ln_max = (int)$wquery['ln'];
		$ln_min = (int)(ceil($wquery['ln']/1000)*1000)-1000;
		$ln_me = (int)$wquery['ln']-1;
		$mysql->query("
			UPDATE toony_module_board_data_$board_id
			SET ln=ln-1
			WHERE ln<$ln_max AND ln>$ln_min AND rn>0
		");
		//rn값 처리
		$mysql->select("
			SELECT rn+1 AS rn_max 
			FROM toony_module_board_data_$board_id
			WHERE idno='$read'
		");
		$rn_array['rn_max'] = $mysql->fetch("rn_max");
		//회원인 경우 회원 아이디를 이메일 주소로 기록
		if($type==2&&isset($__toony_member_idno)){
			$email = $member['me_id'];	
		}
		//회원인 경우 회원 이름을 작성자에 기록
		if($type==2&&isset($__toony_member_idno)){
			$writer = $member['me_nick'];
		}
		//비회원의 비밀글에 대한 답글인 경우 원본글의 비밀번호를 기록
		if($wquery['use_secret']=="Y"&&$wquery['me_idno']==0){
			$password = $wquery['password'];
		}
		//DB 입력
		$mysql->query("
			INSERT INTO toony_module_board_data_$board_id
			(category,me_idno,writer,password,email,ment,subject,file1,file2,use_secret,use_notice,use_html,use_email,ip,regdate,ln,rn,td_1,td_2,td_3,td_4,td_5)
			VALUES
			('{$wquery['category']}','{$member['me_idno']}','$writer','$password','$email','$ment','$subject','$file1_name','$file2_name','$use_secret','$use_notice','$use_html','$use_email','{$_SERVER['REMOTE_ADDR']}',now(),'$ln_me','{$rn_array['rn_max']}','$td_1','$td_2','$td_3','$td_4','$td_5')
		");
		
		//작성된 글을 다시 로드해옴
		$mysql->select("
			SELECT idno 
			FROM toony_module_board_data_$board_id
			WHERE writer='$writer' AND subject='$subject' AND ment='$ment'
		");
		//원본글이 답글 이메일 수신 옵션이 켜져 있는 경우 원본글 작성자에게 메일 발송
		if($wquery['use_html']=="Y"){
			$viewType_uri = "";
			$saveViewType = "p";
		}else{
			$viewType_uri = "m/";
			$saveViewType = "m";
		}
		if($wquery['use_email']=="Y"){
			$memo = "
				회원님의 게시판 글에 답글이 달렸습니다.<br />
				아래 주소를 클릭하여 확인 하실 수 있습니다.<br /><br />
				
				<a href=\"".__URL_PATH__.$viewType_uri."?article={$article}&p=read&read={$mysql->fetch("idno")}&saveViewType={$saveViewType}\">".__URL_PATH__.$viewType_uri."?article={$article}&read={$mysql->fetch("idno")}&saveViewType={$saveViewType}</a>
			";
			$mailSender->template = "mailling";
			$mailSender->t_email = $wquery['email'];
			$mailSender->t_name = $site_config['ad_site_name'];
			$mailSender->subject = "회원님의 게시글에 답글이 달렸습니다.";
			$mailSender->memo = str_replace('\"','"',stripslashes($memo));
			$mailSender->mail_send();	
		}
		//조회수 세션 등록
		$session->session_register('__toony_board_view_'.$mysql->fetch("idno"),$mysql->fetch("idno"));
		//완료 후 리턴
		$validator->validt_success("","?article={$article}&category=".urlencode($category_ed)."&p=read&read={$mysql->fetch("idno")}&page={$page}&where={$where}&keyword={$keyword}");
	}
?>