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
	'usedb2' => 1,
);
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	$table_season = "`savers_secret`.season";
	$table_game = "`savers_secret`.game";
	$table_team = "`savers_secret`.team";
	$table_player = "`savers_secret`.player";
	$table_record = "`savers_secret`.record";
	
	$gid = $_GET['gid'];
	if(!$gid)	back('경기 정보가 없습니다.');
	
	//경기 기본정보 가져오기
	$trs = db_query(" SELECT *, sid as s_id FROM {$table_game} WHERE gid={$gid} ");
	$tct = db_count($trs);
	if(!$tct) back('경기 정보가 없습니다.');
	else {
		$tlist = db_array($trs);
		
		// 국민은행이면 무조건 먼저
		if($tlist['g_away'] == 13){
			$tmp = $tlist['g_away'];
			$tlist['g_away'] = $tlist['g_home'];
			$tlist['g_home'] = $tmp;
			
			$tmp = $tlist['away_score'];
			$tlist['away_score'] = $tlist['home_score'];
			$tlist['home_score'] = $tmp;
		}
		
		//홈팀 정보
		$htrs = db_query( " SELECT * FROM {$table_team} WHERE tid='{$tlist['g_home']}' ");
		$htct = db_count( $htrs );
		if($htct)	{
			$htlist = db_array( $htrs );
		}
		
		//어웨이팀 정보
		$atrs = db_query( " SELECT * FROM {$table_team} WHERE tid='{$tlist['g_away']}' ");
		$atct = db_count( $atrs );
		if($atct)	{
			$atlist = db_array( $atrs );
		}
	}
	
	if($tlist['home_score'] or $tlist['away_score']){
		if($tlist['home_score'] < $tlist['away_score']){
			$htlist['winlose'] = "lose";
			$atlist['winlose'] = "win";
		} else {
			$htlist['winlose'] = "win";
			$atlist['winlose'] = "lose";
		}	
	}
	
	$_GET['q'] = (int)$_GET['q'];
	if($_GET['q']<1 or $_GET['q']>5) $_GET['q']=1;
	$msg = $tlist["sms_q{$_GET['q']}"];
		
//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
?>
<html>

<head>
<link href="/style.css" rel="stylesheet" type="text/css">

<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>★문자중계실★ KB국민은행 세이버스</title>
<script language="JavaScript">
	<!--
	function na_restore_img_src(name, nsdoc){
		var img = eval((navigator.appName.indexOf('Netscape', 0) != -1) ? nsdoc+'.'+name : 'document.all.'+name);
		if (name == '')
		return;
		if (img && img.altsrc){
		img.src	= img.altsrc;
		img.altsrc = null;
		} 
	}

	function na_preload_img()
	{ 
		var img_list = na_preload_img.arguments;
		if (document.preloadlist == null) 
		document.preloadlist = new Array();
		var top = document.preloadlist.length;
		for (var i=0; i < img_list.length; i++){
		document.preloadlist[top+i]	= new Image;
		document.preloadlist[top+i].src = img_list[i+1];
		} 
	}

	function na_change_img_src(name, nsdoc, rpath, preload)
	{ 
		var img = eval((navigator.appName.indexOf('Netscape', 0) != -1) ? nsdoc+'.'+name : 'document.all.'+name);
		if (name == '')
		return;
		if (img){
		img.altsrc = img.src;
		img.src	= rpath;
		} 
	}

	// -->
</script>
</head>

