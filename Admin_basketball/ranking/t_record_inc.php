<?php
//=======================================================
// ChangeLog
//	DATE	수정인			 수정 내용
// -------- ------ --------------------------------------
// 25/01/XX Auto 단축 태그 <?= → <?php echo 변경
//=======================================================
function go1(){
?>
	<table width="97%"	border="0" align="center" cellpadding="6" cellspacing="1" bgcolor="#666666">
		<tr align="center" bgcolor="#D2BF7E">
			<td width="5%" height="30" rowspan="2" bgcolor="#D2BF7E"><strong>순위</strong></td>
			<td width="10%" rowspan="2" bgcolor="#D2BF7E"><strong>구 단</strong></td>
			<td width="6%" rowspan="2" bgcolor="#D2BF7E"><strong>경기수</strong></td>
			<td height="25" colspan="3" bgcolor="#D2BF7E"><strong>평균득점</strong></td>
			<td colspan="2" bgcolor="#D2BF7E"><strong>2점슛성공률</strong></td>
			<td colspan="2" bgcolor="#D2BF7E"><strong>3점슛성공률</strong></td>
			<td colspan="2" bgcolor="#D2BF7E"><strong>자유투성공률</strong></td>
			<td colspan="3" bgcolor="#D2BF7E"><strong>어시스트</strong></td>
		</tr>
		<tr bgcolor="#D2BF7E">
			<td width="6%" height="25" align="center" bgcolor="#D2BF7E"><strong>홈</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>원정</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>차이</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>홈</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>원정</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>홈</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>원정</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>홈</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>원정</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>홈</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>원정</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>차이</strong></td>
		</tr>
<?php
	if($_GET['season']){
		$sql_view_a = " SELECT a.*,b.* FROM record_tmp2 as a,record_tmp3 as b where a.tid = b.tid ORDER BY a.t_name, a.score desc ";
		$rs_view_a = db_query($sql_view_a);
		$cnt_view_a = db_count($rs_view_a);

		for($i=1; $i<=$cnt_view_a; $i++) {
			$list_view_a = db_array($rs_view_a);

			$list_view_a['score3'] = number_format($list_view_a['score'] - $list_view_a['score2'],1);
			$list_view_a['gnum']	= $list_view_a['g_num'] + $list_view_a['g_num2'];
			$list_view_a['ast3'] = number_format($list_view_a['ast'] - $list_view_a['ast2'],1);
?>
		<tr align="center" bgcolor="#F8F8EA" onMouseOver="this.style.backgroundColor='#C6E2F9'" onMouseOut="this.style.backgroundColor=''">
			<td height="30" align="center"><?php echo $i; ?></td>
			<td align="center"><?php echo $list_view_a['t_name']; ?></td>
			<td align="center"><?php echo $list_view_a['gnum']; ?></td>
			<td align="center"><?php echo $list_view_a['score']; ?></td>
			<td align="center"><?php echo $list_view_a['score2']; ?></td>
			<td align="center"><?php echo $list_view_a['score3']; ?></td>
			<td align="center"><?php echo $list_view_a['p2']; ?></td>
			<td align="center"><?php echo $list_view_a['p22']; ?></td>
			<td align="center"><?php echo $list_view_a['p3']; ?></td>
			<td align="center"><?php echo $list_view_a['p32']; ?></td>
			<td align="center"><?php echo $list_view_a['fp']; ?></td>
			<td align="center"><?php echo $list_view_a['fp2']; ?></td>
			<td align="center"><?php echo $list_view_a['ast']; ?></td>
			<td align="center"><?php echo $list_view_a['ast2']; ?></td>
			<td align="center"><?php echo $list_view_a['ast3']; ?></td>
		</tr>
<?php
	}
?>
		<tr>
			<td height=1 bgcolor="#E6E2E0" colspan="18"></td>
		</tr>
</table>
<?php
	}
}


