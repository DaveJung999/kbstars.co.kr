<?php
$HEADER=array(
		'priv' =>	"운영자,뉴스관리자,사진관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'html_echo' => '', // html header, tail 삽입(tail은 파일 마지막에 echo $SITE['tail'])
		'log' => '' // log_site 테이블에 지정한 키워드로 로그 남김
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $HTTP_HOST);
//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
// $seHTTP_REFERER는 어디서 링크하여 왔는지 저장하고, 로그인하면서 로그에 남기고 삭제된다.
if( !isset($_SESSION['seUserid']) && !isset($_SESSION['seHTTP_REFERER']) && isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]) == false ){
	$_SESSION['seHTTP_REFERER']=$_SERVER['HTTP_REFERER'];
}
//=======================================================
// Start... (DB 작업 및 display)
//=======================================================

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

$list = [];
if($mode == "modify" && $uid)	{
	$sql = " SELECT *, sid as s_id FROM `savers_secret`.season_rank	WHERE uid = " . (int)$uid;
	$rs = db_query($sql);
	$cnt = db_count($rs);

	if($cnt){
		$list = db_array($rs);
	}
} else {
	$list['s_id'] = $_GET['season'] ?? null;
}

//시즌 정보 가져오기
//팀아이디를 팀이름으로 변경
$list['season_name'] = db_resultone("select s_name FROM `savers_secret`.season where sid='" . db_escape($list['s_id']) . "'",0,'s_name');


//팀정보 가져오기
$tsql = " select * from `savers_secret`.team order by tid";
$trs = db_query($tsql);
$tcnt = db_count($trs);

$t_tid = [];
$t_name = [];
if($tcnt)	{
	for($i = 0 ; $i < $tcnt ; $i++)	{
		$tlist = db_array($trs);
		$t_tid[$i] 	= $tlist['tid'];
		$t_name[$i] = $tlist['t_name']." (".$tlist['tid'].")";
	}
}

?>
<link href="/css/basic_text.css" rel="stylesheet" type="text/css">
<link href="/css/link01.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body {
	margin-left: 5px;
	margin-top: 15px;
	margin-right: 5px;
	margin-bottom: 5px;
	background-color:F8F8EA;
}
-->
</style>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td><table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
		<td width="22"><img src="/images/admin/tbox_l.gif" width="22" height="22"></td>
		<td background="/images/admin/tbox_bg.gif"><strong>시즌팀순위 </strong></td>
		<td align="right" width="5"><img src="/images/admin/tbox_r.gif" width="5" height="22"></td>
		</tr>
	</table>
		<br>
		<form action="ok.php" method="post" name="write" id="write">
			<input name="mode" type="hidden" value="<?php echo htmlspecialchars($mode, ENT_QUOTES, 'UTF-8') ; ?>" />
			<input name="uid" type="hidden" value="<?php echo htmlspecialchars($uid, ENT_QUOTES, 'UTF-8') ; ?>" />
			<input name="season" type="hidden" value="<?php echo htmlspecialchars($_GET['season'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" />
			<table width="97%"	border="0" align="center" cellpadding="6" cellspacing="1" bgcolor="#666666">
			<tr>
				<td width="14%" height="30" bgcolor="#D2BF7E" align="center"><strong>시 즌 명 </strong></td>
				<td width="86%" bgcolor="#F8F8EA">&nbsp;&nbsp;<?php echo htmlspecialchars($list['season_name'] ?? '', ENT_QUOTES, 'UTF-8') ; ?></td>
			</tr>
			<tr>
				<td height="30" bgcolor="#D2BF7E" align="center"><strong>순서</strong></td>
				<td bgcolor="#F8F8EA">&nbsp;&nbsp;
					<input name="rank" type="text" id="rank" size="3" value="<?php echo htmlspecialchars($list['rank'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /></td>
			</tr>
			<tr>
				<td height="30" bgcolor="#D2BF7E" align="center"><strong>실제 순위 </strong></td>
				<td bgcolor="#F8F8EA">&nbsp;&nbsp;
					<input name="rank_real" type="text" id="rank_real" size="3" value="<?php echo htmlspecialchars($list['rank_real'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /></td>
			</tr>
			<tr>
				<td height="30" bgcolor="#D2BF7E" align="center"><strong>팀명</strong></td>
				<td bgcolor="#F8F8EA">&nbsp;&nbsp;
					<select name="tid" id="tid">
<?php
					for($i=0 ; $i<count($t_tid) ; $i++)	{
						if(isset($list['tid']) && $list['tid'] == $t_tid[$i])
							echo "<option value=\"".htmlspecialchars($t_tid[$i], ENT_QUOTES, 'UTF-8') . "\" selected>".htmlspecialchars($t_name[$i], ENT_QUOTES, 'UTF-8') . "</option>";
						else
							echo "<option value=\"".htmlspecialchars($t_tid[$i], ENT_QUOTES, 'UTF-8') . "\">".htmlspecialchars($t_name[$i], ENT_QUOTES, 'UTF-8') . "</option>";
					}
