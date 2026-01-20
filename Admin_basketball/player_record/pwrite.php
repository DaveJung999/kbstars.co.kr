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
		'priv' => "운영자,뉴스관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
		'usedb2' => 1, // DB 커넥션 사용
		'useApp' => 1, // cut_string()
		'useBoard2' => 1, // board2Count(),board2CateInfo()
		'useSkin' => 0, // 템플릿 사용
		'html' => 0,
	);
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');
//page_security("", $HTTP_HOST);
//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
$thisPath		= dirname(__FILE__);	
$thisUrl		= "/Admin_basketball/player_record"; // 마지막 "/"이 빠져야함

// 템플릿 기반 웹 페이지 제작
$tpl = new phemplate("","remove_nonjs");
if( !is_file("{$thisPath}/stpl/basic/pwrite.htm") ) $dbinfo['skin']="board_basic";
$tpl->set_file('html',"{$thisPath}/stpl/basic/pwrite.htm",TPL_BLOCK);

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

	
if($_GET['mode'] == "modify" ){
	$psql = "SELECT * FROM player_league WHERE uid = '{$uid}' ORDER BY pid DESC";
	$prs_list = db_query($psql);
	$list = db_array($prs_list);
	
	$tpl->set_var('list',$list);
	
	$href["delete"]	= "{$thisUrl}/pok.php?mode=delete&pid={$pid}&uid={$list['uid']}";
	$tpl->set_var('href.delete',$href["delete"]);
}
	

$form_write = " method='post' action='{$thisUrl}/pok.php' ENCTYPE='multipart/form-data' > ";
$form_write .= substr(href_qs("mode={$_GET['mode']}&uid={$uid}&pid={$pid}",$qs_basic,1),0,-1);

// 선수정보 가져오기
$p_rs = db_query("SELECT * FROM player where tid = 13 and p_gubun = '현역' order by p_name, p_num ");
$cnt = db_count($p_rs);
if($cnt){
	for($i = 0 ; $i < $cnt ; $i++)	{
		$player = db_array($p_rs);

		//저장된 팀 항목 셀렉트
		if($player['uid'] && $player['uid'] == $pid){
			$psel = "selected";
			$pselect .= "<option value='{$player['uid']}' {$psel}>{$player['p_name']} [{$player['p_position']}]</option>\n";
		} else {
			$pselect .= "<option value='{$player['uid']}'>{$player['p_name']} [{$player['p_position']}]</option>\n";
		}
	}
}
//팀명, 팀아이디 가져오기
$tsql = " SELECT * FROM team ORDER BY tid ASC ";
$trs = db_query($tsql);
$tcnt = db_count($trs);

if($tcnt){
	for($i = 0 ; $i < $tcnt ; $i++)	{
		$tlist = db_array($trs);
		$teamid = $tlist['tid'];
		$t_name[$i] = $tlist['t_name']." (".$tlist['tid'].")";
		//저장된 팀 항목 셀렉트
		if($list['tid'] && $list['tid'] == $teamid){
			$tsel = "selected";
			$tselect .= "<option value={$teamid} {$tsel}>{$t_name[$i]}</option>\n";
		} else {
			$tselect .= "<option value={$teamid}>{$t_name[$i]}</option>\n";
		}
	}
}

// 시즌정보 가져오기
$s_rs = db_query("SELECT * FROM season order by s_start desc, s_end desc");
$cnt = db_count($s_rs);
if($cnt){
	for($i = 0 ; $i < $cnt ; $i++)	{
		$season = db_array($s_rs);

		//저장된 팀 항목 셀렉트
		if($season['sid'] && ($season['sid'] == $list['sid']) ){
			$ssel = "selected";
			$sselect .= "<option value='{$season['sid']}' {$ssel}>{$season['s_name']}</option>\n";
		} else {
			$sselect .= "<option value='{$season['sid']}'>{$season['s_name']}</option>\n";
		}
	}
}
$tpl->set_var('form_write',$form_write);
$tpl->set_var('tselect',$tselect);
$tpl->set_var('pselect',$pselect);
$tpl->set_var('sselect',$sselect);

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