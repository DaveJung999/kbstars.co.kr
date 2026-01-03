<?php
//=======================================================
// 설	명 : 게시판 글읽기(read.php)
// 책임자 : 박선민 (sponsor@new21.com), 검수: 04/07/28
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 04/07/28 박선민 마지막 수정
// 2025/08/13 Gemini	 PHP 7.x, MariaDB 11.x 환경에 맞춰 수정
//=======================================================
$HEADER=array(
	'priv' =>	"운영자,뉴스관리자", // 인증유무 (0:모두에게 허용, 숫자가 높을 수록 레벨업)
	'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
	'useSkin' =>	1, // 템플릿 사용
	'useBoard2' => 1, // 보드관련 함수 포함
	'useApp' => 1
);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $HTTP_HOST);

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
$thisPath		= dirname(__FILE__);
$thisUrl	= "/Admin_basketball/sthis_house"; // 마지막 "/"이 빠져야함
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

// 기본 URL QueryString
if(isset($_GET['getinfo']) && $_GET['getinfo'] == "cont") $qs_basic = "mode=&limitno=&limitrows=";
else $qs_basic = "mode=&pern=&row_pern&page_pern&limitno=&limitrows=&html_headpattern=&html_headtpl&skin=";
$qs_basic		= href_qs($qs_basic); // 해당값 초기화

// info 테이블 정보 가져와서 $dbinfo로 저장
if(isset($_GET['db'])){
	$sql = "SELECT * from {$table_dbinfo} WHERE db='" . db_escape($_GET['db']) . "'";
	$dbinfo=db_arrayone($sql) or back("사용하지 않은 DB입니다.","/");

	// redirect 유무
	if($dbinfo['redirect']) go_url($dbinfo['redirect']);

	$dbinfo['table']	= "{$SITE['th']}{$prefix}_" . $dbinfo['db']; // 게시판 테이블

	$dbinfo['upload_dir'] = trim($dbinfo['upload_dir']) ? trim($dbinfo['upload_dir']) . "/{$SITE['th']}{$prefix}_{$dbinfo['db']}" : dirname(__FILE__) . "/upload/{$SITE['th']}{$prefix}_{$dbinfo['db']}";
}
else back("DB 값이 없습니다");

//=================
// 해당 게시물 읽음
//=================
$sql = "SELECT * FROM {$dbinfo['table']} WHERE uid='" . db_escape($_GET['uid']) . "'";
$list=db_arrayone($sql) or back("게시물이 존재하지 않습니다.");
// 게시물의 카테고리로 변경
$_GET['cateuid'] = $list['cateuid'];

//===================
// 카테고리 정보 구함
//===================
if($dbinfo['enable_cate'] == 'Y'){
	$dbinfo['table_cate']	= {$dbinfo['table']} . "_cate";

//		// 카테고리정보구함 (dbinfo, cateuid, $enable_catelist='Y', sw_topcatetitles, sw_notitems, sw_itemcount,string_firsttotal)
//		// return : highcate[], samecate[], subcate[], subsubcate[], subcateuid[], catelist
//		$tmp_itemcount = trim($_GET['sc_string']) ? 0 : 1;
//		$cateinfo=board2CateInfo($dbinfo, $_GET['cateuid'], 'N', 1,1,$tmp_itemcount,"(종합)");

	// davej............. 2025-08-11
	// 카테고리정보구함 (dbinfo, cateuid, sw_catelist, string_view_firsttotal)
	// return : highcate[], samecate[], subcate[], subsubcate[], subcateuid[], catelist
	$sw_catelist = CATELIST_VIEW | CATELIST_VIEW_TOPCATE_TITLE;
	if(isset($_GET['sc_string']) && strlen($_GET['sc_string'])) $sw_catelist |= CATELIST_NOVIEW_NODATA;
	$cateinfo=board2CateInfo($dbinfo, isset($_REQUEST['cateuid']) ? $_REQUEST['cateuid'] : '', $sw_catelist,'(전체)');

	// 카테고리 정보가 없다면
	if(!$cateinfo['uid']){
		$cateinfo['title']	= "(전체)";
	} else {
		// redirect 유무
		if($cateinfo['redirect']) go_url($cateinfo['redirect']);

		// 카테고리 정보에 따른 dbinfo 변수 변경
		if($dbinfo['enable_cateinfo'] == 'Y'){
			if($cateinfo['bid']>0) $dbinfo['cid'] = $cateinfo['bid'];
			if( $cateinfo['skin'] and is_file("{$thisPath}/stpl/{$cateinfo['skin']}/read.htm") )
				$dbinfo['skin']		= $cateinfo['skin'];
			if($cateinfo['html_headpattern'])	{
				$dbinfo['html_headpattern']	= $cateinfo['html_headpattern'];
				if( $cateinfo['html_headtpl'] and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$cateinfo['html_headtpl']}.php") )
					$dbinfo['html_headtpl']	= $cateinfo['html_headtpl'];
				$dbinfo['html_head']			= $cateinfo['html_head'];
				$dbinfo['html_tail']			= $cateinfo['html_tail'];
			}
			// 나머지 dbinfo값 일괄 변경
			$tmp = array('cut_length', 'imagesize_read', 'enable_memo', 'enable_vote', 'enable_readlog', 'enable_readlist', 'enable_userid', 'enable_getinfo', 'priv_write', 'priv_memowrite', 'priv_reply', 'priv_read', 'priv_download', 'priv_delete');
			foreach($tmp as $tmp_field) {
				if($cateinfo[$tmp_field] != null) $dbinfo[$tmp_field]	= $cateinfo[$tmp_field];
			}
		}
	} // end if
} // end if

