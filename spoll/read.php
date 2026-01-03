<?php
//=======================================================
// 설	명 : 게시판 글읽기(read.php)
// 책임자 : 박선민 (sponsor@new21.com), 검수: 03/12/02
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 03/03/06 박선민 메보부분 버그 수정
// 03/12/02 박선민 마지막 수정
// 25/08/13 Gemini	php 7.x, mariadb 11 버전으로 마이그레이션
//=======================================================
// 앞으로 : 메모부분 인증루틴이 단순 무식함을 보완필요
$HEADER=array(
	'priv' => '', // 인증유무 (0:모두에게 허용, 숫자가 높을 수록 레벨업)
	'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
	'useSkin' =>  1, // 템플릿 사용
	'useBoard2' => 1, // 보드관련 함수 포함
	'useApp' => 1
);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $HTTP_HOST);

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
$thisPath	= dirname(__FILE__);
$thisUrl	= "/spoll"; // 마지막 "/"이 빠져야함

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text', 'html_headtpl'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

$qs_basic = "db={$db}".					//table 이름
			"&mode=".					// mode값은 list.php에서는 당연히 빈값
			"&cateuid={$cateuid}".		//cateuid
			"&team={$team}".		//team
			"&pern={$pern}" .	// 페이지당 표시될 게시물 수
			"&sc_column={$sc_column}".	//search column
			"&html_headtpl={$html_headtpl}".
			"&sc_string=" . urlencode(stripslashes($sc_string)) . //search string
			"&page={$page}";				//현재 페이지

include_once("./dbinfo.php"); // $dbinfo, $table 값 정의

//===================
// 카테고리 정보 구함
//===================
if(isset($dbinfo['enable_cate']) && $dbinfo['enable_cate'] == 'Y'){
	$table_cate	=	$table	. "_cate";

	// 카테고리정보구함 (dbinfo, table_cate, cateuid, $enable_catelist='Y', sw_topcatetitles, sw_notitems, sw_itemcount,string_firsttotal)
	// highcate[], samecate[], subcate[], subsubcate[], subcateuid[], catelist
	$tmp_itemcount = isset($_GET['sc_string']) && trim($_GET['sc_string']) ? 0 : 1;
	$cateinfo=boardCateInfo($dbinfo, $table_cate, $_GET['cateuid'], 'Y', 1,1,$tmp_itemcount,"(종합)");

	if(!isset($_GET['cateuid'])){
		$cateinfo['uid']		= "{$_SERVER['PHP_SELF']}?" . href_qs("",$qs_basic);
		$cateinfo['title']	= "전체";
	}
} // end if

// 넘어온 값에 따라 $dbinfo값 변경
if(isset($dbinfo['enable_getinfo']) && $dbinfo['enable_getinfo'] == 'Y'){
	// PHP 7에서 Undefined index 오류를 방지하기 위해 isset() 체크
	if(isset($_GET['cut_length']))		$dbinfo['cut_length']	= $_GET['cut_length'];
	if(isset($_GET['pern']))			$dbinfo['pern']		= $_GET['pern'];
	if(isset($_GET['row_pern']))		$dbinfo['row_pern']		= $_GET['row_pern'];
	if(isset($_GET['sql_where']))		$sql_where		= $_GET['sql_where'];	//davej..............

	// skin관련
	if(isset($_GET['html_headpattern']))	$dbinfo['html_headpattern'] = $_GET['html_headpattern'];
	// eregi() 함수는 PHP 7에서 제거되었으므로 preg_match()로 변경하고, 변수 존재 여부를 확인
	if( isset($_GET['html_headtpl']) && preg_match("/^[_a-z0-9]+$/i",$_GET['html_headtpl'])
		and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$_GET['html_headtpl']}.php") )
		$dbinfo['html_headtpl'] = $_GET['html_headtpl'];
	if( isset($_GET['skin']) && preg_match("/^[_a-z0-9]+$/i",$_GET['skin'])
		and is_dir("{$thisPath}/stpl/{$_GET['skin']}") )
		$dbinfo['skin']	= $_GET['skin'];
}

