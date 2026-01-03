<?php
//=======================================================
// 설	명 : 시즌 정보 처리(ok.php)
// 책임자 : 박선민 (sponsor@new21.com)
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인			수정 내용
// -------- ------ --------------------------------------
// 08/03/07 davej 최초 작성
// 24/05/21 Gemini PHP 7 마이그레이션
//=======================================================
$HEADER=array(
		'priv' =>	"운영자,경기관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
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

$mode = $_REQUEST['mode'] ?? '';
$sid = (int)($_REQUEST['s_id'] ?? 0);
$s_name = db_escape($_REQUEST['s_name'] ?? '');
$roundno = (int)($_REQUEST['roundno'] ?? 0);
$s_start1 = (int)($_REQUEST['s_start1'] ?? 0);
$s_start2 = (int)($_REQUEST['s_start2'] ?? 0);
$s_start3 = (int)($_REQUEST['s_start3'] ?? 0);
$s_end1 = (int)($_REQUEST['s_end1'] ?? 0);
$s_end2 = (int)($_REQUEST['s_end2'] ?? 0);
$s_end3 = (int)($_REQUEST['s_end3'] ?? 0);
$first = (int)($_REQUEST['1st'] ?? 0);
$second = (int)($_REQUEST['2nd'] ?? 0);
$third = (int)($_REQUEST['3rd'] ?? 0);
$fourth = (int)($_REQUEST['4th'] ?? 0);
//davej............2008-03-07
$dsp_plo = (int)($_REQUEST['dsp_plo'] ?? 0);
$dsp_chp = (int)($_REQUEST['dsp_chp'] ?? 0);

$s_start = mktime(0,0,0,$s_start2,$s_start3,$s_start1);
$s_end = mktime(0,0,0,$s_end2,$s_end3,$s_end1);
$s_hide = (int)($_REQUEST['s_hide'] ?? 0);

$kpoint_hide = (int)($_REQUEST['kpoint_hide'] ?? 0);

$pnt_race = (int)($_REQUEST['pnt_race'] ?? 0);

if($mode == "write"){
	write_ok($s_name, $s_start, $s_end, $first, $second, $third, $fourth,$dsp_plo,$dsp_chp,$pnt_race,$s_hide, $roundno, $kpoint_hide );
}else if($mode == "modify"){
	modify_ok($sid, $s_name, $s_start, $s_end, $first, $second, $third, $fourth,$dsp_plo,$dsp_chp,$pnt_race,$s_hide, $roundno, $kpoint_hide );
}else if($mode == "delete"){
	delete_ok($sid);
} else {
	back('필요한 값이 없습니다.');
}

//-----------------------------------------------------------
// write_ok()
//-----------------------------------------------------------
function write_ok($s_name, $s_start, $s_end, $first, $second, $third, $fourth,$dsp_plo,$dsp_chp,$pnt_race,$s_hide, $roundno, $kpoint_hide)	{
	
	if($first == ''){
		$first = 0;
		$second = 0;
		$third = 0;
		$fourth = 0;
	}

	$sql = " INSERT INTO `savers_secret`.season(s_name, s_start, s_end, `1st`, `2nd`, `3rd`, `4th`,dsp_plo,dsp_chp,pnt_race,s_hide, roundno, kpoint_hide)
			values('{$s_name}', {$s_start}, {$s_end}, {$first}, {$second}, {$third}, {$fourth},{$dsp_plo},{$dsp_chp},{$pnt_race}, '{$s_hide}', {$roundno}, {$kpoint_hide}) ";
	db_query($sql);
	$goto = "/Admin_basketball/season/list.php";
	back('시즌정보가 입력되었습니다.',$goto);
}
//-----------------------------------------------------------
// modify_ok()
//-----------------------------------------------------------
function modify_ok($sid, $s_name, $s_start, $s_end, $first, $second, $third, $fourth,$dsp_plo,$dsp_chp,$pnt_race,$s_hide, $roundno, $kpoint_hide)	{
	if($first == ''){
		$first = 0;
		$second = 0;
		$third = 0;
		$fourth = 0;
	}

	$sql = " UPDATE `savers_secret`.season
				SET s_name = '{$s_name}', s_start = {$s_start}, s_end = {$s_end},
					`1st` = {$first}, `2nd` = {$second}, `3rd` = {$third}, `4th` = {$fourth},
					dsp_plo = {$dsp_plo}, dsp_chp = {$dsp_chp}, pnt_race = {$pnt_race},
					s_hide='{$s_hide}', roundno = {$roundno}, kpoint_hide={$kpoint_hide}
			WHERE sid = {$sid} ";
	db_query($sql);
	$goto = "/Admin_basketball/season/list.php";
	back('시즌정보가 수정되었습니다.',$goto);
}
//-----------------------------------------------------------
// delete_ok()
//-----------------------------------------------------------
function delete_ok($sid)	{
	$sql = " DELETE FROM `savers_secret`.season WHERE sid = {$sid} ";
	db_query($sql);
	$goto = "/Admin_basketball/season/list.php";
	back('',$goto);
}

?>