function go2(){
?>
	<table width="97%"	border="0" align="center" cellpadding="6" cellspacing="1" bgcolor="#666666">
		<tr align="center" bgcolor="#D2BF7E">
			<td width="5%" height="30" rowspan="2" bgcolor="#D2BF7E"><strong>순위</strong></td>
			<td width="10%" rowspan="2" bgcolor="#D2BF7E"><strong>구 단</strong></td>
			<td width="6%" rowspan="2" bgcolor="#D2BF7E"><strong>경기수</strong></td>
			<td height="25" colspan="3" bgcolor="#D2BF7E"><strong>평균리바운드</strong></td>
			<td colspan="3" bgcolor="#D2BF7E"><strong>평균블럭슛</strong></td>
			<td colspan="3" bgcolor="#D2BF7E"><strong>평균스틸</strong></td>
		</tr>
		<tr bgcolor="#D2BF7E">
			<td width="6%" height="25" align="center" bgcolor="#D2BF7E"><strong>홈</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>원정</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>차이</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>홈</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>원정</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>차이</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>홈</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>원정</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>차이</strong></td>
		</tr>
<?php
	if($_GET['season']){
		$sql_view_a = " SELECT a.*,b.* FROM record_tmp2 as a,record_tmp3 as b where a.tid = b.tid ORDER BY a.t_name, a.rea desc ";
		$rs_view_a = db_query($sql_view_a);
		$cnt_view_a = db_count($rs_view_a);
		
		for($i=1; $i<=$cnt_view_a; $i++) {
			$list_view_a = db_array($rs_view_a);
			
			$list_view_a['gnum']	= $list_view_a['g_num'] + $list_view_a['g_num2'];
			$list_view_a['rea3'] = number_format($list_view_a['rea'] - $list_view_a['rea2'],1);
			$list_view_a['bs3'] = number_format($list_view_a['bs'] - $list_view_a['bs2'],1);
			$list_view_a['stl3'] = number_format($list_view_a['stl'] - $list_view_a['stl2'],1);
?>
		<tr align="center" bgcolor="#F8F8EA" onMouseOver="this.style.backgroundColor='#C6E2F9'" onMouseOut="this.style.backgroundColor=''">
			<td height="30" align="center"><?php echo $i; ?></td>
			<td align="center"><?php echo $list_view_a['t_name']; ?></td>
			<td align="center"><?php echo $list_view_a['gnum']; ?></td>
			<td align="center"><?php echo $list_view_a['rea']; ?></td>
			<td align="center"><?php echo $list_view_a['rea2']; ?></td>
			<td align="center"><?php echo $list_view_a['rea3']; ?></td>
			<td align="center"><?php echo $list_view_a['bs']; ?></td>
			<td align="center"><?php echo $list_view_a['bs2']; ?></td>
			<td align="center"><?php echo $list_view_a['bs3']; ?></td>
			<td align="center"><?php echo $list_view_a['stl']; ?></td>
			<td align="center"><?php echo $list_view_a['stl2']; ?></td>
			<td align="center"><?php echo $list_view_a['stl3']; ?></td>
		</tr>
<?php
	}
?>
		<tr>
			<td height=1 bgcolor="#E6E2E0" colspan="20"></td>
		</tr>
</table>
<?php
	}
}

function go3(){
?>
	<table width="97%"	border="0" align="center" cellpadding="6" cellspacing="1" bgcolor="#666666">
		<tr align="center" bgcolor="#D2BF7E">
			<td width="5%" height="30" rowspan="2" bgcolor="#D2BF7E"><strong>순위</strong></td>
			<td width="10%" rowspan="2" bgcolor="#D2BF7E"><strong>구 단</strong></td>
			<td width="6%" rowspan="2" bgcolor="#D2BF7E"><strong>경기수</strong></td>
			<td height="25" colspan="3" bgcolor="#D2BF7E"><strong>실책</strong></td>
			<td colspan="3" bgcolor="#D2BF7E"><strong>파울</strong></td>
		</tr>
		<tr bgcolor="#D2BF7E">
			<td width="6%" height="25" align="center" bgcolor="#D2BF7E"><strong>홈</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>원정</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>차이</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>홈</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>원정</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>차이</strong></td>
		</tr>
<?php
	if($_GET['season']){
		$sql_view_a = " SELECT a.*,b.* FROM record_tmp2 as a,record_tmp3 as b where a.tid = b.tid ORDER BY a.t_name, a.tov desc ";
		$rs_view_a = db_query($sql_view_a);
		$cnt_view_a = db_count($rs_view_a);
		
		for($i=1; $i<=$cnt_view_a; $i++) {
			$list_view_a = db_array($rs_view_a);
			
			$list_view_a['gnum']	= $list_view_a['g_num'] + $list_view_a['g_num2'];
			$list_view_a['pf3'] = number_format($list_view_a['pf'] - $list_view_a['pf2'],1);
			$list_view_a['tov3'] = number_format($list_view_a['tov'] - $list_view_a['tov2'],1);
?>
		<tr align="center" bgcolor="#F8F8EA" onMouseOver="this.style.backgroundColor='#C6E2F9'" onMouseOut="this.style.backgroundColor=''">
			<td height="30" align="center"><?php echo $i; ?></td>
			<td align="center"><?php echo $list_view_a['t_name']; ?></td>
			<td align="center"><?php echo $list_view_a['gnum']; ?></td>
			<td align="center"><?php echo $list_view_a['tov']; ?></td>
			<td align="center"><?php echo $list_view_a['tov2']; ?></td>
			<td align="center"><?php echo $list_view_a['tov3']; ?></td>
			<td align="center"><?php echo $list_view_a['pf']; ?></td>
			<td align="center"><?php echo $list_view_a['pf2']; ?></td>
			<td align="center"><?php echo $list_view_a['pf3']; ?></td>
		</tr>
<?php
	}	
?>
		<tr>
			<td height=1 bgcolor="#E6E2E0" colspan="20"></td>
		</tr>
</table>
<?php
	}
}

