<?php
//=======================================================
// 설	명 : 심플리스트
// 책임자 : 박선민 (), 검수: 05/11/20
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 05/11/20 박선민 마지막 수정
//=======================================================
$HEADER = array();
$HEADER['priv']		= ''; // 인증유무 (비회원,회원,운영자,서버관리자)
$HEADER['usedb2']	= 1; // DB 커넥션 사용
$HEADER['useSkin']	= 1; // 템플릿 사용
$HEADER['useBoard2']	= 1; // board2Count()
$HEADER['useApp']	= 1; // cut_string()
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	// 1 . 넘어온값 체크

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
// 템플릿 기반 웹 페이지 제작
$skinfile=basename(__FILE__,'.php').'.html';
if( !is_file('skin/'.$dbinfo['skin'].'/'.$skinfile) ) $dbinfo['skin']='basic';
$tpl = new phemplate('skin/'.$dbinfo['skin']); // 템플릿 시작
$tpl->set_file('html',$skinfile,TPL_BLOCK);

$tpl->set_var('todaydate', date('Y.m.d'));
$tpl->set_var('todaytime', date('H:m:s'));
echo $tpl->process('', 'html'); ?>
