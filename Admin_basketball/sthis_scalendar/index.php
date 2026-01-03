<?php
//=======================================================
// 설	명 : 일정관리(index.php)
// 책임자 : 박선민 (sponsor@new21.com), 검수: 03/10/06
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인			수정 내용
// -------- ------ --------------------------------------
// 03/09/16 박선민 마지막 수정
// 03/10/06 박선민 버그 수정
//=======================================================
$HEADER=array(
		'priv' =>	"운영자,뉴스관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'useBoard2' => 1,
		'useApp' => 1,
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $_SERVER['HTTP_HOST']);

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	$thisPath			= dirname(__FILE__);
	include_once("{$thisPath}/userfuntions.php");
	include_once("{$thisPath}/function_lunartosol.php");
	$thisUrl			= "/sthis/sthis_scalendar/scalendar"; // 마지막 "/"이 빠져야함

	// 기본 URL QueryString
	$qs_basic = isset($_GET['db']) ? "db=" . $_GET['db'] : "";

	$table_calendarinfo	= $SITE['th'] . "calendarinfo";

	if(isset($_GET['db'])){
		$sql = "SELECT * from {$table_calendarinfo} WHERE db='{$_GET['db']}'";
		if( !$dbinfo=db_arrayone($sql) )
			back("사용하지 않은 DB입니다.","infoadd.php?mode=user");

		$table_calendar	= "{$SITE['th']}calendar_" . $dbinfo['table_name']; // 게시판 테이블

		$sql_where_cal = " infouid='{$dbinfo['uid']}' ";
	}
	else back("DB 값이 없습니다");

	// 넘어온 mode값 체크
	if(empty($_GET['mode'])) $_GET['mode'] = "month";

	// 넘오온 date값 체크
	if(empty($_GET['date']))
		$_GET['date'] = date("Y-m-d");
	// PHP 7+ 호환성을 위해 ereg()를 preg_match()로 변경
	elseif( !preg_match("/^[0-9]{4}-[01]?[0-9]-[0123]?[0-9]$/", $_GET['date']) ){
		back("잘못된 날짜입니다");
	}
	$_GET['date'] = date("Y-m-d",strtotime($_GET['date']));

	// 각종 날짜변수 - 현재 날짜
	$NowThisYear	= date("Y");
	$NowThisMonth	= date("m");
	$NowThisDay		= date("d");

	// 각종 날짜 변수 - 넘오온 날짜
	$intThisTimestamp	= strtotime($_GET['date']);
	$intThisYear	= date("Y",$intThisTimestamp);
	$intThisMonth	= date("m",$intThisTimestamp);
	$intThisDay		= date("d",$intThisTimestamp);
	$intThisWeekday	= date("w",$intThisTimestamp);
	switch ($intThisWeekday){
		Case 0: $varThisWeekday="일"; break;
		Case 1: $varThisWeekday="월"; break;
		Case 2: $varThisWeekday="화"; break;
		Case 3: $varThisWeekday="수"; break;
		Case 4: $varThisWeekday="목"; break;
		Case 5: $varThisWeekday="금"; break;
		Case 6: $varThisWeekday="토"; break;
	}

	// 각종 날짜변수 - 이전달,다음달
	if($intThisMonth == 1) { // 1월달이라면
		$intPrevYear	= $intThisYear-1;	//이전달 년도 = 이번년도 - 1
		$intPrevMonth	= 12;				//이전달 = 12월
		$intNextYear	= $intThisYear ;	//다음달 년도 = 이번달 년도
		$intNextMonth	= 2;				//다음달 = 2월
	}
	elseif($intThisMonth == 12) { //12월달이라면
		$intPrevYear	= $intThisYear;		//이전달 년도 = 이번달 년도
		$intPrevMonth	= 11;				//이전달 = 11월
		$intNextYear	= $intThisYear + 1;	//다음달 년도 = 이번달 년도 + 1
		$intNextMonth	= 1;				// 다음달 = 1월
	} else { //1월과 12월을 제외한 경우에는
		$intPrevYear	= $intThisYear;		//이전달 년도 = 이번달 년도
		$intPrevMonth	= $intThisMonth - 1;//이전달 = 이번달	- 1
		$intNextYear	= $intThisYear;		//다음달 년도 = 이번달 년도
		$intNextMonth	= $intThisMonth+1;	//다음달 = 이번달 + 1
	}

	// 각종 날짜변수 - 월말일
	$intLastDay		= userLastDay($intThisMonth,$intThisYear);	//이번달
	$intPrevLastDay = userLastDay($intPrevMonth,$intPrevYear);	//지난달
	$intNextLastDay = userLastDay($intNextMonth,$intNextYear);	//다음달

	// 각종 날짜변수 - 월 1일의 요일(숫자로)
	$intFirstWeekday = date('w', strtotime($intThisYear."-".$intThisMonth."-1"));

	// 각종 날짜 변수 - ex)2003년 9월 1일, 월요일 (음력 8월 5일)
	$thisFullDate	= date("Y년 n월 j일",$intThisTimestamp) . " {$varThisWeekday}요일";
	$sol2lun = sol2lun(date("Ymd",$intThisTimestamp));
	$sol2lun = explode("-", $sol2lun);
	$thisFullDate.= "(음력 {$sol2lun[1]}월 {$sol2lun[2]}일)";
	// URL Link
	$href['today']	= "{$_SERVER['PHP_SELF']}?". href_qs("mode=day&date=".date("Y-m-d"),$qs_basic);
	$href['day']	= "{$_SERVER['PHP_SELF']}?". href_qs("mode=day&date=".$_GET['date'],$qs_basic);
	$href['week']	= "{$_SERVER['PHP_SELF']}?". href_qs("mode=week&date=".$_GET['date'],$qs_basic);
	$href['month']	= "{$_SERVER['PHP_SELF']}?". href_qs("mode=month&date=".$_GET['date'],$qs_basic);
