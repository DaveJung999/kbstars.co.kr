<?php
//=======================================================
// 설  명 : 인클루드 파일 - inc_week.php
// 책임자 : 박선민 , 검수: 03/10/09
// Project: sitePHPbasic
// ChangeLog
//   DATE   수정인			 수정 내용
// -------- ------ --------------------------------------
// 03/10/09 박선민 마지막 수정
//=======================================================

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
	// 인쿨루드인 경우에만 허용
	if(realpath($_SERVER["PATH_TRANSLATED"]) == realpath(__FILE__)) {
		echo "직접 호출되어서 거부함";
		exit;
	}

	////////////////////////////
	// 반복되지 않은 일정 구하기
	// $outCal[$tmp_day]
	$intWeekFirstDay_T	= $intThisTimestamp - date("w",$intThisTimestamp)*3600*24;
	$intWeekEndDay_T	= $intWeekFirstDay_T + 86400*7;
	$searchDateFrom	= date("Y-m-d",$intWeekFirstDay_T);
	$searchDateTo	= date("Y-m-d",$intWeekEndDay_T);


	$sql = "SELECT * from {$table_calendar} WHERE {$sql_where_cal} AND retimes=0 ";
	$sql .= " AND (startdate>='{$searchDateFrom}' AND startdate<='{$searchDateTo}') ";
	$sql .= " AND (dtype = 'hour' OR dtype = 'day') ";
	$sql .= " ORDER BY startdate, starthour";
	$result	= db_query($sql);
	while( $list=db_array($result) ) {
		if($list['dtype'] == "day" )
			$lhour= "[ 하루 종일 ]";
		else
			$lhour="[{$list['starthour']}:{$list['startmin']}~{$list['endhour']}:{$list['endmin']}]";

		// 권한체크
		if(!privAuth($list,"priv_level")) {
			$list['title']	= "비공개 일정";
			$list['content']	= "비공개 일정";

			// URL Link
			$href['view'] = "javascript: return false;";
		}
		else {
			//$list['title'] = cut_string($list['title'], 12);
			$list['title'] = htmlspecialchars($list['title'],ENT_QUOTES);
			$list['content'] = cut_string($list['content'], 150);
			$list['content'] = htmlspecialchars($list['content'],ENT_QUOTES);
			$list['content'] = replace_string($list['content'], 'text');	// 문서 형식에 맞추어서 내용 변경

			// URL Link
			$href['view'] = "./index.php?".href_qs("mode=view&bmode={$_GET['mode']}&uid={$list['uid']}",$qs_basic);

		} // end if.. else

		$outCal[$list['startdate']] .= "<img src=images/micon.gif border=0><font face=굴림><span style='font-size:9pt'><a href='{$href['view']}' onMouseOver=\"view('{$list['title']}', '{$lhour}','{$list['content']}');\" onMouseOut=\"noview();\">{$list['title']}</a></span></font><br> \n"	;
	} // end while
	////////////////////////////

	////////////////////////////

	////////////////////////////
	// 반복 일정 구하기
	// $outCal['day']
	$sql = "SELECT * from {$table_calendar} WHERE {$sql_where_cal} AND retimes>0 ";
	$sql .= " AND (startdate<='{$searchDateTo}' AND enddate >='{$searchDateFrom}') ";
	$sql .= " AND (dtype = 'hour' or dtype = 'day') ";
	$sql .="  ORDER BY starthour";
	$result	= db_query($sql);
	while( $list=db_array($result) ) {
		// 반복되는 첫 $tmp_time 구함
		if(strcmp($list['startdate'],$searchDateFrom)<0) {
			$tmp_time = strtotime($searchDateFrom);
			switch($list['retype']) {
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
					$tmp_time = strtotime("substr($searchDateFrom,0,8)".substr($list['startdate'],-2));
					break;
			} // end switch
		}
		else {
			// 기간안에 startdate가 있기에 그것이 첫날임
			$tmp_time = strtotime($list['startdate']);
		}

		if($list['dtype'] == "day" )
			$lhour= "[ 하루 종일 ]";
		else
			$lhour="[{$list['starthour']}:{$list['startmin']}~{$list['endhour']}:{$list['endmin']}]";

		// 권한체크
		// 권한체크
		if(!privAuth($list,"priv_level")) {
			$list['title']	= "비공개 일정";
			$list['content']	= "비공개 일정";

			// URL Link
			$href['view'] = "javascript: return false;";
		}
		else {
			//$list['title'] = cut_string($list['title'], 12);
			$list['title'] = htmlspecialchars($list['title'],ENT_QUOTES);
			$list['content'] = cut_string($list['content'], 150);
			$list['content'] = htmlspecialchars($list['content'],ENT_QUOTES);
			$list['content'] = replace_string($list['content'], 'text');	// 문서 형식에 맞추어서 내용 변경

			// URL Link
			$href['view'] = "./index.php?".href_qs("mode=view&bmode={$_GET['mode']}&uid={$list['uid']}",$qs_basic);

		} // end if.. else


		// 일정 변수에 저장
		$tmp_enddate = (strcmp($searchDateTo,$list['enddate'])<0) ? $searchDateTo : $list['enddate'];
		$tmp_time_enddate = strtotime($tmp_enddate);
		while($tmp_time<=$tmp_time_enddate) {// 말일을 지나기 전까지
			$tmp = date("Y-m-d",$tmp_time);
			$outCal[$tmp] .= "<img src=images/micon.gif border=0><font face=굴림><span style='font-size:9pt'><a href='{$href['view']}' onMouseOver=\"view('{$list['title']}', '{$lhour}','{$list['content']}');\" onMouseOut=\"noview();\">{$list['title']}</a></span></font><br> \n"	;

			switch($list['retype']) {
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
<div ID="overDiv" STYLE="position:absolute;top=50;substr=100; visibility:hide; z-index:2;"></div>
<script LANGUAGE="JavaScript" src="cal_div.js" type="Text/JavaScript"></script>

<table border="0" width="590" cellspacing="0" cellpadding="0" bordercolor="#000000" bordercolorlight="#000000">
	<tr>
		<td>
			<div align="center">
			<table border="1" width="590" cellspacing="0" cellpadding="0" bordercolor="#ffffff" bordercolorlight="#000000">
<?php
	for($i=0; $i < 7; $i++)	{
		$tmp_time	= $intWeekFirstDay_T + 86400*$i;
		$intcday	= date("Y-m-d",$tmp_time);
		$intPrintDay = intval(date("d",$tmp_time));
		$varPrintDay = date("m월 d일",$tmp_time);

		switch ($i){
			Case 0: $varPWeekday="일"; break;
			Case 1: $varPWeekday="월"; break;
			Case 2: $varPWeekday="화"; break;
			Case 3: $varPWeekday="수"; break;
			Case 4: $varPWeekday="목"; break;
			Case 5: $varPWeekday="금"; break;
			Case 6: $varPWeekday="토"; break;
		}
		$varPrintDay .= " ({$varPWeekday})";
		
		if( ($i%2) == 0 ) {
			echo "<tr bgcolor=#E3F1FF> \n";  //'짝수일때 테이블 행단위로 색깔 부여
			echo "	<td align=right width=100 height=50 bgcolor=c7c7c7 > \n";
		}
		else {
			echo "<tr> \n"	;
			echo "	<td align=right width=100 height=50 bgcolor=d7d7d7 > \n";
		}

		// URL Link
		$href['goinput']	= "./index.php?" .  href_qs("mode=input&date={$intcday}",$qs_basic);
		$href['goday']	= "./index.php?" . href_qs("mode=day&date={$intcday}",$qs_basic);


		echo "	<a href='{$href['goday']}'><font face=굴림><span style='font-size:9pt'>{$varPrintDay}</span></font></a>&nbsp;<a href='{$href['goday']}'> <img src=images/add.gif border=0></a>&nbsp; \n";
		echo "	</td> \n";
		echo "	<td align=left> \n";

		// 일정 내용 출력
		if($outCal[$intcday]) echo $outCal[$intcday]; 
		else echo "<span style='font-size:9pt'>&nbsp;</span> \n";
		
		echo "	</td> \n";
		echo"</tr> \n";

	} // end for
?>
</table>
</td></tr></table>
<p align=right><span style='font-size:9pt'>
<?php
$tmp_prevWeek	= date("Y-m-d",$intThisTimestamp - 3600*24*7);
$tmp_nextWeek	= date("Y-m-d",$intThisTimestamp + 3600*24*7);
// URL Link
$href['PrevWeek'] = "{$_SERVER['PHP_SELF']}?" 
					. href_qs("mode=week&date={$tmp_prevWeek}",$qs_basic);
$href['NextWeek'] = "{$_SERVER['PHP_SELF']}?" 
					. href_qs("mode=week&date={$tmp_nextWeek}",$qs_basic);

echo "<a href='{$href['PrevWeek']}'>◀ 이전주[{$tmp_prevWeek}, {$varThisWeekday}요일] </a>&nbsp;&nbsp;";
echo "<a href='{$href['NextWeek']}'> 다음주 [{$tmp_nextWeek}, {$varThisWeekday}요일] ▶ </a>";
?>
</span></p>
