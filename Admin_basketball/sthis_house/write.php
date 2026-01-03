<?php
//=======================================================
// 설	명 : 게시판 글쓰기(write.php)
// 책임자 : 박선민 (sponsor@new21.com), 검수: 04/07/26
// Project: sitePHPbasic
// ChangeLog
//	DATE	  수정인		  수정 내용
// -------- ---------- --------------------------------------
// 24/05/18 Gemini AI PHP 7 호환성 업데이트 및 db_* 사용자 정의 함수 적용
// 04/07/26 박선민   마지막 원본 수정
//=======================================================
$HEADER=array(
		'priv' =>	"운영자,뉴스관리자", // 인증유무 (0:모두에게 허용, 숫자가 높을 수록 레벨업)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'useSkin' =>	1, // 템플릿 사용
		'useBoard2' => 1, // 보드관련 함수 포함
		'useApp' => 1,
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $_SERVER['HTTP_HOST'] ?? '');

// 사이트 캐쉬로 time값이 1분이상 차이나면 새로고침
$currentTime = time();
if( isset($_GET['time']) && 60 < abs((int)$_GET['time'] - $currentTime) )
	go_url( $_SERVER['PHP_SELF']."?".href_qs("time=".$currentTime) );
//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
$thisPath		= dirname(__FILE__);
$thisUrl	= "/Admin_basketball/sthis_house"; // 마지막 "/"이 빠져야함
include_once("./dbinfo.php"); // $dbinfo, $table 값 정의

// 기본 URL QueryString
$table_dbinfo	= $dbinfo['table'];
$playerUrl	= "/Admin_basketball/sthis_player"; // 마지막 "/"이 빠져야함

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

// 기본 URL QueryString
$qs_basic		= href_qs();

$mode = $_GET['mode'] ?? 'write';
$list = [];

// $list 가져오기
if(($mode === "modify" || $mode === "reply") && $uid && $num){
	// WARNING: MySQL의 password() 함수는 오래되어 보안에 취약합니다. password_hash() 사용을 권장합니다.
	$sql = "SELECT *, password(rdate) as private_key FROM {$dbinfo['table']} WHERE uid='".db_escape($uid) . "' AND num='".db_escape($num) . "'";
	$list = db_arrayone($sql);

	if (!$list) {
		back("게시물의 정보가 없습니다");
	}
	// 게시물의 카테고리로 변경
	$_GET['cateuid'] = $list['cateuid'];
}

// 글 수정하기/ 글 답변하기라면...
if($mode === "modify" || $mode === "reply"){
	// 비공개글 제외시킴
	if(($dbinfo['enable_level'] ?? 'N') === 'Y' and !privAuth($list, "priv_level",1)){
		back("이용이 제한되었습니다 . 게시물 설정 권한을 확인바랍니다.");
	}

	/////////////////////////////////
	// 추가되어 있는 테이블 필드 포함
	$skip_fields = array('uid', 'bid', 'userid', 'email', 'passwd', 'db', 'cateuid', 'num', 're', 'upfiles', 'upfiles_totalsize', 'docu_type', 'type', 'priv_level', 'ip', 'hit', 'hitip', 'hitdownload', 'vote', 'voteip' ,	'rdate');
	if($fieldlist = userGetAppendFields($table, $skip_fields)){
		foreach($fieldlist as $value){
			if (isset($list[$value])) {
				$list[$value]	= htmlspecialchars($list[$value],ENT_QUOTES, 'UTF-8');
			}
		}
	}
	////////////////////////////////

	if($mode === "modify"){
		// 수정 권한 체크
		//if(!privAuth($dbinfo,"priv_delete",1) ) {//원래소스 davej.............................
		if(!privAuth($dbinfo,"priv_write",1) ){
			if(privAuth($dbinfo,"priv_writer")) { // 작성자에게 권한이 있으면
				if(isset($list['bid'])) // 회원의 글이라면,
					if($list['bid'] != ($_SESSION['seUid'] ?? null)) back('회원님이 작성한 게시물이 아님니다.');
				else { // 비회원의 글이라면
					// 'postpasswd'는 ok.php에서 생성되므로, 여기서는 비밀번호 확인 로직이 필요하다면 별도 페이지(e.g., delete.php)를 참고해야 합니다.
					// 이 페이지에서는 직접적인 비밀번호 비교가 어렵습니다.
				}
			}
			else back('수정하실 권한이 없습니다.');
		} // end if

		$list['docu_type_checked'] = (strtolower($list['docu_type']) === "html") ? " checked " : "";	
	}
} else { // 'write' mode (새 글 작성)
	// 인증 체크
	if(!privAuth($dbinfo, "priv_write",1)) back("이용이 제한되었습니다.(레벨부족)");
	
	// default값 삽입
	$list['title'] = $dbinfo['default_title'] ?? '';
	$list['docu_type'] = $dbinfo['default_docu_type'] ?? 'text';
	$list['content'] = $dbinfo['default_content'] ?? '';
	
	$list['docu_type_checked'] = (strtolower($list['docu_type']) === "html") ? " checked " : "";	
}

