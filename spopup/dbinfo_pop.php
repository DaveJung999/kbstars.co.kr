<?php

// 테이블 정의
$table		= $SITE['th'] . 'board2_popup';
$table_popup		= $SITE['th'] . 'board2_popup';
$table_logon			= $SITE['th'] . 'logon';
$table_userinfo			= $SITE['th'] . 'userinfo';
$table_shop_config		= $SITE['th'] . 'shop_config';

// dbinfo 배열
$dbinfo['db']			= 'popup'; // 현재 DB 이름
$dbinfo['db_pop']		= 'popup'; // 팝업 DB 이름
$dbinfo['title']		= '주문관리'; // 게시판 제목
$dbinfo['skin']			= 'basic'; // 사용 스킨
$dbinfo['pern']			= 50; // 한 페이지에 표시할 게시물 수
$dbinfo['bpern']		= 50; // 블록당 페이지 수
$dbinfo['cut_length']	= 50; // 글 제목을 자를 길이
$dbinfo['priv_list']	= 0; // 목록 보기 권한 레벨
$dbinfo['priv_write']	= 0; // 글쓰기 권한 레벨
$dbinfo['priv_read']	= 0; // 글 읽기 권한 레벨
$dbinfo['priv_delete']	= 0; // 삭제 권한 레벨
$dbinfo['enable_upload']= 'multi'; // 파일 업로드 지원 (single, multi, N)
$dbinfo['enable_getinfo'] = 'Y'; // URL 파라미터로 설정값 변경 허용 여부
$dbinfo['html_headpattern'] = 'no';
$dbinfo['html_headtpl']	= 'admin_basic';
?>
