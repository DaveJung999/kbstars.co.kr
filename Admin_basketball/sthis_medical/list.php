<?php
//=======================================================
// 설	명 : 게시판 목록보기(list.php)
// 책임자 : 박선민 (sponsor@new21.com), 검수: 04/07/28
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 04/07/28 박선민 마지막 수정
//=======================================================
	$HEADER=array(
		'priv' =>	"운영자,뉴스관리자", // 인증유무 (0:모두에게 허용, 숫자가 높을 수록 레벨업)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'useSkin' =>	1, // 템플릿 사용
		'useBoard2' => 1,
		'useApp' => 1, // cut_string()
		);
	require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $HTTP_HOST);

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
$thisPath		= dirname(__FILE__);
$thisUrl	= "/Admin_basketball/sthis_medical"; // 마지막 "/"이 빠져야함
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
$playerUrl	= "/Admin_basketball/sthis_player"; // 마지막 "/"이 빠져야함

if (!isset($cateuid)) $cateuid = $_GET['cateuid'];

// 기본 URL QueryString
if(isset($_GET['getinfo']) && $_GET['getinfo'] == "cont") $qs_basic = "mode=&limitno=&limitrows=";
else $qs_basic = "mode=&pern=&row_pern&page_pern&limitno=&limitrows=&html_headpattern=&html_headtpl&skin=";
$qs_basic		= href_qs($qs_basic); // 해당값 초기화

// 권한 체크
if(!privAuth($dbinfo, "priv_list",1)) back("이용이 제한되었습니다.(레벨부족)");

// 넘어온 값에 따라 $dbinfo값 변경
if($dbinfo['enable_getinfo'] == 'Y'){
	if(isset($_GET['pern']))			$dbinfo['pern']		= (int)$_GET['pern'];
	if(isset($_GET['row_pern']))		$dbinfo['row_pern']	= (int)$_GET['row_pern'];
	if(isset($_GET['cut_length']))	$dbinfo['cut_length']	= (int)$_GET['cut_length'];

	// skin 변경
	if( isset($_GET['skin']) and preg_match("/^[_a-z0-9]+$/",$_GET['skin'])
		and is_file("{$thisPath}/stpl/{$_GET['skin']}/list.htm") )
		$dbinfo['skin']	= $_GET['skin'];
	// 사이트 해더테일 변경
	if(isset($_GET['html_headpattern']))	$dbinfo['html_headpattern'] = $_GET['html_headpattern'];
	if( isset($_GET['html_headtpl']) and preg_match("/^[_a-z0-9]+$/",$_GET['html_headtpl'])
		and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$_GET['html_headtpl']}.php") )
		$dbinfo['html_headtpl'] = $_GET['html_headtpl'];
}

//===================
// SQL문 where절 정리
//===================

if(!isset($_GET['p_uid'])){
	back("고유번호가 없습니다.");
} else {
	if(isset($sql_where)) $sql_where .= ' and ';
	
	$sql_where .="puid = ".$_GET['p_uid'];
}

// 서치 게시물만..
if(trim($_GET['sc_string'])){
	if(isset($sql_where)) $sql_where .= ' and ';
	if(isset($_GET['sc_column']))
		if(in_array($_GET['sc_column'],array('bid','uid')))
			$sql_where .=" (".$_GET['sc_column']."='".$_GET['sc_string']."') ";
		else
			$sql_where .=" (".$_GET['sc_column']." like '%".$_GET['sc_string']."%') ";
	else
		$sql_where .=" ((userid like '%".$_GET['sc_string']."%') or (title like '%".$_GET['sc_string']."%') or (content like '%".$_GET['sc_string']."%')) ";
}

if(!isset($sql_where)) $sql_where= " 1 ";

