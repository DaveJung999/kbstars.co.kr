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

	
//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
?>
document.writeln('<table width="259" border="0" cellspacing="0" cellpadding="0">');
document.writeln('	<tr>');
document.writeln('	<td height="24" background="/img/m_lb04_title.gif"><table width="230" border="0" align="right" cellpadding="0" cellspacing="0">');
document.writeln('	<tr>');
document.writeln('		<td width="159" height="22"><strong><font color="#FFFFFF">종합순위</font></strong></td>');
document.writeln('		<td width="71"><img src="/img/m_lb_button.gif" width="63" height="13" border="0" /></td>');
document.writeln('	</tr>');
document.writeln('	</table></td>');
document.writeln('	</tr>');
document.writeln('	<tr>');
document.writeln('	<td><table width="259" border="0" align="center" cellpadding="0" cellspacing="0">');
document.writeln('	<tr>');
document.writeln('		<td width="32"><img src="/img/m_lb04_01.gif" width="32" height="20" /></td>');
document.writeln('		<td width="125"><img src="/img/m_lb04_02.gif" width="125" height="20" /></td>');
document.writeln('		<td width="32"><img src="/img/m_lb04_03.gif" width="32" height="20" /></td>');
document.writeln('		<td width="10"><img src="/img/m_lb04_04.gif" width="32" height="20" /></td>');
document.writeln('		<td width="60"><img src="/img/m_lb04_05.gif" width="38" height="20" /></td>');
document.writeln('	</tr>');
document.writeln('	</table></td>');
document.writeln('	</tr>');
document.writeln('	<tr>');
document.writeln('	<td height="132" align="center" bgcolor="FAF1DA" style="line-height:180%;">지금은 진행중인 리그가 없습니다.</td>');
document.writeln('	</tr>');
document.writeln('	<tr>');
document.writeln('	<td height="1" bgcolor="#D5D5D5"></td>');
document.writeln('	</tr>');
document.writeln('</table>');