// 넘어온 값에 따라 $dbinfo값 변경
if($dbinfo['enable_getinfo'] == 'Y'){
	// skin 변경
	if( isset($_GET['skin']) and preg_match("/^[_a-z0-9]+$/i",$_GET['skin'])
		and is_file("{$thisPath}/stpl/{$_GET['skin']}/read.htm") )
		$dbinfo['skin']	= $_GET['skin'];
	// 사이트 해더테일 변경
	if(isset($_GET['html_headpattern']))	$dbinfo['html_headpattern'] = $_GET['html_headpattern'];
	if( isset($_GET['html_headtpl']) and preg_match("/^[_a-z0-9]+$/i",$_GET['html_headtpl'])
		and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$_GET['html_headtpl']}.php") )
		$dbinfo['html_headtpl'] = $_GET['html_headtpl'];
}

//=================
// 해당 게시물 처리
//=================
// 인증 체크(자기 글이면 무조건 보기)
if(!privAuth($dbinfo, "priv_read",1)){
	if($list['bid']){
		if($list['bid'] != $_SESSION['seUid']){
			// 답변글이고 부모글이 자신이면 읽을 수 있도록
			if(strlen($list['re']) == 0){
				back("이용이 제한되었습니다.(레벨부족)");
			} else {
				// ( re='' or re='a' or re='ac' ) 만들기, re='aca"일때
				$sql_where_privAuth = " num='{$list['num']}' and (re='' ";
				for($i=0;$i<strlen($list['re'])-1;$i++){
					$sql_where_privAuth .= " or re='" . substr($list['re'],0,$i+1) ."' ";
				}
				$sql_where_privAuth .= ") and bid='{$_SESSION['seUid']}' ";
				$sql = "select uid from {$dbinfo['table']} where {$sql_where_privAuth} LIMIT 1";
				if(!db_arrayone($sql))
					back("이용이 제한되었습니다.(레벨부족)");
			} // end if..else..
		} // end if
	}
	else back("이용이 제한되었습니다.(레벨부족)");
} // end if

// 비공개글 제외시킴
if($dbinfo['enable_level'] == 'Y' and !privAuth($list, "priv_level",1)){
	back("읽을 권한이 없습니다.");
}

$list['rdate_date'] = date("y년 m월 d일 H시 i분", $list['rdate']);
$list['title'] = htmlspecialchars($list['title'],ENT_QUOTES);
$list['content'] = replace_string($list['content'], $list['docu_type']);	// 문서 형식에 맞추어서 내용 변경