//============================
// SQL문 order by..부분 만들기
//============================
switch($_GET['sort']){
	case 'title': $sql_orderby = 'title'; break;
	case '!title':$sql_orderby = 'title DESC'; break;
	case 'rdate': $sql_orderby = 'rdate'; break;
	case '!rdate':$sql_orderby = 'rdate DESC'; break;
	case 'hit' : $sql_orderby = 'hit';	break;
	case '!hit' : $sql_orderby = 'hit DESC'; break;
	case 'vote' : $sql_orderby = 'vote'; break;
	case '!vote' : $sql_orderby = 'vote DESC'; break;
	default :
		$sql_orderby = isset($dbinfo['orderby']) ? $dbinfo['orderby'] : ' num DESC, re ';
}

//=====
// misc
//=====
// 페이지 나눔등 각종 카운트 구하기
$count['total']=db_resultone("SELECT count(*) FROM {$dbinfo['table']} WHERE  $sql_where ", 0, "count(*)"); // 전체 게시물 수
// 게시물 일부만 본다면
if(isset($_GET['limitrows'])) $dbinfo['pern'] = $count['total'];
$count=board2Count($count['total'],$page,$dbinfo['pern'],$dbinfo['page_pern']); // 각종 카운트 구하기
$count['today']=db_resultone("SELECT count(*) FROM {$dbinfo['table']} WHERE (rdate > unix_timestamp(curdate())) and $sql_where " , 0, "count(*)");

// URL Link...
$href['listdb']	= "{$thisUrl}/list.php?db={$table_player}";
$href['list']="{$thisUrl}/list.php?db={$table_player}&cateuid={$cateinfo['uid']}";
$href['write']	= "{$thisUrl}/write.php?" . href_qs("mode=write&time=".time(),$qs_basic);	// 글씨기
$href['reply']	= "{$thisUrl}/write.php?" . href_qs("mode=reply&uid={$list['uid']}&num={$list['num']}&time=".time(),$qs_basic);
$href['modify']	= "{$thisUrl}/write.php?" . href_qs("mode=modify&uid={$list['uid']}&num={$list['num']}&time=".time(),$qs_basic);
$href['delete']	= "{$thisUrl}/ok.php?" . href_qs("mode=delete&uid={$list['uid']}",$qs_basic);

if($count['nowpage'] > 1) { // 처음, 이전 페이지
	$href['firstpage']=$_SERVER['PHP_SELF']."?" . href_qs("page=1",$qs_basic);
	$href['prevpage']	=$_SERVER['PHP_SELF']."?" . href_qs("page=" . ($count['nowpage']-1),$qs_basic);
} else {
	$href['firstpage']="javascript: void(0)";
	$href['prevpage']	="javascript: void(0)";
}
if($count['nowpage'] < $count['totalpage']){ // 다음, 마지막 페이지
	$href['nextpage']	=$_SERVER['PHP_SELF']."?" . href_qs("page=" . ($count['nowpage']+1),$qs_basic);
	$href['lastpage']	=$_SERVER['PHP_SELF']."?" . href_qs("page=".$count['totalpage'],$qs_basic);
} else {
	$href['nextpage']	="javascript: void(0)";
	$href['lastpage'] ="javascript: void(0)";
}
$href['prevblock']= ($count['nowblock']>1)					? $_SERVER['PHP_SELF']."?" . href_qs("page=" . ($count['firstpage']-1) ,$qs_basic): "javascript: void(0)";// 이전 페이지 블럭
$href['nextblock']= ($count['totalpage'] > $count['lastpage'])? $_SERVER['PHP_SELF']."?" . href_qs("page=" . ($count['lastpage'] +1),$qs_basic) : "javascript: void(0)";// 다음 페이지 블럭

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
// 템플릿 기반 웹 페이지 제작
$tpl = new phemplate("","remove_nonjs");
if( !is_file("{$thisPath}/stpl/{$dbinfo['skin']}/list.htm") ) $dbinfo['skin']="board_basic";
$tpl->set_file('html',"{$thisPath}/stpl/{$dbinfo['skin']}/list.htm",TPL_BLOCK);