function go4(){
?>
	<table width="97%"	border="0" align="center" cellpadding="6" cellspacing="1" bgcolor="#666666">
		<tr align="center" bgcolor="#D2BF7E">
			<td width="5%" height="30" rowspan="2" bgcolor="#D2BF7E"><strong>순위</strong></td>
			<td width="10%" rowspan="2" bgcolor="#D2BF7E"><strong>구 단</strong></td>
			<td width="6%" rowspan="2" bgcolor="#D2BF7E"><strong>경기수</strong></td>
			<td height="25" colspan="3" bgcolor="#D2BF7E"><strong>70점이상일때</strong></td>
			<td colspan="3" bgcolor="#D2BF7E"><strong>상대팀 70점이상일때</strong></td>
		</tr>
		<tr bgcolor="#D2BF7E">
			<td width="6%" height="25" align="center" bgcolor="#D2BF7E"><strong>승</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>패</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>승률</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>승</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>패</strong></td>
			<td width="6%" align="center" bgcolor="#D2BF7E"><strong>승률</strong></td>
		</tr>
<?php
	if($_GET['season']){
		$sql_view_a = " SELECT a.*,b.* FROM record_tmp2 as a,record_tmp3 as b where a.tid = b.tid ORDER BY a.rea desc ";
		$rs_view_a = db_query($sql_view_a);
		$cnt_view_a = db_count($rs_view_a);
		
		for($i=1; $i<=$cnt_view_a; $i++) {
			$list_view_a = db_array($rs_view_a);
			
			$list_view_a['gnum']	= $list_view_a['g_num'] + $list_view_a['g_num2'];
			$list_view_a['pf3'] = number_format($list_view_a['pf'] - $list_view_a['pf2'],1);
			$list_view_a['tov3'] = number_format($list_view_a['tov'] - $list_view_a['tov2'],1);
?>
		<tr align="center" bgcolor="#F8F8EA" onMouseOver="this.style.backgroundColor='#C6E2F9'" onMouseOut="this.style.backgroundColor=''">
			<td height="30" align="center"><?php echo $i; ?></td>
			<td align="center"><?php echo $list_view_a['t_name']; ?></td>
			<td align="center"><?php echo $list_view_a['gnum']; ?></td>
			<td align="center"><?php echo $list_view_a['tov']; ?></td>
			<td align="center"><?php echo $list_view_a['tov2']; ?></td>
			<td align="center"><?php echo $list_view_a['tov3']; ?></td>
			<td align="center"><?php echo $list_view_a['pf']; ?></td>
			<td align="center"><?php echo $list_view_a['pf2']; ?></td>
			<td align="center"><?php echo $list_view_a['pf3']; ?></td>
		</tr>
<?php
	}
?>
		<tr>
			<td height=1 bgcolor="#E6E2E0" colspan="20"></td>
		</tr>
</table>
<?php
	}
}
?>