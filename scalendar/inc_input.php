<?php
//=======================================================
// 설  명 : 인클루드 파일 - inc_input.php
// 책임자 : 박선민 , 검수: 03/09/16
// Project: sitePHPbasic
// ChangeLog
//   DATE   수정인			 수정 내용
// -------- ------ --------------------------------------
// 03/09/16 박선민 마지막 수정
//=======================================================

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
	// 인쿨루드인 경우에만 허용
	if ($_SERVER["PATH_TRANSLATED"] == realpath(__FILE__)) {
		echo "직접 호출되어서 거부함";
		exit;
	}
	if($_GET['mode']=="edit") {
		$sql = "SELECT *  FROM {$table_calendar} WHERE uid ='{$_GET['uid']}'";
		if(!$list=db_arrayone($sql))
			back("해당 일정이 없습니다");

		// 인증 체크(자기 글이면 무조건 보기)
		if(!privAuth($list, "priv_level",1)) back("비공개 일정이거나 레벨이 부족합니다");

		$list['title']	= htmlspecialchars($list['title'],ENT_QUOTES);
		$list['content']	= htmlspecialchars($list['content'],ENT_QUOTES);

		$list['start_timestamp'] = strtotime($list['startdate']) + $list['starthour']*3600 + $list['startmin']*60;
		$list['end_timestamp'] = strtotime($list['enddate']) + $list['endhour']*3600 + $list['endmin']*60;
	}
	else {
		$list['startdate']	= $_GET['date'] ? $_GET['date'] : date("Y-m-d");
		$list['enddate']		= $list['startdate'];
		$list['starthour']	= $_GET['starthour'] ? $_GET['starthour']: 9;
		$list['endhour']		= $list['starthour'] +1;
		$list['dtype']		= "hour";

		$_GET['mode'] == "input";
	}
	$form_input = "name=cal action='ok.php' method=post >";
	$form_input .= substr(href_qs("mode={$_GET['mode']}&uid={$_GET['uid']}",$qs_basic,1),0,-1);
