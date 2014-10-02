<?php
	/*
	기본 라이브러리 클래스
	*/
	class libraryClass extends mysqlConnection{
		/*
		블랙리스트 회원 또는 IP 접속 차단
		*/
		function func_index_security(){
			global $site_config,$member;
			//블랙리스트 회원인 경우
			$this->select("select * from toony_admin_security_member where me_idno='$member[me_idno]' or me_id='{$member['me_id']}';");
			if($this->numRows()>0){
				include __DIR_PATH__."security_error.php";
				exit;
			}
			//블랙리스트 IP인 경우
			$this->select("select * from toony_admin_security_ip where ip='".$_SERVER['REMOTE_ADDR']."';");
			if($this->numRows()>0){
				include __DIR_PATH__."security_error.php";
				exit;
			}
		}
		
		/*
		현재 접속자 기록
		*/
		function func_member_online_status(){
			global $member,$_SESSION,$_SERVER;
			$this->session = $_SESSION;
			if(isset($this->session['__toony_member_idno'])){
				$this->select("select me_idno from toony_admin_member_online where me_idno='{$member['me_idno']}';");
				if($this->numRows()<1){
					$this->query("insert into toony_admin_member_online (me_idno,visitdate) values ('{$member['me_idno']}',now());");
				}else{
					$this->query("update toony_admin_member_online set visitdate=now(), guest_ip='{$_SERVER['REMOTE_ADDR']}' where me_idno='{$member['me_idno']}';");
				}
				$this->query("delete from toony_admin_member_online where guest_ip='{$_SERVER['REMOTE_ADDR']}'");
			}else{
				$this->select("select me_idno from toony_admin_member_online where guest_ip='{$_SERVER['REMOTE_ADDR']}';");
				if($this->numRows()<1){
					$this->query("insert into toony_admin_member_online (guest_ip,visitdate) values ('{$_SERVER['REMOTE_ADDR']}',now());");
				}else{
					$this->query("update toony_admin_member_online set visitdate=now() where guest_ip='{$_SERVER['REMOTE_ADDR']}';");
				}
			}
		}
		
		/*
		방문자 통계 기록
		*/
		function func_visiter_counter_status(){
			global $member;
			$this->select("select * from toony_admin_counter where ip='{$_SERVER['REMOTE_ADDR']}' and regdate >= DATE_SUB(now(),interval 1 hour)");
			//If not Database data
			if($this->numRows()<1){
				$this->query("insert into toony_admin_counter (me_idno,me_id,ip,regdate) values ('{$member['me_idno']}','{$member['me_id']}','{$_SERVER['REMOTE_ADDR']}',now())");
			//If have Database data
			}else if($member['me_idno']!=$this->fetch("me_idno")&&$this->fetch("me_idno")==0){
				//modify then data is guest data
				$this->query("update toony_admin_counter set me_idno='{$member['me_idno']}',me_id='{$member['me_id']}' where ip='{$_SERVER['REMOTE_ADDR']}' order by regdate desc limit 1");
			}
		}
		
		/*
		포인트 증가/감소 기록
		*/
		function func_member_point_add($me_idno,$mode,$point,$memo){
			$memo = addslashes($memo);
			if($mode=="in"){
				$this->query("insert into toony_member_point (me_idno,point_in,memo,regdate) values ('$me_idno', '$point', '$memo',now());");
				$this->query("update toony_member_list set me_point=me_point+$point where me_idno='$me_idno';");
			}
			if($mode=="out"){
				$this->query("insert into toony_member_point (me_idno,point_out,memo,regdate) values ('$me_idno', '$point', '$memo',now());");
				$this->query("update toony_member_list set me_point=me_point-$point where me_idno='$me_idno';");
			}
		}
		
		/*
		현재 기준 시간 반환
		*/
		function func_regdate_replacer($date){
			$this->dateOrg = date("Y-m-d H:i:s",strtotime($date));
			$this->dateNow = date("Y-m-d H:i:s");
			$this->dateRe = date("Y-m-d H:i:s",strtotime($this->dateNow)-strtotime("$this->dateOrg GMT"));
			$this->dateRe_Y = date("Y",strtotime($this->dateRe)); //Y
			$this->dateRe_m = date("m",strtotime($this->dateRe)); //m
			$this->dateRe_d = date("d",strtotime($this->dateRe)); //d
			$this->dateRe_H = date("H",strtotime($this->dateRe)); //H
			$this->dateRe_i = date("i",strtotime($this->dateRe)); //i
			$this->dateRe_s = date("s",strtotime($this->dateRe)); //s
			if($this->dateRe_Y>=1971||$this->dateRe_m>=2||$this->dateRe_d>=2){
				if($this->dateRe_d==2&&$this->dateRe_y==1&&$this->dateRe_m==1){
					return "어제";
				}else if($this->dateRe_d>2&&$this->dateRe_d<=7&&$this->dateRe_y==1&&$this->dateRe_m==1){
					return ($this->dateRe_d-1)."일 전";
				}else{
					return date("Y.m.d",strtotime($date));
				}
			}else if($this->dateRe_H>=1&&$this->dateRe_H<=24){
				if(date("d")>date("d",strtotime($this->dateOrg))){
					return "어제";
				}else{
					return number_format($this->dateRe_H)."시간 전";
				}
			}else if($this->dateRe_i>=1&&$this->dateRe_i<=60){
				return number_format($this->dateRe_i)."분 전";
			}else if($this->dateRe_s>=1&&$this->dateRe_s<=60){
				return number_format($this->dateRe_s)."초 전";
			}else{
				return "방금";
			}
		}
		
		/*
		이미지 출력
		*/
		private $img2;
		private $img2_name;
		function func_img_resize($path,$img,$width,$height,$margin,$quad){
			if($quad==1){
				if(strtolower(array_pop(explode(".",$img)))=='gif'||strtolower(array_pop(explode(".",$img)))=='jpg'||strtolower(array_pop(explode(".",$img)))=='bmp'||strtolower(array_pop(explode(".",$img)))=='png'){
					$this->img2 = getimagesize(__DIR_PATH__.$path.$img);
					if($width==""||$width=="0") $width=$this->img2['0'];
					if($height==""||$height=="0") $height=$this->img2['1'];
					
					if($this->img2['0']>$this->img['1']){
						$img_per_size = $this->img2['0']/$this->img2['1'];
						if($width<$this->img2['0']){
							$width_re = $width;
						}else{
							$width_re = $this->img2['0'];
						}
						$height_re = $width_re/$img_per_size;
						if($height_re>$height){
							$img_per_size = $height_re/$height;
							$width_re = $width_re/$img_per_size;
							$height_re = $height_re/$img_per_size;
						}
					}else{
						$img_per_size = $this->img2['1']/$this->img2['0'];
						$width_re = $height_re/$img_per_size;
						$height_re = $height;
						if($width_re>$width){
							$width_re = $width_re-($width_re-$width);
							$height_re = $height_re-($width_re-$width);
						}
					}
					return "<img src=\"".__URL_PATH__.$path.$img."\" border=\"0\" width=\"".$width_re."\" height=\"".$height_re."\" style=\"margin:".$margin."px;\" />";
				}
			}else{
				if(strtolower(array_pop(explode(".",$img)))=='gif'||strtolower(array_pop(explode(".",$img)))=='jpg'||strtolower(array_pop(explode(".",$img)))=='bmp'||strtolower(array_pop(explode(".",$img)))=='png'){
					$this->img2 = getimagesize(__DIR_PATH__.$path.$img);
					if($width==""||$width=="0") $width=$this->img2['0'];
					if($height==""||$height=="0") $height=$this->img2['1'];
					return "<img src=\"".__URL_PATH__.$path.$img."\" border=\"0\" style=\"margin:".$margin."px; width:".$width."px; height:".$height."px;\" />";
				}
			}
		}
		
		/*
		용량 수치화 출력
		*/
		private $size;
		function func_file_size($file,$byte){
			if($byte=="K"){
				$this->size = number_format(filesize($file)/1024,1);
			}else if($byte=="M"){
				$this->size = number_format(filesize($file)/1024/1024,1);
			}else if($byte=="G"){
				$this->size = number_format(filesize($file)/1024/1024/1024,1);
			}
			return $this->size."&nbsp;".$byte."B";
		}
		
		/*
		페이지 이동
		*/
		function func_location($url){
			echo"<script type=\"text/javascript\">location.href='".$url."';</script>";
			exit;
		}
		
		/*
		페이지 이동 (프레임 무시)
		*/
		function func_location_parent($url){
			echo"<script type=\"text/javascript\">parent.location.href='".$url."';</script>";
			exit;
		}
		
		/*
		페이지 접근 레벨 지정 (접근 권한 없는 경우 페이지 이동)
		*/
		function func_page_level($loginAfterUri,$level){
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
		private $cutstr;
		function func_length_limit($str,$start,$end){
			$this->cutstr = mb_substr($str,$start,$end,"UTF-8");
			if(strlen($this->cutstr)<strlen($str)){
				return $this->cutstr."…";
			}else{
				return $this->cutstr;
			}
		}
		
		/*
		에러 -> Alert창 띄움
		*/
		function error_alert($msg,$for){
			global $__toony_member_idno;
			if($for=="A"){
				echo"<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">alert('".$msg."');</script>";
			}else if($for=="M"){
				if(isset($__toony_member_idno)){
					echo"<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">alert('".$msg."');</script>";
				}
			}else if($for=="N"){
				if(!isset($__toony_member_idno)){
					echo"<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">alert('".$msg."');</script>";
				}
			}
		}
		
		/*
		에러 -> Alert창 띄운 뒤 뒤로 페이지 이동
		*/
		function error_alert_back($msg,$for){
			global $__toony_member_idno;
			if($for=="A"){
				echo"<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">alert('".$msg."');history.back();</script>";
				exit;
			}else if($for=="M"){
				if(isset($__toony_member_idno)){
					echo"<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">alert('".$msg."');history.back();</script>";
					exit;
				}
			}else if($for=="N"){
				if(!isset($__toony_member_idno)){
					echo"<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">alert('".$msg."');history.back();</script>";
					exit;
				}
			}
		}
		
		/*
		에러 -> Alert창 띄운 뒤 페이지 이동
		*/
		function error_alert_location($msg,$url,$for){
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
		function error_alert_close($msg,$for){
			global $__toony_member_idno;
			if($for=="A"){
				echo"<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">alert('".$msg."');self.close();</script>";
				exit;
			}else if($for=="M"){
				if(isset($__toony_member_idno)){
					echo"<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">alert('".$msg."');self.close();</script>";
					exit;
				}
			}else if($for=="N"){
				if(!isset($__toony_member_idno)){
					echo"<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">alert('".$msg."');self.close();</script>";
					exit;
				}
			}
		}
		
		/*
		에러 -> alert 없이 페이지 이동
		*/
		function error_location($url,$for){
			if($for=="A"){
				echo"<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">location.href='".$url."';</script>";
				exit;
			}else if($for=="M"){
				if(isset($__toony_member_idno)){
					echo"<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">location.href='".$url."';</script>";
					exit;
				}
			}else if($for=="N"){
				if(!isset($__toony_member_idno)){
					echo"<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><script type=\"text/javascript\">location.href='".$url."';</script>";
					exit;
				}
			}
		}
		
		/*
		동적 페이지 Submit시 보안 검사
		*/
		function security_filter($obj){
			global $_SERVER, $REQUEST_METHOD;
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
		}
		
		/*
		Input 입력 값 유무성 검사
		*/
		function func_method_param_check($type,$val,$errorMsg){
			mb_internal_encoding('UTF-8');
			switch($type){
				case "id" :
					$filter_id = "/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/";
					if(!preg_match($filter_id,$val)){ echo $errorMsg; exit; }
					break;
				case "nick" :
					$filter_nick = "/^[a-zA-Z0-9가-힣]+$/";
					if(mb_strlen($val)<2||mb_strlen($val)>12){echo $errorMsg; exit; }
					if(!preg_match($filter_nick,$val)){ echo $errorMsg; exit; }
					break;
				case "password" :
					if(mb_strlen($val)<5||mb_strlen($val)>30){ echo $errorMsg; exit; }
					break;
				case "name" :
					$filter_name = "/^[가-힣]+$/";
					if(!preg_match($filter_name,$val)){ echo $errorMsg; exit; }
					break;
				case "phone" :
					$filter_phone = "/^[0-9]+$/";
					if($val!=""&&!preg_match($filter_phone,$val)){ echo $errorMsg; exit; }
					break;
				case "telephone" :
					$filter_tel = "/^[0-9]+$/";
					if($val!=""&&!preg_match($filter_tel,$val)){ echo $errorMsg; exit; }
					break;
				case "idx" :
					$filter_tel = "/^[0-9a-zA-Z_]+$/";
					if($val!=""&&!preg_match($filter_tel,$val)){ echo $errorMsg; exit; }
					break;
				case "number" :
					$filter_tel = "/^[0-9]+$/";
					if($val!=""&&!preg_match($filter_tel,$val)){ echo $errorMsg; exit; }
					break;
			}
		}
		
		/*
		htmlspecialchars_decode+br2nl 리턴 함수
		(mysql에서 Array된 변수값은 htmlspecialchars+nl2br이 기본 적용됨)
		*/
		function htmldecode($val){
			return $this->deHtmlspecialchars($this->br2nl($val));
		}
		
		/*
		deHtmlspecialchars 함수
		*/
		function deHtmlspecialchars($val){
			return htmlspecialchars_decode($val);
		}
		
		/*
		br2nl 함수
		*/
		function br2nl($val){
			return preg_replace("/\<br(\s*)?\/?\>/i","\n",$val);
		}
		
		/*
		이미지 원하는 사이즈의 썸네일로 저장
		*/
		function func_resize_save($dst_file,$src_file,$save_path,$max_x,$max_y){
			$this->img_size = getimagesize($src_file);
			$this->img_replace = basename($src_file);
			switch($this->img_size['2']){
				//gif
				case 1 :
					$this->src_img = ImageCreateFromgif($src_file);
					$this->dst_img = ImageCreateTrueColor($max_x, $max_y);
					ImageCopyResampled($this->dst_img,$this->src_img,0,0,0,0,$max_x,$max_y,$this->img_size['0'],$this->img_size['1']);
					Imagegif($this->dst_img,$save_path.$this->img_replace,100);
					break;
				//jpeg or jpg
				case 2 :
					$this->src_img = ImageCreateFromjpeg($src_file);
					$this->dst_img = ImageCreateTrueColor($max_x, $max_y);
					ImageCopyResampled($this->dst_img,$this->src_img,0,0,0,0,$max_x,$max_y,$this->img_size['0'],$this->img_size['1']);
					Imagejpeg($this->dst_img,$save_path.$this->img_replace,100);
					break;
				//png
				case 3 :
					$this->src_img = ImageCreateFrompng($src_file);
					$this->dst_img = ImageCreateTrueColor($max_x, $max_y);
					ImageCopyResampled($this->dst_img,$this->src_img,0,0,0,0,$max_x,$max_y,$this->img_size['0'],$this->img_size['1']);
					Imagepng($this->dst_img,$save_path.$this->img_replace,9);
					break;
			}	
			ImageDestroy($this->dst_img);
			ImageDestroy($this->src_img);
		}
	}
?>