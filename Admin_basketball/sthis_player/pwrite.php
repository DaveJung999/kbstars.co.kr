<?php
//=======================================================
// 설	명 : 게시판 글쓰기(write.php)
// 책임자 : 박선민 (sponsor@new21.com), 검수: 03/12/08
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 03/09/15 박선민 $HEADER['nocache'] -> $HEADER['private']
// 03/10/14 박선민 마지막 수정
// 03/12/08 박선민 bugfix - replay 부분
//=======================================================
$HEADER=array(
		'priv' =>	"운영자,뉴스관리자", // 인증유무 (0:모두에게 허용, 숫자가 높을 수록 레벨업)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'useSkin' =>	1, // 템플릿 사용
		'useBoard2' => 1, // 보드관련 함수 포함
		'useApp' => 1,
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $HTTP_HOST);
//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
$thisPath		= dirname(__FILE__);	
$thisUrl		= "/sthis/sthis_player"; // 마지막 "/"이 빠져야함

$dbinfo['html_headtpl'] = "intro";
$dbinfo['html_headpattern'] = "ht";
$dbinfo['skin']		= "yboard_album";

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

// 템플릿 기반 웹 페이지 제작
$tpl = new phemplate("","remove_nonjs");
if( !is_file("{$thisPath}/stpl/yboard_album/pwrite.htm") ) $dbinfo['skin']="board_basic";
$tpl->set_file('html',"{$thisPath}/stpl/yboard_album/pwrite.htm",TPL_BLOCK);
if($_GET['mode'] == "modify" ){
	$prs_list = db_query("SELECT * FROM new21_slist_player_league WHERE p_uid = '{$puid}' ORDER BY p_uid DESC");
	$list = db_array($prs_list);
	$tpl->set_var('list',$list);
	
	$href["delete"]	= "{$thisUrl}/pok.php?mode=delete&puid={$puid}&uid={$uid}";
	$tpl->set_var('href.delete',$href["delete"]);
}

$form_write = " method='post' action='{$thisUrl}/pok.php' ENCTYPE='multipart/form-data'>";
$form_write .= substr(href_qs("mode={$_GET['mode']}&uid={$uid}",$qs_basic,1),0,-1);

$tpl->set_var('form_write',$form_write);

// 마무리
$val="\\1{$thisUrl}/stpl/{$dbinfo['skin']}/images/";
switch($dbinfo['html_headpattern']){
	case "ht":
		// 전체 홈페이지 템플릿 읽어오기
		$HEADER['header'] == 2;
		if( $dbinfo['html_headtpl'] != "" and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php") ) 
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php");
		else
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");

		echo $SITE['head'] . $dbinfo['html_head'];
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));	
		echo $dbinfo['html_tail'] . $SITE['tail'];
		break;
	case "h":
		// 전체 홈페이지 템플릿 읽어오기
		$HEADER['header'] == 2;
		if( $dbinfo['html_headtpl'] != "" and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php") ) 
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php");
		else
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");

		echo $SITE['head'] . $dbinfo['html_head'];
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));	
		echo $dbinfo['html_tail'];
		break;
	case "t":
		// 전체 홈페이지 템플릿 읽어오기
		$HEADER['header'] == 2;
		if( $dbinfo['html_headtpl'] != "" and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php") ) 
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php");
		else
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");

		echo $dbinfo['html_head'];
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		echo $dbinfo['html_tail'] . $SITE['tail'];
		break;
	case "no":
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		break;
	default:
		echo $dbinfo['html_head'];
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));	
		echo $dbinfo['html_tail'];
} // end switch 
?>
