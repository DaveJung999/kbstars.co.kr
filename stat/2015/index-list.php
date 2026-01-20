<?php
//=======================================================
// 설	명 : 템플릿 샘플
// 책임자 : 박선민 (sponsor@new21.com), 검수: 05/11/20
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 05/11/20 박선민 마지막 수정
//
// 25/08/12 Gemini (PHP 7, MariaDB 11 호환성 개선)
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
	$table_season = "season";
	$table_game = "game";
	$table_team = "team";

	// season 선택시에
	if(isset($_GET['choSeason'])){
		$_GET['choSeason'] = (int)$_GET['choSeason'];
		$sql = "select * from {$table_season} where s_hide=0 and sid = '{$_GET['choSeason']}' limit 1";
		if(!$season = db_arrayone($sql)) back('다른 시즌을 선택하세요');
	
		$_GET['date'] = date("Y-m-d",$season['s_start']);
	} else {
		$sql = "select * from {$table_season} where s_hide=0 order by s_start DESC limit 1";
		if(!$season = db_arrayone($sql)) back('잘못된 요청입니다');	
	}	
//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
?>
<table style="line-height:150%; margin-top:0; margin-bottom:0;" border="0" cellpadding="0" cellspacing="0" width="690">
	<tr>
		<td width="680">
			<p style="line-height:150%; margin-top:0; margin-bottom:0;"><span style="font-size:10pt;"><img src="/img/intro-18.gif" width="690" height="81" border="0" /></span></p>
		</td>
	</tr>
	<tr>
		<td width="680"><p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;">&nbsp;</p>
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
						default:
							// default case for other seasons
							break;
					}
?>
					</div>
					
					<form action="index.php" style="margin:0px">
						<select name="choSeason" onchange="javascript: window.location='?choSeason='+this.value;" >
<?php
$sql = "select * from {$table_season} where s_hide=0 order by s_start DESC";
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
						<span style="line-height:100%; margin-top:0; margin-bottom:0;"><input type="image" src="/img/go-dal.gif" width="76" height="20" border="0" align="absmiddle" /></span>
					</form>
					</td>
				</tr>
				<tr>
					<td height="5" colspan="11" align="right"></td>
				</tr>										
				<tr>
					<td width="90" bgcolor="#E6DED6"><p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><img src="/img/team-s-1.gif" width="42" height="26" border="0"></p> </td>
					<td width="6" bgcolor="#E6DED6"><p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><img src="/img/sell-bar-s.gif" width="1" height="26" border="0"></p> </td>
					<td width="47" bgcolor="#E6DED6"><p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><img src="/img/team-s-2.gif" width="29" height="26" border="0"></p> </td>
					<td width="9" bgcolor="#E6DED6"><p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><img src="/img/sell-bar-s.gif" width="1" height="26" border="0"></p> </td>
					<td width="100" bgcolor="#E6DED6"><div align="center"><img src="/img/team-s-3.gif" border="0" /></div></td>
					<td width="7" bgcolor="#E6DED6"><p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><img src="/img/sell-bar-s.gif" width="1" height="26" border="0"></p></td>
					<td width="100" bgcolor="#E6DED6"><p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><img src="/img/team-s-3-1.gif" border="0" /></p> </td>
					<td width="8" bgcolor="#E6DED6"><p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><img src="/img/sell-bar-s.gif" width="1" height="26" border="0"></p> </td>
					<td width="133" bgcolor="#E6DED6"><p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><img src="/img/team-s-4.gif" width="27" height="26" border="0"></p> </td>
					<td width="8" bgcolor="#E6DED6"><p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><img src="/img/sell-bar-s.gif" width="1" height="26" border="0"></p> </td>
					<td width="92" bgcolor="#E6DED6"><p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><img src="/img/team-s-5.gif" width="53" height="26" border="0"></p> </td>
				</tr>
