
<?php
	$table				= "{$SITE['th']}" . "pollinfo"; // new21_slist_event
	$dbinfo['db'] = $table;

	$dbinfo['title']		= "전체 경기일정 및 결과";
	$dbinfo['skin']		= "yboard_enjoy";
	$dbinfo['pern']		= 10;
	$dbinfo['cut_length']	= 50;
	$dbinfo['priv_list']	= ''; // 본 list.php 볼 권한 설정
	$dbinfo['priv_write']	= '회원'; // write.php 글 올릴 권한 설정
	$dbinfo['priv_read']	= ''; // 본 read.php 볼 권한 설정
	$dbinfo['priv_delete']= '운영자'; // 무조건 삭제권한
	$dbinfo['enable_upload']="multi"; // 업로드지원 
	$dbinfo['html_type'] = "ht";
	$dbinfo['html_skin'] = "supporters";
	$dbinfo['orderby'] = "uid DESC"; 
?>
