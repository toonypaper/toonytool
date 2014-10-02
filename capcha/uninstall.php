<?php
/*
	* @file  uninstall.php
	* @author 지앤미(ZnMee) <znmee@naver.com>
	* ZmSpamFree(zmCaptcha) uninstall Program
	* 2009-11-02 Released.
	지엠스팸프리(ZmSpamFree)를 삭제하기 원하신다면,							(To run this program,
	아래 10번째 줄의 $run = 0; 의 값 0을 1로 고친 후 다시 접속하세요.		(change the value of $run = 0 to 1)
*/
$run = 0;	# 제거 프로그램 활성화(activation) 0:실행안함(inactivate), 1:실행(activate)
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko">
<head>
	<title> ZmSpamFree 1.1 Uninstall - http://www.casternet.com/spamfree/</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<style type="text/css">
body { font-size: 13px; line-height: 1.6; }
h1 { font-size: 20px; }
.Succeeded { color: #3eaf0e; }
.Failed { color: #d00; }
</style>
</head>
<body>
<h1>지엠스팸프리(ZmSpamFree) 제거 프로그램</h1>
<h2>ZmSpamFree Uninstall program</h2>
<?php
$copy = '<p><a href="http://www.spamfree.kr" title="SpamFree.kr">SpamFree.kr</a></p>';
if ( !$run ) {
	echo '<p>보안을 위해 본 제거 프로그램이 실행되지 않도록 설정되어 있습니다.</p>
	<p>이 프로그램을 실행하시려면 이 파일(uninstall.php)을 열어서<br />10번째 줄의 $run = 0;을 1로 고친 후 다시 실행해 주세요.</p>';
	echo '<p>This program is inactivated. To activate this program, edit this file at line 10.</p>',$copy;
	echo '</body></html>';
	exit;
}
$msg = array();
$dir = array ( 'Log/', 'Connect/' );
if ( is_dir($dir[0].$dir[1]) ) {
	$zsfDir = opendir($dir[0].$dir[1]);
	# 세션ID 파일 제거
	$i=0;
	while ( $file = readdir($zsfDir) ) {
		if ( $file!='.' && $file!='..' ) {
			if ( unlink ( $dir[0].$dir[1].$file ) ) {
				$msg[$i] = 'Succeeded';
			} else {
				$msg[$i] = 'Failed';
			}
			echo '<p class="',$msg[$i],'"><strong>File deletion ',$msg[$i],'</strong> : ',$dir[0],$dir[1],$file,'</p>';
		}
		$i++;
	}
}
# 로그 파일 제거
$file = array ( 'Denied.php', 'Passed.php' );
foreach ( $file as $v ) {
	if ( is_file( $dir[0].$v ) ) {
		if ( unlink ( $dir[0].$v ) ) {
			$msg[$i] = 'Succeeded';
		} else {
			$msg[$i] = 'Failed';
		}
		echo '<p class="',$msg[$i],'"><strong>File deletion ',$msg[$i],'</strong> : ',$dir[0],$v,'</p>';
	}
	$i++;
}
# 디렉토리 제거
$file = array ( $dir[0].$dir[1], $dir[0]  );
foreach ( $file as $v ) {
	if ( is_dir( $v ) ) {
		if ( rmdir ( $v ) ) {
			$msg[$i] = 'Succeeded';
		} else {
			$msg[$i] = 'Failed';
		}
		echo '<p class="',$msg[$i],'"><strong>Directory deletion ',$msg[$i],'</strong> : ',$v,'</p>';
	}
	$i++;
}

$total = array_count_values($msg);
$total = $total['Succeeded']*1;
?>
<p>총 <?php echo $total; ?> 개의 파일과 디렉토리가 지워졌습니다.</p>
<p>Total <?php echo $total; ?> Files and Directories was deleted.</p>
<p>이제 나머지 파일과 디렉토리를 FTP 등을 이용해서 삭제하시면 완전히 제거됩니다.</p>
<p>Now, You can delete the remaining files and directories to remove this program.</p>
<p>감사합니다.</p>
<p>Thank You.</p>
<?php echo $copy; ?>
</body>
</html>