//===================
// SQL문 where절 정리
//===================
// 한 table에 여러 게시판 생성의 경우
if(!isset($sql_where)) $sql_where= " 1 ";

// 한 table에 여러 게시판 생성의 경우
if($dbinfo['table_name'] != $dbinfo['db']) $sql_where .= " and db='{$dbinfo['db']}' "; // $sql_where 사용 시작
if($dbinfo['enable_type'] == 'Y') $sql_where .= " and (type='docu' or type='info') ";

//=================
// 해당 게시물 읽음
//=================
if (!isset($_GET['uid'])) back("잘못된 접근입니다.");
$uid = db_escape($_GET['uid']);
$sql = "SELECT * FROM {$table} WHERE uid='{$uid}' and  $sql_where ";
if(!$list=db_arrayone($sql)) back("게시물이 존재하지 않습니다.");

// 인증 체크(자기 글이면 무조건 보기)
if(!privAuth($dbinfo, "priv_read",1)){
	if(isset($list['bid'])){
		if(isset($_SESSION['seUid']) && $list['bid'] !== $_SESSION['seUid']){
			// 답변글이고 부모글이 자신이면 읽을 수 있도록
			if(strlen($list['re']) == 0){
				back("이용이 제한되었습니다.(레벨부족)");
			} else {
				// ( re='' or re='a' or re='ac' ) 만들기, re='aca"일때
				$sql_where_privAuth = " $sql_where and num='{$list['num']}' and (re='' ";
				for($i=0;$i<strlen($list['re'])-1;$i++){
					$sql_where_privAuth .= " or re='" . substr($list['re'],0,$i+1) ."' ";
				}
				$sql_where_privAuth .= ") and bid='{$_SESSION['seUid']}' ";
				$sql = "select * from {$table} where {$sql_where_privAuth}";
				if(!db_arrayone($sql))
					back("이용이 제한되었습니다.(레벨부족)");
			} // end if..else..
		} // end if
	}
	else back("이용이 제한되었습니다.(레벨부족)");
} // end if

// 비공개글 제외시킴
if(isset($dbinfo['enable_level']) && $dbinfo['enable_level'] == 'Y' and !privAuth($list, "priv_level",1)){
	back("이용이 제한되었습니다 . 게시물 설정 권한을 확인바랍니다.");
}

$list['rdate'] = date("Y년 m월 d일 H시 i분", $list['rdate']);
$list['title'] = htmlspecialchars($list['title'],ENT_QUOTES);
$list['content'] = replace_string($list['content'], $list['docu_type']);	// 문서 형식에 맞추어서 내용 변경

