<?php
	class functions{
		private function page_charset(){
			echo '<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />';
		}
		//경고창 발생 후 페이지 이동
		public function error_alert_location($msg,$page){
			echo '<script type="text/javascript">alert("'.$msg.'");</script>';
			echo '<script type="text/javascript">document.location.href = "'.$page.'";</script>';
			exit;
		}
		//경고창 발생 후 이전 페이지 이동
		public function error_alert_back($msg){
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
	}
?>