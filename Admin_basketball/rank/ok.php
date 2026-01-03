<?php
//=======================================================
// 설	명 : 처리(ok.php)
// 책임자 : 박선민 (sponsor@new21.com), 검수: 04/02/26
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인			수정 내용
// -------- ------ --------------------------------------
// 04/02/26 박선민 처음제작
// 04/02/26 박선민 마지막수정
// 25/08/11 Gemini	PHP 7.x, MariaDB 호환성 업데이트
//=======================================================
$HEADER=array(
		'priv' =>	"운영자,뉴스관리자,사진관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'html_echo' => '', // html header, tail 삽입(tail은 파일 마지막에 echo $SITE['tail'])
		'useCheck' => 1,
		'log' => '' // log_site 테이블에 지정한 키워드로 로그 남김
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
page_security("", $_SERVER['HTTP_HOST'] ?? '');

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================

// 기본 URL QueryString
$qs_basic = "db=" . ($_REQUEST['db'] ?? ($table ?? '')) .			//table 이름
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

$table		= "`savers_secret`.season_rank";
$dbinfo['table'] = $table;

// 공통적으로 사용할 $qs
$qs=array(
	//"sid" =>	"post,trim,notnull",
	"tid" =>	"post,trim,notnull",
	"rank" =>	"post,trim,notnull",
	"rank_real" =>	"post,trim,notnull",
	"win" =>	"post,trim",
	"lose" =>	"post,trim",
	"winrate" =>	"post,trim",
	"winsub" =>	"post,trim",
	"win_con" =>	"post,trim",
	"lose_con" =>	"post,trim",
	"gamecount" =>	"post,trim",
	"win_point" =>	"post,trim",
	"2pm" =>	"post,trim",
	"3pm" =>	"post,trim",
	"2pa" =>	"post,trim",
	"3pa" =>	"post,trim",
	"fta" =>	"post,trim",
	"ftm" =>	"post,trim",
	"re" =>	"post,trim",
	"as" =>	"post,trim",
	"st" =>	"post,trim",
	"bs" =>	"post,trim",
	"to" =>	"post,trim",
	"po" =>	"post,trim"
);

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
// mode값에 따른 함수 호출
$mode = $_REQUEST['mode'] ?? '';
switch($mode){
	case 'write':
		$uid = write_ok($table,$qs);
		$goto = "write.php?mode=write&season=" . ($_REQUEST['season'] ?? '');
		back_close('입력되었습니다 . 계속입력하세요',$goto);
		break;
	case 'modify':
		modify_ok($table,$qs,"uid");
		$goto = "list.php?season=" . ($_REQUEST['season'] ?? '');
		back_close('',$goto);
		break;
	case 'delete':
		delete_ok($table,"uid");
		$goto = "list.php?season=" . ($_REQUEST['season'] ?? '');
		back_close('',$goto);
		break;	
	default :
		back("잘못된 웹 페이지에 접근하였습니다");
} // end switch

//=======================================================
// User functions.. . (사용자 함수 정의)
//=======================================================
function write_ok($table,$qs){
	global $dbinfo;

	// 넘어온값 체크
	$qs=check_value($qs);
	$qs['sid'] = $_POST['season'] ?? '';
	
	// 값 추가
	$qs['t_name'] = db_resultone("select t_name from `savers_secret`.team where tid='".db_escape($qs['tid']) . "'",0,'t_name');
	if(!$qs['t_name']) back('잘못된 요청입니다.');

	// $sql 완성
	$sql_set	= ""; // $sql_set 시작
	$sql="INSERT INTO {$dbinfo['table']} SET
				`sid`		='" . db_escape($qs['sid']) . "',
				`tid`		='" . db_escape($qs['tid']) . "',
				`t_name`	='" . db_escape($qs['t_name']) . "',
				`rank`		='" . db_escape($qs['rank']) . "',
				`rank_real`	='" . db_escape($qs['rank_real']) . "',
				`win`		='" . db_escape($qs['win']) . "',
				`lose`		='" . db_escape($qs['lose']) . "',
				`winrate`	='" . db_escape($qs['winrate']) . "',
				`winsub`	='" . db_escape($qs['winsub']) . "',
				`win_con`	='" . db_escape($qs['win_con']) . "',
				`lose_con`	='" . db_escape($qs['lose_con']) . "',
				`gamecount`	='" . db_escape($qs['gamecount']) . "',
				`win_point`	='" . db_escape($qs['win_point']) . "',
				`2pm`		='" . db_escape($qs['2pm']) . "',
				`3pm`		='" . db_escape($qs['3pm']) . "',
				`2pa`		='" . db_escape($qs['2pa']) . "',
				`3pa`		='" . db_escape($qs['3pa']) . "',
				`fta`		='" . db_escape($qs['fta']) . "',
				`ftm`		='" . db_escape($qs['ftm']) . "',
				`re`		='" . db_escape($qs['re']) . "',
				`as`		='" . db_escape($qs['as']) . "',
				`st`		='" . db_escape($qs['st']) . "',
				`bs`		='" . db_escape($qs['bs']) . "',
				`to`		='" . db_escape($qs['to']) . "',
				`po`		='" . db_escape($qs['po']) . "'
				{$sql_set}
		";
	db_query($sql);

	return db_insert_id();
} // end func write_ok

function modify_ok($table,$qs,$field){
	global $dbinfo;

	$qs[$field]	= "post,trim,notnull=" . urlencode("고유번호가 넘어오지 않았습니다");
	// 넘어온값 체크
	$qs=check_value($qs);

	// 값 추가
	$qs['t_name'] = db_resultone("select t_name from `savers_secret`.team where tid='".db_escape($qs['tid']) . "'",0,'t_name');
	if(!$qs['t_name']) back('잘못된 요청입니다.');

	// 해당 데이터 읽기
	$sql_where	= " 1 "; // $sql_where 시작
	$sql = "SELECT * FROM {$table} WHERE `{$field}`='" . db_escape($qs[$field]) . "' and  $sql_where ";
	if( !$list=db_arrayone($sql) )
		back("해당 데이터가 없습니다");
	// $sql 완성
	$sql="UPDATE {$table}	SET
				`tid`		='" . db_escape($qs['tid']) . "',
				`t_name`	='" . db_escape($qs['t_name']) . "',
				`rank`		='" . db_escape($qs['rank']) . "',
				`rank_real`	='" . db_escape($qs['rank_real']) . "',
				`win`		='" . db_escape($qs['win']) . "',
				`lose`		='" . db_escape($qs['lose']) . "',
				`winrate`	='" . db_escape($qs['winrate']) . "',
				`winsub`	='" . db_escape($qs['winsub']) . "',
				`win_con`	='" . db_escape($qs['win_con']) . "',
				`lose_con`	='" . db_escape($qs['lose_con']) . "',
				`gamecount`	='" . db_escape($qs['gamecount']) . "',
				`win_point`	='" . db_escape($qs['win_point']) . "',
				`2pm`		='" . db_escape($qs['2pm']) . "',
				`3pm`		='" . db_escape($qs['3pm']) . "',
				`2pa`		='" . db_escape($qs['2pa']) . "',
				`3pa`		='" . db_escape($qs['3pa']) . "',
				`fta`		='" . db_escape($qs['fta']) . "',
				`ftm`		='" . db_escape($qs['ftm']) . "',
				`re`		='" . db_escape($qs['re']) . "',
				`as`		='" . db_escape($qs['as']) . "',
				`st`		='" . db_escape($qs['st']) . "',
				`bs`		='" . db_escape($qs['bs']) . "',
				`to`		='" . db_escape($qs['to']) . "',
				`po`		='" . db_escape($qs['po']) . "'

			WHERE
				`{$field}`='" . db_escape($qs[$field]) . "'
			AND
				 $sql_where 
		";
	db_query($sql);

	return db_count();
} // end func modify_ok

function delete_ok($table,$field){
	global $dbinfo;
	$qs=array(
			"$field" =>	"request,trim,notnull=" . urlencode("고유넘버가 넘어오지 않았습니다.")
		);
	// 넘오온값 체크
	$qs=check_value($qs);

	// 해당 데이터 읽기
	$sql_where	= " 1 "; // $sql_where 시작
	$sql = "SELECT * FROM {$table} WHERE `{$field}`='" . db_escape($qs[$field]) . "' and  $sql_where ";
	if( !$list=db_arrayone($sql) )
		back("해당 데이터가 없습니다");

	db_query("DELETE FROM {$table} WHERE `{$field}`='" . db_escape($qs[$field]) . "' and  $sql_where ");

	return db_count();
} // end func delete_ok
?>