// 업로드파일 처리
if(isset($dbinfo['enable_upload']) && $dbinfo['enable_upload'] != 'N' and isset($list['upfiles']) && $list['upfiles']){
	$upfiles=unserialize($list['upfiles']);
	if(!is_array($upfiles))	{
		// 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
		$upfiles['upfile']['name']=$list['upfiles'];
		$upfiles['upfile']['size']=(int)$list['upfiles_totalsize'];
	}

	$thumbimagesize=explode("x",isset($dbinfo['imagesize_read']) ? $dbinfo['imagesize_read'] : "");
	if((int)$thumbimagesize[0] == 0)	$thumbimagesize[0]=300;
	//if((int)$thumbimagesize[1] == 0)	$thumbimagesize[1]=300; // height는 설정않함

	foreach($upfiles as $key =>  $value){
		if(isset($value['name']) && $value['name']){
			// $filename구함(절대디렉토리포함)
			$filename=(isset($dbinfo['upload_dir']) ? $dbinfo['upload_dir'] : "") . "/{$list['bid']}/" . $value['name'];
			if( !is_file($filename) ){
				// 한단계 위에 파일이 있다면 그것으로..
				$filename=(isset($dbinfo['upload_dir']) ? $dbinfo['upload_dir'] : "") . "/" . $value['name'];
				if( !is_file($filename) ){
					unset($upfiles[$key]);
					continue;
				} // end if
			} // end if

			$upfiles[$key]['href']="{$thisUrl}/download.php?" . href_qs("uid={$list['uid']}&upfile={$key}",$qs_basic);

			// $upfiles[$key][imagesize]를 width="xxx"(height는 설정 않함)로 저장
			if( is_array($tmp_imagesize=@getimagesize($filename)) ){
				if(isset($dbinfo['imagesize_read']) && strlen($dbinfo['imagesize_read'])>0 and $tmp_imagesize[2] == 4) { // 플래쉬(swf)이면
					$list['content'] = "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0\" WIDTH=\"500\" HEIGHT=\"400\"> 
										<param name=movie value=\"{$upfiles[$key]['href']}\"> <param name=quality value=high></object><br>" . $list['content'];
				} else {
					$upfiles[$key]['imagesize'] = " width=\"" . (($tmp_imagesize[0] > $thumbimagesize[0]) ? $thumbimagesize[0] : $tmp_imagesize[0]) . "\"";

					// 본문에 그림파일 삽입
					if( isset($dbinfo['imagesize_read']) && strlen($dbinfo['imagesize_read'])>0 and $dbinfo['enable_upload'] != "image" )
						$list['content'] = "<center><a href='{$upfiles[$key]['href']}' target=_blank><img src='{$upfiles[$key]['href']}' {$upfiles[$key]['imagesize']} border=0></a></center><br>" . $list['content'];
				}
			}
			// PHP 7에서는 eregi()가 제거되었으므로 preg_match()로 변경
			elseif( isset($dbinfo['imagesize_read']) && strlen($dbinfo['imagesize_read'])>0 and preg_match("/avi|asx|wax|m3u|wpl|wvx|mpeg|mpg|mp2|mp3|wav|au|wmv|asf|wm|wma|mid/i",substr(basename($value['name']), strrpos(basename($value['name']), ".") + 1)) ){
				// movie 파일이면
				$list['content'] = "<center><object id='NSOPlay' width='{$thumbimagesize[0]}'	classid='clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95' codebase='http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,5,715' stanby='Loading Microsoft Windows Media Player Components..' type='application/x-oleobject'>
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
				</object></center>" . $list['content'];

			} else {
				if(isset($dbinfo['enable_upload']) && $dbinfo['enable_upload'] == "image") unset($upfiles[$key]);
			}
		} // end if
	} // end foreach
	$list['upfiles']=$upfiles;
	unset($upfiles);
} // end if 업로드파일 처리

// URL Link...
$href['list']	= "{$thisUrl}/list.php?" . href_qs("uid=",$qs_basic);
$href['write']	= "{$thisUrl}/write.php?" . href_qs("mode=write&time=".time(),$qs_basic);
$href['reply']	= "{$thisUrl}/write.php?" . href_qs("mode=reply&uid={$list['uid']}&num={$list['num']}&time=".time(),$qs_basic);
$href['modify']	= "{$thisUrl}/write.php?" . href_qs("mode=modify&uid={$list['uid']}&num={$list['num']}&time=".time(),$qs_basic);
$href['delete']	= "{$thisUrl}/ok.php?" . href_qs("mode=delete&uid={$list['uid']}",$qs_basic);

