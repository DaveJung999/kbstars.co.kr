<?php
//=======================================================
// 설	명 : 사이트 이용약관 페이지
// 책임자 : 박선민 , 검수: 05/01/04
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인			수정 내용
// -------- ------ --------------------------------------
// 05/01/04 박선민 마지막 수정
// 25/08/13 Gemini	php 7.x, mariadb 11 버전으로 마이그레이션
//=======================================================
$HEADER = array();
$HEADER['priv']		= ''; // 인증유무 (비회원,회원,운영자,서버관리자)
$HEADER['usedb2']	= 1; // DB 커넥션 사용
$HEADER['useSkin']	= 1; // 템플릿 사용
$HEADER['useBoard2']	= 1; // board2Count()
$HEADER['useApp']	= 1; // cut_string()
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');
//page_security('', $_SERVER['HTTP_HOST']);

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	include_once('config.php');	// $dbinfo 가져오기
	$dbinfo['html_skin'] = '2015_d12';

	$dbinfo['enable_getinfo']	= 'Y'; // 무조건

	// 넘어온 값에 따라 $dbinfo값 변경
	if($dbinfo['enable_getinfo'] == 'Y'){
		// skin관련
		// PHP 7에서는 eregi()가 제거되었으므로 preg_match()로 변경하고, isset()으로 변수 존재 여부 확인
		if(isset($_GET['html_type']))	$dbinfo['html_type'] = $_GET['html_type'];
		if( isset($_GET['html_skin']) && preg_match('/^[_a-z0-9]+$/i',$_GET['html_skin'])
			and is_file($SITE['html_path'].'index_'.$_GET['html_skin'].'.php') )
			$dbinfo['html_skin'] = $_GET['html_skin'];
		if( isset($_GET['skin']) && preg_match('/^[_a-z0-9]+$/i',$_GET['skin'])
			and is_dir('skin/'.$_GET['skin']) )
			$dbinfo['skin']	= $_GET['skin'];
	}

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================

// 템플릿 기반 웹 페이지 제작
$skinfile=basename(__FILE__,'.php').'.html';
if( !is_file('skin/'.$dbinfo['skin'].'/'.$skinfile) ) $dbinfo['skin']='basic';
$tpl = new phemplate('skin/'.$dbinfo['skin']); // 템플릿 시작
$tpl->set_file('html',$skinfile,TPL_BLOCK);

// 마무리할 할당
$tpl->set_var('site'	,$SITE);

// 마무리
$tpl->echoHtml($dbinfo, $SITE);
?>
