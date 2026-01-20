<?php
//=======================================================
// 설	명 : 템플릿 샘플
// 책임자 : 박선민 (sponsor@new21.com), 검수: 05/11/20
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 05/11/20 박선민 마지막 수정
//=======================================================
$HEADER = array(
	'priv' => '', // 인증유무 (비회원,회원,운영자,서버관리자)
	'html_echo' => 1,
	'html_skin' => 'stat'
);
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	$table_season = "season";
	$table_game = "game";
	$table_team = "team";
	
	// season 선택시에
	if($_GET['choSeason']){
		$_GET['choSeason'] = (int)$_GET['choSeason'];
		$sql = "SELECT * from {$table_season} where s_hide=0 and sid = '{$choSeason}' limit 1";
		if(!$season = db_arrayone($sql)) back('다른 시즌을 선택하세요');
		
		$_GET['date'] = date("Y-m-d",$season['s_start']);
	} else {
		$sql = "SELECT * from {$table_season} where s_hide=0 order by s_start DESC limit 1";
		$season = db_arrayone($sql);
		$choSeason = $season['sid'];
		if(!$season) back('잘못된 요청입니다');	
	}	
//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
$sql = "SELECT * from {$table_game} where sid='{$season['sid']}' and (g_home=13 or g_away=13) order by g_start";
$rs_list = db_query($sql);
$strWeek = array('일','월','화','수','목','금','토');
$i=0;
while($list = db_array($rs_list)){
	$list['startdate'] = date("Y/m/d",$list['g_start']);
	if($list['g_start']>time()+3600*24) $list['startdate'] = "<font color=#CCCCCC>".$list['startdate']."</font>";

	if($list['g_home'] == 13){
		$outIcon[$list['startdate']] = "<img src='/img/h-icon.gif' width=11 height=13 border=0>";
		$list['strLogo'] = "/img/team/logo-{$list['g_away']}.gif";
		$list['strWin'] = ($list['home_score'] > $list['away_score']) ? "승" : "패";
	} else {
		$outIcon[$list['startdate']] = "<img src='/img/a-icon.gif' width=11 height=13 border=0>";		
		$list['strLogo'] = "/img/team/logo-{$list['g_home']}.gif";
		$list['strWin'] = ($list['home_score'] < $list['away_score']) ? "승" : "패";
	}
	
	if($list['sms_q1']) 
		$list['href'] = "<a href=\"javascript: MM_openBrWindow('5-read.php?gid={$list['gid']}','smskbsavers','status=yes,resizable=yes,width=500,height=560')\"><img src='{$list['strLogo']}' width='60' height='50' border='0'	/></a>";
	elseif($list['startdate'] <= date("Y/m/d"))
		$list['href'] = "<a href=\"javascript: live('{$list['sms_season_gu']}','{$list['sms_game_type']}','{$list['sms_gameno']}')\"><img src='{$list['strLogo']}' width='60' height='50' border='0'	/></a>";
	else 
		$list['href'] = "<img src='{$list['strLogo']}' width='60' height='50' border='0'	/>";
	
	
	$around[++$i] = "
			<table style='line-height:100%; margin-top:0; margin-bottom:0;' border='0' cellpadding='0' cellspacing='0' width='134' height='116' background='/img/sms-box.gif'>
				<tr>
				<td width='680'><p style='line-height:100%; margin-top:0; margin-bottom:0;' align='center'>$list['href']</p>
					<br />
						<div style='text-align:center;mso-char-wrap:1;mso-kinsoku-overflow:1;mso-word-wrap:0'>
						<b>{$list['startdate']}</b></span>
					</div></td>
				</tr>
			</table>
	";
	
	
} 

?>
<script type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
	window.open(theURL,winName,features);
}


	function live(sms_season_gu, sms_gametype, sms_gameno){
		var url = "http://wkbl.or.kr/live/page/live_pop_result.asp?season_gu=" + sms_season_gu + "&game_type=" + sms_gametype + "&game_no=" + sms_gameno;
		var live = window.open(url,"live","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=680,height=800")
	}
	
	//-->
</script>
<table style="line-height:100%; margin-top:0; margin-bottom:0;" border="0" cellpadding="0" cellspacing="0" width="690">
	<tr>
	<td width="680"><p style="line-height:100%; margin-top:0; margin-bottom:0;"><span style="font-size:10pt;"><img src="/img/intro-22.gif" width="690" height="69" border="0" /></span></p></td>
	</tr>
	<tr>
	<td width="680"><table width="690" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td height="20"></td>
	</tr>
	<tr>
	<td style="position:relative;">		<div style="position:absolute; left:0; top:-4">
