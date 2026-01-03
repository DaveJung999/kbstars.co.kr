<?php
//=======================================================
// 설	명 : 게시판 글쓰기(write.php)
// 책임자 : 박선민 (sponsor@new21.com), 검수: 03/12/08
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 03/09/15 박선민 $HEADER['nocache'] -> $HEADER['private']
// 03/10/14 박선민 마지막 수정
// 03/12/08 박선민 bugfix - replay 부분
// 25/08/11 Gemini	PHP 7 마이그레이션
//=======================================================
$HEADER=array(
//		"private" => 1,
		'priv' => '운영자', // 인증유무 (0:모두에게 허용, 숫자가 높을 수록 레벨업)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'useSkin' =>  1, // 템플릿 사용
		'useBoard2' => 1, // 보드관련 함수 포함
		'useApp' => 1,
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $_SERVER['HTTP_HOST']);
//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
$thisPath		= dirname(__FILE__);

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text', 'html_headtpl'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

$qs_basic = "db={$db}".					//table 이름
			"&mode={$mode}".		// mode값은 list.php에서는 당연히 빈값
			"&team={$team}".		//cateuid
			"&cateuid={$cateuid}".		//cateuid
			"&pern={$pern}" .				// 페이지당 표시될 게시물 수
			"&sc_column={$sc_column}".	//search column
			"&sc_string=" . urlencode(stripslashes($sc_string)) . //search string
			"&html_headtpl={$html_headtpl}".
			"&goto={$goto}".
			"&m_category=5".
			"&m_bcode=1".
			"&page={$page}";				//현재 페이지

include("./dbinfo.php"); // $dbinfo, $table 값 정의

//===================
// SQL문 where절 정리
//===================
if(!isset($sql_where)) $sql_where= " 1 ";

//===================//

// 글 수정하기/ 글 답변하기라면...
if(isset($_GET['mode']) && ($_GET['mode'] == "modify" || $_GET['mode'] == "reply")){
	$sql = "SELECT *, PASSWORD(rdate) AS private_key FROM {$table} WHERE $sql_where AND uid='{$_GET['uid']}' AND num='{$_GET['num']}'";
	$list = db_arrayone($sql);
	
	if(!$list) back("게시물의 정보가 없습니다");

	// 비공개글 제외시킴
	if(isset($dbinfo['enable_level']) && $dbinfo['enable_level'] == 'Y' and !privAuth($list, "priv_level",1)){
		back("이용이 제한되었습니다 . 게시물 설정 권한을 확인바랍니다.");
	}

	$list['title']	= htmlspecialchars($list['title'],ENT_QUOTES);
	$list['content']	= htmlspecialchars($list['content'],ENT_QUOTES);
	/////////////////////////////////
	// 추가되어 있는 테이블 필드 포함
	$skip_fields = array('uid', 'bid', 'userid', 'email', 'passwd', 'db', 'cateuid', 'num', 're', 'title', 'content', 'upfiles', 'upfiles_totalsize', 'docu_type', 'type', 'priv_level', 'ip', 'hit', 'hitip', 'hitdownload', 'vote', 'voteip' ,	'rdate');
	if($fieldlist = userGetAppendFields($table, $skip_fields)){
		foreach($fieldlist as $value){
			$list[$value] = isset($list[$value]) ? htmlspecialchars($list[$value],ENT_QUOTES) : '';
		}
	}
	////////////////////////////////
	

	if($_GET['mode'] == "modify"){
		// 인증 체크
		if( (isset($list['bid']) && $list['bid'] == 0) or (isset($list['bid']) && $list['bid'] == $_SESSION['seUid']) or privAuth($dbinfo, "priv_delete", 1) ){
			// nothing...
		}
		else back("글쓴이가 아니면 수정을 하실수 없습니다.");

		$list['name'] = isset($list['name']) ? htmlspecialchars($list['name'],ENT_QUOTES) : '';
		$list['docu_type_checked'] = (isset($list['docu_type']) && strtoupper($list['docu_type']) == "HTML") ? "checked" : "";
		//$list['writeinfo_checked'] = (isset($list['type']) && $list['type'] == "info") ? "checked" : "";

		// 업로드 처리
//			if(isset($dbinfo['enable_upload']) && $dbinfo['enable_upload'] != 'N' and $list['upfiles']) {	//davej...................2005.12.21
		if(isset($dbinfo['enable_upload']) && $dbinfo['enable_upload'] != 'N'){
			$upfiles=isset($list['upfiles']) ? unserialize($list['upfiles']) : '';
			if(!is_array($upfiles))	{
				// 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
				$upfiles['upfile']['name']=isset($list['upfiles']) ? $list['upfiles'] : '';
				$upfiles['upfile']['size']=(int)(isset($list['upfiles_totalsize']) ? $list['upfiles_totalsize'] : 0);
			}
			$list['upfiles'] = $upfiles;
		}
	}
	elseif($_GET['mode'] == "reply"){
		// 인증 체크
		if(!privAuth($dbinfo, "priv_reply", 1)) back("이용이 제한되었습니다.(레벨부족)");
		$qs_basic .= "&rec_email=" . (isset($list['email']) ? $list['email'] : '');

		$list['content'] = isset($list['content']) ? preg_replace("/\n/", "\n ", $list['content']) : '';
		$list['content'] = isset($list['content']) ? preg_replace("/^/", "\n\n\n[ {$list['userid']} ]님이 작성하신 글입니다\n---------------------------------------\n ", $list['content']) : '';
		/* 혹은 글 앞에 ":"붙이기
		$list['content'] = preg_replace("/<([^<>\n]+)\n([^\n<>]+)>/", "<\\1 \\2>", $list['content']); // 테그 붙이기
		$list['content'] = preg_replace("/^/", ": ", $list['text']);
		$list['content'] = preg_replace("/\n/", "\n: ", $list['text']);
		$list['content'] = htmlspecialchars($list['text']);
		*/
	}
} else {
	// 인증 체크
	if(!privAuth($dbinfo, "priv_write",1)) back("이용이 제한되었습니다.(레벨부족)");
	if(!isset($_GET['mode'])) $_GET['mode']="write";
}

