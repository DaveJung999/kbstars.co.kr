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
	'usedb2' => 1, // DB 커넥션 사용
	'html_echo' => 1,
	'html_skin' => 'stat'
);
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	$table_season = "`savers_secret`.season";
	$table_game = "`savers_secret`.game";
	$table_team = "`savers_secret`.team";
	
	// season 선택시에
	if($_GET['choSeason']){
		$_GET['choSeason'] = (int)$_GET['choSeason'];
		$sql = "SELECT * from {$table_season} where s_hide=0 and sid = '{$choSeason}' limit 1";
		if(!$season = db_arrayone($sql)) back('다른 시즌을 선택하세요');
		
		$_GET['date'] = date("Y-m-d",$season['s_start']);
	} else {
		$sql = "SELECT * from {$table_season} where s_hide=0 order by s_start DESC limit 1";
		if(!$season = db_arrayone($sql)) back('잘못된 요청입니다');	
	}	
	
//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
?>
<table style="line-height:150%; margin-top:0; margin-bottom:0;" border="0" cellpadding="0" cellspacing="0" width="690">
							<tr>
								<td width="680">
									<p style="line-height:150%; margin-top:0; margin-bottom:0;"><span style="font-size:10pt;"><img src="/img/intro-19.gif" width="690" height="69" border="0"></span></p>
								</td>
							</tr>
							<tr>
								<td width="680">						
									<p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;">&nbsp;</p>
								<p style="line-height:150%; margin-top:0; margin-bottom:0;" align="center">&nbsp;</p>
									<table border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="line-height:150%; margin-top:0; margin-bottom:0;">
							
										<tr>
											<td colspan="11" align="right" style="position:relative;">
												<div style="position:absolute; left:0; top:-4">
<?php
switch($season['sid']){
											case 10:
												echo "<img src='/img/indexlist_top_2007_winter.gif' />";
												break;
											case 7:
												echo "<img src='/img/indexlist_top_2006_summer.gif' />";
												break;
											} 

?>
										</div>
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
												</select>																					</td>
										</tr>
										<tr>
											<td height="5" colspan="11" align="right"></td>
										</tr>										
										<tr>
											<td width="90" bgcolor="#E6DED6">
											<p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><img src="/img/team-s-1.gif" width="42" height="26" border="0"></p> </td>
											<td width="6" bgcolor="#E6DED6">
											<p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><img src="/img/sell-bar-s.gif" width="1" height="26" border="0"></p> </td>
											<td width="47" bgcolor="#E6DED6">
											<p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><img src="/img/team-s-2.gif" width="29" height="26" border="0"></p> </td>
											<td width="9" bgcolor="#E6DED6">
											<p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><img src="/img/sell-bar-s.gif" width="1" height="26" border="0"></p> </td>
											<td width="100" bgcolor="#E6DED6"><div align="center"><img src="/img/team-s-3.gif" width="29" height="26" /></div></td>
											<td width="7" bgcolor="#E6DED6"><p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><img src="/img/sell-bar-s.gif" width="1" height="26" border="0"></p></td>
											<td width="100" bgcolor="#E6DED6">
											<p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><img src="/img/team-s-3-1.gif" width="53" height="26" /></p> </td>
											<td width="8" bgcolor="#E6DED6">
											<p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><img src="/img/sell-bar-s.gif" width="1" height="26" border="0"></p> </td>
											<td width="133" bgcolor="#E6DED6">
											<p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><img src="/img/team-s-4.gif" width="27" height="26" border="0"></p> </td>
											<td width="8" bgcolor="#E6DED6">
											<p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><img src="/img/sell-bar-s.gif" width="1" height="26" border="0"></p> </td>
											<td width="92" bgcolor="#E6DED6">
											<p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><img src="/img/team-s-5.gif" width="53" height="26" border="0"></p> </td>
										</tr>
<?php
$sql = "SELECT * from {$table_game} where sid='{$season['sid']}' order by g_start ";
$rs_list = db_query($sql);
$strWeek = array('일','월','화','수','목','금','토');
$i=0;
while($glist = db_array($rs_list)){
	$glist['g_start_week'] = $strWeek[date('w',$glist['g_start'])];

	$glist['g_start'] = date("Y-m-d", $glist['g_start']);
	
	//팀이름
	$sql = "select t_name from {$table_team} where tid='{$glist['g_home']}'";
	$glist['g_home_name'] = db_resultone($sql,0,'t_name');
	$sql = "select t_name from {$table_team} where tid='{$glist['g_away']}'";
	$glist['g_away_name'] = db_resultone($sql,0,'t_name');
	
	if($glist['home_score'] and $glist['away_score'] ){
		// 팀승리에 따른 색깔변경
		if($glist['home_score'] > $glist['away_score'] ){
			$glist['home_score']	= "<font color#574F43 => <b>".$glist['home_score']."</b>";
			$glist['g_home_name']	= "<font color=#574F43><b>".$glist['g_home_name']."</b>";
		} else {
			$glist['away_score']	= "<font color=#574F43><b>".$glist['away_score']."</b>";
			$glist['g_away_name']	= "<font color=#574F43><b>".$glist['g_away_name']."</b>";
		}
	
		$glist['score'] = "{$glist['home_score']}:{$glist['away_score']}";	
	}
		
	if($i++%2) $glist['bgcolor'] = "background='/img/list-bar.gif'";

	if($glist['home_score'] or $glist['away_score'])
		$glist['href'] = "2-read.php?gid=".$glist['gid'];
	else $glist['href'] = "javascript: void(0);"; 
?>
										
										
										<tr height="30">
										<td <?php echo $glist['bgcolor'] ; ?>><div align="center"> <?php echo $glist['g_start']; ?></div></td>
											<td <?php echo $glist['bgcolor'] ; ?>>
												<p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;">&nbsp;</p> </td>
										<td <?php echo $glist['bgcolor'] ; ?>><div align="center"> <?php echo $glist['g_start_week']; ?></div></td>
											<td <?php echo $glist['bgcolor'] ; ?>>
												<p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;">&nbsp;</p> </td>
											<td <?php echo $glist['bgcolor'] ; ?>><div align="center"> <?php echo $glist['g_home_name']; ?></div></td>
											<td <?php echo $glist['bgcolor'] ; ?>><div align="center"></div></td>
											<td <?php echo $glist['bgcolor'] ; ?>>
												<p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;">
<?php echo $glist['g_away_name']; ?></p> </td>
											<td <?php echo $glist['bgcolor'] ; ?>>
												<p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;">&nbsp;</p> </td>
											<td <?php echo $glist['bgcolor'] ; ?>>
												<p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;">
<?php echo $glist['g_ground']; ?> </p> </td>
											<td <?php echo $glist['bgcolor'] ; ?>>
												<p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;">&nbsp;</p> </td>
										<td <?php echo $glist['bgcolor'] ; ?>>
												<p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;">
<?php echo $glist['score']; ?></p> </td>
										</tr>
<?php
} 
?>
									</table>
								</td>
							</tr>
</table>
<?php echo $SITE['tail']; ?>
