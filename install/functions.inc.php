<?php
	class functions{
		//경고창 발생 후 페이지 이동
		public function error_alert_location($msg,$page){
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
			echo '<script type="text/javascript">alert("'.$msg.'");</script>';
			echo '<script type="text/javascript">document.location.href = "'.$page.'";</script>';
			exit;
		}
		//경고창 발생 후 이전 페이지 이동
		public function error_alert_back($msg){
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
			echo '<script type="text/javascript">alert("'.$msg.'");</script>';
			echo '<script type="text/javascript">history.back();</script>';
			exit;
		}
		//파일이 존재하는지 검사
		public function file_check($file){
			if(is_file($file)){
				return TRUE;
			}else{
				return FALSE;
			}
		}
		//퍼미션 검사
		public function file_permission($file){
			$open = @is_writable($file);
			if(!$open){
				return FALSE;
			}else{
				return TRUE;
			}
		}
		//확장모듈 설치 되었는지 검사
		public function module_check($module){
			if(in_array($module,get_loaded_extensions())==TRUE){
				return TRUE;
			}else{
				return FALSE;
			}
		}
		//PHP버전 확인
		public function php_versionCheck(){
			$ver = phpversion();
			if(version_compare($ver,'5.1.6','>')){  
				return TRUE;  
			}else{  
				return FALSE;
			}  
		}
		//재설치 여부 확인
		public function reinstallCheck(){
			if($this->file_check("../include/path.info.php")==TRUE&&$this->file_check("../include/mysql.info.php")==TRUE){
				return TRUE;
			}else{
				return FALSE;
			}
		}
	}
?>