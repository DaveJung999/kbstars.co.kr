<?php
//=======================================================
// 설	명 : 경기 정보 처리(ok.php)
// 책임자 : 박선민 (sponsor@new21.com)
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인			수정 내용
// -------- ------ --------------------------------------
// 24/05/18 Gemini PHP 7 마이그레이션
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
if( !isset($_SESSION['seUserid']) && !isset($_SESSION['seHTTP_REFERER']) && isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]) === false ){
	$_SESSION['seHTTP_REFERER'] = $_SERVER['HTTP_REFERER'];
}

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

// 기본 URL QueryString
$qs_basic = "db=" . ($_REQUEST['db'] ?? ($table ?? '')) .			//table 이름
			"&mode=" . ($_REQUEST['mode'] ?? '') .		// mode값은 list.php에서는 당연히 빈값
			"&cateuid=" . ($_REQUEST['cateuid'] ?? '') .		//cateuid
			"&team=" . ($_REQUEST['team'] ?? '') .				// 페이지당 표시될 게시물 수
			"&pern=" . ($_REQUEST['pern'] ?? '') .				// 페이지당 표시될 게시물 수
			"&sc_column=" . ($_REQUEST['sc_column'] ?? '') .	//search column
			"&sc_string=" . urlencode(stripslashes(isset($sc_string) ? $sc_string : '')) . //search string
			"&team=" . ($_REQUEST['team'] ?? '').
			"&html_headtpl=" . (isset($html_headtpl) ? $html_headtpl : '').
			"&pid=" . ($_REQUEST['pid'] ?? '').
			"&pname=" . ($_REQUEST['pname'] ?? '').
			"&goto=" . ($_REQUEST['goto'] ?? '').
			"&page=" . ($_REQUEST['page'] ?? '');

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
$mode = $_REQUEST['mode'] ?? '';
$gid = (int)($_REQUEST['gid'] ?? 0);
$gameno = $_REQUEST['gameno'] ?? ''; //경기번호
$s_id = (int)($_REQUEST['s_id'] ?? 0); //시즌 아이디

$start_y = (int)($_REQUEST['start_y'] ?? 0); //경기시작 년
$start_m = (int)($_REQUEST['start_m'] ?? 0); //경기시작 월
$start_d = (int)($_REQUEST['start_d'] ?? 0); //경기시작 일
$start_h = (int)($_REQUEST['start_h'] ?? 0); //경기시작 시
$start_mm = (int)($_REQUEST['start_mm'] ?? 0); //경기시작 분
$g_start = mktime($start_h, $start_mm, 0, $start_m, $start_d, $start_y); //경기시작 년월일 unix_timestamp

$end_y = (int)($_REQUEST['end_y'] ?? 0); //경기종료 년
$end_m = (int)($_REQUEST['end_m'] ?? 0); //경기종료 월
$end_d = (int)($_REQUEST['end_d'] ?? 0); //경기종료 일
$end_h = (int)($_REQUEST['end_h'] ?? 0); //경기종료 시
$end_mm = (int)($_REQUEST['end_mm'] ?? 0); //경기종료 분
$g_end = mktime($end_h, $end_mm, 0, $end_m, $end_d, $end_y); //경기종료 년월일 unix_timestamp

$g_ground = $_REQUEST['g_ground'] ?? ''; // 경기장
$g_home = (int)($_REQUEST['g_home'] ?? 0); // 홈팀
$g_away = (int)($_REQUEST['g_away'] ?? 0); // 어웨이팀

$g_referee1 = $_REQUEST['g_referee1'] ?? ''; // 심판1
$g_referee2 = $_REQUEST['g_referee2'] ?? ''; // 심판2
$g_referee3 = $_REQUEST['g_referee3'] ?? ''; // 심판3
$g_recorder1 = $_REQUEST['g_recorder1'] ?? ''; // 기록원1
$g_recorder2 = $_REQUEST['g_recorder2'] ?? ''; // 기록원2
$g_recorder3 = $_REQUEST['g_recorder3'] ?? ''; // 기록원3
$g_recorder4 = $_REQUEST['g_recorder4'] ?? ''; // 기록원4
$g_audience = $_REQUEST['g_audience'] ?? ''; // 관중
$g_division = $_REQUEST['g_division'] ?? ''; // 경기구분

