<?php
//=======================================================
// 설	명 : 게시판 처리(ok.php)
// 책임자 : 박선민 (sponsor@new21.com), 검수: 04/01/03
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인			수정 내용
// -------- ------ --------------------------------------
// 03/03/06 박선민 delete_ok() 버그 수정
// 03/11/13 박선민 마지막 수정
// 03/12/08 박선민 추가 필드, userGetAppendFields()
// 04/01/03 박선민 심각한 간단 버그수정
//=======================================================
// 앞으로 : 게시물 삭제시 메모로 삭제되도록...
$HEADER=array(
	'priv' =>	"운영자,뉴스관리자", // 인증유무 (비회원,회원,운영자,서버관리자)
	'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
	'useCheck' => 1, // check_email()
	'useBoard2' => 1, // 보드관련 함수 포함
	'useApp' => 1,
	'useImage' => 1, // thumbnail()
	'useClassSendmail' =>	1
);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
page_security("", $_SERVER['HTTP_HOST']);

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
$thisPath	= dirname(__FILE__);
$thisUrl	= "/Admin_basketball/totalgame_result"; // 마지막 "/"이 빠져야함
include_once("./dbinfo.php"); // $dbinfo, $table 값 정의

// 기본 URL QueryString
$qs_basic = "db=" . ($_REQUEST['db'] ?? $table) .			//table 이름
			"&mode=" . ($_REQUEST['mode'] ?? '') .		// mode값은 list.php에서는 당연히 빈값
			"&cateuid=" . ($_REQUEST['cateuid'] ?? '') .		//cateuid
			"&team=" . ($_REQUEST['team'] ?? '') .				// 페이지당 표시될 게시물 수
			"&pern=" . ($_REQUEST['pern'] ?? '') .				// 페이지당 표시될 게시물 수
			"&sc_column=" . ($_REQUEST['sc_column'] ?? '') .	//search column
			"&sc_string=" . urlencode(stripslashes(isset($sc_string) ? $sc_string : '')) . //search string
			"&team=" . ($_REQUEST['team'] ?? '').
			"&html_headtpl=" . (isset($html_headtpl) ? $html_headtpl : '').
			"&page=" . ($_REQUEST['page'] ?? '');

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

// 공통적으로 사용할 $qs
$qs=array(
		"bid" =>	"post,trim",
		"userid" =>	"post,trim",
		"email" =>	"post,trim",
		"passwd" =>	"post,trim",
		"db" =>	"post,trim",
		"cateuid" =>	"post,trim",
		"num" =>	"post,trim",
		"re" =>	"post,trim",
		"title" =>	"post,trim",
		"content" =>	"post,trim",
		"data1" =>	"post,trim",
		"data2" =>	"post,trim",
		"data3" =>	"post,trim",
		"data4" =>	"post,trim",
		"data5" =>	"post,trim",
		"upfiles" =>	"post,trim",
		"upfiles_totalsize" =>	"post,trim",
		"docu_type" =>	"post,trim",
		"type" =>	"post,trim",
		"priv_level" =>	"post,trim",
		"ip" =>	"post,trim",
		"hit" =>	"post,trim",
		"hitdownload" =>	"post,trim",
		"hitip" =>	"post,trim",
		"vote" =>	"post,trim",
		"rdate" =>	"post,trim",
		"sid" =>	"post,trim",
		"tr_season" =>	"post,trim",
		"tr_ranking" =>	"post,trim",
		"tr_game" =>	"post,trim",
		"tr_win" =>	"post,trim",
		"tr_loss" =>	"post,trim",
		"tr_score" =>	"post,trim",
		"tr_2p1" =>	"post,trim",
		"tr_2p2" =>	"post,trim",
		"tr_3p1" =>	"post,trim",
		"tr_3p2" =>	"post,trim",
		"tr_free1" =>	"post,trim",
		"tr_free2" =>	"post,trim",
		"tr_re" =>	"post,trim",
		"tr_as" =>	"post,trim",
		"tr_st" =>	"post,trim",
		"tr_blk" =>	"post,trim",
		"tr_to" =>	"post,trim",
		"tr_po" =>	"post,trim"
	);

