<?php
	@ini_set("session.save_path",__DIR_PATH__."upload/sessionCookies/");
	@ini_set("session.cache_expire",1440);
	@ini_set("session.gc_maxlifetime",3600);
	session_start();
?>