// 업로드파일 처리
if($dbinfo['enable_upload'] != 'N' and $list['upfiles']){
	$upfiles=unserialize($list['upfiles']);
	if(!is_array($upfiles))	{
		// 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
		$upfiles['upfile']['name']=$list['upfiles'];
		$upfiles['upfile']['size']=(int)$list['upfiles_totalsize'];
	}

	$thumbimagesize=explode("x",$dbinfo['imagesize_read']);
	if((int)$thumbimagesize[0] == 0)	$thumbimagesize[0]=300;
	//if((int)$thumbimagesize[1] == 0)	$thumbimagesize[1]=300; // height는 설정않함

	$appendContent = '';
	foreach($upfiles as $key =>	$value){
		if($value['name']){
			// $filename구함(절대디렉토리포함)
			$filename=$dbinfo['upload_dir'] . "/{$list['bid']}/" . $value['name'];
			if( !is_file($filename) ){
				// 한단계 위에 파일이 있다면 그것으로..
				$filename=$dbinfo['upload_dir'] . "/" . $value['name'];
				if( !is_file($filename) ){
					unset($upfiles[$key]);
					continue;
				} // end if
			} // end if

			$upfiles[$key]['href']="{$thisUrl}/download.php?" . href_qs("uid={$list['uid']}&upfile={$key}",$qs_basic);

			// $upfiles[$key][imagesize]를 width="xxx"(height는 설정 않함)로 저장
			if( is_array($tmp_imagesize=@getimagesize($filename)) ){
				$upfiles[$key]['imagesize'] = " width='" . (($tmp_imagesize[0] > $thumbimagesize[0]) ? $thumbimagesize[0] : $tmp_imagesize[0]) . "'";

				if(strlen($dbinfo['imagesize_read'])>0 and $tmp_imagesize[2] == 4) { // 플래쉬(swf)이면
					$appendContent .= "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' {$upfiles[$key]['imagesize']}
										codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0' >
											<param name=movie value='{$upfiles[$key]['href']}'>
											<param name=quality value=high></object><br>";
				} else {
					// 본문에 그림파일 삽입
					if( strlen($dbinfo['imagesize_read'])>0 and $dbinfo['enable_upload'] != "image" )
						$appendContent .= "<a href='{$upfiles[{$key}]['href']}' target=_blank><img src='{$upfiles[{$key}]['href']}' {$upfiles[{$key}]['imagesize']} border=0></a><br>" ;
				}
			}
			elseif( strlen($dbinfo['imagesize_read'])>0 and preg_match("/\.(avi|asx|wax|m3u|wpl|wvx|mpeg|mpg|mp2|mp3|wav|au|wmv|asf|wm|wma|mid)$/i",substr(basename($value['name']), strrpos(basename($value['name']), ".") + 1)) ){
				// movie 파일이면
				$appendContent .= "<object id='NSOPlay' width='{$thumbimagesize[0]}'	classid='clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95' codebase='http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,5,715' stanby='Loading Microsoft Windows Media Player Components..' type='application/x-oleobject'>
				<param name='FileName' value='{$upfiles[$key]['href']}'>
				<param name='CurrentPosition' value='0'>
				<param name='SetCurrentEntry' value='1'>
				<param name='ClickToPlay' value='0'>
				<param name='AutoSize' value='0'>
				<param name='AutoResize' value='0'>
				<param name='AutoStart' value='1'>
				<param name='ShowControls' value='1'>
				<param name='ShowAudioControls' value='true'>
				<param name='ShowDisplay' value='0'>
				<param name='ShowTracker' value='true'>
				<param name='ShowStatusBar' value='true'>
				<param name='AnimationAtStart' value='0'>
				<param name='TransparentAtStart' value='1'>
				<param name='ShowPositionControls' value='false'>
				<param name='DisplayBackColor' value='0'>
				<param name='ShowTracker' value='0'>
				<param name='SendOpenStateChangeEvents' value='0'>
				<param name='SendPlayStateChangeEvents' value='0'>
				<param name='ShowCaptioning' value='0'>
				<embed type='application/x-mplayer2' pluginspage='http://www.microsoft.com/isapi/redir.dll?prd=windows&sbp=mediaplayer&ar=Media&sba=Plugin' showcontrols=true volume=50 showdisplay=0 showvideo=0 showstatusbar=True width='{$thumbimagesize[0]}'></embed>
				</object>" ;
			} else {
				if($dbinfo['enable_upload'] == "image") unset($upfiles[$key]);
			}
		} // end if
	} // end foreach
	$list['upfiles']=$upfiles;
	unset($upfiles);

	// 이미지등을 본문 앞에 붙임
	if($appendContent) $list['content'] = '<center>'	. $appendContent	. '</center>'	. $list['content'];
} // end if 업로드파일 처리