$form_write = " method='post' action='{$thisUrl}/ok.php' ENCTYPE='multipart/form-data'>";
$form_write .= substr(href_qs("mode={$_GET['mode']}&uid=". (isset($list['uid']) ? $list['uid'] : '') . "&private_key=". (isset($list['private_key']) ? $list['private_key'] : ''),$qs_basic,1),0,-1);

// URL Link...
$href["list"] = "./list.php?" . href_qs("",$qs_basic);

//팀명, 팀아이디 가져오기
$tsql = " SELECT * FROM team ORDER BY tid ASC ";
$trs = db_query($tsql);
$tcnt = db_count($trs);
$tselect = '';

if($tcnt){
	for($i = 0 ; $i < $tcnt ; $i++)	{
		$tlist = db_array($trs);
		$teamid = $tlist['tid'];
		$t_name[$i] = $tlist['t_name'];
		//저장된 팀 항목 셀렉트
		if(isset($list['tid']) && $list['tid'] == $teamid){
			$tsel = "selected";
			$tselect .= "<option value='{$teamid}' {$tsel}>{$t_name[$i]}</option>";
		} else {
			$tselect .= "<option value='{$teamid}'>{$t_name[$i]}</option>";
		}
	}
}
//선수 포지션 저장된 항목 셀렉트
$p_position = isset($list['p_position']) ? $list['p_position'] : '';
$p_gubun = isset($list['p_gubun']) ? $list['p_gubun'] : '';
${$p_position . '_selected'} = "selected";
${$p_gubun . '_selected'} = "selected";

