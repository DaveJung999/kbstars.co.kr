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
//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
$thisPath	= dirname(__FILE__);
$thisUrl	= "/sthis/sthis_player"; // 마지막 "/"이 빠져야함

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
if(!isset($sql_where)) $sql_where= " 1 ";

// 한 table에 여러 게시판 생성의 경우
if($dbinfo['table_name'] != $dbinfo['db']) $sql_where .= " and db='{$dbinfo['db']}' "; // $sql_where 사용 시작
if($dbinfo['enable_type'] == 'Y') $sql_where .= " and (type='docu' or type='info') ";

// 해당 카테고리만 볼려면
if(isset($cateinfo['subcate_uid']) && is_array($cateinfo['subcate_uid']) and sizeof($cateinfo['subcate_uid'])>0 ) $sql_where = isset($sql_where) ? $sql_where	. " and ( cateuid in ( " . implode(",",$cateinfo['subcate_uid']) . ") ) " : " ( cateuid in ( " . implode(",",$cateinfo['subcate_uid']) . ") ) ";
if(isset($_GET['team'])) $team = $_GET['team'];
if(isset($team)) $sql_where= "	tid = {$team} ";
if(!isset($sql_where)) $sql_where= "	tid = {$team} ";

$sql_orderby = isset($dbinfo['orderby']) ? $dbinfo['orderby'] : "	num DESC, re ";
//===================
// SQL문 where절 정리
//===================
if(!isset($sql_where)) $sql_where= " 1 ";

// 한 table에 여러 게시판 생성의 경우
if($dbinfo['table_name'] != $dbinfo['db']) $sql_where .= " and db='{$dbinfo['db']}' "; // $sql_where 사용 시작
if($dbinfo['enable_type'] == 'Y') $sql_where .= " and (type='docu' or type='info') ";

if(!privAuth($dbinfo, "priv_list",1)) back("이용이 제한되었습니다.(레벨부족)");

//=================
// 해당 게시물 읽음
//=================
$sql = "SELECT * FROM {$table} WHERE uid='{$_GET['uid']}' and  $sql_where ";
if(!$list=db_arrayone($sql)) back("게시물이 존재하지 않습니다.");

