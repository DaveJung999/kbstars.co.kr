<?php
//=======================================================
// 설	명 : 인클루드 파일 - inc_month.php
// 책임자 : 박선민 (sponsor@new21.com), 검수: 03/10/10
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인			수정 내용
// -------- ------ --------------------------------------
// 03/10/10 박선민 마지막 수정
//=======================================================

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
// 인쿨루드인 경우에만 허용
if (realpath($_SERVER["PATH_TRANSLATED"]) == realpath(__FILE__)){
	echo "직접 호출되어서 거부함";
	exit;
}

//===================================================
// GET 값 대입......2025-08-08
$intThisYear = $_GET['intThisYear']?$_GET['intThisYear']:$intThisYear;
$intThisMonth = $_GET['intThisMonth']?$_GET['intThisMonth']:$intThisMonth;
$intThisDay = $_GET['intThisDay']?$_GET['intThisDay']:$intThisDay;

$session_memid = $_GET['session_memid']?$_GET['session_memid']:$session_memid;
$datFirstDay = $_GET['datFirstDay']?$_GET['datFirstDay']:$datFirstDay;
$vbSunday = $_GET['vbSunday']?$_GET['vbSunday']:$vbSunday;
$intLastDay = $_GET['intLastDay']?$_GET['intLastDay']:$intLastDay;

$intPrevYear = $_GET['intPrevYear']?$_GET['intPrevYear']:$intPrevYear;
$intPrevMonth = $_GET['intPrevMonth']?$_GET['intPrevMonth']:$intPrevMonth;
$intNextYear = $_GET['intNextYear']?$_GET['intNextYear']:$intNextYear;
$intNextMonth = $_GET['intNextMonth']?$_GET['intNextMonth']:$intNextMonth;
$NowThisYear = $_GET['NowThisYear']?$_GET['NowThisYear']:$NowThisYear;
$NowThisMonth = $_GET['NowThisMonth']?$_GET['NowThisMonth']:$NowThisMonth;
//===================================================

////////////////////////////
// 반복되지 않은 일정 구하기
// $outCal[YYYY-MM-DD]
$searchDateFrom = "{$intThisYear}-{$intThisMonth}-01";
$searchDateTo	= "{$intThisYear}-{$intThisMonth}-{$intLastDay}";

$sql = "SELECT * from {$table_calendar} WHERE {$sql_where_cal} AND retimes=0 ";
$sql .= "AND (startdate>='{$searchDateFrom}' AND startdate<='{$searchDateTo}') ";
//$sql .= " AND (dtype = 'hour' OR dtype = 'day') AND (open = '1' or (open='0' and bid = '{$_SESSION['seUid']}'))	";
$sql .= " ORDER BY startdate, starthour";

$result	= db_query($sql);
while( $list=db_array($result) ){
	if($list['dtype'] == "day" )
		$lhour= "[ 하루 종일 ]";
	else
		$lhour="[{$list['starthour']}:{$list['startmin']}~{$list['endhour']}:{$list['endmin']}]";
		
	// 권한체크
	if(!privAuth($list,"priv_level")){
		$list['title']	= "비공개 일정";
		$list['content']	= "비공개 일정";

		// URL Link
		$href['view'] = "javascript: return false;";
	} else {
		$list['title'] = cut_string($list['title'], 6);
		$list['title'] = htmlspecialchars($list['title'],ENT_QUOTES);
		$list['content'] = cut_string($list['content'], 150);
		$list['content'] = htmlspecialchars($list['content'],ENT_QUOTES);
		$list['content'] = replace_string($list['content'], 'text');	// 문서 형식에 맞추어서 내용 변경

		// URL Link
		$href['view'] = "./index.php?".href_qs("mode=view&bmode={$_GET['mode']}&uid={$list['uid']}&m_category=1",$qs_basic);

	} // end if.. else

	$outCal[$list['startdate']] = "<ceinter><a href='{$href['view']}&date={$list['startdate']}'><img src=images/log.gif border=0 align=absmiddle></a></ceinter> \n"	;
} // end while
////////////////////////////