//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
?>
<script LANGUAGE="JavaScript" src="/scommon/js/chkform.js" type="Text/JavaScript"></script>
<script LANGUAGE="JavaScript" src="/scommon/js/inputcalendar.js" type="Text/JavaScript"></script>
<table border="0" width="590" cellspacing="0" cellpadding="0" bordercolor="#000000" bordercolorlight="#000000">
	<tr>
		<td>
			<table bgcolor=#E3F1FF border="1" width="590" cellspacing="0" cellpadding="0" bordercolor="#ffffff" bordercolorlight="#000000">
			<form onsubmit="return chkForm(this)" <?=$form_input?>>
			<tr>
				<td width=100 height=30>
					<font color=#333399><span style="font-size: 9pt"><b>제목:</b></font>
				</td>
				<td valign="top">
					<input type=text name=title size=40 maxlength=40 value="<?= $list['title']?>" hname="일정 제목을 입력하여 주세요." required></font>
				</td>
			</tr>
			<tr>
				<td>
					<font color=#333399><span style="font-size: 9pt"><b>장소:</b></font>
				</td>
				<td>
					<font><input type=text name=place maxlength=40 value="<?= $list['place']?>"></font>
				</td>
			</tr>

			<tr>
				<td height=30>
					<font color=#333399><span style="font-size: 9pt"><b>일정성격:</b></font>
				</td>
				<td>
					<font><select name="kind">
						<!--option value="약속"<?php if ($list['kind'] =="약속" ) echo "selected"?>>약속</option>
						<option value="회의"<?php if ($list['kind'] =="회의" ) echo "selected"?>>회의</option>
						<option value="행사"<?<?php if ($list['kind'] =="행사" ) echo "selected" ?>>행사</option>
						<option value="출장"<?<?php if ($list['kind'] =="출장" ) echo "selected" ?>>출장</option>
						<option value="휴가"<?<?php if ($list['kind'] =="휴가" ) echo "selected" ?>>휴가</option-->
						<option value="경기"<?<?php if ($list['kind'] =="경기" ) echo "selected" ?>>경기</option>
						<option value="생일"<?<?php if ($list['kind'] =="생일" ) echo "selected" ?>>생일</option>
						<option value="외박"<?<?php if ($list['kind'] =="외박" ) echo "selected" ?>>외박</option>
						<option value="전지"<?<?php if ($list['kind'] =="전지" ) echo "selected" ?>>전지</option>
						<option value="합숙"<?<?php if ($list['kind'] =="합숙" ) echo "selected" ?>>행사</option>
						<option value="훈련"<?<?php if ($list['kind'] =="훈련" ) echo "selected" ?>>훈련</option>
						<option value="휴가"<?<?php if ($list['kind'] =="휴가" ) echo "selected" ?>>휴가</option>
						<option value="휴식"<?<?php if ($list['kind'] =="휴식" ) echo "selected" ?>>휴식</option>
					</select></font>					
					<font><input type=text name="priv_level" value='<?=(int)$list['priv_level'] ?>' size=4><span style="font-size: 9pt">레벨 이상(0:모두에게공개)</span></font>					
					
				</td>
			</tr>
			<tr>
				<td height=30>
					<font color=#333399><span style="font-size: 9pt"><b>일자:</b></font>
				</td>
				<td>
					<INPUT name="startdate" TYPE=text id="startdate" ONCLICK="Calendar(this);" VALUE="<?=$list['startdate'] ?>" size='10' readonly>
				</td>
			</tr>
			<tr>
				<td height=30>
					<font color=#333399><span style="font-size: 9pt"><b>시간 구분:</b></font>
				</td>
				<td>
					<font color=black><span style="font-size: 9pt">
					<input type=radio name='dtype' value='hour'<?<?php if ($list['dtype'] =="hour" ) echo "checked" ?>> 시간단위 일정&nbsp;&nbsp;
					<input type=radio name='dtype' value='day'<?<?php if ($list['dtype'] =="day" ) echo "checked" ?>> 하루 종일&nbsp;&nbsp;
					<input type=radio name='dtype' value='month'<?<?php if ($list['dtype'] =="month" ) echo "checked" ?>> 월중행사&nbsp;&nbsp;
					</span></font>
				</td>
			</tr>
			<tr>
				<td bgcolor=#E3e1FF>
					<font color=#333399><span style="font-size: 9pt"><b>내용:</b></font>
				</td>
				<td bgcolor=#E3e1FF>
					<font><textarea name="content" rows=5 cols=50 wrap=soft hname="일정 내용을 입력하여 주세요." required><?= $list['content'] ?></textarea></font>
				</td>
			</tr>

			<tr>
				<td height=35 bgcolor=#E3e1FF>
					<font color=#333399><span style="font-size: 9pt"><b>시작 시간:</b></font>
				</td>
				<td bgcolor=#E3e1FF>
					<font><select name=starthour onChange="changeEndHour(this.form)">
<?php
						for($i=0; $i < 24; $i++)
						{
							echo "<option value=".$i;
							if(intval($list['starthour'])==$i )
								echo " selected ";

							if($i < 13)
								echo " >".$i." AM \n";
							else
								echo " >".($i-12)." PM \n";
						}
?>
					</select></font>

					<font><select name=startmin onChange="changeEndMin(this.form)">
<?php
						for($i=0; $i < 56 ; $i+=5)
						{
							echo "<option value=".$i;
							if(intval($list['startmin'])==$i )
								echo " selected ";

							echo " >".$i.$vbCR;
						}