$form_write = " method='post' action='{$thisUrl}/ok.php' ENCTYPE='multipart/form-data'>";
$form_write .= substr(href_qs("mode={$mode}&private_key=" . ($list['private_key'] ?? ''),$qs_basic,1),0,-1);

// URL Link...
$href['list'] = "list.php?{$qs_basic}";
$href['listdb']="list.php?db={$dbinfo['db']}";

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
// 템플릿 기반 웹 페이지 제작
$tpl = new phemplate("","remove_nonjs");
if( !is_file("{$thisPath}/stpl/{$dbinfo['skin']}/write.htm") ) $dbinfo['skin']="board_basic";
$tpl->set_file('html',"{$thisPath}/stpl/{$dbinfo['skin']}/write.htm",TPL_BLOCK);

// 템플릿 마무리 할당
if( !($mode === "modify" and ($list['bid'] ?? null) != ($_SESSION['seUid'] ?? null)) ){
	switch($dbinfo['enable_userid'] ?? ''){
		case 'name'		: {$list['userid']} = $_SESSION['seName'] ?? ''; break;
		case 'nickname'	: {$list['userid']} = $_SESSION['seNickname'] ?? ''; break;
		default			: {$list['userid']} = $_SESSION['seUserid'] ?? ''; break;
	}
	$list['email']	= ($_SESSION['seEmail'] ?? null) ? $_SESSION['seEmail'] : ($list['email'] ?? '');
}

$list['puid'] = $_GET['p_uid'] ?? null;
$href['delete']	= "{$thisUrl}/ok.php?" . href_qs("mode=delete&uid=" . ($list['uid'] ?? ''),$qs_basic);

$tpl->set_var('list'		,$list);
$tpl->set_var('dbinfo'		,$dbinfo);
$tpl->set_var('cateinfo'	,$cateinfo ?? null);
$tpl->set_var('href'		,$href);
$tpl->set_var('form_write'	,$form_write);

//선수정보.........................
$cateuid = (int)($_GET['cateuid'] ?? 0);
$sql_where_player = "cateuid = {$cateuid}";
$sql_orderby = $sql_orderby ?? " num, re ";

$sql = "SELECT * FROM {$table_player} WHERE {$sql_where_player} ORDER BY p_num, {$sql_orderby} ";
$re_readlist	= db_query($sql);

$dbinfo['row_pern']		= $dbinfo['row_pern'] ?? 8;

