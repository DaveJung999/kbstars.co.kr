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
);
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	$table_season = "season";
	$table_rank = "season_rank";
	
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
<table width="244" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr align="center">
	<td height="20"><img src="/images/main/main_scoreboard_rank_title.jpg" width="244" height="20" /></td>
	</tr>
	<tr>
		<td height="2"></td>
	</tr>
	<tr>
	<td><table width="244" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="14">&nbsp;</td>
		<td width="216"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
<?php		
$sql = "SELECT * from {$table_rank} where sid='{$season['sid']}' order by rank";
$rs = db_query($sql);
$total = db_count($rs);
if ($total > 0 ){
	for($l=1;$l<=$total;$l++){
		$list = db_array($rs);
		
		$tr_bgcolor = ($list['t_name'] == 'KB국민은행') ? 'background="/images/main/main_scoreboard_savers_bg.jpg"' : '';
		$td_prv = ($list['t_name'] == 'KB국민은행') ? '<font color="white"><strong>' : '<font color="#666666">';
		$td_next = ($list['t_name'] == 'KB국민은행') ? '</strong></font>' : '</font>'; ?>
		<tr align="center">
			<td><table width="216" border="0" cellspacing="0" cellpadding="0"<?php echo $tr_bgcolor	; ?>>
				<tr style="padding-top:1px;">
				<td width="36" height="18" align="center" style="line-height:1"><span class="style31">
<?php 
 echo $td_prv ; 
 echo $list['rank_real'] ; 
 echo $td_next ; 
?>
				</span></td>
				<td width="20" height="18" style="line-height:1"></td>
				<td width="100" align="center" style="line-height:1"><span class="style31">
<?php 
 echo $td_prv ; 
 echo $list['t_name'] ; 
 echo $td_next ; 
?>
				</span></td>
				<td width="30" height="18" align="center" style="line-height:1"><span class="style31">
<?php 
 echo $td_prv ; 
 echo $list['win'] ; 
 echo $td_next ; 
?>
				</span></td>
				<td width="30" height="18" align="center" style="line-height:1"><span class="style31">
<?php 
 echo $td_prv ; 
 echo $list['lose'] ; 
 echo $td_next ; 
?>
				</span></td>
				</tr>
			</table></td>
		</tr>
		<tr>
			<td height="1" bgcolor="eaeaea"></td>
		</tr>
<?php
}
} else { 
?>
		<tr align="center" >
			<td width="18%"height="100">진행중인 리그가 없습니다.</td>
		</tr>
<?php
} 
?>
	<tr>
		<td height="4"></td>
	</tr>
		</table></td>
		<td width="14">&nbsp;</td>
	</tr>
	</table>
	</td>
	</tr>
</table>