// Limit로 필요한 게시물만 읽음.
$limitno	= isset($_GET['limitno']) ? $_GET['limitno'] : $count['firstno'];
$limitrows	= isset($_GET['limitrows']) ? $_GET['limitrows'] : $count['pern'];
$sql = "SELECT * FROM {$dbinfo['table']} WHERE $sql_where ORDER BY {$sql_orderby} LIMIT {$limitno},{$limitrows}";
$rs_list = db_query($sql);

if(!$total=db_count($rs_list)) {	// 게시물이 하나도 없다면...
	if(isset($_GET['sc_string'])) { // 서치시 게시물이 없다면..
		$href['list']	= "{$thisUrl}/list.php?" . href_qs("uid=",$qs_basic);
		$href['listdb'] = "list.php?db={$table_player}";
		$tpl->set_var('href.list'			, $href['list']);
		$tpl->set_var('list'			, $list);
		$tpl->set_var('sc_string',htmlspecialchars(stripslashes($_GET['sc_string']),ENT_QUOTES));
		$tpl->process('LIST', 'nosearch');
	}
	else{ // 게시물이 없다면. .
		$href['write']	= "{$thisUrl}/write.php?" . href_qs("mode=write&time=".time(),$qs_basic);
		$tpl->set_var('href.write'		, $href['write']);
		$tpl->process('LIST', 'nolist');
		}
} else {
	if($dbinfo['row_pern']<1 or !$tpl->get_var('cell')) $dbinfo['row_pern']=1; // 한줄에 여러값 출력이 아닌 경우
	for($i=0; $i<$total; $i+=$dbinfo['row_pern']){
		if($dbinfo['row_pern'] > 1){
			$blockloop = $tpl->get_var('blockloop');
			$tpl->drop_var('blockloop');
			$tpl->drop_var('CELL');
		}
		for($j=$i; ($j-$i < $dbinfo['row_pern']) && ($j < $total); $j++) { // 한줄에 여러값 출력시 루틴
			if( $j>=$total ){
				if($dbinfo['row_pern'] > 1) $tpl->process('CELL','nocell',TPL_APPEND);
				continue;
			}
			$list		= db_array($rs_list);
			$list['no']	= $count['lastnum']--;
			$list['rede']	= strlen($list['re']);

			// new image넣을 수 있게 <opt name="enable_new">..
			if($list['rdate']>time()-3600*24) $list['enable_new']="Y";
			else $list['enable_new']="";

			$list['rdate_date']= $list['rdate'] ? date("Y-m-d", $list['rdate']) : "";	//	날짜 변환
			
			$list['go_date']= $list['rdate'] ? date("m-d", $list['rdate']) : "";	//	날짜 변환
			
			$list['icrc_date']= $list['rdate'] ? date("M	. d , Y", $list['rdate']) : "";	//	icrc 날짜 변환
			
			if(!$list['title']) $list['title'] = "제목없음…";

			$list['content'] = replace_string($list['content'], $list['docu_type']);	// 문서 형식에 맞추어서 내용 변경
			$list['data1'] = replace_string($list['data1'], $list['docu_type']);	// 문서 형식에 맞추어서 내용 변경
			$list['data2'] = replace_string($list['data2'], $list['docu_type']);	// 문서 형식에 맞추어서 내용 변경
			$list['data3'] = replace_string($list['data3'], $list['docu_type']);	// 문서 형식에 맞추어서 내용 변경
			$list['data4'] = replace_string($list['data4'], $list['docu_type']);	// 문서 형식에 맞추어서 내용 변경
			$list['data5'] = replace_string($list['data5'], $list['docu_type']);	// 문서 형식에 맞추어서 내용 변경
			$list['data6'] = replace_string($list['data6'], $list['docu_type']);	// 문서 형식에 맞추어서 내용 변경
			$list['data7'] = replace_string($list['data7'], $list['docu_type']);	// 문서 형식에 맞추어서 내용 변경
			$list['data8'] = replace_string($list['data8'], $list['docu_type']);	// 문서 형식에 맞추어서 내용 변경
			$list['data9'] = replace_string($list['data9'], $list['docu_type']);	// 문서 형식에 맞추어서 내용 변경

			//답변이 있을 경우 자리는 길이를 더 줄임
			$cut_length = $list['rede'] ? $dbinfo['cut_length'] - $list['rede'] -3 : $dbinfo['cut_length'];
			$list['cut_title'] = cut_string($list['title'], $cut_length);

			//	Search 단어 색깔 표시
			if(isset($_GET['sc_string'])){
				if(isset($_GET['sc_column'])){
					if($_GET['sc_column'] == "title")
						$list['cut_title'] = preg_replace("/(".$_GET['sc_string'].")/i", "<font color=darkred>\\0</font>",	$list['cut_title']);
					else
						$list[$_GET['sc_column']]	= preg_replace("/(".$_GET['sc_string'].")/i", "<font color='darkred'>\\0</font>", $list[$_GET['sc_column']]);
				} else {
					$list['userid']	= preg_replace("/(".$_GET['sc_string'].")/i", "<font color=darkred>\\0</font>", $list['userid']);
					$list['cut_title']= preg_replace("/(".$_GET['sc_string'].")/i", "<font color=darkred>\\0</font>",	$list['cut_title']);
				}
			}
			// 업로드파일 처리
			if($dbinfo['enable_upload'] != 'N' and $list['upfiles']){
				$upfiles=unserialize($list['upfiles']);
				if(!is_array($upfiles)) {
					// 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
					$upfiles['upfile']['name']=$list['upfiles'];
					$upfiles['upfile']['size']=(int)$list['upfiles_totalsize'];
				}
				foreach($upfiles as $key =>	$value){
					if($value['name'])
						$upfiles[$key]['href']="{$thisUrl}/download.php?" . href_qs("uid={$list['uid']}&upfile={$key}",$qs_basic);
				} // end foreach
				$list['upfiles']=$upfiles;
				unset($upfiles);
			} // end if 업로드파일 처리
			
			// URL Link...
			$href['download']	= "{$thisUrl}/download.php?db={$table_player}&uid={$list['uid']}";
			$href['read']		= "{$thisUrl}/read.php?" . href_qs("uid={$list['uid']}",$qs_basic) . "&".href_qs("db={$table_player}",$qs_basic);
			$href['modify']	= "{$thisUrl}/write.php?" . href_qs("mode=modify&uid={$list['uid']}&num={$list['num']}&time=".time(),$qs_basic);
			$href['delete']	= "{$thisUrl}/ok.php?" . href_qs("mode=delete&uid={$list['uid']}",$qs_basic);
			$href['write']	= "{$thisUrl}/write.php?" . href_qs("mode=write&time=".time(),$qs_basic);
			
			// 템플릿 YESRESULT 값들 입력
			$tpl->set_var('href.read'		, $href['read']);
			$tpl->set_var('href.write'		, $href['write']);
			$tpl->set_var('href.modify'		, $href['modify']);
			$tpl->set_var('href.delete'		, $href['delete']);
			$tpl->set_var('href.download'	, $href['download']);
			$tpl->set_var('list'			, $list);

			if($dbinfo['row_pern'] > 1){
				if($j == 0) $tpl->drop_var('blockloop');
				else $tpl->set_var('blockloop',true);
				$tpl->process('CELL','cell',TPL_OPTIONAL|TPL_APPEND);
			}
		} // end for (j)
		if($dbinfo['row_pern'] > 1){
			$tpl->set_var('blockloop',$blockloop);
		}
		$tpl->process('LIST','list',TPL_OPTIONAL|TPL_APPEND);
		$tpl->set_var('blockloop',true);

		// 업로드부분 템플릿내장값 지우기
		if(is_array($list['upfiles'])){
			foreach($list['upfiles'] as $key =>	$value){
				if(is_array($list['upfiles'][$key])){
					foreach($list['upfiles'][$key] as $key2 =>	$value){
						$tpl->drop_var("list.upfiles.{$key}.{$key2}");
					}
				}
				else $tpl->drop_var("list.upfiles.{$key}"); // 이럴일 없겠지만..
			}
		}
	} // end for (i)
	//	템플릿내장값 지우기
	$tpl->drop_var('blockloop');
	$tpl->drop_var('href.read'); unset($href['read']);
	$tpl->drop_var('href.download'); unset($href['download']);
	
	if(is_array($list)){
		foreach($list as $key =>	$value){
			if(is_array($list[$key])){
				foreach($list as $key2 =>	$value){
					$tpl->drop_var("list.{$key}.{$key2}");
				}
			}
			else $tpl->drop_var("list.{$key}");
		}
		unset($list);
	}
} // end if (게시물이 있다면...)
//선수정보.........................
$sql_where_player = "cateuid = {$cateuid}";

