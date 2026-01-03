<?php
//=======================================================
// 설	명 : 심플리스트-추가/수정 - Modernized for PHP 7.4+
// 책임자 : 박선민 (), 검수: 05/11/20
// Project: sitePHPbasic
// ChangeLog
//	DATE		수정인			수정 내용
// --------	----------	--------------------------------------
// 25/08/11	Gemini AI	PHP 7.4+ 호환성 업데이트, MySQLi 적용, 보안 강화
// 05/11/20	박선민		마지막 수정
//=======================================================
$HEADER = array(
	'html_echo' => 1, // html header, tail 삽입(tail은 파일 마지막에 echo $SITE['tail'])
	'html_skin' => "schedule" // html header 파일(/stpl/basic/index_$HEADER['html'].php 파일을 읽음)
);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");

@session_start();
// global $mysqli 객체가 header.php 또는 dbconn.inc 에서 설정된다고 가정합니다.
include("../global/dbconn.inc");
	
//===================================================
// GET 값 대입......2025-08-08
$intThisYear = $_GET['intThisYear'] ?? date('Y');
$intThisMonth = $_GET['intThisMonth'] ?? date('m');
$session_memid = $_GET['session_memid'] ?? ($_SESSION['memid'] ?? null); // 세션 값도 확인
//===================================================
?>
<table border="0" width="130" cellspacing="0" cellpadding="0" bordercolor="#000000" bordercolorlight="#000000">
	<tr>
		<td>
			<table border="1" width="590" cellspacing="0" cellpadding="0" bordercolor="#ffffff" bordercolorlight="#000000">
				<tr height=25>
					<td bgcolor=FFC125>
						<font face=굴림><span style='font-size:9pt'>
						&nbsp;<?php echo htmlspecialchars($intThisMonth, ENT_QUOTES, 'UTF-8'); ?>월중 일정
						</span></font>
					</td>
				</tr>
				<tr height=40>
					<td>
<?php
					$intMday = $intThisYear . "-" . $intThisMonth . "-01";
					
					// SQL 인젝션 방지를 위해 Prepared Statement 사용
					$sqlList = "SELECT cc_no, cc_title, cc_sdate, cc_shour, cc_smin, cc_ehour, cc_emin, cc_desc 
								FROM club_cal 
								WHERE (cc_memid = ? OR cc_open = '1')
									AND cc_sdate = ?
									AND cc_dtype = '3'
								ORDER BY cc_shour ASC";

					$stmt = $mysqli->prepare($sqlList);
					$stmt->bind_param("ss", $session_memid, $intMday);
					$stmt->execute();
					$result = $stmt->get_result();
					
					$rcount = $result ? $result->num_rows : 0;

					if ($rcount > 0) {
						while ($rsList = $result->fetch_assoc()) {
							$cc_no = $rsList['cc_no'];
							$cc_title = $rsList['cc_title'];
							$cc_sdate = $rsList['cc_sdate'];
							$cc_shour = $rsList['cc_shour'];
							$cc_smin = $rsList['cc_smin'];
							$cc_ehour = $rsList['cc_ehour'];
							$cc_emin = $rsList['cc_emin'];
							$cc_desc = $rsList['cc_desc'];

							// HTML 출력 시 htmlspecialchars 사용 권장
							$cc_title_safe = htmlspecialchars($cc_title, ENT_QUOTES, 'UTF-8');
							
							$cc_desc_safe = substr($cc_desc, 0, 150);
							$cc_desc_safe = htmlspecialchars($cc_desc_safe, ENT_QUOTES, 'UTF-8');
							$cc_desc_safe = nl2br($cc_desc_safe); // str_replace(chr(13).chr(10), "<br>", ...) 대신 사용

							$lhour = htmlspecialchars($intThisMonth, ENT_QUOTES, 'UTF-8') . "월중 일정";

							echo "<img src=images/micon.gif border=0>";
							echo "<font face=굴림><span style='font-size:9pt'><a href=diary.php?d=".urlencode($d ?? '') . "&m=view&cid=".urlencode($cc_no) . " onMouseOver=\"view('".addslashes($cc_title_safe) . "', '".addslashes($lhour) . "','".addslashes($cc_desc_safe) . "');\"	onMouseOut=\"noview();\" >".$cc_title_safe."</a></span></font><br> \n"	;
						}
						$result->free();
					} else {
						echo "<font face=굴림><span style='font-size:9pt'>등록된 월중일정이 없습니다</span></font> \n";
					}
					$stmt->close();
?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php if (isset($SITE['tail'])) echo $SITE['tail']; ?>