?>
					</select></font>

					<font color=#339999 size=1>▶</font>
					<span style="font-size: 9pt"><B>기간</B></FONT>

					<font><select name="durHour" onChange="_changeEndHour(this.form)">
						<option value=0 >0</option>
						<option value=1 selected >1</option>
						<option value=2 >2</option>
						<option value=3 >3</option>
						<option value=4 >4</option>
						<option value=5 >5</option>
						<option value=6 >6</option>
						<option value=7 >7</option>
						<option value=8 >8</option>
						<option value=9 >9</option>
						<option value=10 >10</option>
						<option value=11 >11</option>
						<option value=12 >12</option>
						<option value=13 >13</option>
						<option value=14 >14</option>
						<option value=15 >15</option>
						<option value=16 >16</option>
						<option value=17 >17</option>
						<option value=18 >18</option>
						<option value=19 >19</option>
						<option value=20 >20</option>
						<option value=21 >21</option>
						<option value=22 >22</option>
						<option value=23 >23</option>
					</select>

					<span style="font-size: 9pt">시간</FONT>
					<font><select name="durmin" onChange="_changeEndMin(this.form)">

						<option selected value=00>00
						<option value=05>05
						<option value=10>10
						<option value=15>15
						<option value=20>20
						<option value=25>25
						<option value=30>30
						<option value=35>35
						<option value=40>40
						<option value=45>45
						<option value=50>50
						<option value=55>55
					</select><span style="font-size: 9pt">분 동안</FONT>
				</td>
			</tr>
			<tr>
				<td bgcolor=#E3e1FF>
					<font color=#333399><span style="font-size: 9pt"><b>종료시간:</b></font>&nbsp;
				</td>
				<td bgcolor=#E3e1FF>
					<font>	<select name=endhour onChange="changeDurHour(this.form)">
<?php
						for($i=0; $i < 23; $i++)
						{
							echo "<option value=".$i;
							if(intval($list['endhour'])==$i )
								echo " selected ";
							if($i < 13 )
								echo " >".$i." AM \n";
							else
								echo " >".($i-12)." PM \n";
						}
?>
					</select></font>

					<font><select name=endmin onChange="changeDurMin(this.form)">
<?php
						for($i=0; $i < 56; $i+=5)
						{
							echo "<option value=".$i;
							if(intval($list['endmin'])==$i )
								echo " selected ";
							echo " >".$i.$vbCR;
						}
?>
					</select></font>
				</td>
			</tr>
			<tr>
				<td bgcolor=#E3e1FF rowspan=2>
					<font color=#cc3333><span style="font-size: 9pt"><b>반복옵션:</b></font>
				</td>
				<td bgcolor=#E3e1FF height=50>
					<span style="font-size: 9pt">반복적인 일정의 기간을 선택합니다.<br>

					<font><select name="retimes">
						<option value=0<?<?php if($list['retimes'] == 0 ) echo "selected" ?> >반복하지 않는다</option>
						<option value=1<?<?php if($list['retimes'] == 1 ) echo "selected" ?>>매</option>
						<option value=2<?<?php if($list['retimes'] == 2 ) echo "selected" ?>>둘째</option>
						<option value=3<?<?php if($list['retimes'] == 3 ) echo "selected" ?>>셋째</option>
						<option value=4<?<?php if($list['retimes'] == 4 ) echo "selected" ?>>넷째</option>
					</select></font>
					
					<font><select name="retype">
						<option value='day'<?<?php if($list['retype'] == 'day' ) echo "selected" ?>>일</option>
						<option value='week'<?<?php if($list['retype'] == 'week' ) echo "selected" ?> >주</option>
						<option value='month'<?<?php if($list['retype'] == 'month' ) echo "selected" ?>>월</option>
						<option value='year'<?<?php if($list['retype'] == 'year' ) echo "selected" ?>>년</option>
					</select></font>
				</td>
			</tr>
			<tr>
				<td bgcolor=#E3e1FF height=50>
					<span style="font-size: 9pt">입력한 반복 일정의 종료 기간을 선택합니다.<br>
					<INPUT name="enddate" TYPE=text id="enddate" ONCLICK="Calendar(this);" VALUE="<?=$list['enddate'] ?>" size='10' readonly>
					<span style="font-size: 9pt"><b>까지 </b>
				</td>
			</tr>
			<tr>
				<td width="100" bgcolor=#efefef height=40>&nbsp;
				</td>
				<td bgcolor=#efefef>&nbsp;
					<input type=submit name=Submit value="확인">
					<a href='javascript:history.back(-1)'>
					<img src="images/cancle.gif" width="43" height="22" border="0">
					</a>
					</font>
				</td>
			</tr>
			</form>
		</table>
	</td>
</td>
</table>