//=================================
// 해당 게시물의 카테고리 정보 구함
//=================================
if(isset($dbinfo['enable_cate']) && $dbinfo['enable_cate'] == 'Y'){
	$table_cate	= (isset($dbinfo['enable_type']) && $dbinfo['enable_type'] == 'Y') ? $table : $table . "_cate";

	// 카테고리정보구함 (dbinfo, table_cate, cateuid, $enable_catelist='Y', sw_topcatetitles, sw_notitems, sw_itemcount,string_firsttotal)
	// highcate[], samecate[], subcate[], subsubcate[], subcateuid[], catelist
	$tmp_itemcount = isset($sc_string) && trim($sc_string) ? 0 : 1;
	$cateinfo=boardCateInfo($dbinfo, $table_cate, $list['cateuid'], 'N', 1,1,$tmp_itemcount,"(종합)");

	if(!isset($list['cateuid'])){
		$cateinfo['uid']		= "{$_SERVER['PHP_SELF']}?" . href_qs("",$qs_basic);
		$cateinfo['title']	= "전체";
	}
} // end if
//=====
// misc
//=====
// 조회수 증가
if (isset($_GET['uid']) && isset($_SERVER['REMOTE_ADDR']) && (!isset($list['bid']) || (isset($list['bid']) && $list['bid'] !== $_SESSION['seUid']))){
	$uid = db_escape($_GET['uid']);
	$remote_addr = db_escape($_SERVER['REMOTE_ADDR']);
	$sql = "UPDATE LOW_PRIORITY {$table} SET hit=hit +1, hitip='{$remote_addr}' WHERE uid='{$uid}' and hitip<>'{$remote_addr}' LIMIT 1";
	db_query($sql);
}

