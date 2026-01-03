<?php
//=======================================================
// 설	명 : 게시판 삭제 비밀번호 입력 페이지(delete.php)
// 책임자 : 박선민 (sponsor@new21.com), 검수: 03/10/12
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 03/10/12 박선민 마지막 수정
// 24/05/18 Gemini	PHP 7 마이그레이션 및 db_* 함수 적용
//=======================================================
$HEADER=array(
		'nocache' => 1,
		'priv' =>	"운영자,뉴스관리자", // 인증유무 (0:모두에게 허용, 숫자가 높을 수록 레벨업)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'useSkin' =>	1, // 템플릿 사용
		'useBoard2' => 1, // 보드관련 함수 포함
		'html_echo' => ''	// 게시판은 무조건 '0', (boardinfo[html_headtpl]에서 템플릿 설정함
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $_SERVER['HTTP_HOST']);

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
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
$qs_basic		= href_qs();
$table_dbinfo	= $dbinfo['table'];

// info 테이블 정보 가져와서 $dbinfo로 저장
if(isset($_GET['db'])){
	$sql="SELECT * FROM {$table_dbinfo} WHERE db='".db_escape($_GET['db']) . "'";
	$dbinfo=db_arrayone($sql) or back("사용하지 않은 DB입니다.");

	$table=$SITE['th'] . "{$prefix}_" . $dbinfo['table_name'];
}
else back("DB 값이 없습니다");

//===================
// 카테고리 정보 구함
//===================
if(($dbinfo['enable_cate'] ?? 'N') == 'Y'){
	$dbinfo['table_cate']	= {$dbinfo['table']} . "_cate";

//		// 카테고리정보구함 (dbinfo, cateuid, $enable_catelist='Y', sw_topcatetitles, sw_notitems, sw_itemcount,string_firsttotal)
//		// return : highcate[], samecate[], subcate[], subsubcate[], subcateuid[], catelist
//		$tmp_itemcount = trim($_GET['sc_string']) ? 0 : 1;
//		$cateinfo=board2CateInfo($dbinfo, $_GET['cateuid'], 'Y', 1,1,$tmp_itemcount,"(전체)");
	
	// davej............. 2025-08-11
	// 카테고리정보구함 (dbinfo, cateuid, sw_catelist, string_view_firsttotal)
	// return : highcate[], samecate[], subcate[], subsubcate[], subcateuid[], catelist
	$sw_catelist = CATELIST_VIEW | CATELIST_VIEW_TOPCATE_TITLE;
	if(isset($_GET['sc_string']) && strlen($_GET['sc_string'])) $sw_catelist |= CATELIST_NOVIEW_NODATA;
	$cateinfo=board2CateInfo($dbinfo, $_GET['cateuid'] ?? '', $sw_catelist,'(전체)');
	
	// 카테고리 정보가 없다면	
	if(!($cateinfo['uid'] ?? '')){
		$cateinfo['title']	= "(전체)";
	} else {
		// 카테고리 정보에 따른 dbinfo 변수 변경
		if(($dbinfo['enable_cateinfo'] ?? 'N') == 'Y'){
			if(($cateinfo['bid'] ?? 0) > 0) $dbinfo['cid'] = $cateinfo['bid'];
			if( isset($cateinfo['skin']) and is_file("{$thisPath}/stpl/{$cateinfo['skin']}/list.htm") )
				$dbinfo['skin']		= $cateinfo['skin'];
			if(isset($cateinfo['html_headpattern']))	{
				$dbinfo['html_headpattern']	= $cateinfo['html_headpattern'];
				if( isset($cateinfo['html_headtpl']) and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$cateinfo['html_headtpl']}.php") )	
					$dbinfo['html_headtpl']	= $cateinfo['html_headtpl'];
				$dbinfo['html_head']			= $cateinfo['html_head'];
				$dbinfo['html_tail']			= $cateinfo['html_tail'];
			}
		}
	} // end if
} // end if
//===================

$form_delete	= " ACTION='{$thisUrl}/ok.php' method='POST'>";
if(($_GET['mode'] ?? '') == "memo") // memo 삭제 요청의 경우(하지만 메모는 로그인한사람만 쓸 수 있음)
	$form_delete.= substr(href_qs("mode=memodelete",$qs_basic,1),0,-1);
else
	$form_delete.= substr(href_qs("mode=delete",$qs_basic,1),0,-1);

// URL Link..
$href["list"] = "{$thisUrl}/list.php?" . href_qs("",$qs_basic);

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
// 템플릿 기반 웹 페이지 제작
$tpl = new phemplate("","remove_nonjs");
if( !is_file("{$thisPath}/stpl/{$dbinfo['skin']}/delete.htm") ) $dbinfo['skin']="board_basic";
$tpl->set_file('html',"{$thisPath}/stpl/{$dbinfo['skin']}/delete.htm",TPL_BLOCK);

// 템플릿 마무리 할당
$tpl->set_var('form_delete',$form_delete);
$tpl->set_var('href',$href);

// 마무리
$val="\\1{$thisUrl}/stpl/{$dbinfo['skin']}/images/";
// - 사이트 템플릿 읽어오기
if(preg_match("/^(ht|h|t)$/", $dbinfo['html_headpattern'])){
	$HEADER['header'] = 2;
	if( isset($dbinfo['html_headtpl']) and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php") )
		@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php");
	else
		@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");
}
switch($dbinfo['html_headpattern']){
	case "ht":
		echo ($SITE['head'] ?? '') . ($dbinfo['html_head'] ?? '');
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));	
		echo ($dbinfo['html_tail'] ?? '') . ($SITE['tail'] ?? '');
		break;
	case "h":
		echo ($SITE['head'] ?? '') . ($dbinfo['html_head'] ?? '');
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));	
		echo ($dbinfo['html_tail'] ?? '');
		break;
	case "t":
		echo ($dbinfo['html_head'] ?? '');
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		echo ($dbinfo['html_tail'] ?? '') . ($SITE['tail'] ?? '');
		break;
	case "no":
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		break;
	default:
		echo ($dbinfo['html_head'] ?? '');
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));	
		echo ($dbinfo['html_tail'] ?? '');
} // end switch
?>