?>
				</select></td>
			</tr>
			<tr>
				<td height="30" bgcolor="#D2BF7E" align="center"><strong>승 / 패 </strong></td>
				<td bgcolor="#F8F8EA">&nbsp;
					<input name="win" type="text" id="win" value="<?php echo htmlspecialchars($list['win'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="3" maxlength="4" />
				&nbsp;/&nbsp;
				<input name="lose" type="text" id="lose" value="<?php echo htmlspecialchars($list['lose'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="3" maxlength="4" /></td>
			</tr>
			<tr>
				<td height="30" bgcolor="#D2BF7E" align="center"><strong>승률 / 승차 </strong></td>
				<td bgcolor="#F8F8EA">&nbsp;
					<input name="winrate" type="text" id="winrate" value="<?php echo htmlspecialchars($list['winrate'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="6" />
				&nbsp;/&nbsp;
				<input name="winsub" type="text" id="winsub" value="<?php echo htmlspecialchars($list['winsub'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="6" /></td>
			</tr>
			<tr>
				<td height="30" bgcolor="#D2BF7E" align="center"><strong>연승 승 / 패 </strong></td>
				<td bgcolor="#F8F8EA">&nbsp;
					<input name="win_con" type="text" id="win_con" value="<?php echo htmlspecialchars($list['win_con'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="3" maxlength="4" />
				&nbsp;/&nbsp;
				<input name="lose_con" type="text" id="lose_con" value="<?php echo htmlspecialchars($list['lose_con'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="3" maxlength="4" /></td>
			</tr>
			<tr>
				<td height="30" bgcolor="#D2BF7E" align="center"><strong>점수</strong></td>
				<td bgcolor="#F8F8EA"><table width="100%" border="0" cellspacing="3" cellpadding="6">
					<tr>
					<td>게임수
						<input name="gamecount" type="text" id="gamecount" value="<?php echo htmlspecialchars($list['gamecount'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="3" maxlength="4" /></td>
					<td>총득점
						<input name="win_point" type="text" id="win_point" value="<?php echo htmlspecialchars($list['win_point'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="3" maxlength="4" /></td>
					<td>2점
						<input name="2pm" type="text" id="2pm" value="<?php echo htmlspecialchars($list['2pm'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="3" maxlength="4" />
						/
						<input name="2pa" type="text" id="2pa" value="<?php echo htmlspecialchars($list['2pa'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="3" maxlength="4" /></td>
					<td>3점
						<input name="3pm" type="text" id="3pm" value="<?php echo htmlspecialchars($list['3pm'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="3" maxlength="4" />
						/
						<input name="3pa" type="text" id="3pa" value="<?php echo htmlspecialchars($list['3pa'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="3" maxlength="4" /></td>
					<td>자유투
						<input name="ftm" type="text" id="ftm" value="<?php echo htmlspecialchars($list['ftm'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="3" maxlength="4" />
						/
						<input name="fta" type="text" id="fta" value="<?php echo htmlspecialchars($list['fta'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="3" maxlength="4" /></td>
					<td>라비운드
						<input name="re" type="text" id="re" value="<?php echo htmlspecialchars($list['re'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="3" maxlength="4" /></td>
					</tr>
					<tr>
					<td>어시스트
						<input name="as" type="text" id="as" value="<?php echo htmlspecialchars($list['as'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="3" maxlength="4" /></td>
					<td>스틸
						<input name="st" type="text" id="st" value="<?php echo htmlspecialchars($list['st'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="3" maxlength="4" /></td>
					<td>블록
						<input name="bs" type="text" id="bs" value="<?php echo htmlspecialchars($list['bs'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="3" maxlength="4" /></td>
					<td>턴오버
						<input name="to" type="text" id="to" value="<?php echo htmlspecialchars($list['to'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="3" maxlength="4" /></td>
					<td>파울
						<input name="po" type="text" id="po" value="<?php echo htmlspecialchars($list['po'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="3" maxlength="4" /></td>
					<td>&nbsp;</td>
					</tr>
				</table></td>
			</tr>
			</table>
			</br>
			<table	width="97%" border="0" align="center">
			<tr>
				<td colspan="4" align="center"><input name="submit" type="submit" class="CCbox03" value=" 입 력 " />
				&nbsp;
				<input name="back" type="button" class="CCbox03" id="back" onclick="javascript:location.href='list.php?season=<?php echo $season; ?>'" value=" 뒤 로 " /></td>
			</tr>
			</table>
	</form></td>
	</tr>
</table>

<br />
<?php echo $SITE['tail']; ?>