////////////////////////////
// 반복 일정 구하기
// $outCal['day']
$sql = "SELECT * from {$table_calendar} WHERE {$sql_where_cal} AND retimes>0 ";
$sql .= " AND (startdate<='{$searchDateTo}' AND enddate >='{$searchDateFrom}') ";
$sql .= " AND (dtype = 'hour' or dtype = 'day') AND (open = '1' or (open='0' and bid = '{$_SESSION['seUid']}'))	";
$sql .="	ORDER BY starthour";
$result	= db_query($sql);

while( $list=db_array($result) ){
	// 반복되는 첫 $tmp_time 구함
	if(strcmp($list['startdate'],$searchDateFrom)<0){
		$tmp_time = strtotime($searchDateFrom);
		switch($list['retype']){
			case "day"://일일단위 반복설정
				// - 레코드 저장일과 출력셀의 날짜와의 날짜차이
				$cday	= userDateDiff("d",$list['startdate'],$searchDateFrom)-1;
				
				if($cday%$list['retimes']>0)
					$tmp_time += ($list['retimes']-$cday%$list['retimes']) * 86400;
				break;
			case "week"://주단위 반복설정
				// - 레코드 저장일과 출력셀의 날짜와의 날짜차이
				$cday	= userDateDiff("d",$list['startdate'],$searchDateFrom)-1;

				// 주단위기에 retimes에서 7을 곱함
				if($cday%($list['retimes']*7)>0)
					$tmp_time += ($list['retimes']*7-$cday%($list['retimes']*7)) * 86400;
				break;
			case "month"://월단위 반복설정
				// 월단위기에 startdate의 일(Day)임
				$tmp_time = strtotime("substr({$searchDateFrom},0,8)".substr($list['startdate'],-2));
				break;
		} // end switch
	} else {
		// 기간안에 startdate가 있기에 그것이 첫날임
		$tmp_time = strtotime($list['startdate']);
	}

	if($list['dtype'] == "day" )
		$lhour= "[ 하루 종일 ]";
	else
		$lhour="[{$list['starthour']}:{$list['startmin']}~{$list['endhour']}:{$list['endmin']}]";

	// 권한체크
	// 권한체크
	if(!privAuth($list,"priv_level")){
		$list['title']	= "비공개 일정";
		$list['content']	= "비공개 일정";

		// URL Link
		$href['view'] = "javascript: return false;";
	} else {
		$list['title'] = cut_string($list['title'], 12);
		$list['title'] = htmlspecialchars($list['title'],ENT_QUOTES);
		$list['content'] = cut_string($list['content'], 150);
		$list['content'] = htmlspecialchars($list['content'],ENT_QUOTES);
		$list['content'] = replace_string($list['content'], 'text');	// 문서 형식에 맞추어서 내용 변경

		// URL Link
		$href['view'] = "./index.php?".href_qs("mode=view&bmode={$_GET['mode']}&uid={$list['uid']}&m_category=1",$qs_basic);

	} // end if.. else
	// 일정 변수에 저장
	$tmp_enddate = (strcmp($searchDateTo,$list['enddate'])<0) ? $searchDateTo : $list['enddate'];
	$tmp_time_enddate = strtotime($tmp_enddate);
	while($tmp_time<=$tmp_time_enddate) {// 말일을 지나기 전까지
		$tmp = date("Y-m-d",$tmp_time);
		$outCal[$tmp] .= "<img src=images/dot_green.gif border=0 align=absmiddle><font face=굴림><span style='font-size:9pt'><a href='{$href['view']}' onMouseOver=\"view('{$list['title']}', '{$lhour}','{$list['content']}');\" onMouseOut=\"noview();\">{$list['title']}</a></span></font><br> \n"	;

		switch($list['retype']){
			case "day":
				$tmp_time	+= $list['retimes'] * 86400;
				break;
			case "week":
				$tmp_time	+= $list['retimes'] * 7*86400;
				break;
			case "month": 
				$tmp_time	+= $list['retimes'] * 30*86400;
				break;
		} // end switch
	} // end while
} // end while
////////////////////////////
// 쓰기 권한이 있는지 확인
if(privAuth($dbinfo, "priv_write"))	$enable_write = true;
//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
?>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#000000" bordercolorlight="#000000">
	<tr>
		<td>
			<table width="95%" border=0 align=center cellpadding=1 cellspacing=1 bgcolor=#DBDBDB>
				<tr>
					<td width="14%" bgcolor="#e2cbcb" align="center"	height=30><font face="굴림" color="#C45B4D"><strong><span style="font-size: 9pt"> 일(日)</span></strong></font></td>
					<td width="14%" bgcolor="#f5f5f5" align="center"><font color="#666666" face="굴림"><strong><span style="font-size: 9pt"> 월(月)</span></strong></font></td>
					<td width="14%" bgcolor="#f5f5f5" align="center"><font color="#666666" face="굴림"><strong><span style="font-size: 9pt"> 화(火)</span></strong></font></td>
					<td width="14%" bgcolor="#f5f5f5" align="center"><font color="#666666" face="굴림"><strong><span style="font-size: 9pt"> 수(水)</span></strong></font></td>
					<td width="14%" bgcolor="#f5f5f5" align="center"><font color="#666666" face="굴림"><strong><span style="font-size: 9pt"> 목(木)</span></strong></font></td>
					<td width="14%" bgcolor="#f5f5f5" align="center"><font color="#666666" face="굴림"><strong><span style="font-size: 9pt"> 금(金)</span></strong></font></td>
					<td width="14%" bgcolor="#cbd5e2" align="center"><font face="굴림" color="navy"><strong><span style="font-size: 9pt"> 토(土)</span></strong></font></td>
				</tr>