$away_tr = (int)($_REQUEST['away_tr'] ?? 0); //어웨이팀 팀리바운드
$home_tr = (int)($_REQUEST['home_tr'] ?? 0); //홈팀 팀리바운드
$away_bf = (int)($_REQUEST['away_bf'] ?? 0); //어웨이팀 벤치파울
$home_bf = (int)($_REQUEST['home_bf'] ?? 0); //홈팀 벤치파울
$away_score = (int)($_REQUEST['away_score'] ?? 0); //어웨이팀 점수
$home_score = (int)($_REQUEST['home_score'] ?? 0); //홈팀 점수
$g_ground_tv = $_REQUEST['g_ground_tv'] ?? '';

switch($mode) {
	case "write":
		write_ok($s_id, $gameno, $g_start, $g_end, $g_ground, $g_home, $g_away, $g_referee1, $g_referee2, $g_referee3, $g_recorder1, $g_recorder2, $g_recorder3, $g_recorder4, $g_audience, $g_division, $away_tr, $home_tr, $away_bf, $home_bf, $away_score, $home_score, $g_ground_tv);
		break;
	case "modify":
		modify_ok($gid, $s_id, $gameno, $g_start, $g_end, $g_ground, $g_home, $g_away, $g_referee1, $g_referee2, $g_referee3, $g_recorder1, $g_recorder2, $g_recorder3, $g_recorder4, $g_audience, $g_division, $away_tr, $home_tr, $away_bf, $home_bf, $away_score, $home_score, $g_ground_tv);
		break;
	case "delete":
		delete_ok($gid);
		break;
	default:
		back('필요한 값이 없습니다.');
		break;
}

