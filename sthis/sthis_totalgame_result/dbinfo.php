
<?php
	$table				= "{$SITE['th']}slist_" . "totalgame_result"; // new21_slist_event

	$dbinfo['title']		= "전체 경기일정 및 결과";
	$dbinfo['skin']		= "yboard_enjoy";
	$dbinfo['pern']		= 1000;
	$dbinfo['cut_length']	= 50;
	$dbinfo['priv_list']	= 0; // 본 list.php 볼 권한 설정
	$dbinfo['priv_write']	= 99; // write.php 글 올릴 권한 설정
	$dbinfo['priv_read']	= 0; // 본 read.php 볼 권한 설정
	$dbinfo['priv_delete']= 99; // 무조건 삭제권한
	$dbinfo['enable_upload']="multi"; // 업로드지원 
	$dbinfo['html_headpattern'] = "ht";
	$dbinfo['html_headtpl'] = "infogame";
	$dbinfo['orderby'] = "tr_season "; 
?>