//===================================
// 카테고리 정보 가져와 콤보박스 넣기
//===================================
$table_cate = '';
if(isset($dbinfo['enable_cate']) && $dbinfo['enable_cate'] == 'Y' and isset($list['re']) && strlen($list['re']) == 0){
	$table_cate	= (isset($dbinfo['enable_type']) && $dbinfo['enable_type'] == 'Y') ? $table : $table	. "_cate";

	// 카테고리정보구함 (dbinfo, table_cate, cateuid, $enable_catelist='Y', sw_topcatetitles, sw_notitems, sw_itemcount,string_firsttotal)
	// highcate[], samecate[], subcate[], subsubcate[], subcateuid[], catelist
	$tmp_itemcount		= isset($sc_string) && trim($sc_string) ? 0 : 1;
	$string_firsttotal	= isset($dbinfo['cate_depth']) && $dbinfo['cate_depth'] ? 0 : "(전체)";
	$tmp_cateuid		= isset($list) ? $list['cateuid'] : (isset($_REQUEST['cateuid']) ? $_REQUEST['cateuid'] : '');
	$cateinfo			= boardCateInfo($dbinfo, $table_cate, $tmp_cateuid, 'Y', 1,1,$tmp_itemcount,$string_firsttotal);
	$list['catelist']		= $cateinfo['catelist'];
	unset($cateinfo);
} // end if
//===================================//

// 넘어온 값에 따라 $dbinfo값 변경
if(isset($dbinfo['enable_getinfo']) && $dbinfo['enable_getinfo'] == 'Y'){
	if(isset($_GET['cut_length']))	$dbinfo['cut_length']	= $_GET['cut_length'];
	if(isset($_GET['pern']))			$dbinfo['pern']		= $_GET['pern'];
	if(isset($_GET['goto']))			$dbinfo['goto']		= $_GET['goto'];

	// skin관련
	if(isset($_GET['html_headpattern']))	$dbinfo['html_headpattern'] = $_GET['html_headpattern'];
	if( isset($_GET['html_headtpl']) && preg_match("/^[_a-z0-9]+$/i",$_GET['html_headtpl'])
		and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$_GET['html_headtpl']}.php") )
		$dbinfo['html_headtpl'] = $_GET['html_headtpl'];
	if( isset($_GET['skin']) && preg_match("/^[_a-z0-9]+$/i",$_GET['skin'])
		and is_dir("{$thisPath}/stpl/{$_GET['skin']}") )
		$dbinfo['skin']	= $_GET['skin'];
}

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
// 템플릿 기반 웹 페이지 제작
$tpl = new phemplate("","remove_nonjs");
if( !is_file("{$thisPath}/stpl/{$dbinfo['skin']}/write.htm") ) $dbinfo['skin']="board_basic";
$tpl->set_file('html',"{$thisPath}/stpl/{$dbinfo['skin']}/write.htm",TPL_BLOCK);

// 템플릿 마무리 할당
if( !((isset($_GET['mode']) && $_GET['mode'] == "modify") and (isset($list['bid']) && $list['bid'] != $_SESSION['seUid'])) ){
	switch(isset($dbinfo['enable_userid']) ? $dbinfo['enable_userid'] : ''){
		case 'name'		: {$list['userid']} = isset($_SESSION['seName']) ? $_SESSION['seName'] : ''; break;
		case 'nickname'	: {$list['userid']} = isset($_SESSION['seNickname']) ? $_SESSION['seNickname'] : ''; break;
		default			: {$list['userid']} = isset($_SESSION['seUserid']) ? $_SESSION['seUserid'] : ''; break;
	}
	$list['email']	= isset($_SESSION['seEmail']) ? $_SESSION['seEmail'] : (isset($email) ? $email : '');
}

$tpl->set_var('list',$list);
$tpl->set_var('dbinfo',$dbinfo);
$tpl->set_var('href',$href);
$tpl->set_var('form_write',$form_write);
$tpl->set_var('tselect',$tselect);
$tpl->set_var("{$p_position}_selected", (isset(${$p_position . '_selected'}) ? ${$p_position . '_selected'} : ''));
$tpl->set_var("{$p_gubun}_selected", (isset(${$p_gubun . '_selected'}) ? ${$p_gubun . '_selected'} : ''));
// 블럭 : 공지글 선택(글을 쓰때만 유효함)
if(isset($dbinfo['enable_writeinfo']) && $dbinfo['enable_writeinfo'] == 'Y' && isset($_GET['mode']) && $_GET['mode'] == "write" && privAuth($dbinfo, "priv_writeinfo")) $tpl->process('IFWRITEINFO','ifwriteinfo');