//-----------------------------------------------------------
// write_ok()
//-----------------------------------------------------------
function write_ok($s_id, $gameno, $g_start, $g_end, $g_ground, $g_home, $g_away, $g_referee1, $g_referee2, $g_referee3, $g_recorder1, $g_recorder2, $g_recorder3, $g_recorder4, $g_audience, $g_division, $away_tr, $home_tr, $away_bf, $home_bf, $away_score, $home_score, $g_ground_tv)	{
	
	global $_REQUEST;
	
	// SQL Injection 방지를 위해 모든 변수 이스케이프 및 형 변환
	$gameno_esc = db_escape($gameno);
	$g_ground_esc = db_escape($g_ground);
	$g_referee1_esc = db_escape($g_referee1);
	$g_referee2_esc = db_escape($g_referee2);
	$g_referee3_esc = db_escape($g_referee3);
	$g_recorder1_esc = db_escape($g_recorder1);
	$g_recorder2_esc = db_escape($g_recorder2);
	$g_recorder3_esc = db_escape($g_recorder3);
	$g_recorder4_esc = db_escape($g_recorder4);
	$g_audience_esc = db_escape($g_audience);
	$g_division_esc = db_escape($g_division);
	$g_ground_tv_esc = db_escape($g_ground_tv);
	
	$etv_url_esc = db_escape($_REQUEST['etv_url'] ?? '');
	$etv_width_esc = db_escape($_REQUEST['etv_width'] ?? '');
	$etv_height_esc = db_escape($_REQUEST['etv_height'] ?? '');
	$sms_season_gu_esc = db_escape($_REQUEST['sms_season_gu'] ?? '');
	$sms_game_type_esc = db_escape($_REQUEST['sms_game_type'] ?? '');
	$sms_gameno_esc = db_escape($_REQUEST['sms_gameno'] ?? '');
	$sms_q1_esc = db_escape($_REQUEST['sms_q1'] ?? '');
	$sms_q2_esc = db_escape($_REQUEST['sms_q2'] ?? '');
	$sms_q3_esc = db_escape($_REQUEST['sms_q3'] ?? '');
	$sms_q4_esc = db_escape($_REQUEST['sms_q4'] ?? '');
	$sms_q5_esc = db_escape($_REQUEST['sms_q5'] ?? '');

	$home_1q_esc = db_escape(intval($_REQUEST['home_1q'] ?? 0));
	$home_2q_esc = db_escape(intval($_REQUEST['home_2q'] ?? 0));
	$home_3q_esc = db_escape(intval($_REQUEST['home_3q'] ?? 0));
	$home_4q_esc = db_escape(intval($_REQUEST['home_4q'] ?? 0));
	$home_eq_esc = db_escape(intval($_REQUEST['home_eq'] ?? 0));
	$away_1q_esc = db_escape(intval($_REQUEST['away_1q'] ?? 0));
	$away_2q_esc = db_escape(intval($_REQUEST['away_2q'] ?? 0));
	$away_3q_esc = db_escape(intval($_REQUEST['away_3q'] ?? 0));
	$away_4q_esc = db_escape(intval($_REQUEST['away_4q'] ?? 0));
	$away_eq_esc = db_escape(intval($_REQUEST['away_eq'] ?? 0));

	$sql = " INSERT INTO `savers_secret`.game
				(sid, gameno, g_start, g_end, g_ground, g_home, g_away, g_referee1, g_referee2, g_referee3, 
				g_recorder1, g_recorder2, g_recorder3, g_recorder4, g_audience, g_division, 
				away_tr, home_tr, away_bf, home_bf, away_score, home_score, g_ground_tv, etv_url, etv_width, etv_height, 
				sms_season_gu, sms_game_type, sms_gameno, sms_q1, sms_q2, sms_q3, sms_q4, sms_q5, 
				`home_1q`, `home_2q`, `home_3q`, `home_4q`, `home_eq`, `away_1q`, `away_2q`, `away_3q`, `away_4q`, `away_eq`)
			values({$s_id}, '{$gameno_esc}', {$g_start}, {$g_end}, '{$g_ground_esc}', {$g_home}, {$g_away}, '{$g_referee1_esc}', '{$g_referee2_esc}', '{$g_referee3_esc}', 
				'{$g_recorder1_esc}', '{$g_recorder2_esc}', '{$g_recorder3_esc}', '{$g_recorder4_esc}', '{$g_audience_esc}', '{$g_division_esc}', 
				{$away_tr}, {$home_tr}, {$away_bf}, {$home_bf}, {$away_score}, {$home_score}, '{$g_ground_tv_esc}', '{$etv_url_esc}', '{$etv_width_esc}', '{$etv_height_esc}', 
				'{$sms_season_gu_esc}', '{$sms_game_type_esc}', '{$sms_gameno_esc}', '{$sms_q1_esc}', '{$sms_q2_esc}', '{$sms_q3_esc}', '{$sms_q4_esc}', '{$sms_q5_esc}', 
				{$home_1q_esc}, {$home_2q_esc}, {$home_3q_esc}, {$home_4q_esc}, {$home_eq_esc}, {$away_1q_esc}, {$away_2q_esc}, {$away_3q_esc}, {$away_4q_esc}, {$away_eq_esc} ) ";
	db_query($sql);
	
	if(isset($_REQUEST['view_main']) && $_REQUEST['view_main'] == '1'){
		$gid = db_insert_id();
		if ($gid > 0) {
			$sql = "update `savers_secret`.game set view_main=0 where view_main=1";
			db_query($sql);
			$sql = "update `savers_secret`.game set view_main=1 where gid={$gid}";
			db_query($sql);
		}
	}
	$goto = "/Admin_basketball/game/write.php?mode=write&season=" . (int)($_REQUEST['season'] ?? 0);
	back_close('입력되었습니다 . 계속입력하세요',$goto);
}
//-----------------------------------------------------------
// modify_ok()
//-----------------------------------------------------------
function modify_ok($gid, $s_id, $gameno, $g_start, $g_end, $g_ground, $g_home, $g_away, $g_referee1, $g_referee2, $g_referee3, $g_recorder1, $g_recorder2, $g_recorder3, $g_recorder4, $g_audience, $g_division, $away_tr, $home_tr, $away_bf, $home_bf, $away_score, $home_score, $g_ground_tv)	{
	
	global $_REQUEST;
	
	if ($gid <= 0) {
		back("유효하지 않은 게임 ID입니다.");
	}
	// SQL Injection 방지를 위해 모든 변수 이스케이프 및 형 변환
	$gameno_esc = db_escape($gameno);
	$g_ground_esc = db_escape($g_ground);
	$g_referee1_esc = db_escape($g_referee1);
	$g_referee2_esc = db_escape($g_referee2);
	$g_referee3_esc = db_escape($g_referee3);
	$g_recorder1_esc = db_escape($g_recorder1);
	$g_recorder2_esc = db_escape($g_recorder2);
	$g_recorder3_esc = db_escape($g_recorder3);
	$g_recorder4_esc = db_escape($g_recorder4);
	$g_audience_esc = db_escape($g_audience);
	$g_division_esc = db_escape($g_division);
	$g_ground_tv_esc = db_escape($g_ground_tv);

	$etv_url_esc = db_escape($_REQUEST['etv_url'] ?? '');
	$etv_width_esc = db_escape($_REQUEST['etv_width'] ?? '');
	$etv_height_esc = db_escape($_REQUEST['etv_height'] ?? '');
	$sms_season_gu_esc = db_escape($_REQUEST['sms_season_gu'] ?? '');
	$sms_game_type_esc = db_escape($_REQUEST['sms_game_type'] ?? '');
	$sms_gameno_esc = db_escape($_REQUEST['sms_gameno'] ?? '');
	$sms_q1_esc = db_escape($_REQUEST['sms_q1'] ?? '');
	$sms_q2_esc = db_escape($_REQUEST['sms_q2'] ?? '');
	$sms_q3_esc = db_escape($_REQUEST['sms_q3'] ?? '');
	$sms_q4_esc = db_escape($_REQUEST['sms_q4'] ?? '');
	$sms_q5_esc = db_escape($_REQUEST['sms_q5'] ?? '');

	$home_1q_esc = db_escape(intval($_REQUEST['home_1q'] ?? 0));
	$home_2q_esc = db_escape(intval($_REQUEST['home_2q'] ?? 0));
	$home_3q_esc = db_escape(intval($_REQUEST['home_3q'] ?? 0));
	$home_4q_esc = db_escape(intval($_REQUEST['home_4q'] ?? 0));
	$home_eq_esc = db_escape(intval($_REQUEST['home_eq'] ?? 0));
	$away_1q_esc = db_escape(intval($_REQUEST['away_1q'] ?? 0));
	$away_2q_esc = db_escape(intval($_REQUEST['away_2q'] ?? 0));
	$away_3q_esc = db_escape(intval($_REQUEST['away_3q'] ?? 0));
	$away_4q_esc = db_escape(intval($_REQUEST['away_4q'] ?? 0));
	$away_eq_esc = db_escape(intval($_REQUEST['away_eq'] ?? 0));
	

	$sql = " UPDATE `savers_secret`.game SET 
				sid={$s_id}, gameno='{$gameno_esc}', g_start={$g_start}, g_end={$g_end}, g_ground='{$g_ground_esc}', g_home={$g_home}, g_away={$g_away},
				g_referee1='{$g_referee1_esc}', g_referee2='{$g_referee2_esc}', g_referee3='{$g_referee3_esc}', 
				g_recorder1='{$g_recorder1_esc}', g_recorder2='{$g_recorder2_esc}',	g_recorder3='{$g_recorder3_esc}', 
				g_recorder4='{$g_recorder4_esc}', g_audience='{$g_audience_esc}', g_division='{$g_division_esc}',
				away_tr={$away_tr}, home_tr={$home_tr}, away_bf={$away_bf}, home_bf={$home_bf}, 
				away_score={$away_score}, home_score={$home_score}, g_ground_tv='{$g_ground_tv_esc}',
				etv_url='{$etv_url_esc}', etv_width='{$etv_width_esc}', etv_height='{$etv_height_esc}', 
				sms_season_gu='{$sms_season_gu_esc}', sms_game_type='{$sms_game_type_esc}', sms_gameno='{$sms_gameno_esc}',
				sms_q1='{$sms_q1_esc}', sms_q2='{$sms_q2_esc}', sms_q3='{$sms_q3_esc}', sms_q4='{$sms_q4_esc}', sms_q5='{$sms_q5_esc}',
				`home_1q`={$home_1q_esc}, `home_2q`={$home_2q_esc}, `home_3q`={$home_3q_esc}, `home_4q`={$home_4q_esc}, `home_eq`={$home_eq_esc},
				`away_1q`={$away_1q_esc}, `away_2q`={$away_2q_esc}, `away_3q`={$away_3q_esc}, `away_4q`={$away_4q_esc}, `away_eq`={$away_eq_esc}
			WHERE gid={$gid} ";
	db_query($sql);

	if(isset($_REQUEST['view_main']) && $_REQUEST['view_main'] == '1'){
		$sql = "update `savers_secret`.game set view_main=0 where view_main=1";
		db_query($sql);

		$sql = "update `savers_secret`.game set view_main=1 where gid={$gid}";
		db_query($sql);
	}
	$goto = "/Admin_basketball/game/list.php?season=" . (int)($_REQUEST['season'] ?? 0);
	back_close('',$goto);
}
//-----------------------------------------------------------
// delete_ok()
//-----------------------------------------------------------
function delete_ok($gid)	{
	if ($gid <= 0) {
		back("유효하지 않은 게임 ID입니다.");
	}
	$sql = " DELETE FROM `savers_secret`.game WHERE gid = {$gid} ";
	db_query($sql);
	db_query(" DELETE FROM `savers_secret`.record WHERE gid = {$gid} ");
	$goto = "/Admin_basketball/game/list.php";
	back_close('',$goto);
}

?>
