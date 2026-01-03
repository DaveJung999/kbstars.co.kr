
<?php
include("../global/dbconn.inc");
	//include("../lib/func_date.inc");
	
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
?>
<!-- 역서부터-->		
<center>
<font size=3 color=red><b>
<?php echo $intThisYear ; ?>년 
<?php echo $intThisMonth ; ?>월 
<?php echo $intThisDay ; ?>일</font></b><br>
<table border=0 width=130>
	<tr>
		<td align=left>
			<a href=diary.php?d=m&F_Year=<?php echo $intPrevYear."&F_Month=".$intPrevMonth."&F_Day=1" ; ?>><font color=navy size=2>&lt;&lt;</font></a>
		</td>
		<td align=right>
			<a href=diary.php?d=m&F_Year=<?php echo $intNextYear."&F_Month=".$intNextMonth."&F_Day=1" ; ?>><font color=navy size=2>&gt;&gt;</font></a>
		</td>
	</tr>
</table>
<table border=0 width=130 cellpadding=1 cellspacing=1>
	<tr >
		<td	align=center bgcolor=#3B42A8><font size=2 color=#FFFFFF><b>일</b></font></td>
		<td	align=center bgcolor=#3B42A8><font size=2	color=#FFFFFF><b>월</b></font></td>
		<td	align=center bgcolor=#3B42A8><font size=2	color=#FFFFFF><b>화</b></font></td>
		<td	align=center bgcolor=#3B42A8><font size=2	color=#FFFFFF><b>수</b></font></td>
		<td	align=center bgcolor=#3B42A8><font size=2	color=#FFFFFF><b>목</b></font></td>
		<td	align=center bgcolor=#3B42A8><font size=2 color=#FFFFFF><b>금</b></font></td>
		<td	align=center bgcolor=#3B42A8><font size=2	color=#FFFFFF><b>토</b></font></td>
	</tr>
<?php
		$Stop_Flag=0;
		$intFirstWeekday=Weekday($datFirstDay, $vbSunday); //넘겨받은 날짜의 주초기값 파악
		$intPrintDay=1;
		For ($intNextWeek=1; $intNextWeek < 7;$intNextWeek++)	//주단위 루프 시작, 최대 6주 
		{
			echo "<tr> \n";
			For ($intNextDay=1; $intNextDay < 8;$intNextDay++) //요일단위 루프 시작, 일요일부터
			{
				if ($intFirstWeekday > 0 ) //첫주시작일이 1보다 크면
				{
					echo "<td bgcolor=white	align=right valign=top><font size=2 color=white>.</font> \n";
					$intFirstWeekday=$intFirstWeekday-1;
				}
				else	//
				{
					if ($intPrintDay > $intLastDay ) //입력날짜가 월말보다 크다면
					{
						echo "<td bgcolor=white	align=right valign=top><font size=2 color=white>.</font> \n";
					}
					else //입력날짜가 현재월에 해당되면
					{
						if ($intThisYear-$NowThisYear == 0 && $intThisMonth-$NowThisMonth == 0 && $intPrintDay-$intThisDay == 0 ) //오늘 날짜이면은 글씨폰트를 다르게
						{
							echo "<td	align=right valign=top><b><a href=diary.php?d=d&F_Year=".$intThisYear."&F_Month=".$intThisMonth."&F_Day=".$intPrintDay." ><font size=2 color=darkorange>".$intPrintDay."</font></a></b><br> \n";
						}
						elseif	($intNextDay == 1 ) //일요일이면 빨간 색으로
						{
							echo "<td bgcolor=white	align=right valign=top><a href=diary.php?d=d&F_Year=".$intThisYear."&F_Month=".$intThisMonth."&F_Day=".$intPrintDay." ><font size=2 color=red>".$intPrintDay."</font></a><br> \n";
						}
						else // 그외의 경우
						{
							echo "<td bgcolor=white	align=right valign=top><font size=2 color=#000000><a href=diary.php?d=d&F_Year=".$intThisYear."&F_Month=".$intThisMonth."&F_Day=".$intPrintDay." >".$intPrintDay."</a></font><br> \n";
						}
					}										
					$intPrintDay=$intPrintDay+1; //날짜값을 1 증가
					if ($intPrintDay > $intLastDay ) //만약 날짜값이 월말값보다 크면 루프문 탈출
					{
						$Stop_Flag=1;
					}
				}
				echo "</td>";
			}
			echo "</tr>";
			if ($Stop_Flag == 1 )
				break;
		} 

?>
	<tr>
		<td colspan=7 align=center >
			<form method="POST" action="diary.php" id=form1 name=cal><font color=white> 
				<div align="center">
					<select name="F_Year" size="1">
<?php
For ($i=1990; $i < 2050;$i++)
					{
						if ($i-$intThisYear == 0 )
							echo "<option selected value=".$i.">".$i."</option> \n";
						else
							echo "<option value=".$i.">".$i."</option> \n";
					}

					echo "</select><small><font color=black>년</font></small> \n";
?>
					<select name=F_Month size=1	onchange="window.open('diary.php?d=m&F_Day=1&F_Month='+F_Month.value+'&F_Year='+F_Year.value,'_self')" >
<?php
for ($i=1; $i < 13; $i++)
					{
						if ($i-$intThisMonth == 0 )
							echo "<option selected value=".$i.">".$i."</option> \n";
						else
							echo "<option value=".$i.">".$i."</option> \n";
					} 

?>
					</select>
					<small><font color=black>월</font></small>
					</div> 
		</td>
	</tr></form>
</table>
<!--달력삽입 끝-->