//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
// mode값에 따른 함수 호출
switch($_REQUEST['mode']){
	case 'write':
		$uid = write_ok($table, $qs);
		go_url($_REQUEST['goto'] ? $_REQUEST['goto'] : "{$thisUrl}/list.php?" . href_qs("uid={$uid}",$qs_basic));
		break;
	case 'modify':
		$uid = $_POST['uid'] ?? $uid;
		modify_ok($table,$qs,"uid");
		go_url($_REQUEST['goto'] ? $_REQUEST['goto'] : "{$thisUrl}/list.php?" . href_qs("uid={$uid}",$qs_basic));
		break;
	case 'delete':
		$goto = $_REQUEST['goto'] ? $_REQUEST['goto'] : "{$thisUrl}/list.php?" . href_qs("",$qs_basic);
		delete_ok($table,"uid",$goto);
		go_url($goto);
		break;
	default :
		back("잘못된 웹 페이지에 접근하였습니다");
} // end switch
//=======================================================
// User functions... (사용자 함수 정의)
//=======================================================
function write_ok($table, $qs){
	global $dbinfo, $db_conn, $_SESSION;
	if(!privAuth($dbinfo, "priv_write")) back("이용이 제한되었습니다(레벨부족). 확인바랍니다.");

	$qs['writeinfo'] = "post,trim";
	// 넘어온값 체크
	$qs=check_value($qs);

	if(isset($qs['docu_type']) and strtolower($qs['docu_type']) != "html") $qs['docu_type']="text";
	$qs['priv_level']=(int)($qs['priv_level'] ?? 0);
	if(isset($qs['catelist'])) $qs['cateuid'] = $qs['catelist'];

	// 값 추가
	if(isset($_SESSION['seUid'])){
		$qs['bid']	= $_SESSION['seUid'];
		switch($dbinfo['enable_userid']){
			case 'name'		: $qs['userid'] = $_SESSION['seName']; break;
			case 'nickname'	: $qs['userid'] = $_SESSION['seNickname']; break;
			default			: $qs['userid'] = $_SESSION['seUserid']; break;
		}
		$qs['email']	= $_SESSION['seEmail'];
	} else {
		$qs['email']	= check_email($qs['email'] ?? '');
	}
	$qs['ip']		= remote_addr();
	// - num의 최대값 구함
	$sql_where = '';
	if($dbinfo['table_name'] != $dbinfo['db']) $sql_where=" db='{$dbinfo['db']}' "; // $sql_where 사용 시작
	if(empty($sql_where)) $sql_where= " 1 ";
	$sql = "SELECT max(num) FROM {$table} WHERE  $sql_where ";
	$qs['num'] = db_resultone($sql,0,"max(num)") + 1;

	/////////////////////////////////
	// 추가되어 있는 테이블 필드 포함
	$sql_set = ''; // 변수 초기화
	$skip_fields = array('uid', 'bid', 'userid', 'email', 'passwd', 'db', 'cateuid', 'num', 're', 'title', 'content', 'upfiles', 'upfiles_totalsize', 'docu_type', 'type', 'priv_level', 'ip', 'hit', 'hitip', 'hitdownload', 'vote', 'voteip', 'rdate');
	if($fieldlist = userGetAppendFields($table, $skip_fields)){
		foreach($fieldlist as $value){
			if(isset($_POST[$value])) $sql_set .= ", `{$value}` = '" . $_POST[$value] . "' ";
		}
	}
	////////////////////////////////
	
	$qs['rdate'] = strtotime($qs['rdate']);
	
	$qs['tg_year'] = date("Y",$qs['rdate']);
	$qs['tg_month'] = date("m",$qs['rdate']);
	$qs['tg_day'] = date("d",$qs['rdate']);
//	$qs['tr_season'] = substr($qs['data1'], -2).$qs['data2'];//davej...........시즌순 정렬때문에....

	// sql문 완성
	if($dbinfo['enable_type'] == 'Y' and ($qs['writeinfo'] ?? '') == "info") $sql_set	.= ", `type`='info' ";// $sql_set 시작
	$sql="INSERT INTO {$table}
			SET
				`bid`		='{$qs['bid']}',
				`userid`	='{$qs['userid']}',
				`title`		='{$qs['title']}',
				`data1`		='{$qs['data1']}',
				`data2`		='{$qs['data2']}'
				{$sql_set}
		";
	db_query($sql);
	$uid = db_insert_id();

	return $uid;
} // end func.