<body bgcolor="white" text="black" link="blue" vlink="purple" alink="red" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" OnLoad="na_preload_img(false, '/img/sms-menu-1-1.gif', '/img/sms-menu-2-1.gif', '/img/sms-menu-3-1.gif', '/img/sms-menu-4-1.gif', '/img/sms-menu-5-1.gif');">
<table style="line-height:100%; margin-top:0; margin-bottom:0;" border="0" cellpadding="0" cellspacing="0" width="500" height="560">
	<tr>
		<td>
			<p align="center"><img src="/img/sms-title.gif" width="180" height="25" border="0"></p>
		</td>
	</tr>
	<tr>
		<td height="188">
			<table border="0" cellpadding="0" cellspacing="0" width="241" align="center">
				<tr>
					<td width="243">
						<p><img src="/img/s-box-1.gif" width="241" height="18" border="0"></p>
					</td>
				</tr>
				<tr>
					<td width="243" background="/img/s-box-2.gif">
						<table align="center" border="0" cellpadding="0" cellspacing="0" width="230">
							<tr>
								<td width="100">
									<p align="right" style="line-height:100%; margin-top:0; margin-bottom:0;">&nbsp;</p>
									<p style="line-height:100%; margin-top:0; margin-bottom:0;" align="right"><img src="/img/<?php echo $htlist['winlose'] ; ?>-icon.gif" width="56" height="27" border="0" /></p>
									<p style="line-height:100%; margin-top:0; margin-bottom:0;" align="right">&nbsp;</p>
								</td>
								<td width="31">
									<p style="line-height:100%; margin-top:0; margin-bottom:0;">&nbsp;</p>
								</td>
								<td width="99">
									<p style="line-height:100%; margin-top:0; margin-bottom:0;"><img src="/img/<?php echo $atlist['winlose'] ; ?>-icon.gif" width="56" height="27" border="0" /></p>
							</td>
							</tr>
							<tr>
								<td width="100">
									<p align="right" style="line-height:100%; margin-top:0; margin-bottom:0;"><img src="/img/team/logo-<?php echo $htlist['tid'] ; ?>.gif" width="60" height="50" border="0" /></p>
									<p style="line-height:100%; margin-top:0; margin-bottom:0;" align="right">&nbsp;</p>
							</td>
								<td width="31">
									<p align="center" style="line-height:100%; margin-top:0; margin-bottom:0;"><img src="/img/vs.gif" width="27" height="20" border="0"></p>
									<p style="line-height:100%; margin-top:0; margin-bottom:0;" align="center">&nbsp;</p>
								</td>
								<td width="99">
									<p align="left" style="line-height:100%; margin-top:0; margin-bottom:0;"><img src="/img/team/logo-<?php echo $atlist['tid'] ; ?>.gif" width="60" height="50" border="0" /></p>
									<p style="line-height:100%; margin-top:0; margin-bottom:0;" align="left">&nbsp;</p>
							</td>
							</tr>
							<tr>
								<td width="230" colspan="3">
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="170" style="line-height:100%; margin-top:0; margin-bottom:0;">
										<tr>
											<td width="170" height="24" align="center" background="/img/point-box.gif">
											<?php echo $tlist['home_score'] ; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $tlist['away_score'] ; ?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td width="243">
						<p><img src="/img/s-box-3.gif" width="241" height="14" border="0"></p>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table align="center" border="0" cellpadding="0" cellspacing="0" width="500">
				<tr>
					<td>
						<p align="center">
							<a href="5-read.php?gid=<?php echo $_GET['gid'] ; ?>&amp;q=1" onMouseOut="na_restore_img_src('image1', 'document')" onMouseOver="na_change_img_src('image1', 'document', '/img/sms-menu-1-1.gif', true);"><img src="/img/sms-menu-1.gif" width="53" height="21" border="0" name="image1" /></a>
							<a href="5-read.php?gid=<?php echo $_GET['gid'] ; ?>&amp;q=2" OnMouseOut="na_restore_img_src('image2', 'document')" OnMouseOver="na_change_img_src('image2', 'document', '/img/sms-menu-2-1.gif', true);"><img src="/img/sms-menu-2.gif" width="46" height="21" border="0" name="image2"></a>
							<a href="5-read.php?gid=<?php echo $_GET['gid'] ; ?>&amp;q=3" OnMouseOut="na_restore_img_src('image3', 'document')" OnMouseOver="na_change_img_src('image3', 'document', '/img/sms-menu-3-1.gif', true);"><img src="/img/sms-menu-3.gif" width="50" height="21" border="0" name="image3"></a>
							<a href="5-read.php?gid=<?php echo $_GET['gid'] ; ?>&amp;q=4" OnMouseOut="na_restore_img_src('image4', 'document')" OnMouseOver="na_change_img_src('image4', 'document', '/img/sms-menu-4-1.gif', true);"><img src="/img/sms-menu-4.gif" width="49" height="21" border="0" name="image4"></a>
							<a href="5-read.php?gid=<?php echo $_GET['gid'] ; ?>&amp;q=5" OnMouseOut="na_restore_img_src('image5', 'document')" OnMouseOver="na_change_img_src('image5', 'document', '/img/sms-menu-5-1.gif', true);"><img src="/img/sms-menu-5.gif" width="62" height="21" border="0" name="image5"></a>
						</p>
					</td>
				</tr>
				<tr>
					<td width="392" background="/img/sms-bg.gif">
						<p><img src="/img/sms-box-top.gif" width="500" height="32" border="0"></p> </td>
				</tr>
				<tr>
					<td width="392" background="/img/sms-bg.gif">
					<div	align="left" style=" margin-left:20pt; overflow-y:auto; width:450; height:250;">
						<?php echo nl2br($msg) ; ?>
					</div> </td>
				</tr>
				<tr>
					<td width="392">
						<p><img src="/img/sms-end.gif" width="500" height="35" border="0"></p> </td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<p style="line-height:100%; margin-top:0; margin-bottom:0;">&nbsp;</p>
</body>

</html>