// 유저별 읽은 유무 로그화(boardreadlog 테이블에)
if(isset($dbinfo['enable_readlog']) && $dbinfo['enable_readlog'] == 'Y' && isset($table_readlog)){
	if(isset($list['bid']) && isset($_SESSION['seUid']) && $list['bid'] == $_SESSION['seUid']){
		// 글쓴이라면, 로그 안남기고, 본문에데가 읽은 사람 리스트화함
		$sql = "select * from {$table_readlog} where db='{$table}' and db_uid='{$list['uid']}'";
		$rs_readlog=db_query($sql);
		if(db_count($rs_readlog)){
			$tmp_readlog	= "<br><br><br><font size=2><b><> 읽은 사람 리스트</b><br>";
			while($rows=db_array($rs_readlog)){
				$tmp_readlog.=	date("Y-m-d [H:i]",$rows['rdate']) . "- {$rows['userid']}<br>\n";
			} // end while
			$list['content'] .= $tmp_readlog	. "</font>";
		}
		db_free($rs_readlog);
	} else {
		$sql = "update {$table_readlog} set rdate=UNIX_TIMESTAMP(), ip='{$_SERVER['REMOTE_ADDR']}' where db='{$table}' and db_uid='{$list['uid']}' and bid='{$_SESSION['seUid']}'";
		db_query($sql);
		// db_count()의 반환값을 사용하여 업데이트된 행 수를 확인
		if(db_count() == 0){
			$sql = "insert into {$table_readlog} set db='{$table}', db_uid='{$list['uid']}', bid='{$_SESSION['seUid']}', userid='{$_SESSION['seUserid']}',ip='{$_SERVER['REMOTE_ADDR']}', rdate=UNIX_TIMESTAMP()";
			db_query($sql);
		}
	} // end if. . else..
} // end if

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
if(isset($dbinfo['enable_readlist']) && $dbinfo['enable_readlist'] == 'Y' and (isset($dbinfo['row_pern']) && $dbinfo['row_pern']<2) ){
	$sql = "SELECT * FROM {$table} WHERE num='{$list['num']}' and $sql_where ORDER BY re";
	$re_readlist	= db_query($sql);
	while($readlist=db_array($re_readlist)){
		if(isset($list['uid']) && $readlist['uid'] == $list['uid']) $readlist['no']	= "<font color=blue>▶</font>";
		else $readlist['no']	= "";
		$readlist['rede']	= strlen($readlist['re']);
		$readlist['rdate']= isset($readlist['rdate']) ? date("Y/m/d", $readlist['rdate']) : "";	//	날짜 변환
		if(!isset($readlist['title']) || !$readlist['title']) $readlist['title'] = "제목없음…";

		//답변이 있을 경우 자리는 길이를 더 줄임
		$cut_length = isset($readlist['rede']) && $readlist['rede'] ? $dbinfo['cut_length'] - $readlist['rede'] -3 : $dbinfo['cut_length'];
		$readlist['cut_title'] = cut_string($readlist['title'], $cut_length);

		//	Search 단어 색깔 표시
		if(isset($sc_string) && $sc_string){
			if(isset($sc_column) && $sc_column){
				if($sc_column == "title")
					// eregi_replace() 함수는 PHP 7에서 제거되었으므로 preg_replace()로 변경
					$readlist['cut_title'] = preg_replace("/({$sc_string})/i", "<font color=darkred>\\0</font>",	$readlist['cut_title']);
				else
					// eregi_replace() 함수는 PHP 7에서 제거되었으므로 preg_replace()로 변경
					$readlist[$sc_column]	= preg_replace("/({$sc_string})/i", "<font color='darkred'>\\0</font>", $readlist[$sc_column]);
			} else {
				// eregi_replace() 함수는 PHP 7에서 제거되었으므로 preg_replace()로 변경
				$readlist['userid']	= preg_replace("/({$sc_string})/i", "<font color=darkred>\\0</font>", $readlist['userid']);
				$readlist['cut_title']= preg_replace("/({$sc_string})/i", "<font color=darkred>\\0</font>",	$readlist['cut_title']);
			}
		}

		// 메모개수 구해서 제목 옆에 붙임
		if(isset($dbinfo['enable_memo']) && $dbinfo['enable_memo'] == 'Y'){
			// 메모 테이블 구함
			if(isset($dbinfo['enable_type']) && $dbinfo['enable_type'] == "Y"){
				$table_memo		=$table;
				$sql_where_memo	=" type='memo' ";
			} else {
				$table_memo		=$table	. "_memo";
				$sql_where_memo	= " 1 ";
			} // end if

			$sql="select count(*) as count from {$table_memo} where {$sql_where_memo} and num='{$readlist['uid']}'";
			$count_memo=db_resultone($sql,0,"count");
			if($count_memo){
				$sql = "select count(*) as count from {$table_memo} where {$sql_where_memo} and num='{$readlist['uid']}' and rdate > unix_timestamp()-86400";
				$count_memo_24h=db_resultone($sql,0,"count");
				if($count_memo_24h) $readlist['cut_title'] .= " [{$count_memo}+]";
				else $readlist['cut_title'] .= " [{$count_memo}]";
			}
		} // end if

		//	답변 게시물 답변 아이콘 표시
		if(isset($readlist['rede']) && $readlist['rede'] > 0){
			//$readlist['cut_title'] = str_repeat("&nbsp;", $count_redespace*($readlist['rede']-1)) . "<img src=\"images/re.gif\" align='absmiddle' border=0> $readlist['cut_title']";
			$readlist['cut_title'] = "<img src='/scommon/spacer.gif' width='" . (($readlist['rede']-1)*8) . "' border=0><img src='/scommon/re.gif' align='absmiddle' border=0> {$readlist['cut_title']}";
		}

		// 업로드파일 처리
		if(isset($dbinfo['enable_upload']) && $dbinfo['enable_upload'] != 'N' and isset($readlist['upfiles']) && $readlist['upfiles']){
			$upfiles=unserialize($readlist['upfiles']);
			if(!is_array($upfiles)) { // 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
				$upfiles['upfile']['name']=$readlist['upfiles'];
				$upfiles['upfile']['size']=(int)$readlist['upfiles_totalsize'];
			}
			foreach($upfiles as $key =>  $value){
				if(isset($value['name']) && $value['name'])
					$upfiles[$key]['href']="{$thisUrl}/download.php?" . href_qs("uid={$readlist['uid']}&upfile={$key}",$qs_basic);
			} // end foreach
			$readlist['upfiles']=$upfiles;
			unset($upfiles);
		} // end if 업로드파일 처리

			// switch 문에서 $db 변수 사용 시 isset 체크
			if (isset($db)) {
				switch($db){
					case 'photo':
						$list['sub_title'] = "<a href='/d06_data/index.php' class='white'>세이버스 룸</a> >>";
						$list['m_title'] = "<a href='/d06_data/index.php' class='white'>포토갤러리</a>";
						break;
					case 'movie':
						$list['sub_title'] = "<a href='/d06_data/index.php' class='white'>세이버스 룸</a> >>";
						$list['m_title'] = "<a href='/d06_data/movie.php' class='white'>동영상갤러리</a>";
						break;
					case 'free_board':
						$list['sub_title'] = "커뮤니티</a> >>";
						$list['m_title'] = "<a href='/d05_supporters/index.php' class='white'>자유게시판</a>";
						break;
					case 'kbplayer':
						$list['sub_title'] = "커뮤니티</a> >>";
						$list['m_title'] = "<a href='/d05_supporters/kbplayer.php' class='white'>KB선수단 게시판</a>";
						break;
					case 'event_board':
						$list['sub_title'] = "커뮤니티</a> >>";
						$list['m_title'] = "<a href='/d05_supporters/event.php' class='white'>이벤트</a>";
						break;
					case 'news':
						$list['sub_title'] = "NEWS</a> >>";
						$list['m_title'] = "<a href='/d04_news/index.php' class='white'>SAVERS NOTICE</a>";
						break;
					case 'briefing':
						$list['sub_title'] = "NEWS</a> >>";
						$list['m_title'] = "<a href='/d04_news/news.php' class='white'>MEDIA CENTER</a>";
					break;
					case 'hot_focus':
						$list['sub_title'] = "NEWS</a> >>";
						$list['m_title'] = "<a href='/d04_news/hotfocus.php' class='white'>HOT FOCUS</a>";
					break;
				} // end switch
			}


		// URL Link...
		$href['read']		= "{$thisUrl}/read.php?" . href_qs("uid={$readlist['uid']}",$qs_basic);
		$href['download']	= "{$thisUrl}/download.php?db={$dbinfo['db']}&uid={$readlist['uid']}";

		// 템플릿 YESRESULT 값들 입력
		$tpl->set_var('href.read'		,$href['read']);
		$tpl->set_var('href.download'	,$href['download']);
		$tpl->set_var('readlist'		,$readlist);
		if (isset($count['lastnum'])) {
			$tpl->set_var('count.lastnum'	,$count['lastnum']--);
		}
		$tpl->process('READLIST','readlist',TPL_APPEND);
	} // end while
} // end if
//==================================== //
//===============
// 메모 부분 처리
//===============
if(isset($dbinfo['enable_memo']) && $dbinfo['enable_memo'] == 'Y' && isset($table_memo) && isset($list['uid'])){
	$sql_where_memo="";
	// 메모 테이블 구함
	if(isset($dbinfo['table_name']) && isset($dbinfo['db']) && $dbinfo['table_name'] != $dbinfo['db']) $sql_where_memo=" db='{$dbinfo['db']}' and "; // $sql_where 사용 시작
	if(isset($dbinfo['enable_type']) && $dbinfo['enable_type'] == "Y"){
		$table_memo		= $table;
		$sql_where_memo.= " type='memo' ";
	} else {
		$table_memo		= $table	. "_memo";
		$sql_where_memo.= " 1 ";
	} // end if

	// 메모 DB 읽어드림
	$sql = "select * from {$table_memo} where {$sql_where_memo} and num='{$list['uid']}' order by rdate";
	$rs_memolist=db_query($sql);
	if(!db_count($rs_memolist)) // 메모된 DB가 없다면
		$tpl->process('MEMOLIST','nomemolist');
	else {
		while($memolist=db_array($rs_memolist)) { // 있다면
			$memolist['rdate']=date("Y-m-d",$memolist['rdate']);

			// URL Link...
			$href['memodelete']="{$thisUrl}/ok.php?" . href_qs("mode=memodelete&memouid={$memolist['uid']}&uid={$list['uid']}",$qs_basic);
			$tpl->set_var("memolist",$memolist);
			$tpl->set_var("href.memodelete",$href['memodelete']);
			$tpl->process("MEMOLIST",'memolist',TPL_APPEND);
		} // end while
	} // end if

	$form_memo=" action='{$thisUrl}/ok.php' method='post'	ENCTYPE='multipart/form-data'>";
	$form_memo .= substr(href_qs("mode=memowrite&uid={$list['uid']}",$qs_basic,1),0,-1);

	$memouserid = '';
	if (isset($dbinfo['enable_userid'])) {
		switch($dbinfo['enable_userid']){
			case 'name'		: $memouserid = isset($_SESSION['seName']) ? $_SESSION['seName'] : ''; break;
			case 'nickname'	: $memouserid = isset($_SESSION['seNickname']) ? $_SESSION['seNickname'] : ''; break;
			default			: $memouserid= isset($_SESSION['seUserid']) ? $_SESSION['seUserid'] : ''; break;
		}
	}

	// 템플릿 할당
	$tpl->set_var('form_memo',$form_memo);
	$tpl->set_var('memouserid',$memouserid);
	$tpl->process("MEMO","memo",TPL_APPEND);
} // end 메모 부분 처리
//===============//

