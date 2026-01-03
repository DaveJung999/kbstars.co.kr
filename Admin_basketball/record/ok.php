<?php
//=======================================================
// 설	명 : 농구 기록 처리(ok.php)
// 책임자 : 박선민 (sponsor@new21.com)
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인			수정 내용
// -------- ------ --------------------------------------
// 08/10/06 davej 최초 작성
// 24/05/21 Gemini PHP 7 마이그레이션
//=======================================================
$HEADER=array(
		'priv' => "운영자,뉴스관리자,사진관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'html_echo' => '', // html header, tail 삽입(tail은 파일 마지막에 echo $SITE['tail'])
		'log' => '' // log_site 테이블에 지정한 키워드로 로그 남김
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $_SERVER['HTTP_HOST']);

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	// $seHTTP_REFERER는 어디서 링크하여 왔는지 저장하고, 로그인하면서 로그에 남기고 삭제된다.
	if( !isset($_SESSION['seUserid']) && !isset($_SESSION['seHTTP_REFERER']) && isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER["HTTP_HOST"]) === false ){
		$_SESSION['seHTTP_REFERER']=$_SERVER['HTTP_REFERER'];
	}
//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

if($mode == "modify"){
	if(!$rid) back_close('수정에 필요한 값이 넘어오지 않았습니다.', "/record/list.php?season=".$season);
}else if($mode == "delete"){	
	if(!$rid)	back_close('삭제할 기록번호가 넘어오지 않았습니다.', "/record/list.php?season=".$season);
}

$pid		= db_escape($_REQUEST['pid'] ?? '');		//선수 아이디


if(!isset($_REQUEST['pback'])){
	// davej....................2008-10-06
	$sql = "select pbackno FROM `savers_secret`.player_teamhistory where pid='" . $pid . "' and sid = " . $s_id;
	$rs2 = db_query($sql);

	$pback = db_result($rs2,0,'pbackno'); 
}

$qs1		= (int)($_REQUEST['qs1'] ?? 0);		//1쿼터 득점
$qs2		= (int)($_REQUEST['qs2'] ?? 0);		//2쿼터 득점
$qs3		= (int)($_REQUEST['qs3'] ?? 0);		//3쿼터 득점
$qs4		= (int)($_REQUEST['qs4'] ?? 0);		//4쿼터 득점
$e1s		= (int)($_REQUEST['e1s'] ?? 0);		//연장1 득점
$e2s		= (int)($_REQUEST['e2s'] ?? 0);		//연장2 득점
$e3s		= (int)($_REQUEST['e3s'] ?? 0);		//연장3 득점
$min1		= (int)($_REQUEST['min1'] ?? 0);	//출전시간 분
$min2		= (int)($_REQUEST['min2'] ?? 0);		//출전시간 초
$m3			= (int)($_REQUEST['m3'] ?? 0);		//3점슛 성공
$m2			= (int)($_REQUEST['m2'] ?? 0);		//2점슛 성공
$mft		= (int)($_REQUEST['mft'] ?? 0);		//자유투 성공
$a3			= (int)($_REQUEST['a3'] ?? 0);		//3점슛 시도
$a2			= (int)($_REQUEST['a2'] ?? 0);		//2점슛 시도
$aft		= (int)($_REQUEST['aft'] ?? 0);		//자유투 시도
$re_off		= (int)($_REQUEST['re_off'] ?? 0);	//공격 리바운드
$re_def		= (int)($_REQUEST['re_def'] ?? 0);	//수비 리바운드
$ast		= (int)($_REQUEST['ast'] ?? 0);		//어시스트
$stl		= (int)($_REQUEST['stl'] ?? 0);		//스틸
$bs			= (int)($_REQUEST['bs'] ?? 0);		//블럭슛
$gd			= (int)($_REQUEST['gd'] ?? 0);		//굿디펜스
$w_ft		= (int)($_REQUEST['w_ft'] ?? 0);		//파울
$w_oft		= (int)($_REQUEST['w_oft'] ?? 0);		//파울(자유투 유)
$tover		= (int)($_REQUEST['tover'] ?? 0);		//턴오버
$ldf		= (int)($_REQUEST['ldf'] ?? 0);		//부정수비
$tf			= (int)($_REQUEST['tf'] ?? 0);		//테크니컬 파울
$min 		= $min1 * 60 + $min2;		//출전시간(초)
$start		= (int)($_REQUEST['start'] ?? 0);


$goto = "read.php?gid=".$gid."&season=".$season."&tid=".$tid ;

if($mode == "write"){
	
	$write_sql 	= " INSERT INTO `savers_secret`.record (
							gid, sid, pid, pback, tid, `1qs`, `2qs`, `3qs`, `4qs`, e1s, e2s, e3s, min, 
							`3p_m`, `3p_a`, `2p_m`, `2p_a`, ft_m, ft_a, re_off, re_def, ast, stl, gd, 
							bs, w_ft, w_oft, tover, ldf, tf, rdate,start 
					) VALUES( 
							{$gid}, {$s_id}, '{$pid}', '{$pback}', {$tid}, {$qs1}, {$qs2}, {$qs3}, {$qs4}, {$e1s}, {$e2s}, {$e3s}, {$min}, 
							{$m3}, {$a3}, {$m2}, {$a2}, {$mft}, {$aft}, {$re_off}, {$re_def}, {$ast}, {$stl}, {$gd}, 
							{$bs}, {$w_ft}, {$w_oft}, {$tover}, {$ldf}, {$tf}, UNIX_TIMESTAMP(), '{$start}'
					) ";

	write_ok($write_sql);
	back_close('등록 되었습니다.', $goto);
	
}else if($mode == "modify"){
	
	$mod_sql = "UPDATE `savers_secret`.record SET
					pback='{$pback}',
					`1qs`={$qs1}, `2qs`={$qs2}, `3qs`={$qs3}, `4qs`={$qs4}, e1s={$e1s}, e2s={$e2s}, e3s={$e3s},
					min={$min}, `3p_m`={$m3}, `3p_a`={$a3}, `2p_m`={$m2}, `2p_a`={$a2}, ft_m={$mft}, ft_a={$aft}, re_off={$re_off}, re_def={$re_def}, ast={$ast}, stl={$stl},
					gd={$gd}, bs={$bs}, w_ft={$w_ft}, w_oft={$w_oft}, tover={$tover}, ldf={$ldf}, tf={$tf}, rdate=UNIX_TIMESTAMP(), start='{$start}'
				WHERE
					rid={$rid}
				";

	modify_ok($mod_sql);
	back_close('수정 되었습니다.', $goto);
	
}else if($mode == "delete"){
	
	delete_ok($rid);
	back_close('삭제 되었습니다.', $goto);
	
} else {
	
	back_close('필요한 값이 없습니다.', $goto);
	
}

//-----------------------------------------------------------
// write_ok()
//-----------------------------------------------------------
function write_ok($write_sql)	{
	
	if($write_sql){
		db_query($write_sql);
	} else {
		back_close('필요한 값이 없습니다.');
	}
}
//-----------------------------------------------------------
// modify_ok()
//-----------------------------------------------------------
function modify_ok($mod_sql)	{
	
	if($mod_sql){
		db_query($mod_sql);
	} else {
		back_close('필요한 값이 없습니다.');
	}
}
//-----------------------------------------------------------
// delete_ok()
//-----------------------------------------------------------
function delete_ok($rid)	{
	$sql = " DELETE from `savers_secret`.record WHERE rid = {$rid} ";
	db_query($sql);
}

?>
