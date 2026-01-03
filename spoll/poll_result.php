<?php
//=======================================================
// 설	명 : 설문조사 삽입 예제
// 책임자 : 박선민 (sponsor@new21.com), 검수: 03/08/25
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인			수정 내용
// -------- ------ --------------------------------------
// 03/08/25 박선민 김평수 소스에서 포팅
//=======================================================
$HEADER=array(
		'priv' => '', // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'useApp' => 1
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $_SERVER['HTTP_HOST']);

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	$table_pollinfo = "{$SITE['th']}pollinfo";
	$table_userinfo = "{$SITE['th']}userinfo";

	if( !$list_pollinfo = db_arrayone("SELECT * from {$table_pollinfo} WHERE db ='{$_REQUEST['db']}'") )
		back("해당 설문이 없습니다 . 감사합니다.");
	
	$table_poll = "{$SITE['th']}poll_" . $list_pollinfo['db'];

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================

## 총 투표수
$result2 = db_query("SELECT * from {$table_poll}");
$total_poll = db_count(); 
?>
<html>
<head>

<SCRIPT>
function detail(db){
	window.self.close();
	window.open("./detail.php?db="+db,'','toolbar=no,location=no,status=no,menubar=no,scrollbars=auto,resizable=yes,width=900,height=600 top=10 left=10');
}
</SCRIPT>

<title>투표결과 보기</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>

<body bgcolor="#FFFFFF" text="#000000" topmargin="0" leftmargin="0" >
<div id="page_content" style="position:absolute;left:0;top:0;width:100%"> 
<br/>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC">
	<tr> 
	<td bgcolor="e6e6e6" height="2"></td>
	</tr>
	<tr> 
	<td bgcolor="f6f6f6" height="23" bordercolor="#FFFFFF"> 
	<div align="center"><b><font size="2" color="#0066CC">:: 투표결과 ::</font></b></div>
	</td>
	</tr>
	<tr> 
	<td bgcolor="f6f6f6" bordercolor="#FFFFFF"> 
	<table width="95%" border="1" cellspacing="0" cellpadding="0" align="center" bordercolor="#FFFFFF">
		<tr> 
		<td bgcolor="#FFFFFF" bordercolor="#999999" valign="top" height="121"> 
			<table width="100%" cellspacing="0" cellpadding="5" align="center" style="border-top : 0;border: 1px solid #484848;background-color:whitesmoke;color:black;">
			<tr bgcolor="#999999"> 
				<td colspan="3" height="25"> 
				<div align="center"><font size="2" color="#FFFFFF"><b> 
<?php echo $list_pollinfo['title']; ?>
					</b></font></div></td>
			</tr>
			<tr> 
				<td colspan="3" height="25" style="border-bottom : 1px solid #b4b4b4;"> 
				<div align="center"><font size="2">총 투표수 : 
<?php echo $total_poll ; ?>
					</font></div></td>
			</tr>
<?php
for($i=1; $i<=$list_pollinfo['q_num']; $i++){
		
		$result = db_query("SELECT count(*) FROM {$table_poll} WHERE val = {$i}");
		$list = db_array($result);
		$list['val'] = db_result($result,0,"count(*)");
		$v_num = "q".$i; 
?>
	
			<tr> 
				<td width="25%" height="25" style="border-bottom : 1px solid #b4b4b4; padding-left:15px;"	bgcolor="#F6F6F6"><font size="2"> 
				<strong><?php echo $list_pollinfo["q{$i}"]; ?>
				</strong></font></td>
				<td width="55%" height="25" bgcolor="#FDFDFD" style="border-bottom : 1px solid #b4b4b4">
				<img src="images/line.gif" width="<?php echo ($total_poll == 0 ? "" : round(($list['val']/$total_poll)*100)); ?>%" height="18"> </td>
				<td width="20%" height="25" bgcolor="#F6F6F6" style="border-bottom : 1px solid #b4b4b4"> 
				<div align="right"><font size="2">
<?php echo $list['val']; ?>
					( 
<?php
echo ($total_poll == 0 ? "" : round(($list['val']/$total_poll)*100)); 
?>
				%)</font><font size="1"> </font></div></td>
			</tr>
<?php
} // end for
?>
			<tr align="center" valign="middle"> 
				<td colspan="3"> 
				<div align="center"><font size="2"><b><br>
					</b></font></div></td>
			</tr>
			</table>
		</td>
		</tr>
	</table>

	</td>
	</tr>
	<tr> 
	<td height="40" align="center" bgcolor="f6f6f6"><a href="#" onClick="window.close()"><img src="./images/pollclose_buttons.gif" width="61" height="19" border="0"></a></td>
	</tr>
	<tr> 
	<td height="5" bgcolor="e6e6e6"></td>
	</tr>
</table>
<!---------------- 이 부분을 해당 페이지의 body 안에 넣습니다 ---------------> 
<script language="JavaScript1.2"> 
function iframe_reset(){ 
		dataobj=document.all? document.all.page_content : document.getElementById("page_content") 
		
		dataobj.style.top=0 
		dataobj.style.left=0 

		pagelength=dataobj.offsetHeight 
		pagewidth=dataobj.offsetWidth 

//		parent.document.all.detail1.height=pagelength 
//		parent.document.all.detail1.width=pagewidth 
} 
window.onload=iframe_reset 
</script> 
<!------------------------------------------------------------------------> 
</div>
</body>
</html>