// URL Link...
if($db == "price"){
$href['list']	= "/d01_peptide/sub3.php";
} else {
$href['list']	= "{$thisUrl}/list.php?" . href_qs("uid=",$qs_basic);
}
$href['listdb'] = "list.php?db={$dbinfo['db']}";
$href['write']	= "{$thisUrl}/write.php?" . href_qs("mode=write&time=".time(),$qs_basic);
$href['reply']	= "{$thisUrl}/write.php?" . href_qs("mode=reply&uid={$list['uid']}&num={$list['num']}&time=".time(),$qs_basic);
$href['modify']	= "{$thisUrl}/write.php?" . href_qs("mode=modify&uid={$list['uid']}&num={$list['num']}&time=".time(),$qs_basic);
$href['delete']	= "{$thisUrl}/ok.php?" . href_qs("mode=delete&uid={$list['uid']}",$qs_basic);

//=====
// misc
//=====
// 관리자이거나 로그거부 ip라면 로그를 남기지 않음
if( $_SESSION['seClass'] != 'root' or !$dbinfo['ipnolog']
	or !in_array($_SERVER['REMOTE_ADDR'],explode(',',$dbinfo['ipnolog'])) ){
	// 조회수 증가
	$sql = "UPDATE LOW_PRIORITY {$dbinfo['table']} SET hit=hit +1, hitip='" . db_escape($_SERVER['REMOTE_ADDR']) . "' WHERE uid='" . db_escape($_GET['uid']) . "' and hitip<>'" . db_escape($_SERVER['REMOTE_ADDR']) . "' and (bid<>'{$_SESSION['seUid']}' or	1>'{$_SESSION['seUid']}') LIMIT 1";
	db_query($sql);

	// 유저별 읽은 유무 로그화(readlog 테이블에)
	if( $dbinfo['enable_readlog'] == 'Y' ){
		if(!isset($_COOKIE["{$dbinfo['table']}_{$_GET['uid']}"]) ){
			if(isset($_SERVER['HTTP_REFERER'])){
				$http_referer_host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
			} else {
				$http_referer_host = '';
			}

			if(isset($_SESSION['seUserid'])) { // 로그인 회원이면
				$sql = "insert into {$dbinfo['table']}_readlog set pid='" . db_escape($list['uid']) . "', bid='" . db_escape($_SESSION['seUid']) . "', userid='" . db_escape($_SESSION['seUserid']) . "',ip='" . db_escape($_SERVER['REMOTE_ADDR']) . "', http_referer_host = '" . db_escape($http_referer_host) . "', http_referer='" . db_escape(preg_replace("/PHPSESSID=[0-9a-z]+/", "", $_SERVER['HTTP_REFERER'])) . "',rdate=UNIX_TIMESTAMP()";
				db_query($sql);
				setcookie("{$dbinfo['table']}_{$_GET['uid']}", 'log',time()+300); // 로그인이후에 로그가 남긴 이후에 다시 않남게
			} else { // 비로그인이면
				$sql = "insert into {$dbinfo['table']}_readlog set pid='" . db_escape($list['uid']) . "', ip='" . db_escape($_SERVER['REMOTE_ADDR']) . "', http_referer_host = '" . db_escape($http_referer_host) . "', http_referer='" . db_escape(preg_replace("/PHPSESSID=[0-9a-z]+/", "", $_SERVER['HTTP_REFERER'])) . "',rdate=UNIX_TIMESTAMP()";
				db_query($sql);
				setcookie("{$dbinfo['table']}_{$_GET['uid']}", @db_insert_id(),time()+300); // 5분간 재방문시 로그 않남게
			}
		} elseif( $_COOKIE["{$dbinfo['table']}_{$_GET['uid']}"] != 'log' and isset($_SESSION['seUserid']) ) { // 로그인이후에 다시 방문이라면
			$sql = "update {$dbinfo['table']}_readlog set bid='" . db_escape($_SESSION['seUid']) . "', userid='" . db_escape($_SESSION['seUserid']) . "' where uid='" . db_escape($_COOKIE["{$dbinfo['table']}_{$_GET['uid']}"]) . "' and ip='" . db_escape($_SERVER['REMOTE_ADDR']) . "'";
			db_query($sql);

			setcookie("{$dbinfo['table']}_{$_GET['uid']}", 'log',time()+300); // 로그인이후에 로그가 남긴 이후에 다시 않남게
		}

		// readlog를 content에 삽입
		if( privAuth($dbinfo, "priv_readlog") or (isset($list['bid']) and $list['bid'] == $_SESSION['seUid']) ){
			// 글쓴이라면, 로그 안남기고, 본문에데가 읽은 사람 리스트화함
			$sql = "select * from {$dbinfo['table']}_readlog where pid='" . db_escape($list['uid']) . "'";
			$rs_readlog=db_query($sql);
			if(db_count($rs_readlog)){
				$tmp_readlog	= "<br><br><br><font size=2><b><> 읽은 사람 리스트</b><br>";
				while($rows=db_array($rs_readlog)){
					$tmp_readlog.=	date("Y-m-d [H:i]",$rows['rdate']) . "- {$rows['userid']}<br>\n";
				} // end while
				$list['content'] .= $tmp_readlog	. "</font>";
			}
			db_free($rs_readlog);
		}
	} // end if
}
//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
// 템플릿 기반 웹 페이지 제작
$tpl = new phemplate("","remove_nonjs");
if( !is_file("{$thisPath}/stpl/{$dbinfo['skin']}/read.htm") ) $dbinfo['skin']="board_basic";
$tpl->set_file('html',"{$thisPath}/stpl/{$dbinfo['skin']}/read.htm",TPL_BLOCK);
//====================================
// 현재 게시물과 관련된 글 List 뿌리기
//====================================
if($dbinfo['enable_readlist'] == 'Y' and $dbinfo['row_pern']<2 ){
	$readlist_num = array($list['num']);
	$tmp_num = db_resultone("select num from {$dbinfo['table']} where num<'{$list['num']}' order by num DESC limit 1",0,'num');
	if($tmp_num) $readlist_num[] = $tmp_num;
	$tmp_num = db_resultone("select num from {$dbinfo['table']} where num>'{$list['num']}' order by num limit 1",0,'num');
	if($tmp_num) $readlist_num[] = $tmp_num;
	$sql = "SELECT * FROM {$dbinfo['table']} WHERE num in (".implode(',',$readlist_num) . ") ORDER BY num, re";
	$re_readlist	= db_query($sql);
	while($readlist=db_array($re_readlist)){
		if($readlist['uid'] == $list['uid']) $readlist['no']	= "<font color=blue>▶</font>";
		else $readlist['no']	= "";
		$readlist['rede']	= strlen($readlist['re']);
		$readlist['rdate']= $readlist['rdate'] ? date("Y/m/d", $readlist['rdate']) : "";	//	날짜 변환
		if(!$readlist['title']) $readlist['title'] = "제목없음…";

		//답변이 있을 경우 자리는 길이를 더 줄임
		$cut_length = $readlist['rede'] ? $dbinfo['cut_length'] - $readlist['rede'] -3 : $dbinfo['cut_length'];
		$readlist['cut_title'] = cut_string($readlist['title'], $cut_length);

		//	Search 단어 색깔 표시
		if(isset($_GET['sc_string'])){
			if(isset($_GET['sc_column'])){
				if($_GET['sc_column'] == "title")
					$readlist['cut_title'] = preg_replace("/(" . preg_quote($_GET['sc_string']) . ")/i", "<font color=darkred>\\0</font>",	$readlist['cut_title']);
				else
					$readlist[$_GET['sc_column']]	= preg_replace("/(" . preg_quote($_GET['sc_string']) . ")/i", "<font color='darkred'>\\0</font>", $readlist[$_GET['sc_column']]);
			} else {
				$readlist['userid']	= preg_replace("/(" . preg_quote($_GET['sc_string']) . ")/i", "<font color=darkred>\\0</font>", $readlist['userid']);
				$readlist['cut_title']= preg_replace("/(" . preg_quote($_GET['sc_string']) . ")/i", "<font color=darkred>\\0</font>",	$readlist['cut_title']);
			}
		}

		// 메모개수 구해서 제목 옆에 붙임
		if($dbinfo['enable_memo'] == 'Y'){
			$dbinfo['table_memo']	= {$dbinfo['table']} . "_memo";

			$sql="select count(*) as count from {$dbinfo['table_memo']} where pid='{$readlist['uid']}'";
			$count_memo=db_resultone($sql,0,"count");
			if($count_memo){
				$sql = "select count(*) as count from {$dbinfo['table_memo']} where pid='{$readlist['uid']}' and rdate > unix_timestamp()-86400 LIMIT 1";
				$count_memo_24h=db_resultone($sql,0,"count");
				if($count_memo_24h) $readlist['cut_title'] .= " [{$count_memo}+]";
				else $readlist['cut_title'] .= " [{$count_memo}]";
			}
		} // end if

		//	답변 게시물 답변 아이콘 표시
		if($readlist['rede'] > 0){
			//$readlist['cut_title'] = str_repeat("&nbsp;", $count_redespace*($readlist['rede']-1)) . "<img src=\"images/re.gif\" align='absmiddle' border=0> {$readlist['cut_title']}";
			$readlist['cut_title'] = "<img src='/scommon/spacer.gif' width='" . ($readlist['rede']-1)*8	. "' height=1 border=0><img src='images/re.gif' align='absmiddle' border=0> {$readlist['cut_title']}";
		}

		//경기결과 Total Score
		if(isset($_GET['db']) && $_GET['db'] == "result")	{
			$list['home_total'] = $list['home_1q'] + $list['home_2q'] + $list['home_3q'] + $list['home_4q'] + $list['home_eq'];
			$list['away_total'] = $list['away_1q'] + $list['away_2q'] + $list['away_3q'] + $list['away_4q'] + $list['away_eq'];
		}

		//팀명
		if(isset($_GET['db']) && $_GET['db'] == "result")	{
			if($list['hometeam'] == "1")	$list['hometeam'] = "KB세이버스";
			else if($list['hometeam'] == "2")	$list['hometeam'] = "금호생명팰컨스";
			else if($list['hometeam'] == "3")	$list['hometeam'] = "삼성생명비추미";
			else if($list['hometeam'] == "4")	$list['hometeam'] = "신세계쿨캣";
			else if($list['hometeam'] == "5")	$list['hometeam'] = "신한에스버드";
			else if($list['hometeam'] == "6")	$list['hometeam'] = "우리은행한새";

			if($list['awayteam'] == "1")	$list['awayteam'] = "KB세이버스";
			else if($list['awayteam'] == "2")	$list['awayteam'] = "금호생명팰컨스";
			else if($list['awayteam'] == "3")	$list['awayteam'] = "삼성생명비추미";
			else if($list['awayteam'] == "4")	$list['awayteam'] = "신세계쿨캣";
			else if($list['awayteam'] == "5")	$list['awayteam'] = "신한에스버드";
			else if($list['awayteam'] == "6")	$list['awayteam'] = "우리은행한새";

		}
		// 업로드파일 처리
		if($dbinfo['enable_upload'] != 'N' and $readlist['upfiles']){
			$upfiles=unserialize($readlist['upfiles']);
			if(!is_array($upfiles)) { // 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
				$upfiles['upfile']['name']=$readlist['upfiles'];
				$upfiles['upfile']['size']=(int)$readlist['upfiles_totalsize'];
			}
			foreach($upfiles as $key =>	$value){
				if($value['name'])
					$upfiles[$key]['href']="{$thisUrl}/download.php?" . href_qs("uid={$readlist['uid']}&upfile={$key}",$qs_basic);
			} // end foreach
			$readlist['upfiles']=$upfiles;
			unset($upfiles);
		} // end if 업로드파일 처리

		// URL Link...
		$href['read']		= "{$thisUrl}/read.php?" . href_qs("uid={$readlist['uid']}",$qs_basic);
		$href['download']	= "{$thisUrl}/download.php?db={$dbinfo['db']}&uid={$readlist['uid']}";

		// 템플릿 YESRESULT 값들 입력
		$tpl->set_var('href.read'		,$href['read']);
		$tpl->set_var('href.download'	,$href['download']);
		$tpl->set_var('readlist'		,$readlist);
		$tpl->set_var('count.lastnum'	,$count['lastnum']--);
		$tpl->process('READLIST','readlist',TPL_APPEND);
	} // end while
} // end if
//==================================== //
//===============
// 메모 부분 처리
//===============
if($dbinfo['enable_memo'] == 'Y'){
	$dbinfo['table_memo']	= {$dbinfo['table']} . "_memo";

	$sql_where_memo = '';
	// 비공개글 제외시킴
	if($dbinfo['enable_memolevel'] == 'Y'){
		if(isset($_SESSION['seUid'])){
			$priv_level	= $dbinfo['gid'] ? (int)$_SESSION['seGroup'][$dbinfo['gid']] : (int)$_SESSION['seLevel'];
			$sql_where_memo .=" ( priv_level<={$priv_level} or bid='{$_SESSION['seUid']}' ) ";
		}
		else $sql_where_memo .="	priv_level=0 ";
	} // end if
	if(!$sql_where_memo) $sql_where_memo = ' 1 ';
	// 메모 DB 읽어드림
	$sql = "select * from {$dbinfo['table_memo']} where pid='" . db_escape($list['uid']) . "' and $sql_where_memo order by rdate";
	$rs_memolist=db_query($sql);
	if(!db_count($rs_memolist)) // 메모된 DB가 없다면
		$tpl->process('MEMOLIST','nomemolist');
	else {
		while($memolist=db_array($rs_memolist)) { // 있다면
			$memolist['rdate']=date("Y-m-d H:i",$memolist['rdate']);

			// URL Link...
			$href['memodelete']="{$thisUrl}/ok.php?" . href_qs("mode=memodelete&uid={$memolist['uid']}&pid={$list['uid']}","db={$_GET['db']}");
			$tpl->set_var("memolist",$memolist);
			$tpl->set_var("href.memodelete",$href['memodelete']);
			$tpl->process("MEMOLIST",'memolist',TPL_APPEND);
		} // end while
	} // end if

	$form_memo=" action='{$thisUrl}/ok.php' method='post'	ENCTYPE='multipart/form-data'>";
	$form_memo .= substr(href_qs("mode=memowrite&pid={$list['uid']}",$qs_basic,1),0,-1);

	$memouserid = '';
	switch($dbinfo['enable_userid']){
		case 'name'		: $memouserid = isset($_SESSION['seName']) ? $_SESSION['seName'] : ''; break;
		case 'nickname'	: $memouserid = isset($_SESSION['seNickname']) ? $_SESSION['seNickname'] : ''; break;
		default			: $memouserid= isset($_SESSION['seUserid']) ? $_SESSION['seUserid'] : ''; break;
	}

	// 템플릿 할당
	$tpl->set_var('form_memo',$form_memo);
	$tpl->set_var('memouserid',$memouserid);
	$tpl->process("MEMO","memo",TPL_APPEND);

	// URL Link
	$href['memowrite'] = "{$thisUrl}/memowrite.php?db={$dbinfo['db']}&uid={$list['uid']}&time=".time() . "&goto=" . urlencode($_SERVER['REQUEST_URI']);
	$href['memolist'] = "{$thisUrl}/memolist.php?db={$dbinfo['db']}";
} // end 메모 부분 처리
//===============//

