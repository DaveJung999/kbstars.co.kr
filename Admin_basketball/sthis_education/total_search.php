<?php
//====================================================================== 
// 설	명 : 검색어 입력으로 각 게시판 별 검색 건수 출력, 각 게시판 list.php로 링크
// 작성자 : 안형진(ahn186@brainvil.com)
// 작성일 : 2005. 4. 1
//====================================================================== 

$HEADER=array(
		'priv' =>	"운영자,뉴스관리자", // 인증유무 (0:모두에게 허용, 숫자가 높을 수록 레벨업)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'useSkin' =>	1, // 템플릿 사용
		'useBoard2' => 1,
		'html_echo' =>	0, 			// html header, tail 삽입(tail은 파일 마지막에 echo $SITE['tail'])
		'useApp' => 1, // cut_string()
		);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $HTTP_HOST);

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
$thisPath		= dirname(__FILE__);
$thisUrl	= "/Admin_basketball/sthis_education"; // 마지막 "/"이 빠져야함
include_once("./dbinfo.php"); // $dbinfo, $table 값 정의

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

// 기본 URL QueryString
$table_dbinfo	= $dbinfo['table'];

$sc_column = $_POST['sc_column'];
$sc_string = $_POST['sc_string'];

$board_db[0] = "free";
$board_db[1] = "qna";
$board_db[2] = "policy";
$board_db[3] = "discussion";
$board_db[4] = "pds";
$board_db[5] = "policy_pds";
$board_db[6] = "data_pds";
$board_db[7] = "notice";
$board_db[8] = "news";

$board_name = Array("자유게시판", "Q&A", "정책제안", "토론방", "일반자료실", "정책자료실", "자료제안", "공지사항", "새소식");

for($i = 0 ; $i < count($board_db) ; $i++)	{
	$sql 				= " SELECT count(*) as cnt FROM new21_board2_".$board_db[$i]." ";
	$sql_where 		= " WHERE {$sc_column} like '%$sc_string%' ";
	$sql = $sql.$sql_where;

	$list = db_arrayone($sql);
	$cnt[$i] = $list['cnt'];
	
} 

?>
<style type="text/css">
<!--
.style1 {
	font-size: 13px;
	font-weight: bold;
}
-->
</style>

<table width="930"	border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td width="60">&nbsp;</td>
	<td>
		<table width="850"	border="0" cellpadding="0" cellspacing="0" background="/images/index_bg1.gif">
			<tr>
					<td width="859" rowspan="2" valign="top">
					<table width="100%"	border="0" cellspacing="0" cellpadding="0">
							<tr>
							<td rowspan="2">&nbsp;</td>
							<td valign="top">&nbsp;</td>
							</tr>
								<tr>
								<td valign="top"><img src="/images/title_23.gif" width="555" height="20"></td>
							</tr>
								<tr>
								<td width="255" valign="bottom">
									<table cellpadding="0" cellspacing="0">
										<tr>
											<td width="245">
												<p><img src="/images/index_bottom_img1.jpg" width="255" height="240" border="0"></p>
												</td>
										</tr>
									</table>
								</td>
									<td align="center" valign="top"><br>다음의 결과가 검색되었습니다.<br><br>
									<!------------------------------------ Content Start --------------------------------------------->
									<table width="450" border="1" cellpadding="0" cellspacing="0" bodercolor="#cccccc">
									<?php
									for($i = 0 ; $i < count($cnt) ; $i++)	{
									?>
										<tr>
											<td width="70%" height="20" bgcolor="#E6EDFB">&nbsp;&nbsp;
											<a href="/sboard2/list.php?db=<?php echo $board_db[$i] ; ?>&sc_coulmn=<?php echo $sc_coulmn ; ?>&sc_string=<?php echo $sc_string ; ?>"> <?php echo $board_name[$i] ; ?></a></td>
											<td align="center" valign="middle">&nbsp;&nbsp;<?php echo $cnt[$i] ; ?> 건</td>
										</tr>
									<?php
									 } 
									?>
									</table>
									<!------------------------------------ Content End --------------------------------------------->
								</td>
							</tr>
							</table>
					</td>
					<td width="44">&nbsp;</td>
					<td width="11">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2" valign="bottom"><img src="/images/index_bottom_right.gif" align="bottom" width="55" height="32" border="0"></td>
				</tr>
				<tr>
					<td colspan="3"><img src="/images/index_bottom.gif" width="870" height="8" border="0"></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php echo $SITE['tail']; ?>