//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
// 마무리
$val="\$1{$thisUrl}/stpl/{$dbinfo['skin']}/images/";
switch($dbinfo['html_headpattern']){
	case "ht":
		// 전체 홈페이지 템플릿 읽어오기
		$HEADER['header'] = 2;
		if( isset($dbinfo['html_headtpl']) and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php") )
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php");
		else
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");

		echo $SITE['head'] . $dbinfo['html_head'];
		break;
	case "h":
		// 전체 홈페이지 템플릿 읽어오기
		$HEADER['header'] = 2;
		if( isset($dbinfo['html_headtpl']) and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php") )
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php");
		else
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");

		echo $SITE['head'] . $dbinfo['html_head'];
		break;
	case "t":
		// 전체 홈페이지 템플릿 읽어오기
		$HEADER['header'] = 2;
		if( isset($dbinfo['html_headtpl']) and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php") )
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php");
		else
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");

		echo $dbinfo['html_head'];
		break;
	case "no":
		break;
	default:
		echo $dbinfo['html_head'];
} // end switch
?>
<link href="/ycommon/ycommon_1201.css" rel="stylesheet" type="text/css">
<table cellpadding="0" cellspacing="0" width="100%" height="21" bgcolor="#CE966B">
	<tr>
		<td width="98%">
			<p align="right" class="base"><font color="white"><a href="/"	class="white">HOME</a>
		&gt;&gt; 팀소개 &gt;&gt; <a href="/d02_intro/schedule.php" class="white">선수단 일정</a></font></p>
		</td>
		<td width="2%">
			<p>&nbsp;</p>
		</td>
	</tr>
</table>
<br>
<br>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td width="573" align="center">
<?php
/////////////////
// 달력 삽입
/////////////////
	if	( $_GET['mode'] == "input" || $_GET['mode'] == "edit"){
		include("./inc_input.php");
	}
	elseif($_GET['mode'] == "view"){
		include("./inc_view.php");
	}
	elseif($_GET['mode'] == "day" ){
		include("./inc_day.php");
	}
	elseif($_GET['mode'] == "week" ){
		include("./inc_week.php");
	}
	elseif($_GET['mode'] == "month" ){
		include("./inc_month.php");
	}
/////////////////?></td>

	</tr>
</table>
<p>&nbsp;</p>
<?php
// 마무리
switch($dbinfo['html_headpattern']){
	case "ht":
		echo $dbinfo['html_tail'] . $SITE['tail'];
		break;
	case "h":
		echo $dbinfo['html_tail'];
		break;
	case "t":
		echo $dbinfo['html_tail'] . $SITE['tail'];
		break;
	case "no":
		break;
	default:
		echo $dbinfo['html_tail'];
} // end switch
?>