//=========
// VOTE 부분
//=========
	$form_vote	=" action='{$thisUrl}/ok.php' method='post'	ENCTYPE='multipart/form-data'>";
	if(isset($list['uid'])) {
		$form_vote.= substr(href_qs("mode=vote&uid={$list['uid']}",$qs_basic,1),0,-1);
	}

	$tpl->set_var('form_vote',$form_vote);
//=========//
// 템플릿 마무리 할당
$tpl->set_var('dbinfo'			,$dbinfo);// shopinfo 정보 변수
$tpl->set_var('cateinfo.uid'	,isset($cateinfo['uid']) ? $cateinfo['uid'] : '');
$tpl->set_var('cateinfo.title'	,isset($cateinfo['title']) ? $cateinfo['title'] : '');
$tpl->set_var('href'			,$href);
$tpl->set_var('list'			,$list);

// 블럭 : 카테고리(상위, 동일, 서브) 생성
if(isset($dbinfo['enable_cate']) && $dbinfo['enable_cate'] == 'Y'){
	if(isset($cateinfo['catelist']) && $cateinfo['catelist']){
		$tpl->set_var('cateinfo.catelist',$cateinfo['catelist']);
		$tpl->process('CATELIST','catelist',TPL_APPEND);
	}

	$i = 0;
	if($i == 0) $tpl->drop_var('blockloop');
	else $tpl->set_var('blockloop',true);
	$tpl->process('LIST','list',TPL_OPTIONAL|TPL_APPEND);

	if(isset($cateinfo['highcate']) && is_array($cateinfo['highcate'])){
		foreach($cateinfo['highcate'] as $key =>  $value){
			$tpl->set_var('href.highcate',"{$_SERVER['PHP_SELF']}?" . href_qs("cateuid=".$key,$qs_basic));
			$tpl->set_var('highcate.uid',$key);
			$tpl->set_var('highcate.title',$value);
			$tpl->process('HIGHCATE','highcate',TPL_OPTIONAL|TPL_APPEND);
			$tpl->set_var('blockloop',true);
		}
		$tpl->drop_var('blockloop');
	} // end if
	if(isset($cateinfo['samecate']) && is_array($cateinfo['samecate'])){
		foreach($cateinfo['samecate'] as $key =>  $value){
			$tpl->set_var('href.samecate',"{$_SERVER['PHP_SELF']}?" . href_qs("cateuid=".$key,$qs_basic));
			$tpl->set_var('samecate.uid',$key);
			$tpl->set_var('samecate.title',$value);
			$tpl->process('SAMECATE','samecate',TPL_OPTIONAL|TPL_APPEND);
			$tpl->set_var('blockloop',true);
		}
		$tpl->drop_var('blockloop');
	} // end if
	if(isset($cateinfo['subcate']) && is_array($cateinfo['subcate'])){
		foreach($cateinfo['subcate'] as $key =>  $value){
			$tpl->set_var('href.subcate',"{$_SERVER['PHP_SELF']}?" . href_qs("cateuid=".$key,$qs_basic));
			$tpl->set_var('subcate.uid',$key);
			$tpl->set_var('subcate.title',$value);
			$tpl->process('SUBCATE','subcate',TPL_OPTIONAL|TPL_APPEND);
			$tpl->set_var('blockloop',true);
		}
		$tpl->drop_var('blockloop');
	} // end if
} // end if