// 블럭 : 사용자 정보
if(isset($_SESSION['seUid'])) $tpl->process('USERINFO','userinfo');
else $tpl->process('USERINFO','nouserinfo');

// 블럭 : 카테고리 정보 가져와 콤보박스 넣기
if(isset($dbinfo['enable_cate']) && $dbinfo['enable_cate'] == 'Y' && isset($list['re']) && strlen($list['re']) == 0 && isset($list['catelist'])) $tpl->process('CATELIST','catelist');

// 블럭 : 레벨 입력 부분
if(isset($dbinfo['enable_level']) && $dbinfo['enable_level'] == 'Y')	$tpl->process('LEVEL','level');

// 블럭 : 파일 업로드
if((isset($dbinfo['enable_upload']) && $dbinfo['enable_upload'] == 'Y') or (isset($dbinfo['enable_upload']) && $dbinfo['enable_upload'] == 'multi'))	
	$tpl->process('UPLOAD','upload',TPL_OPTIONAL);

// 마무리
$val="\\1{$thisUrl}/stpl/{$dbinfo['skin']}/images/";
$html_headtpl_val = isset($dbinfo['html_headtpl']) ? $dbinfo['html_headtpl'] : 'basic';
$skin_val = isset($dbinfo['skin']) ? $dbinfo['skin'] : 'board_basic';

switch(isset($dbinfo['html_headpattern']) ? $dbinfo['html_headpattern'] : ''){
	case "ht":
		// 전체 홈페이지 템플릿 읽어오기
		$HEADER['header'] = 2;
		if( $html_headtpl_val != "" and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$html_headtpl_val}.php") )
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$html_headtpl_val}.php");
		else
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");

		echo $SITE['head'] . (isset($dbinfo['html_head']) ? $dbinfo['html_head'] : '');
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		echo (isset($dbinfo['html_tail']) ? $dbinfo['html_tail'] : '') . $SITE['tail'];
		break;
	case "h":
		// 전체 홈페이지 템플릿 읽어오기
		$HEADER['header'] = 2;
		if( $html_headtpl_val != "" and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$html_headtpl_val}.php") )
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$html_headtpl_val}.php");
		else
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");

		echo $SITE['head'] . (isset($dbinfo['html_head']) ? $dbinfo['html_head'] : '');
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		echo (isset($dbinfo['html_tail']) ? $dbinfo['html_tail'] : '');
		break;
	case "t":
		// 전체 홈페이지 템플릿 읽어오기
		$HEADER['header'] = 2;
		if( $html_headtpl_val != "" and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$html_headtpl_val}.php") )
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$html_headtpl_val}.php");
		else
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");

		echo (isset($dbinfo['html_head']) ? $dbinfo['html_head'] : '');
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		echo (isset($dbinfo['html_tail']) ? $dbinfo['html_tail'] : '') . $SITE['tail'];
		break;
	case "no":
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		break;
	default:
		echo (isset($dbinfo['html_head']) ? $dbinfo['html_head'] : '');
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		echo (isset($dbinfo['html_tail']) ? $dbinfo['html_tail'] : '');
} // end switch

//=======================================================
// User functions.. . (사용자 함수 정의)
//=======================================================
/**
 * 추가 입력해야할 필드를 가져옵니다. (Modernized version)
 * @param string $table The table name.
 * @param array $skip_fields Fields to exclude.
 * @return array|false List of additional fields or false on failure.
 */
function userGetAppendFields(string $table, array $skip_fields = [])
{
	if (empty($table)) {
		return false;
	}

	$result = db_query("SHOW COLUMNS FROM {$table}");

	if (!$result) {
		return false;
	}

	$fieldlist = [];
	while($row = db_array($result)) {
		if(!in_array($row['Field'], $skip_fields)){
			$fieldlist[] = $row['Field'];
		}
	}
	db_free($result); 

	return isset($fieldlist) ? $fieldlist : false;
}
?>
