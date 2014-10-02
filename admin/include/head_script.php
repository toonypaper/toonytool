<?php
	echo "<title>".$site_config['ad_site_title']."</title>";
	//Meta
	echo "\n<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />";
	echo "\n<meta http-equiv=\"X-UA-Compatible\" content=\"IE=EDGE\" />";
	//CSS
	if($site_config['ad_pavicon']!=""){ echo "\n<link rel=\"shortcut icon\" href=\"".__URL_PATH__."upload/siteInformations/".$site_config['ad_pavicon']."\" />"; }
	echo "\n<link href=\"".__URL_PATH__."library/css/jquery-ui.css\" rel=\"stylesheet\" type=\"text/css\" />";
	echo "\n<link href=\"".__URL_PATH__."admin/library/css/common.css\" rel=\"stylesheet\" type=\"text/css\" />";
	echo "\n<link href=\"".__URL_PATH__."admin/library/css/global.css\" rel=\"stylesheet\" type=\"text/css\" />";
	//JS
	echo "\n<script type=\"text/javascript\">__URL_PATH__ = \"".__URL_PATH__."\";</script>";
	echo "\n<script type=\"text/javascript\" src=\"".__URL_PATH__."admin/library/js/jquery-1.7.1.js\"></script>";
	echo "\n<script type=\"text/javascript\" src=\"".__URL_PATH__."admin/library/js/jquery-ui.js\"></script>";
	echo "\n<script type=\"text/javascript\" src=\"".__URL_PATH__."admin/library/js/ghost_html5.js\"></script>";
	echo "\n<script type=\"text/javascript\" src=\"".__URL_PATH__."admin/library/js/respond.min.js\"></script>";
	echo "\n<script type=\"text/javascript\" src=\"".__URL_PATH__."admin/library/js/jquery.form.js\"></script>";
	echo "\n<script type=\"text/javascript\" src=\"".__URL_PATH__."admin/library/js/global.js\"></script>";
?>