$sql = "SELECT * from {$table_player} WHERE {$sql_where_player} ORDER BY p_num, {$sql_orderby} ";
$re_readlist	= db_query($sql);

$dbinfo['row_pern']		= 8;
if(!$total=db_count($re_readlist)) {	// 게시물이 하나도 없다면...
	if(isset($_GET['sc_string'])) { // 서치시 게시물이 없다면..
		$tpl->set_var('sc_string',htmlspecialchars(stripslashes($_GET['sc_string']),ENT_QUOTES));
		$tpl->process('READLIST', 'nosearch');
	}
	else // 게시물이 없다면. .
		$tpl->process('READLIST', 'nolist');
} else {
	if($dbinfo['row_pern']<1) $dbinfo['row_pern']=1; // 한줄에 여러값 출력이 아닌 경우
	for($i=0; $i<$total; $i+=$dbinfo['row_pern']){
		if($dbinfo['row_pern'] >= 1) $tpl->set_var('CELL',"");
		
		for($j=$i; ($j-$i < $dbinfo['row_pern']) && ($j < $total); $j++) { // 한줄에 여러값 출력시 루틴
			if( $j>=$total ){
				if($dbinfo['row_pern'] > 1) $tpl->process('CELL','nocell',TPL_APPEND);
				continue;
			}
			
			$readlist		= db_array($re_readlist);
			
			$readlist['color'] = "#FFFFFF";

			if(isset($_GET['p_uid']) && $_GET['p_uid'] == $readlist['uid'])
			{
				$player['name'] =	$readlist['p_name'];
				$player['download']	= "/sthis/sthis_player/download.php?db={$table_player}&uid={$readlist['uid']}";
				$player['p_position'] = $readlist['p_position'];
				$player['p_uid'] = $_GET['p_uid'];
				
				if (isset($readlist['p_num']) && $readlist['p_num'] != "")	$player['numimages'] = "<img src='/sthis/sthis_player/stpl/sthis_player/images/savers_team_num".$readlist['p_num'].".gif'>";
					else	$player['numimages'] = "";
				$readlist['color'] = "#FCC99B";
			}
	
			$readlist['no']	= $count['lastnum'];
			$readlist['rede']	= strlen($readlist['re']);
		
			// new image넣을 수 있게 <opt name="enable_new">..
			if($readlist['rdate']>time()-3600*24) $readlist['enable_new']="<img src='/images/icon_new.gif' width='30' height='15' border='0'>";

			// 업로드파일 처리
			if($dbinfo['enable_upload'] != 'N' and $readlist['upfiles']){
				$upfiles=unserialize($readlist['upfiles']);
				if(!is_array($upfiles)) {
					// 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
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
			$href_readlist['download']	= "{$playerUrl}/download.php?db={$table_player}&uid={$readlist['uid']}";
			$href_readlist['read']		= "{$thisUrl}/read.php?" . href_qs("uid={$readlist['uid']}",$qs_basic);
			$href_readlist['list']		= "{$thisUrl}/list.php?" . href_qs("uid={$readlist['uid']}&p_uid={$readlist['uid']}",$qs_basic);
			$href_readlist['go']	= "{$thisUrl}/write.php?" . href_qs("mode=modify&uid={$readlist['uid']}&num={$readlist['num']}&time=".time(),$qs_basic);
			
			
			if (isset($readlist['p_num']) && $readlist['p_num'] != "")	$readlist['numimages'] = "<img src='/sthis/sthis_player/stpl/sthis_player/images/savers_team_num".$readlist['p_num'].".gif'>";
			else	$readlist['numimages'] = "";
			
			$tpl->set_var('href_readlist.go'		, $href_readlist['go']);
			$tpl->set_var('href_readlist.read'		, $href_readlist['read']);
			$tpl->set_var('href_readlist.list'		, $href_readlist['list']);
			$tpl->set_var('href_readlist.download'	, $href_readlist['download']);
			$tpl->set_var('readlist'			, $readlist);
			
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
	$tpl->drop_var('href_readlist.read'); unset($href_readlist['read']);
} // end if (게시물이 있다면...)

$tpl->set_var('player'		,$player);
// 템플릿 마무리 할당
$href['download']	= "{$thisUrl}/download.php?db={$table_player}&uid={$uid}";

if (isset($list['p_num']) && $list['p_num'] != "")	$list['numimages'] = "<img src='/sthis/sthis_player/stpl/sthis_player/images/savers_team_num".$list['p_num'].".gif'>";
		else	$list['numimages'] = "";

//선수 관리 이력 반짝 반짝 효과 : 2005/06/10 안형진
$list['monitoring'] = "<img src=/images/player_icon1.gif width=111 height=30 border=0>";
$list['medical'] = "<img src=/images/player_icon2.gif width=111 height=30 border=0>";
$list['education'] = "<img src=/images/player_icon3.gif width=111 height=30 border=0>";
$list['pain'] = "<img src=/images/player_icon4.gif width=111 height=30 border=0>";
$list['house'] = "<img src=/images/player_icon5.gif width=122 height=30 border=0>";
$list['player'] = "<img src=/images/player_icon6.gif width=120 height=30 border=0>";

$list['pid'] = $_GET['p_uid'];
$list['cateuid'] = $_GET['cateuid'];
$now = date("ymd", mktime());

$sql1 = "select rdate from new21_board2_monitoring where puid=".$_GET['p_uid']." limit 0, 1";
$sql2 = "select rdate from new21_board2_medical where puid=".$_GET['p_uid']." limit 0, 1";
$sql3 = "select rdate from new21_board2_education where puid=".$_GET['p_uid']." limit 0, 1";
$sql4 = "select rdate from new21_board2_pain where puid=".$_GET['p_uid']." limit 0, 1";
$sql5 = "select rdate from new21_board2_house where puid=".$_GET['p_uid']." limit 0, 1";

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
// 선수관리이력 아이콘 끝

// 템플릿 마무리 할당
$tpl->set_var('list'			,$list);// dbinfo 정보 변수
$tpl->set_var('dbinfo'			,$dbinfo);// dbinfo 정보 변수
$tpl->set_var('cateinfo'		,$cateinfo);
$tpl->set_var('count'			,$count);	// 게시판 각종 카운트
$tpl->set_var('href'			,$href);	// 게시판 각종 링크
$tpl->set_var('sc_string'		,htmlspecialchars(stripslashes($_GET['sc_string']),ENT_QUOTES));	// 서치 단어
// 서치 폼의 hidden 필드 모두!!
$form_search =" action='{$_SERVER['PHP_SELF']}' method='get'>";
$form_search .= substr(href_qs("",$qs_basic,1),0,-1);
$tpl->set_var('form_search'		,$form_search);	// form actions, hidden fileds

if(!isset($_GET['limitrows'])) { // 게시물 일부 보기에서는 카테고리, 블럭이 필요 없을 것임
	// 블럭 : 카테고리(상위, 동일, 서브) 생성
	if($dbinfo['enable_cate'] == 'Y'){
		if(is_array($cateinfo['highcate'])){
			foreach($cateinfo['highcate'] as $key =>	$value){
				$tpl->set_var('href.highcate',$_SERVER['PHP_SELF']."?" . href_qs("cateuid=".$key,$qs_basic));
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
				$tpl->set_var('href.samecate',$_SERVER['PHP_SELF']."?" . href_qs("cateuid=".$key,$qs_basic));
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
						$tpl->set_var('href.subsubcate',$_SERVER['PHP_SELF']."?" . href_qs("cateuid=".$subkey,$qs_basic));
						$tpl->set_var('subsubcate.uid',$subkey);
						$tpl->set_var('subsubcate.title',$subvalue);
						$tpl->process('SUBSUBCATE','subsubcate',TPL_OPTIONAL|TPL_APPEND);
						$tpl->set_var('blockloop',true);
					}
					$tpl->set_var('blockloop',$blockloop);
				} // end if

				$tpl->set_var('href.subcate',$_SERVER['PHP_SELF']."?" . href_qs("cateuid=".$key,$qs_basic));
				$tpl->set_var('subcate.uid',$key);
				$tpl->set_var('subcate.title',$value);
				$tpl->process('SUBCATE','subcate',TPL_OPTIONAL|TPL_APPEND);
				$tpl->set_var('blockloop',true);
			}
			$tpl->drop_var('blockloop');
		} // end if
	} // end if

	// 블럭 : 첫페이지, 이전페이지
	if($count['nowpage'] > 1){
		$tpl->process('FIRSTPAGE','firstpage');
		$tpl->process('PREVPAGE','prevpage');
	} else {
		$tpl->process('FIRSTPAGE','nofirstpage');
		$tpl->process('PREVPAGE','noprevpage');
	}

	// 블럭 : 페이지 블럭 표시
		// <-- (이전블럭) 부분
		if ($count['nowblock']>1) $tpl->process('PREVBLOCK','prevblock');
		else $tpl->process('PREVBLOCK','noprevblock');
		// 1 2 3 4 5 부분
		for ($i=$count['firstpage'];$i<=$count['lastpage'];$i++) {
			$tpl->set_var('blockcount',$i);
			if($i == $count['nowpage'])
				$tpl->process('BLOCK','noblock',TPL_APPEND);
			else {
				$tpl->set_var('href.blockcount', $_SERVER['PHP_SELF']."?" . href_qs("page=".$i,$qs_basic) );
				$tpl->process('BLOCK','block',TPL_APPEND);
			}
		} // end for
		// --> (다음블럭) 부분
		if ($count['totalpage'] > $count['lastpage']	) $tpl->process('NEXTBLOCK','nextblock');
		else $tpl->process('NEXTBLOCK','nonextblock');

	// 블럭 : 다음페이지, 마지막 페이지
	if($count['nowpage'] < $count['totalpage']){
		$tpl->process('NEXTPAGE','nextpage');
		$tpl->process('LASTPAGE','lastpage');
	} else {
		$tpl->process('NEXTPAGE','nonextpage');
		$tpl->process('LASTPAGE','nolastpage');
	}
} // end if

// 블럭 : 글쓰기
if(privAuth($dbinfo, "priv_write")) $tpl->process('WRITE','write');
else $tpl->process('WRITE','nowrite');

// 블럭 : 글답변
if(privAuth($dbinfo, "priv_reply")) $tpl->process('REPLY','reply');

// 블럭 : 글수정,삭제
if(privAuth($dbinfo, "priv_delete") or $list['bid'] == $_SESSION['seUid'] or $list['bid'] == 0){
	$tpl->process('MODIFY','modify');
	$tpl->process('DELETE','delete');
}

// 마무리
$val="\\1{$thisUrl}/stpl/{$dbinfo['skin']}/images/";
// - 사이트 템플릿 읽어오기
if(preg_match("/^(ht|h|t)$/",$dbinfo['html_headpattern'])){
	$HEADER['header'] == 2;
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