// 블럭 : 업로드파일 처리
if( (isset($dbinfo['enable_upload']) && ($dbinfo['enable_upload'] == 'Y' or $dbinfo['enable_upload'] == 'multi')) and isset($list['upfiles']) and is_array($list['upfiles']) and sizeof($list['upfiles']) ){
	foreach($list['upfiles'] as $key =>  $value){
		if(isset($value) && $value) { // 파일 이름이 있다면
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
if(privAuth($dbinfo, "priv_delete") ){
	$tpl->process('MODIFY','modify');
	$tpl->process('DELETE','delete');
}
// 마무리
$val="\\1{$thisUrl}/stpl/{$dbinfo['skin']}/images/";
switch(isset($dbinfo['html_headpattern']) ? $dbinfo['html_headpattern'] : ""){
	case "ht":
		// 전체 홈페이지 템플릿 읽어오기
		$HEADER['header'] == 2;
		if( isset($dbinfo['html_headtpl']) && $dbinfo['html_headtpl'] != "" and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php") )
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php");
		else
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");

		echo (isset($SITE['head']) ? $SITE['head'] : "") . (isset($dbinfo['html_head']) ? $dbinfo['html_head'] : "");
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		echo (isset($dbinfo['html_tail']) ? $dbinfo['html_tail'] : "") . (isset($SITE['tail']) ? $SITE['tail'] : "");
		break;
	case "h":
		// 전체 홈페이지 템플릿 읽어오기
		$HEADER['header'] == 2;
		if( isset($dbinfo['html_headtpl']) && $dbinfo['html_headtpl'] != "" and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php") )
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php");
		else
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");

		echo (isset($SITE['head']) ? $SITE['head'] : "") . (isset($dbinfo['html_head']) ? $dbinfo['html_head'] : "");
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		echo (isset($dbinfo['html_tail']) ? $dbinfo['html_tail'] : "");
		break;
	case "t":
		// 전체 홈페이지 템플릿 읽어오기
		$HEADER['header'] == 2;
		if( isset($dbinfo['html_headtpl']) && $dbinfo['html_headtpl'] != "" and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php") )
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php");
		else
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");

		echo (isset($dbinfo['html_head']) ? $dbinfo['html_head'] : "");
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		echo (isset($dbinfo['html_tail']) ? $dbinfo['html_tail'] : "") . (isset($SITE['tail']) ? $SITE['tail'] : "");
		break;
	case "no":
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		break;
	default:
		echo (isset($dbinfo['html_head']) ? $dbinfo['html_head'] : "");
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		echo (isset($dbinfo['html_tail']) ? $dbinfo['html_tail'] : "");
} // end switch
?>
