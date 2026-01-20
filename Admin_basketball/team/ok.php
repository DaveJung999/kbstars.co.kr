<?php
//=======================================================
// 설	명 : 팀 정보 처리(ok.php)
// 책임자 : 박선민 (sponsor@new21.com)
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인			수정 내용
// -------- ------ --------------------------------------
// 24/05/18 Gemini PHP 7 마이그레이션
// 25/08/15 Gemini AI SQL 구문 오류 수정 및 보안 강화
//=======================================================
$HEADER=array(
	'priv' =>	"운영자,뉴스관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
	'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
	'html_echo' => '', // html header, tail 삽입(tail은 파일 마지막에 echo $SITE['tail'])
	'log' => '' // log_site 테이블에 지정한 키워드로 로그 남김
);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $_SERVER['HTTP_HOST']);

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
// $seHTTP_REFERER는 어디서 링크하여 왔는지 저장하고, 로그인하면서 로그에 남기고 삭제된다.
if( !isset($_SESSION['seUserid']) && !isset($_SESSION['seHTTP_REFERER']) && isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]) === false ){
	$_SESSION['seHTTP_REFERER'] = $_SERVER['HTTP_REFERER'];
}

// 기본 URL QueryString
$qs_basic = "db=" . ($_REQUEST['db'] ?? '') .			//table 이름
			"&mode=" . ($_REQUEST['mode'] ?? '') .		// mode값은 list.php에서는 당연히 빈값
			"&cateuid=" . ($_REQUEST['cateuid'] ?? '') .		//cateuid
			"&team=" . ($_REQUEST['team'] ?? '') .				// 페이지당 표시될 게시물 수
			"&pern=" . ($_REQUEST['pern'] ?? '') .				// 페이지당 표시될 게시물 수
			"&sc_column=" . ($_REQUEST['sc_column'] ?? '') .	//search column
			"&sc_string=" . urlencode(stripslashes($_REQUEST['sc_string'] ?? '')) . //search string
			"&team=" . ($_REQUEST['team'] ?? '').
			"&html_headtpl=" . ($_REQUEST['html_headtpl'] ?? '').
			"&pid=" . ($_REQUEST['pid'] ?? '').
			"&pname=" . ($_REQUEST['pname'] ?? '').
			"&goto=" . ($_REQUEST['goto'] ?? '').
			"&page=" . ($_REQUEST['page'] ?? '');

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
$mode = $_REQUEST['mode'] ?? '';
$tid = (int)($_REQUEST['tid'] ?? 0);
$t_name = $_REQUEST['t_name'] ?? '';

switch($mode) {
	case "write":
		write_ok($t_name);
		break;
	case "modify":
		modify_ok($tid, $t_name);
		break;
	case "delete":
		delete_ok($tid);
		break;
	default:
		back('필요한 값이 없습니다.');
		break;
}

//-----------------------------------------------------------
// write_ok()
//-----------------------------------------------------------
function write_ok($t_name)	{
	$t_name_escaped = db_escape($t_name);

	$sql = " INSERT INTO `team` (`t_name`) VALUES ('{$t_name_escaped}') ";
	db_query($sql);
	$last_id = db_insert_id();
	
	if ($last_id > 0) {
		$sql = " INSERT INTO `player_cate` (`uid`, `title`, `comment`) VALUES (" . (int)$last_id . ", '{$t_name_escaped}', '{$t_name_escaped}') ";
		db_query($sql);
	}
	$goto = "list.php";
	go_url($goto);
}
//-----------------------------------------------------------
// modify_ok()
//-----------------------------------------------------------
function modify_ok($tid, $t_name)	{
	if ($tid <= 0) {
		back("유효하지 않은 팀 ID입니다.");
	}
	$t_name_escaped = db_escape($t_name);

	$sql = " UPDATE `team` SET `t_name` = '{$t_name_escaped}' WHERE `tid` = " . (int)$tid;
	db_query($sql);

	$sql = " UPDATE `player_cate` SET `title` = '{$t_name_escaped}', `comment` = '{$t_name_escaped}'	WHERE `uid` = " . (int)$tid;
	db_query($sql);
	$goto = "list.php";
	go_url($goto);
}
//-----------------------------------------------------------
// delete_ok()
//-----------------------------------------------------------
function delete_ok($tid)	{
	if ($tid <= 0) {
		back("유효하지 않은 팀 ID입니다.");
	}
	$sql = " DELETE FROM `team` WHERE `tid` = " . (int)$tid;
	db_query($sql);

	$sql = " DELETE FROM `player_cate` WHERE `uid` = " . (int)$tid;
	db_query($sql);
	$goto = "list.php";
	go_url($goto);
}

?>