<?php
switch($season['sid']){
											case 11:
												echo "<img src='/img/indexlist_top_2007_2008.gif' />";
												break;
											case 10:
												echo "<img src='/img/indexlist_top_2007_winter.gif' />";
												break;
											case 7:
												echo "<img src='/img/indexlist_top_2006_summer.gif' />";
												break;
												
											} 

?>
										</div>
			<p style="line-height:100%; margin-top:0; margin-bottom:0;" align="right">&nbsp;
			<select name="choSeason" onchange="javascript: window.location='?choSeason='+this.value;" >
<?php
$sql = "SELECT * from {$table_season} where s_hide=0 order by s_start DESC";
$rs_tmp = db_query($sql);
while($ltmp = db_array($rs_tmp)){
	if( $ltmp['sid'] == $season['sid'] ){
		echo "<option value='{$ltmp['sid']}' selected>{$ltmp['s_name']}</option>\n";
	} else {
		echo "<option value='{$ltmp['sid']}'>{$ltmp['s_name']}</option>\n";
	}
} 

?>
					</select>
		&nbsp;</p></td>
	</tr>
	<tr>
	<td height="20"></td>
	</tr>
</table>

										
		<table style="line-height:100%; margin-top:0; margin-bottom:0;" border="0" cellpadding="0" cellspacing="0" width="690">
		<tr>
			<td colspan="5"><p style="line-height:100%; margin-top:0; margin-bottom:0;"><img src="/img/1round.gif" width="117" height="38" border="0" /></p></td>
		</tr>
		<tr valign="middle">
			<td width="138" valign="top" align="center"> <?php echo $around[1] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[2] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[3] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[4] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[5] ; ?> </td>
		</tr>
		<tr>
			<td colspan="5"><p style="line-height:100%; margin-top:0; margin-bottom:0;"><img src="/img/2round.gif" width="117" height="39" border="0" /></p></td>
		</tr>
		<tr valign="middle">
			<td width="138" valign="top" align="center"> <?php echo $around[6] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[7] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[8] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[9] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[10] ; ?> </td>
		</tr>
		<tr>
			<td colspan="5"><p style="line-height:100%; margin-top:0; margin-bottom:0;"><img src="/img/3round.gif" width="117" height="39" border="0" /></p></td>
		</tr>
		<tr valign="middle">
			<td width="138" valign="top" align="center"> <?php echo $around[11] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[12] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[13] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[14] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[15] ; ?> </td>
		</tr>
		<tr>
			<td colspan="5"><p style="line-height:100%; margin-top:0; margin-bottom:0;"><img src="/img/4round.gif" width="117" height="39" border="0" /></p></td>
		</tr>
		<tr valign="middle">
			<td width="138" valign="top" align="center"> <?php echo $around[16] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[17] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[18] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[19] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[20] ; ?> </td>
		</tr>
<?php
if ( $choSeason > 10 ) {	//2007년 겨울리그 이후부터 7라운드로 늘어남 . 
?>
		
		<tr>
			<td colspan="5"><p style="line-height:100%; margin-top:0; margin-bottom:0;"><img src="/img/5round.gif" width="117" height="39" border="0" /></p></td>
		</tr>
		<tr valign="middle">
			<td width="138" valign="top" align="center"> <?php echo $around[21] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[22] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[23] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[24] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[25] ; ?> </td>
		</tr>
		<tr>
			<td colspan="5"><p style="line-height:100%; margin-top:0; margin-bottom:0;"><img src="/img/6round.gif" width="117" height="39" border="0" /></p></td>
		</tr>
		<tr valign="middle">
			<td width="138" valign="top" align="center"> <?php echo $around[26] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[27] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[28] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[29] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[30] ; ?> </td>
		</tr>
		<tr>
			<td colspan="5"><p style="line-height:100%; margin-top:0; margin-bottom:0;"><img src="/img/7round.gif" width="117" height="39" border="0" /></p></td>
		</tr>
		<tr valign="middle">
			<td width="138" valign="top" align="center"> <?php echo $around[31] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[32] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[33] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[34] ; ?> </td>
			<td width="138" valign="top" align="center"> <?php echo $around[35] ; ?> </td>
		</tr>
<?php
} 
?>
	</table></td>
	</tr>
</table>
<?php echo $SITE['tail']; ?>