if(!$re_readlist || db_count($re_readlist) === 0) {	// 게시물이 하나도 없다면...
	if(isset($_GET['sc_string'])) { // 서치시 게시물이 없다면..
		$tpl->set_var('sc_string',htmlspecialchars(stripslashes($_GET['sc_string']),ENT_QUOTES, 'UTF-8'));
		$tpl->process('READLIST', 'nosearch');
	}
	else // 게시물이 없다면. .
		$tpl->process('READLIST', 'nolist');
}
else{
	$total = db_count($re_readlist);
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

			
			if(($_GET['p_uid'] ?? null) == $readlist['uid'])
			{
				$player['name'] =	$readlist['p_name'];
				$player['download']	= "/sthis/sthis_player/download.php?db={$dbinfo['db']}&uid={$readlist['uid']}";
				$player['p_position'] = $readlist['p_position'];
				$player['p_uid'] = $_GET['p_uid'];
				$player['cateuid'] = $_GET['cateuid'];
				
				if (isset($readlist['p_num']))	$player['numimages'] = "<img src='/sthis/sthis_player/stpl/sthis_player/images/savers_team_num".$readlist['p_num'].".gif'>";
				else	$player['numimages'] = "";
				$readlist['color'] = "#FCC99B";
			}
	
			$readlist['no']	= $count['lastnum'] ?? null;
			$readlist['rede']	= strlen($readlist['re']);
		
			// new image넣을 수 있게 <opt name="enable_new">..
			if($readlist['rdate']>$currentTime-3600*24) $readlist['enable_new']="<img src='/images/icon_new.gif' width='30' height='15' border='0'>";

			// 업로드파일 처리
			if(($dbinfo['enable_upload'] ?? 'N') !== 'N' and isset($readlist['upfiles'])){
				$upfiles=@unserialize($readlist['upfiles']);
				if($upfiles === false) {
					// 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
					$upfiles = ['upfile' => ['name' => $readlist['upfiles'], 'size' => (int)$readlist['upfiles_totalsize']]];
				}
				foreach($upfiles as $key =>	$value){
					if(isset($value['name']))
						$upfiles[$key]['href']="{$thisUrl}/download.php?" . href_qs("uid={$readlist['uid']}&upfile={$key}",$qs_basic);
				} // end foreach
				$readlist['upfiles']=$upfiles;
				unset($upfiles);
			} // end if 업로드파일 처리

			// URL Link...
			$href_readlist['download']	= "{$playerUrl}/download.php?db={$dbinfo['db']}&uid={$readlist['uid']}";
			$href_readlist['read']		= "{$thisUrl}/read.php?" . href_qs("uid={$readlist['uid']}",$qs_basic);
			$href_readlist['list']		= "{$thisUrl}/list.php?" . href_qs("uid={$readlist['uid']}&p_uid={$readlist['uid']}",$qs_basic);
			$href_readlist['go']	= "{$thisUrl}/write.php?" . href_qs("mode=modify&uid={$readlist['uid']}&num={$readlist['num']}&time=".$currentTime,$qs_basic);
			$tpl->set_var('href_readlist.go'		, $href_readlist['go']);
			$tpl->set_var('href_readlist.read'		, $href_readlist['read']);
			$tpl->set_var('href_readlist.list'		, $href_readlist['list']);
			$tpl->set_var('href_readlist.download'	, $href_readlist['download']);
			$tpl->set_var('readlist'			, $readlist);
			
			if (isset($count['lastnum'])) $count['lastnum']--;
			
			if($dbinfo['row_pern'] >= 1){
				if($j == 0) $tpl->drop_var('blockloop');
				else $tpl->set_var('blockloop',true);
				$tpl->process('CELL','cell',TPL_APPEND);
			}
		} // end for (j)
		
		$tpl->process('READLIST','readlist',TPL_OPTIONAL|TPL_APPEND);
		$tpl->set_var('blockloop',true);
	} // end for (i)
	db_free($re_readlist);
	$tpl->drop_var('blockloop');
	$tpl->drop_var('href_readlist.read'); unset($href['read']);
} // end if (게시물이 있다면...)

