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
	'html_skin' => '2015_d12'
);
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');

$_SESSION['joinPriv'] = ""; 
?>
<style type="text/css">
<!--
.font_notice {font-weight: bold;
	color: #FFF;
	font-size: 12px;
}
.gibon_font {font-size: 12px;
	color: #666;
	font-weight: normal;
}
.sitemap {font-size: 12px;
	color: #666;
}
-->
</style>
<table width="760" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
	<td height="52"><table width="760" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="370"><img src="/images/2011/image/sub_title_12_1.jpg" width="420" height="42" /></td>
		<td>&nbsp;</td>
		<td align="right" class="sitemap"> KB STARS 회원 &gt; 회원가입안내</td>
	</tr>
	</table></td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td align="center"><table width="585" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="262"><img src="/images/2011/image/join_logo.jpg" width="262" height="324" /></td>
		<td width="323" align="left" valign="top"><table width="323" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td><img src="/images/2011/image/join_txt_1.jpg" width="323" height="64" /></td>
		</tr>
		<tr>
			<td><table width="323" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="218"><img src="/images/2011/image/join_txt_2.jpg" width="218" height="58" /></td>
				<td align="center"><a href="index.php?priv=person"><img src="/images/2011/image/join_btn_1.jpg" width="86" height="46" border="0" align="absmiddle" /></a></td>
			</tr>
			</table></td>
		</tr>
		<tr>
			<td><img src="/images/2011/image/join_txt_3.jpg" width="323" height="20" /></td>
		</tr>
		<tr>
			<td><table width="323" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="218"><img src="/images/2011/image/join_txt_4.jpg" width="218" height="60" /></td>
				<td align="center"><a href="index.php?priv=junior"><img src="/images/2011/image/join_btn_2.jpg" width="86" height="46" border="0" align="absmiddle" /></a></td>
			</tr>
			</table></td>
		</tr>
		</table></td>
	</tr>
	</table></td>
	</tr>
</table><?php
//=======================================================
echo $SITE['tail']; 
?>