function modify_ok($table,$qs,$field){
	global $dbinfo, $_SESSION;
	$qs["{$field}"]	= "post,trim,notnull=" . urlencode("고유번호가 넘어오지 않았습니다");
	// 넘어온값 체크
	$qs=check_value($qs);

	if(isset($qs['docu_type']) and strtolower($qs['docu_type']) != "html") $qs['docu_type']="text";
	$qs['priv_level']=(int)($qs['priv_level'] ?? 0);

	// 수정 권한 체크와 해당 게시물 읽어오기
	if(privAuth($dbinfo,"priv_delete")) // 게시판 전체 삭제 권한을 가졌다면 수정 권한 무조건 부여
		$sql = "SELECT * FROM {$table} WHERE uid='{$qs['uid']}'";
	elseif(isset($_SESSION['seUid'])) // 회원의 글이라면,
		$sql = "SELECT * FROM {$table} WHERE uid='{$qs['uid']}' and bid='{$_SESSION['seUid']}'";
	else { // 비회원의 글이라면 (비회원의 글에 패스워드가 없을 경우 누구든지 수정 가능, 실수로 안 입력했을 경우 수정가능하게)
		$sql = "SELECT * FROM {$table} WHERE uid='{$qs['uid']}' and passwd=password('{$qs['passwd']}')";
	} // end if
	if(!$list=db_arrayone($sql)) back("게시물이 없거나 수정할 권한이 없습니다");
		
	// 값 추가
	if($list['bid'] == $_SESSION['seUid']){
		switch($dbinfo['enable_userid']){
			case 'name'		: $qs['userid'] = $_SESSION['seName']; break;
			case 'nickname'	: $qs['userid'] = $_SESSION['seNickname']; break;
			default			: $qs['userid'] = $_SESSION['seUserid']; break;
		}
		$qs['email']	= $_SESSION['seEmail'];
	} else {
		$qs['userid']	= $list['userid'];
		$qs['email']	= isset($qs['email']) ? check_email($qs['email']): $list['email']; // email값이 넘어오면 수정하고 아니면 그대로 유지
	}
	$qs['ip']		= remote_addr();
	$qs['cateuid']= ( isset($qs['catelist']) and strlen($list['re']) == 0 ) ? $qs['catelist'] : $list['cateuid']; // 답변이 아닌 경우에만 카테고리 수정 가능
	
	$qs['rdate'] = strtotime($qs['rdate']);
	
	$qs['tg_year'] = date("Y",$qs['rdate']);
	$qs['tg_month'] = date("m",$qs['rdate']);
	$qs['tg_day'] = date("d",$qs['rdate']);
//	$qs['tr_season'] = substr($qs['data1'], -2).$qs['data2']; //davej...........시즌순 정렬때문에....

	/////////////////////////////////
	// 추가되어 있는 테이블 필드 포함
	$sql_set = ''; // 변수 초기화
	$skip_fields = array('uid', 'bid', 'userid', 'email', 'passwd', 'db', 'cateuid', 'num', 're', 'title', 'content', 'upfiles', 'upfiles_totalsize', 'docu_type', 'type', 'priv_level', 'ip', 'hit', 'hitip', 'hitdownload', 'vote', 'voteip', 'rdate');
	if($fieldlist = userGetAppendFields($table, $skip_fields)){
		foreach($fieldlist as $value){
			if(isset($_POST[$value])) $sql_set .= ", `{$value}` = '" . $_POST[$value] . "' ";
		}
	}
	////////////////////////////////
	$sql="UPDATE {$table}
			SET
				`bid`		='{$qs['bid']}',
				`userid`	='{$qs['userid']}',
				`cateuid`	='{$qs['cateuid']}',
				`re`		='{$qs['re']}',
				`title`		='{$qs['title']}',
				`data1`		='{$qs['data1']}',
				`data2`		='{$qs['data2']}'
				{$sql_set}
			WHERE
				uid={$qs['uid']}
		";
	db_query($sql);

	// 만일 카테고리가 변경되었다면, 그 이하 답변글들 역시 cateuid값 변경함
	if( $qs['cateuid'] <> $list['cateuid'] ){
		db_query("update {$table} set cateuid='{$qs['cateuid']}' where db='{$list['db']}' and type='{$list['type']}' and num='{$list['num']}'");
	} // end if
	
	return true;
} // end func.
// 삭제
function delete_ok($table,$field,$goto){
	global $dbinfo,$thisUrl, $_SESSION;
	$qs=array(
			"$field" =>	"request,trim,notnull=" . urlencode("고유넘버가 넘어오지 않았습니다."),
		);
	$qs=check_value($qs);

	// 권한 확인
	if(!privAuth($dbinfo,"priv_delete")) {
		back("삭제할 권한이 없습니다.");
	}
	
	db_query("DELETE FROM {$table} where uid='{$qs['uid']}'");
	
	return true;
} // end func delete_ok()
// 카테고리 새서브 RE값 구함
// 03/10/12
function userReplyRe($table, $num, $re){
	global $dbinfo;

	// 한 table에 여러 게시판 생성의 경우
	$sql_where = '';
	if (($dbinfo['table_name'] ?? '') != ($dbinfo['db'] ?? '')) {
		$sql_where = " db='{$dbinfo['db']}' ";
	}
	if (($dbinfo['enable_type'] ?? '') == 'Y') {
		$sql_where = $sql_where ? $sql_where . " and type='docu' " : " type='docu' ";
	}
	if (!$sql_where) {
		$sql_where = " 1 ";
	}

	$sql = "SELECT re, right(re,1) FROM {$table} WHERE $sql_where and num='{$num}' AND length(re)=length('{$re}')+1 AND locate('{$re}', re)=1 ORDER BY re DESC LIMIT 1";
	$row = db_arrayone($sql);

	if ($row) {
		$ord_head = substr($row['re'], 0, -1);
		if (ord($row['right(re,1)']) >= 255) {
			back("더이상 추가하실 수 없습니다");
		}
		$ord_foot = chr(ord($row['right(re,1)']) + 1);
		$re = $ord_head . $ord_foot;
	} else {
		$re .= "1";
	}
	return $re;
} // end func userReplyRe($table, $num, $re)

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