//=========
// VOTE 부분
//=========
$form_vote	=" action='{$thisUrl}/ok.php' method='post'	ENCTYPE='multipart/form-data'>";
$form_vote.= substr(href_qs("mode=vote&uid={$list['uid']}",$qs_basic,1),0,-1);

$tpl->set_var('form_vote',$form_vote);
//=========//
// 템플릿 마무리 할당
$tpl->set_var('dbinfo'			,$dbinfo);// shopinfo 정보 변수
$tpl->set_var('cateinfo'		,$cateinfo);
$tpl->set_var('href'			,$href);
$tpl->set_var('list'			,$list);

// 블럭 : 카테고리(상위, 동일, 서브) 생성
if($dbinfo['enable_cate'] == 'Y'){
	if(is_array($cateinfo['highcate'])){
		foreach($cateinfo['highcate'] as $key =>	$value){
			$tpl->set_var('href.highcate',"list.php?" . href_qs("cateuid=".$key,$qs_basic));
			$tpl->set_var('highcate.uid',$key);
			$tpl->set_var('highcate.title',$value);
			$tpl->process('HIGHCATE','highcate',TPL_OPTIONAL|TPL_APPEND);
			$tpl->set_var('blockloop',true);
		}
		$tpl->drop_var('blockloop');
	} // end if
	if(is_array($cateinfo['samecate'])){
		foreach($cateinfo['samecate'] as $key =>	$value){
			if($key == $cateinfo['uid'])
				$tpl->set_var('samecate.selected'," selected ");
			else
				$tpl->set_var('samecate.selected',"");
			$tpl->set_var('href.samecate',"list.php?" . href_qs("cateuid=".$key,$qs_basic));
			$tpl->set_var('samecate.uid',$key);
			$tpl->set_var('samecate.title',$value);
			$tpl->process('SAMECATE','samecate',TPL_OPTIONAL|TPL_APPEND);
			$tpl->set_var('blockloop',true);
		}
		$tpl->drop_var('blockloop');
	} // end if
	if(is_array($cateinfo['subcate'])){
		foreach($cateinfo['subcate'] as $key =>	$value){
			// subsubcate...
			$tpl->drop_var('SUBSUBCATE');
			if(is_array($cateinfo['subsubcate'][$key])){
				$blockloop = $tpl->get_var('blockloop');
				$tpl->drop_var('blockloop');
				foreach($cateinfo['subsubcate'][$key] as $subkey =>	$subvalue){
					$tpl->set_var('href.subsubcate',"list.php?" . href_qs("cateuid=".$subkey,$qs_basic));
					$tpl->set_var('subsubcate.uid',$subkey);
					$tpl->set_var('subsubcate.title',$subvalue);
					$tpl->process('SUBSUBCATE','subsubcate',TPL_OPTIONAL|TPL_APPEND);
					$tpl->set_var('blockloop',true);
				}
				$tpl->set_var('blockloop',$blockloop);
			} // end if

			$tpl->set_var('href.subcate',"list.php?" . href_qs("cateuid=".$key,$qs_basic));
			$tpl->set_var('subcate.uid',$key);
			$tpl->set_var('subcate.title',$value);
			$tpl->process('SUBCATE','subcate',TPL_OPTIONAL|TPL_APPEND);
			$tpl->set_var('blockloop',true);
		}
		$tpl->drop_var('blockloop');
	} // end if
} // end if

