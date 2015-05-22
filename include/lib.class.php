<?php
	/*
	기본 라이브러리 클래스
	*/
	class libraryClass extends mysqlConnection{
		
		/*
		블랙리스트 회원 또는 IP 접속 차단
		*/
		public function func_index_security(){
			global $site_config,$member;
			//블랙리스트 회원인 경우
			$this->select("
				SELECT *
				FROM toony_admin_security_member
				WHERE me_idno='$member[me_idno]' OR me_id='{$member['me_id']}'
			");
			if($this->numRows()>0){
				include __DIR_PATH__."security_error.php";
				exit;
			}
			//블랙리스트 IP인 경우
			$this->select("
				SELECT *
				FROM toony_admin_security_ip
				WHERE ip='".$_SERVER['REMOTE_ADDR']."'
			");
			if($this->numRows()>0){
				include __DIR_PATH__."security_error.php";
				exit;
			}
		}
		
		/*
		현재 접속자 기록
		*/
		public function func_member_online_status(){
			global $member,$_SESSION,$_SERVER;
			$session = $_SESSION;
			//회원인 경우
			if(isset($session['__toony_member_idno'])){
				$this->select("
					SELECT me_idno
					FROM toony_admin_member_online
					WHERE me_idno='{$member['me_idno']}'
				");
				//처음 방문인 경우
				if($this->numRows()<1){
					$this->query("
						INSERT into toony_admin_member_online
						(me_idno,visitdate)
						VALUES
						('{$member['me_idno']}',now())
					");
				//방문한 적이 있는 경우
				}else{
					$this->query("
						UPDATE toony_admin_member_online
						SET visitdate=now(),guest_ip='{$_SERVER['REMOTE_ADDR']}'
						WHERE me_idno='{$member['me_idno']}'
					");
				}
				$this->query("
					DELETE
					FROM toony_admin_member_online
					WHERE guest_ip='{$_SERVER['REMOTE_ADDR']}'
				");
			//비회원인 경우
			}else{
				$this->select("
					SELECT me_idno
					FROM toony_admin_member_online
					WHERE guest_ip='{$_SERVER['REMOTE_ADDR']}'
				");
				//처음 방문인 경우
				if($this->numRows()<1){
					$this->query("
						INSERT INTO toony_admin_member_online
						(guest_ip,visitdate)
						VALUES
						('{$_SERVER['REMOTE_ADDR']}',now())
					");
				//방문한 적이 있는 경우
				}else{
					$this->query("
						UPDATE toony_admin_member_online
						SET visitdate=now()
						WHERE guest_ip='{$_SERVER['REMOTE_ADDR']}'
					");
				}
			}
		}
		
		/*
		방문자 통계 기록
		*/
		public function func_visiter_counter_status(){
			global $member;
			$this->select("
				SELECT *
				FROM toony_admin_counter
				WHERE ip='{$_SERVER['REMOTE_ADDR']}' AND regdate>=DATE_SUB(now(),interval 1 hour)
			");
			if($this->numRows()<1){
				$this->query("
					INSERT into toony_admin_counter
					(me_idno,me_id,ip,regdate)
					VALUES
					('{$member['me_idno']}','{$member['me_id']}','{$_SERVER['REMOTE_ADDR']}',now())
				");
			}else if($member['me_idno']!=$this->fetch("me_idno")&&$this->fetch("me_idno")==0){
				$this->query("
					UPDATE toony_admin_counter
					SET me_idno='{$member['me_idno']}',me_id='{$member['me_id']}'
					WHERE ip='{$_SERVER['REMOTE_ADDR']}'
					ORDER BY regdate DESC
					LIMIT 1
				");
			}
		}
		
		/*
		포인트 증가/감소 기록
		*/
		public function func_member_point_add($me_idno,$mode,$point,$memo){
			$memo = addslashes($memo);
			if(!$me_idno||$me_idno<1||$point<1){
				return;
			}
			if($mode=="in"){
				$this->query("
					INSERT into toony_member_point
					(me_idno,point_in,memo,regdate)
					VALUES
					('$me_idno','$point','$memo',now())
				");
				$this->query("
					UPDATE toony_member_list
					SET me_point=me_point+$point
					WHERE me_idno='$me_idno'
				");
			}
			if($mode=="out"){
				$this->query("
					INSERT into toony_member_point
					(me_idno,point_out,memo,regdate)
					VALUES
					('$me_idno','$point','$memo',now());");
				$this->query("
					UPDATE toony_member_list
					SET me_point=me_point-$point
					WHERE me_idno='$me_idno'
				");
			}
		}
		
		/*
		현재 기준 시간 반환
		*/
		public function func_regdate_replacer($date){
			$dateOrg = date("Y-m-d H:i:s",strtotime($date));
			$dateNow = date("Y-m-d H:i:s");
			$dateRe = array();
			$dateRe['FULL'] = date("Y-m-d H:i:s",strtotime($dateNow)-strtotime("$dateOrg GMT"));
			$dateRe['Y'] = date("Y",strtotime($dateRe['FULL']));
			$dateRe['m'] = date("m",strtotime($dateRe['FULL']));
			$dateRe['d'] = date("d",strtotime($dateRe['FULL']));
			$dateRe['H'] = date("H",strtotime($dateRe['FULL']));
			$dateRe['i'] = date("i",strtotime($dateRe['FULL']));
			$dateRe['s'] = date("s",strtotime($dateRe['FULL']));
			if($dateRe['Y']>=1971||$dateRe['m']>=2||$dateRe['d']>=2){
				if($dateRe['d']==2&&$dateRe['Y']==1&&$dateRe['m']==1){
					return "어제";
				}else if($dateRe['d']>2&&$dateRe['d']<=7&&$dateRe['Y']==1&&$dateRe['m']==1){
					return ($this->dateRe['d']-1)."일 전";
				}else{
					return date("Y.m.d",strtotime($date));
				}
			}else if($dateRe['H']>=1&&$dateRe['H']<=24){
				if(date("d")>date("d",strtotime($dateOrg))){
					return "어제";
				}else{
					return number_format($dateRe['H'])."시간 전";
				}
			}else if($dateRe['i']>=1&&$dateRe['i']<=60){
				return number_format($dateRe['i'])."분 전";
			}else if($dateRe['s']>=1&&$dateRe['s']<=60){
				return number_format($dateRe['s'])."초 전";
			}else{
				return "방금";
			}
		}
		
		/*
		파일명 확장명 유효성 검사(fileType 중에 포함 되는지)
		*/
		public function func_fileType_check($img,$fileType){
			$fileTypes = array();
			$fileTypes = explode(",",$fileType);
			$s_file_type=array_pop(explode(".",strtolower($img)));
			$checkVar = FALSE;
			for($i=0;$i<=sizeof($fileTypes)-1;$i++){
				if($s_file_type==$fileTypes[$i]){
					$checkVar = TRUE;
				}
			}
			return $checkVar;
		}
		
		/*
		이미지 출력
		*/
		public function func_img_resize($path,$img,$width,$height,$margin,$quad){
			//비율을 유지하여 출력
			if($quad==1){
				if($this->func_fileType_check($img,"jpg,jpeg,bmp,png,gif")){
					$img_re = getimagesize(__DIR_PATH__.$path.$img);
					if($width==""||$width=="0") $width=$img_re[0];
					if($height==""||$height=="0") $height=$img_re[1];
					if($img_re[0]>$img_re[1]){
						$img_per_size = $img_re[0]/$img_re[1];
						if($width<$img_re[0]){
							$width_re = $width;
						}else{
							$width_re = $img_re[0];
						}
						$height_re = $width_re/$img_per_size;
						if($height_re>$height){
							$img_per_size = $height_re/$height;
							$width_re = $width_re/$img_per_size;
							$height_re = $height_re/$img_per_size;
						}
					}else{
						$img_per_size = $img_re[1]/$img_re[0];
						$height_re = $height;
						$width_re = $height_re/$img_per_size;
						if($width_re>$width){
							$width_re = $width_re-($width_re-$width);
							$height_re = $height_re-($width_re-$width);
						}
					}
					return "<img src=\"".__URL_PATH__.$path.$img."\" border=\"0\" width=\"".$width_re."\" height=\"".$height_re."\" style=\"margin:".$margin."px;\" />";
				}
			}else{
				if($this->func_fileType_check($img,"jpg,jpeg,bmp,png,gif")){
					$img_re = getimagesize(__DIR_PATH__.$path.$img);
					if($width==""||$width==0){
						$width = $img_re[0];
					}
					if($height==""||$height==0){
						$height = $img_re[1];
					}
					return "<img src=\"".__URL_PATH__.$path.$img."\" border=\"0\" style=\"margin:".$margin."px; width:".$width."px; height:".$height."px;\" />";
				}
			}
		}
		
		/*
		용량 수치화 출력
		*/
		public function func_file_size($file,$byte){
			if($byte=="K"){
				$size = number_format(filesize($file)/1024,1);
			}else if($byte=="M"){
				$size = number_format(filesize($file)/1024/1024,1);
			}else if($byte=="G"){
				$size = number_format(filesize($file)/1024/1024/1024,1);
			}
			return $size."&nbsp;".$byte."B";
		}
		
		/*
		페이지 이동
		*/
		public function func_location($url){
			echo"<script type=\"text/javascript\">location.href='".$url."';</script>";
			exit;
		}
		
		/*
		페이지 이동 (프레임 무시)
		*/
		public function func_location_parent($url){
			echo"<script type=\"text/javascript\">parent.location.href='".$url."';</script>";
			exit;
		}
		
		/*
		페이지 접근 레벨 지정 (접근 권한 없는 경우 페이지 이동)
		*/
		public function func_page_level($loginAfterUri,$level){
			global $member,$site_config;
			$this->member = $member;
			$this->site_config = $site_config;
			//비회원인 경우 로그인 페이지로 이동
			if($member['me_level']>9&&$member['me_level']>$level){
				$this->func_location_parent($loginAfterUri);
				exit;
			//회원이지만 레벨이 낮은 경우
			}else if($member['me_level']<10&&$member['me_level']>$level){
				$this->error_alert("접근 권한이 없습니다.","A");
				$this->func_location_parent($site_config['ad_site_url']);
				exit;
			}
		}
		
		/*
		문자열 원하는 글자 수로 자르기
		*/
		public function func_length_limit($str,$start,$end){
			$cutstr = mb_substr($str,$start,$end,"UTF-8");
			if(strlen($cutstr)<strlen($str)){
				return $cutstr."…";
			}else{
				return $cutstr;
			}
		}
		
		/*
		에러 -> Alert창 띄움
		*/
		public function error_alert($msg,$for){
			global $__toony_member_idno;
			if($for=="A"){
				echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">alert('".$msg."');</script>";
			}else if($for=="M"){
				if(isset($__toony_member_idno)){
					echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">alert('".$msg."');</script>";
				}
			}else if($for=="N"){
				if(!isset($__toony_member_idno)){
					echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">alert('".$msg."');</script>";
				}
			}
		}
		
		/*
		에러 -> Alert창 띄운 뒤 뒤로 페이지 이동
		*/
		public function error_alert_back($msg,$for){
			global $__toony_member_idno;
			if($for=="A"){
				echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">alert('".$msg."');history.back();</script>";
				exit;
			}else if($for=="M"){
				if(isset($__toony_member_idno)){
					echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">alert('".$msg."');history.back();</script>";
					exit;
				}
			}else if($for=="N"){
				if(!isset($__toony_member_idno)){
					echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">alert('".$msg."');history.back();</script>";
					exit;
				}
			}
		}
		
		/*
		에러 -> Alert창 띄운 뒤 페이지 이동
		*/
		public function error_alert_location($msg,$url,$for){
			global $__toony_member_idno;
			if($for=="A"){
				echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">alert('".$msg."');location.href='".$url."';</script>";
				exit;
			}else if($for=="M"){
				if(isset($__toony_member_idno)){
					echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">alert('".$msg."');location.href='".$url."';</script>";
					exit;
				}
			}else if($for=="N"){
				if(!isset($__toony_member_idno)){
					echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">alert('".$msg."');location.href='".$url."';</script>";
					exit;
				}
			}
		}
		
		/*
		에러 -> Alert창 띄운 뒤 윈도우 창 닫음
		*/
		public function error_alert_close($msg,$for){
			global $__toony_member_idno;
			if($for=="A"){
				echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">alert('".$msg."');self.close();</script>";
				exit;
			}else if($for=="M"){
				if(isset($__toony_member_idno)){
					echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">alert('".$msg."');self.close();</script>";
					exit;
				}
			}else if($for=="N"){
				if(!isset($__toony_member_idno)){
					echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">alert('".$msg."');self.close();</script>";
					exit;
				}
			}
		}
		
		/*
		에러 -> alert 없이 페이지 이동
		*/
		public function error_location($url,$for){
			if($for=="A"){
				echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">location.href='".$url."';</script>";
				exit;
			}else if($for=="M"){
				if(isset($__toony_member_idno)){
					echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">location.href='".$url."';</script>";
					exit;
				}
			}else if($for=="N"){
				if(!isset($__toony_member_idno)){
					echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">location.href='".$url."';</script>";
					exit;
				}
			}
		}
		
		/*
		동적 페이지 Submit시 보안 검사
		*/
		public function security_filter($obj){
			global $_SERVER,$REQUEST_METHOD;
			if(isset($_SERVER['HTTP_REFERER'])){
				switch($obj){
					case "referer" :
						if(!preg_match(";$_SERVER[HTTP_HOST];",$_SERVER['HTTP_REFERER'])){
							$this->error_alert_location("정상적으로 접근 바랍니다.",__URL_PATH__."index.php","A");
						}
						break;
					case "request_post" :
						if(getenv("REQUEST_METHOD")=="POST"){
							$this->error_alert_location("정상적으로 접근 바랍니다.",__URL_PATH__."index.php","A");
						}
						break;
					case "request_get" :
						if(getenv("REQUEST_METHOD")=="GET"){
							$this->error_alert_location("정상적으로 접근 바랍니다.",__URL_PATH__."index.php","A");
						}
						break;
				}
			}else{
				$this->error_alert_location("정상적으로 접근 바랍니다.",__URL_PATH__."index.php","A");
			}
		}
				
		/*
		htmlspecialchars_decode+br2nl 리턴 함수
		(mysql에서 Array된 변수값은 htmlspecialchars+nl2br이 기본 적용됨)
		*/
		public function htmldecode($val){
			return $this->deHtmlspecialchars($this->br2nl($val));
		}
		
		/*
		deHtmlspecialchars 함수
		*/
		public function deHtmlspecialchars($val){
			return htmlspecialchars_decode($val);
		}
		
		/*
		br2nl 함수
		*/
		public function br2nl($val){
			return preg_replace("/\<br(\s*)?\/?\>/i","\n",$val);
		}
		
		/*
		이미지 원하는 사이즈의 썸네일로 저장
		*/
		public function func_resize_save($dst_file,$src_file,$save_path,$max_x,$max_y){
			$img_size = getimagesize($src_file);
			$img_replace = basename($src_file);
			switch($img_size[2]){
				//gif
				case 1 :
					$src_img = ImageCreateFromgif($src_file);
					$dst_img = ImageCreateTrueColor($max_x, $max_y);
					ImageCopyResampled($dst_img,$src_img,0,0,0,0,$max_x,$max_y,$img_size[0],$img_size[1]);
					Imagegif($dst_img,$save_path.$img_replace,100);
					break;
				//jpeg or jpg
				case 2 :
					$src_img = ImageCreateFromjpeg($src_file);
					$dst_img = ImageCreateTrueColor($max_x, $max_y);
					ImageCopyResampled($dst_img,$src_img,0,0,0,0,$max_x,$max_y,$img_size[0],$img_size[1]);
					Imagejpeg($dst_img,$save_path.$img_replace,100);
					break;
				//png
				case 3 :
					$src_img = ImageCreateFrompng($src_file);
					$dst_img = ImageCreateTrueColor($max_x, $max_y);
					ImageCopyResampled($dst_img,$src_img,0,0,0,0,$max_x,$max_y,$img_size[0],$img_size[1]);
					Imagepng($dst_img,$save_path.$img_replace,9);
					break;
			}	
			ImageDestroy($dst_img);
			ImageDestroy($src_img);
		}
	}
?>