// 인증 체크(자기 글이면 무조건 보기)
if(!privAuth($dbinfo, "priv_read",1)){
	if(isset($list['bid'])){
		if($list['bid']<>$_SESSION['seUid']){
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
$list['p_trade'] = replace_string($list['p_trade'], $list['docu_type']);	// 문서 형식에 맞추어서 내용 변경



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
			$filename= (isset($dbinfo['upload_dir']) ? $dbinfo['upload_dir'] : "") . "/{$list['bid']}/" . $value['name'];
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
										<param name=movie value=\"{$upfiles[$key]['href']}\"> 
										<param name=quality value=high></object><br>" . $list['content'];
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

$href["list"]	= "{$thisUrl}/list.php?" . href_qs("uid=",$qs_basic);
$href["write"]	= "{$thisUrl}/write.php?" . href_qs("mode=write&time=".time(),$qs_basic);
$href["reply"]	= "{$thisUrl}/write.php?" . href_qs("mode=reply&uid={$list['uid']}&num={$list['num']}&time=".time(),$qs_basic);
$href["modify"]	= "{$thisUrl}/write.php?" . href_qs("mode=modify&uid={$list['uid']}&num={$list['num']}&time=".time(),$qs_basic);
$href["delete"]	= "{$thisUrl}/ok.php?" . href_qs("mode=delete&uid={$list['uid']}",$qs_basic);

//=================================
// 해당 게시물의 카테고리 정보 구함
//=================================
if(isset($dbinfo['enable_cate']) && $dbinfo['enable_cate'] == 'Y'){
	$table_cate	= (isset($dbinfo['enable_type']) && $dbinfo['enable_type'] == 'Y') ? $table : $table	. "_cate";

	// 카테고리정보구함 (dbinfo, table_cate, cateuid, $enable_catelist='Y', sw_topcatetitles, sw_notitems, sw_itemcount,string_firsttotal)
	// highcate[], samecate[], subcate[], subsubcate[], subcateuid[], catelist
	$tmp_itemcount = isset($sc_string) && trim($sc_string) ? 0 : 1;
	$cateinfo=boardCateInfo($dbinfo, $table_cate, $list['cateuid'], 'N', 1,1,$tmp_itemcount,"(종합)");

	if(!isset($list['cateuid'])){
		$cateinfo['uid']		= "{$_SERVER['PHP_SELF']}?" . href_qs("",$qs_basic);
		$cateinfo['title']	= "전체";
	}
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
$sql = "SELECT * FROM {$table} WHERE $sql_where ORDER BY {$sql_orderby} ";
$re_readlist	= db_query($sql);

if(!$total=db_count($re_readlist)) {	// 게시물이 하나도 없다면...
	if(isset($_GET['sc_string']) && $_GET['sc_string']) { // 서치시 게시물이 없다면..
		$tpl->set_var('sc_string',htmlspecialchars(stripslashes($_GET['sc_string']),ENT_QUOTES));
		$tpl->process('READLIST', 'nosearch');
	}
	else // 게시물이 없다면. .
		$tpl->process('READLIST', 'nolist');
}
else{
	if(!isset($dbinfo['row_pern']) || $dbinfo['row_pern']<1) $dbinfo['row_pern']=1; // 한줄에 여러값 출력이 아닌 경우
	for($i=0; $i<$total; $i+=$dbinfo['row_pern']){
		if($dbinfo['row_pern'] >= 1) $tpl->set_var('CELL',"");

		for($j=$i; ($j-$i < $dbinfo['row_pern']) && ($j < $total); $j++) { // 한줄에 여러값 출력시 루틴
			if( $j>=$total ){
				if($dbinfo['row_pern'] > 1) $tpl->process('CELL','nocell',TPL_APPEND);
				continue;
			}
			$readlist		= db_array($re_readlist);
			$readlist['no']	= $count['lastnum'];
			$readlist['rede']	= strlen($readlist['re']);

			// new image넣을 수 있게 <opt name="enable_new">..
			if($readlist['rdate']>time()-3600*24) $readlist['enable_new']="<img src='/images/icon_new.gif' width='30' height='15' border='0'>";

			// 업로드파일 처리
			if(isset($dbinfo['enable_upload']) && $dbinfo['enable_upload'] != 'N' and isset($readlist['upfiles']) && $readlist['upfiles']){
				$upfiles=unserialize($readlist['upfiles']);
				if(!is_array($upfiles)) {
					// 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
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

			// URL Link...
			$href['download']	= "{$thisUrl}/download.php?db={$dbinfo['db']}&uid={$readlist['uid']}";
			$href['read']		= "{$thisUrl}/read.php?" . href_qs("uid={$readlist['uid']}",$qs_basic);
			$href['go']	= "{$thisUrl}/write.php?" . href_qs("mode=modify&uid={$readlist['uid']}&num={$readlist['num']}&time=".time(),$qs_basic);


			// 템플릿 YESRESULT 값들 입력
			if(isset($_GET['uid']) && $readlist['uid'] == $_GET['uid']) $readlist['color'] = "#FCC99B";
			else $readlist['color'] = "#FFFFFF";

			if (isset($readlist['p_num']) && $readlist['p_num'] != "")	$readlist['numimages'] = "<img src='images/savers_team_num".$readlist['p_num'].".gif'>";
			else	$readlist['numimages'] = "";

			$tpl->set_var('href.go'		, $href['go']);
			$tpl->set_var('href.read'		, $href['read']);
			$tpl->set_var('href.download'	, $href['download']);
			$tpl->set_var('readlist'			, $readlist);

			if(privAuth($dbinfo, "priv_write")) $tpl->process('GO','go');
			else $tpl->process('NOGO','nogo');
			$count['lastnum']--;

			if($dbinfo['row_pern'] >= 1){
				if($j == 0) $tpl->drop_var('blockloop');
				else $tpl->set_var('blockloop',true);
				$tpl->process('CELL','cell',TPL_APPEND);
			}
		} // end for (j)

		$tpl->process('READLIST','readlist',TPL_OPTIONAL|TPL_APPEND);
		$tpl->set_var('blockloop',true);
	} // end for (i)
	$tpl->drop_var('blockloop');
	if (isset($href['read'])) {
		$tpl->drop_var('href.read');
		unset($href['read']);
	}
} // end if (게시물이 있다면...)
/*
while($readlist=db_array($re_readlist)){
	
	if($readlist['rdate']>time()-3600*24) $readlist['enable_new']="<img src='/images/icon_new.gif' width='30' height='15' border='0'>";
	
	// 업로드파일 처리
	if($dbinfo['enable_upload'] != 'N' and $readlist['upfiles']){
		$upfiles=unserialize($readlist['upfiles']);
		if(!is_array($upfiles)) { // 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
			$upfiles['upfile']['name']=$readlist['upfiles'];
			$upfiles['upfile']['size']=(int)$readlist['upfiles_totalsize'];
		}
		foreach($upfiles as $key =>  $value){
			if($value['name'])
				$upfiles[$key]['href']="{$thisUrl}/download.php?" . href_qs("uid={$readlist['uid']}&upfile={$key}",$qs_basic);
		} // end foreach
		$readlist['upfiles']=$upfiles;
		unset($upfiles);
	} // end if 업로드파일 처리

	// URL Link...
	$href['read']		= "{$thisUrl}/read.php?" . href_qs("uid={$readlist['uid']}",$qs_basic);
	$href['download']	= "{$thisUrl}/download.php?db={$db}info[db]&uid={$readlist['uid']}";
	

	// 템플릿 YESRESULT 값들 입력
	$tpl->set_var('href.read'		,$href['read']);
	$tpl->set_var('href.download'	,$href['download']);
	$tpl->set_var('readlist'		,$readlist);
	$tpl->process('READLIST','readlist',TPL_APPEND);
} // end while
*/

// 템플릿 마무리 할당
$uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : '';
$href['download']	= "{$thisUrl}/download.php?db={$dbinfo['db']}&uid={$uid}";

if (isset($list['p_num']) && $list['p_num'] != "")	$list['numimages'] = "<img src='images/savers_team_num".$list['p_num'].".gif'>";
			else	$list['numimages'] = "";
	//선수 관리 이력 반짝 반짝 효과 : 2005/06/10 안형진
	$list['monitoring'] = "<img src=/images/player_icon1.gif width=111 height=30 border=0>";
	$list['medical'] = "<img src=/images/player_icon2.gif width=111 height=30 border=0>";
	$list['education'] = "<img src=/images/player_icon3.gif width=111 height=30 border=0>";
	$list['pain'] = "<img src=/images/player_icon4.gif width=111 height=30 border=0>";
	$list['house'] = "<img src=/images/player_icon5.gif width=122 height=30 border=0>";
	$list['player'] = "<img src=/images/player_icon6.gif width=120 height=30 border=0>";

	$now = date("ymd", mktime());
	$sql1 = "select rdate from new21_board2_monitoring where puid={$list['uid']} limit 0, 1";
	$sql2 = "select rdate from new21_board2_medical where puid={$list['uid']} limit 0, 1";
	$sql3 = "select rdate from new21_board2_education where puid={$list['uid']} limit 0, 1";
	$sql4 = "select rdate from new21_board2_pain where puid={$list['uid']} limit 0, 1";
	$sql5 = "select rdate from new21_board2_house where puid={$list['uid']} limit 0, 1";

	$rs1 = db_query($sql1);
	$db_date = db_array($rs1);
	if($db_date)	{
		$db_date['rdate'] = date("ymd", $db_date['rdate']);
		if( ($now - $db_date['rdate']) <= 3)
			$list['monitoring'] = "<img src=/images/player_icon1_1.gif width=111 height=30 border=0>";
	}

	$rs2 = db_query($sql2);
	$db_date2 = db_array($rs2);
	if($db_date2)	{
		$db_date2['rdate'] = date("ymd", $db_date2['rdate']);
		if( ($now - $db_date2['rdate']) <= 3)
			$list['medical'] = "<img src=/images/player_icon2_1.gif width=111 height=30 border=0>";
	}

	$rs3 = db_query($sql3);
	$db_date3 = db_array($rs3);
	if($db_date3)	{
		$db_date3['rdate'] = date("ymd", $db_date3['rdate']);
		if( ($now - $db_date3['rdate']) <= 3)
			$list['education'] = "<img src=/images/player_icon3_1.gif width=111 height=30 border=0>";
	}

	$rs4 = db_query($sql4);
	$db_date4 = db_array($rs4);
	if($db_date4)	{
		$db_date4['rdate'] = date("ymd", $db_date4['rdate']);
		if( ($now - $db_date4['rdate']) <= 3)
			$list['pain'] = "<img src=/images/player_icon4_1.gif width=111 height=30 border=0>";
	}

	$rs5 = db_query($sql5);
	$db_date5 = db_array($rs5);
	if($db_date5)	{
		$db_date5['rdate'] = date("ymd", $db_date5['rdate']);
		if( ($now - $db_date5['rdate']) <= 3)
			$list['house'] = "<img src=/images/player_icon5_1.gif width=122 height=30 border=0>";
	}
//선수 이력 아이콘 처리 끝

$tpl->set_var('href.download'	,$href['download']);
$tpl->set_var('dbinfo'			,$dbinfo);// shopinfo 정보 변수
$tpl->set_var('href'			,$href);
$tpl->set_var('list'			,$list);
// 블럭 : 카테고리(상위, 동일, 서브) 생성
if(isset($dbinfo['enable_cate']) && $dbinfo['enable_cate'] == 'Y'){
	if(isset($cateinfo['catelist']) && $cateinfo['catelist']){
		$tpl->set_var('cateinfo.catelist',$cateinfo['catelist']);
		$tpl->process('CATELIST','catelist',TPL_APPEND);
	}

	$i = 0; // for 루프가 아니므로 $i를 초기화
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
if(privAuth($dbinfo, "priv_delete") or (isset($list['bid']) && $list['bid'] == $_SESSION['seUid']) or (isset($list['bid']) && $list['bid'] == 0)){
	$tpl->process('MODIFY','modify');
	$tpl->process('DELETE','delete');
	$tpl->process('CWRITE','cwrite');
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

		echo $SITE['head'] . (isset($dbinfo['html_head']) ? $dbinfo['html_head'] : "");
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		echo (isset($dbinfo['html_tail']) ? $dbinfo['html_tail'] : "") . $SITE['tail'];
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
