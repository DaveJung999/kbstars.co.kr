<?php
//=======================================================
// 설 명 : 게시판 목록보기(profile_list.php)
// 책임자 : 박선민 (sponsor@new21.com), 검수: 04/01/31
// Project: sitePHPbasic
// ChangeLog
// DATE 수정인			 수정 내용
// -------- ------ --------------------------------------
// 04/01/14 박선민 $list['enable_new'] 만듦
// 04/01/27 박선민 카테고리 개선
// 04/01/31 박선민 서치부분 개선
// 25/08/10 Gemini PHP 7+ 호환되도록 마이그레이션
//=======================================================
$HEADER = array(
	'priv' =>  '', // 인증유무 (0:모두에게 허용, 숫자가 높을 수록 레벨업)
	'usedb2' =>  1, // DB 커넥션 사용 (0:미사용, 1:사용)
	'useBoard2' =>  1, // privAuth()
	'useApp' =>  1
);
require_once("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $_SERVER['HTTP_HOST']);

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
$thisPath = dirname(__FILE__);
$thisUrl	= "/sthis/sthis_player"; // 마지막 "/"이 빠져야함

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text', 'html_headtpl'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================
	
$qs_basic = "db={$db}" . //table 이름
	"&mode={$mode}" . // mode값은 list.php에서는 당연히 빈값
	"&cateuid={$cateuid}" . //cateuid
	"&pern={$pern}" . // 페이지당 표시될 게시물 수
	"&sc_column={$sc_column}" . //search column
	"&sc_string=" . urlencode(stripslashes($sc_string)) . //search string
	"&html_headtpl={$html_headtpl}" .
	"&m_category=5" .
	"&m_bcode=1" .
	"&page={$page}"; //현재 페이지

include_once("{$_SERVER['DOCUMENT_ROOT']}/sthis/sthis_player/dbinfo.php"); // $dbinfo, $table 값 정의

// 인증 체크
if (!privAuth($dbinfo, "priv_list", 1)) back("이용이 제한되었습니다.(레벨부족)");

$sql_p_position = '';
if (isset($_GET['p_position'])){
	// SQL Injection 방지를 위해 db_escape와 같은 함수 사용을 권장합니다.
	$sql_p_position = " p_position='" . addslashes($_GET['p_position']) . "' ";
}
$get_skin = $_GET['skin'] ?? "profile";
$row_pern = $_GET['row_pern'] ?? "3";


// 강제셋팅
$_GET = array(
	'db' =>  "player", // 게시물 db
	'pern' =>  "100",
	'row_pern' =>  $row_pern,
	'sql_where' =>  " tid = 13 and p_gubun = '현역' ",
	'sql_orderby' =>  " p_seq ",
	'skin' =>  $get_skin, // 게시판 스킨
	'pid' =>  "0",
	'html_headpattern' =>  "no" // Site 해더 넣지 않음
);

//===================
// 카테고리 정보 구함
//===================
if (($dbinfo['enable_cate'] ?? '') == 'Y'){
	$dbinfo['table_cate'] = $table . "_cate";
//
//	// 카테고리정보구함 (dbinfo, table_cate, cateuid, $enable_catelist='Y', sw_topcatetitles, sw_notitems, sw_itemcount,string_firsttotal)
//	// highcate[], samecate[], subcate[], subsubcate[], subcateuid[], catelist
//	$tmp_itemcount = trim($_GET['sc_string'] ?? '') ? 0 : 1;
//	
//	$cateinfo = board2CateInfo($dbinfo, ($_GET['cateuid'] ?? null), 'Y', 1, 1, $tmp_itemcount, "(종합)");
	
	// davej............. 2025-08-11
	// 카테고리정보구함 (dbinfo, cateuid, sw_catelist, string_view_firsttotal)
	// return : highcate[], samecate[], subcate[], subsubcate[], subcateuid[], catelist
	$sw_catelist = CATELIST_VIEW | CATELIST_VIEW_TOPCATE_TITLE;
	if(isset($_GET['sc_string']) && strlen($_GET['sc_string'])) $sw_catelist |= CATELIST_NOVIEW_NODATA;
	$cateinfo=board2CateInfo($dbinfo, isset($_REQUEST['cateuid']) ? $_REQUEST['cateuid'] : '', $sw_catelist,'(전체)');
	
	if (!($_GET['cateuid'] ?? null)){
		$cateinfo['uid']	 = "{$_SERVER['PHP_SELF']}?" . href_qs("", $qs_basic);
		$cateinfo['title'] = "전체";
	}
} // end if

// 넘어온 값에 따라 $dbinfo값 변경
if (($dbinfo['enable_getinfo'] ?? '') == 'Y'){
	if (isset($_GET['pern'])) $dbinfo['pern'] = (int)$_GET['pern'];
	if (isset($_GET['row_pern'])) $dbinfo['row_pern'] = (int)$_GET['row_pern'];
	if (isset($_GET['cut_length'])) $dbinfo['cut_length'] = (int)$_GET['cut_length'];
	if (isset($_GET['cateuid'])) $dbinfo['cateuid'] = (int)$_GET['cateuid'];
	if (isset($_GET['enable_listreply'])) $dbinfo['enable_listreply'] = $_GET['enable_listreply'];
	if (isset($_GET['sql_orderby'])) $dbinfo['orderby'] = $_GET['sql_orderby'];
	if (isset($_GET['sql_where'])) $sql_where = $_GET['sql_where'];
	if ($sql_p_position) $sql_where = ($sql_where ?? '') ? ($sql_where ?? '') . " and " . $sql_p_position : $sql_p_position;


	// skin관련
	if (isset($_GET['html_headpattern'])) $dbinfo['html_headpattern'] = $_GET['html_headpattern'];
	// PHP 7에서 제거된 eregi()를 preg_match()로 변경
	if (isset($_GET['html_headtpl']) && preg_match("/^[_a-z0-9]+$/i", $_GET['html_headtpl'])
		 && is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$_GET['html_headtpl']}.php"))
		$dbinfo['html_headtpl'] = $_GET['html_headtpl'];
	// PHP 7에서 제거된 eregi()를 preg_match()로 변경
	if (isset($_GET['skin']) && preg_match("/^[_a-z0-9]+$/i", $_GET['skin'])
		 && is_dir("{$thisPath}/stpl/{$_GET['skin']}"))
		$dbinfo['skin'] = $_GET['skin'];
}

//===================
// SQL문 where절 정리
//===================
$sql_where_init = $sql_where ?? ''; // 이전 $sql_where 값을 보존
$sql_where = ''; // 새로 조합하기 위해 초기화

// 한 table에 여러 게시판 생성의 경우
if (($dbinfo['table_name'] ?? '') != ($dbinfo['db'] ?? '')) $sql_where = " db='{$dbinfo['db']}' "; // $sql_where 사용 시작
if (($dbinfo['enable_type'] ?? '') == 'Y') $sql_where = $sql_where ? $sql_where . " and type='docu' " : " type='docu' ";
// 해당 카테고리만 볼려면
if (is_array($cateinfo['subcate_uid'] ?? null) && count($cateinfo['subcate_uid']) > 0) $sql_where = $sql_where ? $sql_where . " and ( cateuid in ( " . implode(",", $cateinfo['subcate_uid']) . ") ) " : " ( cateuid in ( " . implode(",", $cateinfo['subcate_uid']) . ") ) ";

// 강제셋팅으로 들어온 $sql_where 값을 최종적으로 합침
if ($sql_where_init){
	$sql_where = $sql_where ? $sql_where . ' AND ' . $sql_where_init : $sql_where_init;
}

if (!$sql_where) $sql_where = " 1 ";

$sql_orderby = ($dbinfo['orderby'] ?? '') ? $dbinfo['orderby'] : " num DESC, re ";

//=====
// misc
//=====
// 페이지 나눔등 각종 카운트 구하기
$count['total'] = db_resultone("SELECT count(*) FROM {$table} WHERE  $sql_where ", 0, "count(*)"); // 전체 게시물 수
// 게시물 일부만 본다면
if (isset($_GET['limitrows'])) $dbinfo['pern'] = $count['total'];
$count = board2Count($count['total'], $page, ($dbinfo['pern'] ?? 10), ($dbinfo['page_pern'] ?? 10)); // 각종 카운트 구하기
$count['today'] = db_resultone("SELECT count(*) FROM {$table} WHERE (rdate > unix_timestamp(curdate())) and $sql_where ", 0, "count(*)");

//-------------spemin
if (!($dbinfo['pern'] ?? null)) $dbinfo['pern'] = $count['total'];
if (!($dbinfo['row_pern'] ?? null)) $dbinfo['row_pern'] = $count['total'];
//-------------------

// 서치 폼의 hidden 필드 모두!!
$form_search = " action='{$_SERVER['PHP_SELF']}' method='get'>";
$form_search .= substr(href_qs("", $qs_basic, 1), 0, -1);

// URL Link...
$href['list'] = "{$thisUrl}/list.php?db=" . (isset($dbinfo['db']) ? $dbinfo['db'] : '');
$href['write'] = "{$thisUrl}/write.php?" . href_qs("mode=write&time=" . time(), $qs_basic); // 글쓰기
if (($count['nowpage'] ?? 1) > 1) { // 처음, 이전 페이지
	$href['firstpage'] = "{$_SERVER['PHP_SELF']}?" . href_qs("page=1", $qs_basic);
	$href['prevpage'] = "{$_SERVER['PHP_SELF']}?" . href_qs("page=" . ($count['nowpage'] - 1), $qs_basic);
} else {
	$href['firstpage'] = "javascript: void(0)";
	$href['prevpage'] = "javascript: void(0)";
}
if (($count['nowpage'] ?? 1) < ($count['totalpage'] ?? 1)) { // 다음, 마지막 페이지
	$href['nextpage'] = "{$_SERVER['PHP_SELF']}?" . href_qs("page=" . ($count['nowpage'] + 1), $qs_basic);
	$href['lastpage'] = "{$_SERVER['PHP_SELF']}?" . href_qs("page=" . $count['totalpage'], $qs_basic);
} else {
	$href['nextpage'] = "javascript: void(0)";
	$href['lastpage'] = "javascript: void(0)";
}
$href['prevblock'] = (($count['nowblock'] ?? 1) > 1) ? "{$_SERVER['PHP_SELF']}?" . href_qs("page=" . ($count['firstpage'] - 1), $qs_basic) : "javascript: void(0)";// 이전 페이지 블럭
$href['nextblock'] = (($count['totalpage'] ?? 1) > ($count['lastpage'] ?? 1)) ? "{$_SERVER['PHP_SELF']}?" . href_qs("page=" . ($count['lastpage'] + 1), $qs_basic) : "javascript: void(0)";// 다음 페이지 블럭

//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
// 템플릿 기반 웹 페이지 제작
$tpl = new phemplate("", "remove_nonjs");
if (!is_file("{$thisPath}/stpl/{$dbinfo['skin']}/list.htm")) $dbinfo['skin'] = "board_basic";
$tpl->set_file('html', "{$thisPath}/stpl/{$dbinfo['skin']}/list.htm", TPL_BLOCK);


// Limit로 필요한 게시물만 읽음.
$limitno = ($_GET['limitno'] ?? null) ? $_GET['limitno'] : ($count['firstno'] ?? 0);
$limitrows = ($_GET['limitrows'] ?? null) ? $_GET['limitrows'] : ($count['pern'] ?? 10);
$sql = "SELECT * FROM {$table} WHERE $sql_where ORDER BY {$sql_orderby} LIMIT {$limitno},{$limitrows}";

$rs_list = db_query($sql);
if (!$total = db_count($rs_list)) { // 게시물이 하나도 없다면...
	if (isset($_GET['sc_string'])) { // 서치시 게시물이 없다면..
		$tpl->set_var('sc_string', htmlspecialchars(stripslashes($_GET['sc_string']), ENT_QUOTES));
		$tpl->process('LIST', 'nosearch');
	} else // 게시물이 없다면..
		$tpl->process('LIST', 'nolist');
} else {
	if ($dbinfo['row_pern'] < 1) $dbinfo['row_pern'] = 1; // 한줄에 여러값 출력이 아닌 경우
	for ($i = 0; $i < $total; $i += $dbinfo['row_pern']){
		if ($dbinfo['row_pern'] >= 1) $tpl->set_var('CELL', "");

		for ($j = $i; $j - $i < $dbinfo['row_pern']; $j++) { // 한줄에 여러값 출력시 루틴
			if ($j >= $total){
				if ($dbinfo['row_pern'] > 1) $tpl->process('CELL', 'nocell', TPL_APPEND);
				continue;
			}
			$list = db_array($rs_list);
			$list['no'] = $count['lastnum'];
			$list['rede'] = strlen($list['re'] ?? '');

			// new image넣을 수 있게 <opt name="enable_new">..
			if (($list['rdate'] ?? 0) > time() - 3600 * 24) $list['enable_new'] = "<img src='/images/icon_new.gif' width='30' height='15' border='0'>";

			// 업로드파일 처리
			if (($dbinfo['enable_upload'] ?? 'N') != 'N' and isset($list['upfiles'])){
				$upfiles = unserialize($list['upfiles']);
				if (!is_array($upfiles)){
					// 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
					$upfiles['upfile']['name'] = $list['upfiles'];
					$upfiles['upfile']['size'] = (int)($list['upfiles_totalsize'] ?? 0);
				}
				foreach ($upfiles as $key =>  $value){
					if (isset($value['name']))
						$upfiles[$key]['href'] = "{$thisUrl}/download.php?" . href_qs("uid={$list['uid']}&upfile={$key}", $qs_basic);
				} // end foreach
				$list['upfiles'] = $upfiles;
				unset($upfiles);
			} // end if 업로드파일 처리

			// URL Link...
			$href['download'] = "{$thisUrl}/download.php?db=" . (isset($dbinfo['db']) ? $dbinfo['db'] : '') . "&uid={$list['uid']}";
			$href['read'] = "{$thisUrl}/read.php?" . href_qs("uid={$list['uid']}", $qs_basic);
			$href['go'] = "{$thisUrl}/write.php?" . href_qs("mode=modify&uid={$list['uid']}&num={$list['num']}&time=" . time(), $qs_basic);

			// 템플릿 YESRESULT 값들 입력
			if (($list['uid'] ?? 0) == 25) $list['color'] = "#E7D3FD";
			else $list['color'] = "#FFFFFF";

			if (isset($list['p_num'])) $list['numimages'] = "<img src='/sthis/sthis_player/stpl/sthis_player/images/savers_team_num" . $list['p_num'] . ".gif'>";
			else $list['numimages'] = "";

			$tpl->set_var('href.go', $href['go']);
			$tpl->set_var('href.read', $href['read']);
			$tpl->set_var('href.download', $href['download']);
			$tpl->set_var('list', $list);

			if (privAuth($dbinfo, "priv_write")) $tpl->process('GO', 'go');
			else $tpl->process('NOGO', 'nogo');

			$count['lastnum']--;

			if ($dbinfo['row_pern'] >= 1){
				if ($j == 0) $tpl->drop_var('blockloop');
				else $tpl->set_var('blockloop', true);
				$tpl->process('CELL', 'cell', TPL_APPEND);
			}
		} // end for (j)

		$tpl->process('LIST', 'list', TPL_OPTIONAL | TPL_APPEND);
		$tpl->set_var('blockloop', true);
	} // end for (i)
	$tpl->drop_var('blockloop');
	$tpl->drop_var('href.read');
	unset($href['read']);
} // end if (게시물이 있다면...)

// 템플릿 마무리 할당
$tpl->set_var('dbinfo', $dbinfo);// dbinfo 정보 변수
$tpl->set_var('href', $href);	// 게시판 각종 링크

if (!isset($_GET['limitrows'])) { // 게시물 일부 보기에서는 카테고리, 블럭이 필요 없을 것임
	// 블럭 : 카테고리(상위, 동일, 서브) 생성
	if (($dbinfo['enable_cate'] ?? '') == 'Y'){
		if (isset($cateinfo['catelist'])){
			$tpl->set_var('cateinfo.catelist', $cateinfo['catelist']);
			$tpl->process('CATELIST', 'catelist', TPL_APPEND);
		}

		if (is_array($cateinfo['highcate'] ?? null)){
			foreach ($cateinfo['highcate'] as $key =>  $value){
				$tpl->set_var('href.highcate', "{$_SERVER['PHP_SELF']}?" . href_qs("cateuid=" . $key, $qs_basic));
				$tpl->set_var('highcate.uid', $key);
				$tpl->set_var('highcate.title', $value);
				$tpl->process('HIGHCATE', 'highcate', TPL_OPTIONAL | TPL_APPEND);
				$tpl->set_var('blockloop', true);
			}
			$tpl->drop_var('blockloop');
		} // end if
		if (is_array($cateinfo['samecate'] ?? null)){
			foreach ($cateinfo['samecate'] as $key =>  $value){
				if ($key == ($cateinfo['uid'] ?? null))
					$tpl->set_var('samecate.selected', " selected ");
				else
					$tpl->set_var('samecate.selected', "");
				$tpl->set_var('href.samecate', "{$_SERVER['PHP_SELF']}?" . href_qs("cateuid=" . $key, $qs_basic));
				$tpl->set_var('samecate.uid', $key);
				$tpl->set_var('samecate.title', $value);
				$tpl->process('SAMECATE', 'samecate', TPL_OPTIONAL | TPL_APPEND);
				$tpl->set_var('blockloop', true);
			}
			$tpl->drop_var('blockloop');
		} // end if
		if (is_array($cateinfo['subcate'] ?? null)){
			foreach ($cateinfo['subcate'] as $key =>  $value){
				// subsubcate...
				$tpl->drop_var('SUBSUBCATE');
				if (is_array($cateinfo['subsubcate'][$key] ?? null)){
					$blockloop = $tpl->get_var('blockloop');
					$tpl->drop_var('blockloop');
					foreach ($cateinfo['subsubcate'][$key] as $subkey =>  $subvalue){
						$tpl->set_var('href.subsubcate', "{$_SERVER['PHP_SELF']}?" . href_qs("cateuid=" . $subkey, $qs_basic));
						$tpl->set_var('subsubcate.uid', $subkey);
						$tpl->set_var('subsubcate.title', $subvalue);
						$tpl->process('SUBSUBCATE', 'subsubcate', TPL_OPTIONAL | TPL_APPEND);
						$tpl->set_var('blockloop', true);
					}
					$tpl->set_var('blockloop', $blockloop);
				} // end if

				$tpl->set_var('href.subcate', "{$_SERVER['PHP_SELF']}?" . href_qs("cateuid=" . $key, $qs_basic));
				$tpl->set_var('subcate.uid', $key);
				$tpl->set_var('subcate.title', $value);
				$tpl->process('SUBCATE', 'subcate', TPL_OPTIONAL | TPL_APPEND);
				$tpl->set_var('blockloop', true);
			}
			$tpl->drop_var('blockloop');
		} // end if
	} // end if

	// 블럭 : 첫페이지, 이전페이지
	if (($count['nowpage'] ?? 1) > 1){
		$tpl->process('FIRSTPAGE', 'firstpage');
		$tpl->process('PREVPAGE', 'prevpage');
	} else {
		$tpl->process('FIRSTPAGE', 'nofirstpage');
		$tpl->process('PREVPAGE', 'noprevpage');
	}

	// 블럭 : 페이지 블럭 표시
	// <-- (이전블럭) 부분
	if (($count['nowblock'] ?? 1) > 1) $tpl->process('PREVBLOCK', 'prevblock');
	else $tpl->process('PREVBLOCK', 'noprevblock');
	// 1 2 3 4 5 부분
	for ($i = ($count['firstpage'] ?? 1); $i <= ($count['lastpage'] ?? 1); $i++){
		$tpl->set_var('blockcount', $i);
		if ($i == ($count['nowpage'] ?? 1))
			$tpl->process('BLOCK', 'noblock', TPL_APPEND);
		else {
			$tpl->set_var('href.blockcount', "{$_SERVER['PHP_SELF']}?" . href_qs("page=" . $i, $qs_basic));
			$tpl->process('BLOCK', 'block', TPL_APPEND);
		}
	} // end for
	// --> (다음블럭) 부분
	if (($count['totalpage'] ?? 1) > ($count['lastpage'] ?? 1)) $tpl->process('NEXTBLOCK', 'nextblock');
	else $tpl->process('NEXTBLOCK', 'nonextblock');

	// 블럭 : 다음페이지, 마지막 페이지
	if (($count['nowpage'] ?? 1) < ($count['totalpage'] ?? 1)){
		$tpl->process('NEXTPAGE', 'nextpage');
		$tpl->process('LASTPAGE', 'lastpage');
	} else {
		$tpl->process('NEXTPAGE', 'nonextpage');
		$tpl->process('LASTPAGE', 'nolastpage');
	}
} // end if

// 블럭 : 글쓰기
if (privAuth($dbinfo, "priv_write")) $tpl->process('WRITE', 'write');
else $tpl->process('WRITE', 'nowrite');


// 마무리
$val_replacement = '$1' . "{$thisUrl}/stpl/{$dbinfo['skin']}/images/";
// - 사이트 템플릿 읽어오기
// PHP 7에서 제거된 ereg()를 preg_match()로 변경
if (preg_match("/^(ht|h|t)$/", ($dbinfo['html_headpattern'] ?? ''))){
	$HEADER['header'] == 2;
	if (isset($dbinfo['html_headtpl']) && is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php"))
		@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php");
	else
		@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");
}
// PHP 7에서 제거된 ereg_replace()를 preg_replace()로 변경
$pattern = '/(["\'])images\//';
switch ($dbinfo['html_headpattern'] ?? null){
	case "ht":
		echo ($SITE['head'] ?? '') . ($dbinfo['html_head'] ?? '');
		echo preg_replace($pattern, $val_replacement, $tpl->process('', 'html', TPL_OPTIONAL));
		echo ($dbinfo['html_tail'] ?? '') . ($SITE['tail'] ?? '');
		break;
	case "h":
		echo ($SITE['head'] ?? '') . ($dbinfo['html_head'] ?? '');
		echo preg_replace($pattern, $val_replacement, $tpl->process('', 'html', TPL_OPTIONAL));
		echo ($dbinfo['html_tail'] ?? '');
		break;
	case "t":
		echo ($dbinfo['html_head'] ?? '');
		echo preg_replace($pattern, $val_replacement, $tpl->process('', 'html', TPL_OPTIONAL));
		echo ($dbinfo['html_tail'] ?? '') . ($SITE['tail'] ?? '');
		break;
	case "no":
		echo preg_replace($pattern, $val_replacement, $tpl->process('', 'html', TPL_OPTIONAL));
		break;
	default:
		echo ($dbinfo['html_head'] ?? '');
		echo preg_replace($pattern, $val_replacement, $tpl->process('', 'html', TPL_OPTIONAL));
		echo ($dbinfo['html_tail'] ?? '');
} // end switch
?>