// 블럭 : 업로드파일 처리
if( ($dbinfo['enable_upload'] != 'N') and is_array($list['upfiles']) and sizeof($list['upfiles']) ){
	foreach($list['upfiles'] as $key =>	$value){
		if($value) { // 파일 이름이 있다면
			$tpl->set_var('upfile',$value);
			$tpl->set_var('upfile.size',number_format($value['size']));
			$tpl->process('UPFILE','upfile',TPL_APPEND);
		}
	}
	$tpl->process('UPFILES','upfiles');
}
// 블럭 : 글쓰기
if(privAuth($dbinfo, "priv_write")) $tpl->process('WRITE','write');

// 블럭 : 글답변
if(privAuth($dbinfo, "priv_reply")) $tpl->process('REPLY','reply');

// 블럭 : 글수정,삭제
if(privAuth($dbinfo, "priv_delete") or (isset($list['bid']) && $list['bid'] == $_SESSION['seUid']) or (isset($list['bid']) && $list['bid'] == 0)){
	$tpl->process('MODIFY','modify');
	$tpl->process('DELETE','delete');
}
// 마무리
$val="\\1{$thisUrl}/stpl/{$dbinfo['skin']}/images/";
// - 사이트 템플릿 읽어오기
if(preg_match("/^(ht|h|t)$/",$dbinfo['html_headpattern'])){
	$HEADER['header'] = 2;
	if( $dbinfo['html_headtpl'] != "" and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php") )
		@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php");
	else
		@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");
}
switch($dbinfo['html_headpattern']){
	case "ht":
		echo $SITE['head'] . $dbinfo['html_head'];
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		echo $dbinfo['html_tail'] . $SITE['tail'];
		break;
	case "h":
		echo $SITE['head'] . $dbinfo['html_head'];
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		echo $dbinfo['html_tail'];
		break;
	case "t":
		echo $dbinfo['html_head'];
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		echo $dbinfo['html_tail'] . $SITE['tail'];
		break;
	case "no":
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		break;
	default:
		echo $dbinfo['html_head'];
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		echo $dbinfo['html_tail'];
} // end switch
?>