<?php
$sql = "select * from {$table_game} where sid='{$season['sid']}' and (g_home=13 or g_away=13) order by g_start";
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

	if(isset($glist['home_score']) && isset($glist['away_score'])){
		// 팀승리에 따른 색깔변경
		if($glist['home_score'] > $glist['away_score'] ){
			if($glist['g_home'] == 13){
				$glist['home_score']	= "<font color=red><b>".$glist['home_score']."</b></font>";
				$glist['g_home_name']	= "<font color=red><b>".$glist['g_home_name']."</b></font>";
				$glist['away_score']	= "<font color=blue>".$glist['away_score']."</font>";
				$glist['g_away_name']	= "<font color=blue>".$glist['g_away_name']."</font>";
			} else {
				$glist['away_score']	= "<font color=red><b>".$glist['away_score']."</b></font>";
				$glist['g_away_name']	= "<font color=red><b>".$glist['g_away_name']."</b></font>";
				$glist['home_score']	= "<font color=blue>".$glist['home_score']."</font>";
				$glist['g_home_name']	= "<font color=blue>".$glist['g_home_name']."</font>";
			}
		} else if($glist['home_score'] < $glist['away_score']){
			if($glist['g_away'] == 13){
				$glist['away_score']	= "<font color=red><b>".$glist['away_score']."</b></font>";
				$glist['g_away_name']	= "<font color=red><b>".$glist['g_away_name']."</b></font>";
				$glist['home_score']	= "<font color=blue>".$glist['home_score']."</font>";
				$glist['g_home_name']	= "<font color=blue>".$glist['g_home_name']."</font>";
			} else {
				$glist['home_score']	= "<font color=red><b>".$glist['home_score']."</b></font>";
				$glist['g_home_name']	= "<font color=red><b>".$glist['g_home_name']."</b></font>";
				$glist['away_score']	= "<font color=blue>".$glist['away_score']."</font>";
				$glist['g_away_name']	= "<font color=blue>".$glist['g_away_name']."</font>";
			}
		} else { // 무승부
			$glist['home_score']	= "<font>".$glist['home_score']."</font>";
			$glist['g_home_name']	= "<font>".$glist['g_home_name']."</font>";
			$glist['away_score']	= "<font>".$glist['away_score']."</font>";
			$glist['g_away_name']	= "<font>".$glist['g_away_name']."</font>";
		}
		
		$glist['score'] = "{$glist['home_score']}:{$glist['away_score']}";
	}
	
	if($i++%2) $glist['bgcolor'] = "background='/img/list-bar.gif'";
	else $glist['bgcolor'] = "";

	if( (isset($glist['home_score']) && $glist['home_score']) || (isset($glist['away_score']) && $glist['away_score']) )
		$glist['href'] = "2-read.php?gid=".$glist['gid'];
	else $glist['href'] = "javascript: void(0);";
?>
				<tr height="30">
					<td <?php echo $glist['bgcolor'] ; ?>><div align="center"><a href="<?php echo htmlspecialchars($glist['href']); ?>"><?php echo htmlspecialchars($glist['g_start']); ?></a></div></td>
					<td <?php echo $glist['bgcolor'] ; ?>><p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;">&nbsp;</p> </td>
					<td <?php echo $glist['bgcolor'] ; ?>><div align="center"> <?php echo htmlspecialchars($glist['g_start_week']); ?></div></td>
					<td <?php echo $glist['bgcolor'] ; ?>><p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;">&nbsp;</p> </td>
					<td <?php echo $glist['bgcolor'] ; ?>><div align="center"> <?php echo $glist['g_home_name']; ?></div></td>
					<td <?php echo $glist['bgcolor'] ; ?>><div align="center"></div></td>
					<td <?php echo $glist['bgcolor'] ; ?>><p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><?php echo $glist['g_away_name']; ?></p> </td>
					<td <?php echo $glist['bgcolor'] ; ?>><p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;">&nbsp;</p> </td>
					<td <?php echo $glist['bgcolor'] ; ?>><p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><?php echo htmlspecialchars($glist['g_ground']); ?> </p> </td>
					<td <?php echo $glist['bgcolor'] ; ?>><p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;">&nbsp;</p> </td>
					<td <?php echo $glist['bgcolor'] ; ?>><p align="center" style="line-height:150%; margin-top:0; margin-bottom:0;"><?php echo isset($glist['score']) ? $glist['score'] : ''; ?></p> </td>
				</tr>
<?php
}
?>
			</table>
		</td>
	</tr>
</table>
<?php echo $SITE['tail']; ?>