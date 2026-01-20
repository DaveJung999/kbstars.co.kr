<?php
$HEADER=array(
	'priv' =>	"운영자,경기관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
	'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
	'html_echo' => '', // html header, tail 삽입(tail은 파일 마지막에 echo $SITE['tail'])
	'log' => '' // log_site 테이블에 지정한 키워드로 로그 남김
);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $_SERVER['HTTP_HOST']); // PHP 7에서 $HTTP_HOST 대신 $_SERVER['HTTP_HOST'] 사용
//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
// $seHTTP_REFERER는 어디서 링크하여 왔는지 저장하고, 로그인하면서 로그에 남기고 삭제된다.
// session_register 함수는 PHP 5.4.0부터 삭제
if( !isset($_SESSION['seUserid']) && !isset($_SESSION['seHTTP_REFERER']) && isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]) === false ){
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
?>

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
	eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
	if (restore) selObj.selectedIndex=0;
}

function del(){
	var answer=confirm("삭제하시겠습니까?");

	if(answer)
		return true;
	else
		return false;
}
//-->
</script>
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
		<td background="/images/admin/tbox_bg.gif"><strong>시즌정보 </strong></td>
		<td align="right" width="5"><img src="/images/admin/tbox_r.gif" width="5" height="22"></td>
		</tr>
	</table>
		<br>
		<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr align="right">
				<td height="40"><input name="back3" type="button" class="CCbox04" id="back3" onclick="location.href='write.php?mode=write'" value=" 시즌등록 "/></td>
			</tr>
	</table>
		<table width="97%" border="0" align="center" cellpadding="6" cellspacing="1" bordercolordark="white" bgcolor="#666666">
			<tr	bgcolor="#D2BF7E">
				<td height="30" align="center"><strong>시즌</strong></td>
				<td align="center"><strong>시작일</strong></td>
				<td align="center"><strong>종료일</strong></td>
				<td align="center"><strong>우승</strong></td>
				<td align="center"><strong>준우승</strong></td>
				<td align="center"><strong>PO1</strong></td>
				<td align="center"><strong>PO2</strong></td>
				<td align="center"><strong>기록<br />숨기기</strong></td>
				<td align="center"><strong>kpoint<br />숨기기</strong></td>
				<td align="center"><strong>sid</strong></td>
				<td align="center"><strong>수정</strong></td>
				<td align="center"><strong>삭제</strong></td>
			</tr>
<?php
	//팀이름 가져오기
	$tsql = " select * from team order by tid ";
	$trs = db_query($tsql);
	$tcnt = db_count($trs);
	$tname = [];
	$tid_array = [];
	if($tcnt){
		while($tlist = db_array($trs)){
			$tname[$tlist['tid']] = $tlist['t_name']." (".$tlist['tid'].")";
		}
	}

	//시즌정보
	$sql = " SELECT *, sid as s_id FROM season ORDER BY s_start DESC ";
	$rs = db_query($sql);
	$cnt = db_count($rs);
	if($cnt)	{
		while($list = db_array($rs)){
			$list['s_start'] = date("y/m/d", $list['s_start']);
			$list['s_end'] = date("y/m/d", $list['s_end']);
			$first = isset($tname[$list['1st']]) ? $tname[$list['1st']] : '';
			$second = isset($tname[$list['2nd']]) ? $tname[$list['2nd']] : '';
			$third = isset($tname[$list['3rd']]) ? $tname[$list['3rd']] : '';
			$fourth = isset($tname[$list['4th']]) ? $tname[$list['4th']] : '';

			$s_hide = $list['s_hide'] == 0 ? '' : '숨김' ;
			$kpoint_hide = $list['kpoint_hide'] == 0 ? '' : '숨김' ; 
?>

			<tr align="center" bgcolor="#F8F8EA" onMouseOver="this.style.backgroundColor='#C6E2F9'" onMouseOut="this.style.backgroundColor=''">
				<td align="center"> <?php echo htmlspecialchars($list['s_name']) ; ?></td>
				<td align="center"> <?php echo htmlspecialchars($list['s_start']) ; ?></td>
				<td align="center"> <?php echo htmlspecialchars($list['s_end']) ; ?></td>
				<td align="center"> <?php echo htmlspecialchars($first) ; ?></td>
				<td align="center"> <?php echo htmlspecialchars($second) ; ?></td>
				<td align="center"> <?php echo htmlspecialchars($third) ; ?></td>
				<td align="center"> <?php echo htmlspecialchars($fourth) ; ?></td>
				<td align="center"> <?php echo htmlspecialchars($s_hide) ; ?></td>
				<td align="center"> <?php echo htmlspecialchars($kpoint_hide) ; ?></td>
				<td> <?php echo htmlspecialchars($list['s_id']) ; ?></td>
				<td align="center"><input name="back" type="button" class="CCboxw" id="back" onclick="location.href='write.php?mode=modify&s_id=<?php echo htmlspecialchars($list['s_id']) ; ?>'" value=" 수정 "/></td>
				<td align="center"><input name="back2" type="button" class="CCboxw" id="back2" onclick="javascript:if(del()) location.href='ok.php?mode=delete&s_id=<?php echo htmlspecialchars($list['s_id']) ; ?>' " value=" 삭제 "/></td>
			</tr>
<?php
		}
	} else { 
		echo "<tr align=center	bgcolor='#F8F8EA'><td colspan=12 height=30>&nbsp;등록된 시즌이 없습니다.</td></tr>";
	}
?>
	</table>
		<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr align="right">
				<td height="40"><input name="back4" type="button" class="CCbox04" id="back4" onclick="location.href='write.php?mode=write'" value=" 시즌등록 "/></td>
			</tr>
		</table>		<br>
	</td>
	</tr>
</table>
<?php echo $SITE['tail']; ?>