<?php
// for문 초기값 정의
$intPrintDay	= 1;	//출력 초기일 값은 1부터
$Stop_Flag		= 0;
for($intNextWeek=1; $intNextWeek < 7 ; $intNextWeek++) {	//주단위 루프 시작, 최대 6주 
	echo "<tr> \n";
	for($intNextDay=1; $intNextDay < 8	; $intNextDay++) {	//요일단위 루프 시작, 일요일부터
		
		if ($intPrintDay == 1 and $intNextDay<$intFirstWeekday+1) { //첫주시작일이 1보다 크면
			echo "<td height=70 valign=top align=left bgcolor=white> ";
			echo "<font size=2 color=white>.</font> \n";
			//$intFirstWeekday=$intFirstWeekday-1;
		} else {	//
			if ($intPrintDay > $intLastDay ) { //입력날짜가 월말보다 크다면
				echo "<td height=70 valign=top align=left bgcolor=white> ";
				echo "<font size=2 color=white>.</font> \n";
			} else { //입력날짜가 현재월에 해당되면
				$intcday=$intThisYear."-".$intThisMonth."-" . (($intPrintDay<10)?"0":"").$intPrintDay;

				// URL Link
				$href['goinput']	= "./index.php?" .	href_qs("mode=input&date={$intcday}&m_category=1",$qs_basic);
				$href['goday']	= "./index.php?" . href_qs("mode=day&date={$intcday}&m_category=1",$qs_basic);

				if( $intThisYear-$NowThisYear == 0 and $intThisMonth-$NowThisMonth == 0 and $intPrintDay-$NowThisDay == 0 ){
					//오늘 날짜이면은 글씨폰트를 다르게
					if($enable_write or $outCal[$intcday]){
						echo "<td height=70 valign=top align=left bgcolor=white align='center'> ";
						echo "<b><a href='{$href['goday']}'><font size=2 color=darkorange>{$intPrintDay}◈</font></a></b> ";
					} else {
						echo "<td height=70 valign=top align=left bgcolor=white align='center'> ";
						echo "<b><font size=2 color=darkorange>{$intPrintDay}◈</font></b> <br>\n";
					}
				}
				elseif( $intNextDay == 1 ) { 
					//일요일이면 빨간 색으로
					if($enable_write or $outCal[$intcday]){
						echo "<td height=70 valign=top align=left bgcolor=#fcf5f5> ";
						echo "<b><a href='{$href['goday']}'><font size=2 color=#C45B4D>{$intPrintDay}</font></a></b>\n";
					} else {
						echo "<td height=70 valign=top align=left bgcolor=white> ";
						echo "<b><font size=2 color=#C45B4D>{$intPrintDay}</font></b>\n";
					}
				}
				elseif( $intNextDay == 7 ) { 
					//토요일이면 파란 색으로
					if($enable_write or $outCal[$intcday]){
						echo "<td height=70 valign=top align=left bgcolor=#eff4f9> ";
						echo "<b><a href='{$href['goday']}'><font size=2 color=navy>{$intPrintDay}</font></a></b>\n";
					} else {
						echo "<td height=70 valign=top align=left bgcolor=white> ";
						echo "<b><font size=2 color=#C45B4D>{$intPrintDay}</font></b>\n";
					}
				}
				else{	
					// 그외의 경우
					if($enable_write or $outCal[$intcday]){
						echo "<td height=70 valign=top align=left bgcolor=white> ";
						echo "<b><font size=2 color=#000000><a href='{$href['goday']}'>{$intPrintDay}</a></font></b>\n";
					} else {
						echo "<td height=70 valign=top align=left bgcolor=white> ";
						echo "<b><font size=2 color=#000000>{$intPrintDay}</font></b>\n";
					}
				}

				// 일정 추가가 가능하면
				if($enable_write) 
					echo "<a href='{$href['goinput']}'><img src=images/add.gif border=0></a><br>\n";
				else 
					echo "<br>";
				
				// 일정 내용 출력
				if($outCal[$intcday]) echo $outCal[$intcday];
				else echo "<span style='font-size:9pt'>&nbsp;</span> \n";
			} // end if.. else

			$intPrintDay	+= 1;	//날짜값을 1 증가
			if ($intPrintDay>$intLastDay )	//만약 날짜값이 월말값보다 크면 루프문 탈출
				$Stop_Flag=1;
		} // end if.. else
		echo "</td>";
	} // end for intNextDay
	echo "</tr>";
	if ($Stop_Flag == 1 )	break;
} // end for intNextWeek
?>
</table>
</td>
</tr>
</table>
<p align=right><span style='font-size:9pt'>
<?php
	if ($intThisDay >= $intPrevLastDay )
		$intPrevDay=$intPrevLastDay;
	else
		$intPrevDay=$intThisDay;

	if ($intThisDay >= $intNextLastDay )
		$intNextDay=$intNextLastDay;
	else
		$intNextDay=$intThisDay;

	// URL Link
	$href['PrevYear'] = "{$_SERVER['PHP_SELF']}?" 
						. href_qs("mode=month&date={$intPrevYear}-{$intPrevMonth}-{$intPrevDay}&m_category=1",$qs_basic);
	$href['NextYear'] = "{$_SERVER['PHP_SELF']}?" 
						. href_qs("mode=month&date={$intNextYear}-{$intNextMonth}-{$intNextDay}&m_category=1",$qs_basic);

	echo "<a href='{$href['PrevYear']}'>◀ 이전달[{$intPrevMonth}월 {$intPrevDay}일] </a>&nbsp;&nbsp;";
	echo "<a href='{$href['NextYear']}'> 다음달[{$intNextMonth}월 {$intNextDay}일] ▶ </a>"; 
?>
</span></p>