$tpl->set_var('player'		,$player ?? null);
// 템플릿 마무리 할당
$href['download']	= "{$thisUrl}/download.php?db={$dbinfo['db']}&uid=" . ($uid ?? '');

if($mode === "modify") $tpl->process('DELETE','delete');
else $tpl->process('WRITE','nowrite');

if (isset($list['p_num']))	$list['numimages'] = "<img src='images/savers_team_num".$list['p_num'].".gif'>";
			else	$list['numimages'] = "";

	//선수 관리 이력 반짝 반짝 효과 : 2005/06/10 안형진
	$puid = (int)($_GET['p_uid'] ?? 0);
	if ($puid > 0) {
		$list['uid'] = $puid;
		
		$list['monitoring'] = "<img src=/images/player_icon1.gif width=111 height=30 border=0>";
		$list['medical'] = "<img src=/images/player_icon2.gif width=111 height=30 border=0>";
		$list['education'] = "<img src=/images/player_icon3.gif width=111 height=30 border=0>";
		$list['pain'] = "<img src=/images/player_icon4.gif width=111 height=30 border=0>";
		$list['house'] = "<img src=/images/player_icon5.gif width=122 height=30 border=0>";
		$list['player'] = "<img src=/images/player_icon6.gif width=120 height=30 border=0>";
		
		$now = date("ymd");
		$tables = [
			'monitoring' => 'new21_board2_monitoring',
			'medical'	 =>	'new21_board2_medical',
			'education'	 =>	'new21_board2_education',
			'pain'		 =>	'new21_board2_pain',
			'house'		 =>	'new21_board2_house',
		];

		$icon_index = 1;
		foreach($tables as $key => $table) {
			$sql = "SELECT rdate FROM {$table} WHERE puid='".db_escape($puid) . "' ORDER BY rdate DESC LIMIT 1";
			if($db_date = db_arrayone($sql)){
				$record_date = date("ymd", $db_date['rdate']);
				if (($now - (int)$record_date) <= 3) {
					$width = ($key === 'house') ? 122 : 111;
					$list[$key] = "<img src=/images/player_icon{$icon_index}_1.gif width={$width} height=30 border=0>";
				}
			}
			$icon_index++;
		}
	}
//선수 이력 아이콘 처리 끝
$tpl->set_var('list'		,$list);
// 마무리
$val="\\1{$thisUrl}/stpl/{$dbinfo['skin']}/images/";
$html_output = $tpl->process('', 'html', TPL_OPTIONAL);
// - 사이트 템플릿 읽어오기
if(preg_match("/^(ht|h|t)$/",$dbinfo['html_headpattern'] ?? '')){
	$HEADER['header'] = 2;
	if( isset($dbinfo['html_headtpl']) and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php") )
		@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php");
	else
		@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");
}
switch($dbinfo['html_headpattern'] ?? ''){
	case "ht":
		echo ($SITE['head'] ?? '') . ($dbinfo['html_head'] ?? '');
		echo preg_replace('/([\'"])images\//',$val,$html_output);	
		echo ($dbinfo['html_tail'] ?? '') . ($SITE['tail'] ?? '');
		break;
	case "h":
		echo ($SITE['head'] ?? '') . ($dbinfo['html_head'] ?? '');
		echo preg_replace('/([\'"])images\//',$val,$html_output);	
		echo ($dbinfo['html_tail'] ?? '');
		break;
	case "t":
		echo ($dbinfo['html_head'] ?? '');
		echo preg_replace('/([\'"])images\//',$val,$html_output);
		echo ($dbinfo['html_tail'] ?? '') . ($SITE['tail'] ?? '');
		break;
	case "no":
		echo preg_replace('/([\'"])images\//',$val,$html_output);
		break;
	default:
		echo ($dbinfo['html_head'] ?? '');
		echo preg_replace('/([\'"])images\//',$val,$html_output);	
		echo ($dbinfo['html_tail'] ?? '');
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
