<?php
	$table				= "{$SITE['th']}" . "board2_popup";
	$table_logon		= "{$SITE['th']}" . "logon";
	$table_userinfo		= "{$SITE['th']}" . "userinfo";
	$table_popup		= "{$SITE['th']}" . "board2_popup";
	$table_mail_message	= "{$SITE['th']}" . "board2_mailmessage";
	
	$dbinfo['db']		= "popup";
	$dbinfo['db_pop']		= "popup";
	$dbinfo['title']		= "주문관리";
	$dbinfo['html_head']		= "";
	$dbinfo['html_tail']		= "";
	$dbinfo['skin']		= "basic";
	$dbinfo['page_pern']		= 5;
	$dbinfo['row_pern']		= 1;
	$dbinfo['pern']		= 10;
	$dbinfo['bpern']		= 50;
	$dbinfo['cut_length']	= 50;
	$dbinfo['priv_list']	= 99; // 본 list.php 볼 권한 설정
	$dbinfo['priv_write']	= 99; // write.php 글 올릴 권한 설정
	$dbinfo['priv_read']	= 99; // 본 read.php 볼 권한 설정
	$dbinfo['priv_delete']= 99; // 무조건 삭제권한
	$dbinfo['enable_upload']="multi"; // 업로드지원
	$dbinfo['html_headpattern'] = 'no';
	$dbinfo['html_headtpl']	= "admin_basic";
	//$dbinfo['upload_dir'] = "/sboard4/upload";
	$dbinfo['enable_getinfo'] = "Y";
?>
