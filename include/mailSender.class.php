<?php
	/*
	메일 발송 클래스
	*/
	interface mail_interface{
		
		function mail_send();
	
	}
	class mailSender extends mysqlConnection implements mail_interface{
		
		public $template = "mailling";
		public $t_name = "";
		public $t_email = "";
		public $f_email = "";
		public $f_name = "";
		public $account_check_url = "";
		public $account_password  = "";
		public $subject = "";
		public $memo = "";
		public $site_title = "";
		public $site_name = "";
		public $bodySource = "";
		public $smtp_sock = "";
		public $smtp_id = "";
		public $smtp_pwd = "";
		public $smtp_server = "";
		public $smtp_port = "";	
		
		public function mail_send(){ 
			global $site_config;
			
			//SMTP 메일서버를 설정한 경우 socket 발송
			if($site_config['ad_use_smtp']=="Y"){
				$this->f_name = "=?UTF-8?B?".base64_encode($site_config['ad_site_name'])."?=";
				$this->f_email = $site_config['ad_email'];
				$this->site_title = $site_config['ad_site_title'];
				$this->site_name = $site_config['ad_site_name'];
				$this->subject = "=?UTF-8?B?".base64_encode($this->subject)."?=";
				$this->memo = str_replace("{{name}}",$this->t_name,$this->memo);
				$this->smtp_id = base64_encode($site_config['ad_smtp_id']);
				$this->smtp_pwd = base64_encode($site_config['ad_smtp_pwd']);
				$this->smtp_server = $site_config['ad_smtp_server'];
				$this->smtp_port = $site_config['ad_smtp_port'];
				$this->select("
					SELECT source
					FROM toony_admin_mailling_template
					WHERE type='{$this->template}'
				");
				$this->htmlspecialchars = 0;
				$this->nl2br = 0;
				//내용 치환
				$this->bodySource = str_replace("{{site_title}}",$this->site_title,$this->fetch("source"));
				$this->bodySource = str_replace("{{site_name}}",$this->site_name,$this->bodySource);
				$this->bodySource = str_replace("{{check_url}}",$this->account_check_url,$this->bodySource);
				$this->bodySource = str_replace("{{password}}",$this->account_password,$this->bodySource);
				$this->bodySource = str_replace("{{name}}",$this->t_name,$this->bodySource);
				$this->bodySource = base64_encode(str_replace("{{memo}}",$this->memo,$this->bodySource));
				//소켓 연결
				$this->smtp_sock = fsockopen($this->smtp_server,$this->smtp_port);
				if(!$this->smtp_sock){
					die("메일서버 접속에 실패 하였습니다.\n"); 
				}
				fputs($this->smtp_sock,"helo ".$this->smtp_server."\r\n");  
				fputs($this->smtp_sock,"auth login\r\n");
				fgets($this->smtp_sock,128);
				fputs($this->smtp_sock,$this->smtp_id."\r\n");
				fgets($this->smtp_sock,128);
				fputs($this->smtp_sock,$this->smtp_pwd."\r\n");
				fgets($this->smtp_sock,128);
				fputs($this->smtp_sock,"MAIL FROM: <".$this->f_email.">\r\n");
				fgets($this->smtp_sock,128);
				fputs($this->smtp_sock,"rcpt to: <".$this->t_email.">\r\n");
				fgets($this->smtp_sock,128);
				//소켓 메일 데이터 발송
				fputs($this->smtp_sock,"data\r\n");
				fgets($this->smtp_sock,128);
				fputs($this->smtp_sock,"Return-Path: ".$this->f_email."\r\n");
				fputs($this->smtp_sock,"From: ".$this->f_name."<".$this->f_email.">\r\n");
				fputs($this->smtp_sock,"To: <".$this->t_email.">\r\n");
				fputs($this->smtp_sock,"Subject: ".$this->subject."\r\n");
				fputs($this->smtp_sock,"Content-Type: text/html; charset=\"UTF-8\"\r\n");
				fputs($this->smtp_sock,"Content-Transfer-Encoding: base64\r\n");
				fputs($this->smtp_sock,"MIME=Version: 1.0\r\n");
				fputs($this->smtp_sock,"\r\n");
				fputs($this->smtp_sock,$this->bodySource);
				fputs($this->smtp_sock,"\r\n");
				fputs($this->smtp_sock,"\r\n.\r\n");
				fputs($this->smtp_sock,"quit\r\n");
				fclose($this->smtp_sock);
			
			//SMTP 메일서버가 설정되지 않은 경우 apache 발송
			}else{
				$this->f_name = '=?UTF-8?B?'.base64_encode($site_config['ad_site_name']).'?=';
				$this->f_email = $site_config['ad_email'];
				$this->site_title = $site_config['ad_site_title'];
				$this->site_name = $site_config['ad_site_name'];
				$this->memo = str_replace("{{name}}",$this->t_name,$this->memo);
				$this->select("
					SELECT source
					FROM toony_admin_mailling_template
					WHERE type='{$this->template}'
				");
				$this->htmlspecialchars = 0;
				$this->nl2br = 0;
				$this->bodySource = str_replace("{{site_title}}",$this->site_title,$this->fetch("source"));
				$this->bodySource = str_replace("{{site_name}}",$this->site_name,$this->bodySource);
				$this->bodySource = str_replace("{{check_url}}",$this->account_check_url,$this->bodySource);
				$this->bodySource = str_replace("{{password}}",$this->account_password,$this->bodySource);
				$this->bodySource = str_replace("{{name}}",$this->t_name,$this->bodySource);
				$this->bodySource = str_replace("{{memo}}",$this->memo,$this->bodySource);
				//헤더 정보 설정
				$this->headers  = "MIME=Version: 1.0\r\n";
				$this->headers .= "Content-type:text/html; charset=UTF-8\r\n";
				$this->headers .= "From: ".$this->f_name."<".$this->f_email.">\r\n";
				$this->headers .= "Return-Path: ".$this->f_email."\r\n";
				$this->subject = '=?UTF-8?B?'.base64_encode($this->subject).'?=';
				//메일 발송
				mail($this->t_email,$this->subject,$this->bodySource,$this->headers);
			}
		}
	}
?>