<?php
	$table_season		= "season"; // new21_slist_event

	$dbinfo['table']	= "totalgame_result"; // new21_slist_event
	$table		= $dbinfo['table'];
	
	$dbinfo['title']		= "전체 경기일정 및 결과";
	$dbinfo['skin']		= "basic";
	$dbinfo['pern']		= 1000;
	$dbinfo['cut_length']	= 50;
	$dbinfo['priv_list']	= "운영자,뉴스관리자"; // 본 list.php 볼 권한 설정
	$dbinfo['priv_list']	= "운영자,뉴스관리자"; // write.php 글 올릴 권한 설정
	$dbinfo['priv_list']	= "운영자,뉴스관리자"; // 본 read.php 볼 권한 설정
	$dbinfo['priv_list']	= "운영자,뉴스관리자"; // 무조건 삭제권한
	$dbinfo['enable_upload']="multi"; // 업로드지원 
	$dbinfo['html_headpattern'] = "no";
	$dbinfo['orderby'] = "tr_season "; 
?>
