<?php
//=======================================================
// 설 명 : 게시판 카테고리 처리(cateok.php)
// 책임자 : 박선민 , 검수: 05/01/27
// Project: sitePHPbasic
// ChangeLog
//	DATE	 수정인					수정 내용
// -------- ------ --------------------------------------
// 05/01/27 박선민 마지막 수정
//=======================================================
$HEADER=array(
	'usedb2' => 1, // DB 커넥션 사용
	'useApp' => 1, //
	'useCheck' => 1, // check_value()
);
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');
$thisPath	= dirname(__FILE__) .'/'; // 마지막이 '/'으로 끝나야함
$prefix 	= 'board2';
$thisUrl	= './'; // 마지막이 '/'으로 끝나야함

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
	
	global $SITE; // $conn은 사용되지 않으므로 제거

	// table
	$table_dbinfo = $SITE['th'].$prefix.'info';

	// boardinfo 테이블 정보 가져와서 $dbinfo로 저장
	$db_req = $_REQUEST['db'] ?? '';
	$sql = "SELECT * FROM {$table_dbinfo} WHERE db='".db_escape($db_req)."' LIMIT 1";
	$dbinfo = db_arrayone($sql) or back('사용하지 않는 카테고리입니다.');
	if(isset($dbinfo['enable_cate']) && $dbinfo['enable_cate'] != 'Y') back('카테고리 기능을 지원하지 않습니다.');

	// 인증 체크
	if(!privAuth($dbinfo, 'priv_catemanage')) back('이용이 제한되었습니다.(레벨부족)');

	// table
	$dbinfo['table'] = $SITE['th'].$prefix.'_'.$dbinfo['db']; // 테이블이름 가져오기
	$dbinfo['table_cate'] = $dbinfo['table'].'_cate';
	
	$sql_where_cate = ' 1 '; // init
//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
// mode값에 따른 함수 호출
$mode = $_REQUEST['mode'] ?? '';
$db_val = $dbinfo['db'] ?? '';
$cateuid_req = $_REQUEST['cateuid'] ?? '';

switch($mode){
	case 'catewrite' :
		$qs	= array(
				'cateuid' =>	'post,trim',
				'title' =>	'post,trim,notnull=' . urlencode('카테고리 제목을 입력하십시요')
			);
		$cateuid_new = cateWriteOK($dbinfo,$qs);
		if(!$cateuid_new) back('처리되지 않았습니다.');
		go_url(isset($_REQUEST['goto']) ? $_REQUEST['goto'] : "./cate.php?db={$db_val}&cateuid={$cateuid_new}");
		break;
	case 'catemodify' :
		$qs	= array(
				'cateuid' =>	'post,trim,notnull=' . urlencode('고유번호가 넘어오지 않았습니다'),
				'title' =>	'post,trim,notnull',
			);
		cateModifyOK($dbinfo,$qs,'uid');
		go_url(isset($_REQUEST['goto']) ? $_REQUEST['goto'] : "cate.php?db={$db_val}");
		break;
	case 'catedelete' :
		$goto = isset($_REQUEST['goto']) ? $_REQUEST['goto'] : "cate.php?db={$db_val}";
		cateDeleteOK($dbinfo,'uid',$goto);
		go_url($goto);
		break;
	case 'catesort' :
		$qs	= array(
				'db' =>	'post,trim,notnull=' . urlencode('db값이 넘어오지 않았습니다'),
				'srcuid' =>	'post,trim,notnull=' . urlencode('있어야할 값이 넘어오지 않았습니다'),
				'dstuid' =>	'post,trim,notnull=' . urlencode('있어야할 값이 넘어오지 않았습니다')
			);
		cateSortOk($dbinfo,$qs);
		echo ('
				<script language = "JavaScript">
					if(opener)
					{
						opener.location.reload();
						self.close();
					}
				</script>
		');
		exit;
	default :
		back('잘못된 웹페이지에 접근하였습니다.');
}

//=======================================================
// User functions... (사용자 함수 정의)
//=======================================================
function cateSortOk($dbinfo,$qs){
	global $sql_where_cate;

	global $SITE;
	
	$qs=check_value($qs);
	
	$srcuid_safe = db_escape($qs['srcuid'] ?? '');
	$sql = "select * from {$dbinfo['table_cate']} where uid='{$srcuid_safe}' and	{$sql_where_cate} ";
	$src = db_arrayone($sql) or back('해당 카테고리가 존재하지 않습니다');
	
	// 변경할 카테고리 uid 구해서 where절 uid in (..) 만듬
	$src_num_safe = db_escape($src['num'] ?? '');
	$src_re_safe = db_escape($src['re'] ?? '');
	$rs_srcuids=db_query("select * from {$dbinfo['table_cate']} where {$sql_where_cate} and num='{$src_num_safe}' and re like '{$src_re_safe}%'");
	$srcuids = array();
	if($rs_srcuids){
		while( $row=db_array($rs_srcuids) )
			$srcuids[]=$row['uid'];
		db_free($rs_srcuids);
	}
	
	$sql_where_srcuid_in = ' uid in (' . implode(',',$srcuids) . ') ';
	
	if(strlen($src['re'] ?? '')){
		if(($qs['dstuid'] ?? '') == 'first') { // 처음으로 이동한 경우 (re=ab라면 a보다 크고 ab보다 작은 범위를 1씩 증가후 re=a1으로 변경)
			$re_prefix = substr($src['re'],0,-1);
			$re_prefix_safe = db_escape($re_prefix);
			$sql = "select * from {$dbinfo['table_cate']} where {$sql_where_cate} and num='{$src_num_safe}' and re like '{$re_prefix_safe}_\' order by re LIMIT 1";
			$dst = db_arrayone($sql) or back('옮기고자 하는 카테고리 선택이 잘못되었습니다. err110');
		
			$src_length=strlen($src['re']);
			
			if(isset($dst['re']) && $dst['re'] == $re_prefix.'1' ) {
				$re_prefix_len_minus_1 = $src_length - 1;
				$src_length_plus_1 = $src_length + 1;
				$sql_update = "UPDATE {$dbinfo['table_cate']} SET re=concat( substring(re,1,{$re_prefix_len_minus_1}), char(ord(substring(re,{$src_length},1))+1 ), substring(re,{$src_length_plus_1}) ) where {$sql_where_cate} and num='{$src_num_safe}' and strcmp(re,'{$re_prefix_safe}')>0 and strcmp(re,'{$src_re_safe}')< 0";
				db_query($sql_update);
			}
			$sql_update_src = "UPDATE {$dbinfo['table_cate']} SET re=concat( substring(re,1,{$re_prefix_len_minus_1}), '1', substring(re,{$src_length}+1)) where {$sql_where_srcuid_in}";
			db_query($sql_update_src);
		} else {
			$dstuid_safe = db_escape($qs['dstuid'] ?? '');
			$re_prefix = substr($src['re'],0,-1);
			$re_prefix_safe = db_escape($re_prefix);
			$sql = "select * from {$dbinfo['table_cate']} where uid='{$dstuid_safe}' and num='{$src_num_safe}' and re like '{$re_prefix_safe}_\'";
			$dst = db_arrayone($sql) or back('옮기고자 하는 카테고리 선택이 잘못되었습니다. err118');
			
			$dst_re = $dst['re'] ?? '';
			$src_re = $src['re'] ?? '';
			
			if( strlen($src_re) != strlen($dst_re) ) back('카테고리 선택이 잘못되었습니다.');
			
			$src_length=strlen($src_re);
			$src_re_safe = db_escape($src_re);
			$dst_re_safe = db_escape($dst_re);

			if( strcmp($src_re,$dst_re) > 0 ){ // 상위로 이동할 경우 ( 목적위치+1이상에서 본래위치 미만 범위를 1씩 증가후 본래위치는 목적위치+1
				
				$dst_re_next=substr($dst_re,0,-1) . chr(ord(substr($dst_re,-1))+1);
				$dst_re_next_safe = db_escape($dst_re_next);
				$src_length_minus_1 = $src_length - 1;
				$src_length_plus_1 = $src_length + 1;
				
				$sql_update_range = "UPDATE {$dbinfo['table_cate']} SET re=concat( substring(re,1,{$src_length_minus_1}), char(ord(substring(re,{$src_length},1))+1 ), substring(re,{$src_length_plus_1}) ) where {$sql_where_cate} and num='{$src_num_safe}' and strcmp(re,'{$dst_re_next_safe}')>=0 and strcmp(re,'{$src_re_safe}')< 0 ";
				db_query($sql_update_range);
				
				$sql_update_src = "UPDATE {$dbinfo['table_cate']} SET re=concat( substring(re,1,{$src_length_minus_1}), right('{$dst_re_next_safe}',1), substring(re,{$src_length_plus_1})) where {$sql_where_srcuid_in}";
				db_query($sql_update_src);
			} elseif(strcmp($src_re,$dst_re) < 0) { // 하위로 이동할 경우 ( 본래위치+1이상에서 목적위치+1 미만 범위를 1씩 감소후 본래위치는 목적위치
				$src_re_next=substr($src_re,0,-1) . chr(ord(substr($src_re,-1))+1);
				$dst_re_next=substr($dst_re,0,-1) . chr(ord(substr($dst_re,-1))+1);
				
				$src_re_next_safe = db_escape($src_re_next);
				$dst_re_next_safe = db_escape($dst_re_next);
				$src_length_minus_1 = $src_length - 1;
				$src_length_plus_1 = $src_length + 1;
				
				$sql_update_range = "UPDATE {$dbinfo['table_cate']} SET re=concat( substring(re,1,{$src_length_minus_1}), char(ord(substring(re,{$src_length},1))-1 ), substring(re,{$src_length_plus_1}) ) where {$sql_where_cate} and num='{$src_num_safe}' and strcmp(re,'{$src_re_next_safe}')>= 0 and strcmp(re,'{$dst_re_next_safe}')<0 ";
				db_query($sql_update_range);
				
				$sql_update_src = "UPDATE {$dbinfo['table_cate']} SET re=concat( substring(re,1,{$src_length_minus_1}), right('{$dst_re_safe}',1) , substring(re,{$src_length_plus_1}) ) where {$sql_where_srcuid_in}";
				db_query($sql_update_src);
			}
		}
	} else { // re값이 없고 num값을 변경해야될 경우임
		if(($qs['dstuid'] ?? '') == 'first') { // 최상위로 이동할 경우
			$sql = "select * from {$dbinfo['table_cate']} where {$sql_where_cate} order by num LIMIT 1";
			$dst = db_arrayone($sql) or back('옮기고자 하는 카테고리 선택이 잘못되었습니다. 4');
			
			$dst_num = $dst['num'] ?? 0;
			$src_num = $src['num'] ?? 0;

			if(isset($dst['num']) && $dst_num == 1) db_query("UPDATE {$dbinfo['table_cate']} SET num=num+1 WHERE {$sql_where_cate} and num < ".db_escape($src_num));
			db_query("UPDATE {$dbinfo['table_cate']} SET num=1 WHERE {$sql_where_srcuid_in}");
		} else {
			$dstuid_safe = db_escape($qs['dstuid'] ?? '');
			$sql = "select * from {$dbinfo['table_cate']} where uid='{$dstuid_safe}' and re=''";
			$dst = db_arrayone($sql) or back('옮기고자 하는 카테고리 선택이 잘못되었습니다. 6');
			
			$dst_num = $dst['num'] ?? 0;
			$src_num = $src['num'] ?? 0;
			
			if($src_num > $dst_num){ // 상위로 이동할 경우 (dst[num]보다 크고 src[num] 미만범위를 1씩 증가후 src[num]=dst[num]+1로 변경
				db_query("UPDATE {$dbinfo['table_cate']} SET num=num+1 WHERE {$sql_where_cate} and num > ".db_escape($dst_num)." and num < ".db_escape($src_num));
				db_query("UPDATE {$dbinfo['table_cate']} SET num=".db_escape($dst_num)."+1 WHERE {$sql_where_srcuid_in}");
			} elseif($src_num < $dst_num){ // 하위로 이동할 경우 (src[num]보다 크고 dst[num] 이하의 경우 1씩 감소후 본래위치는 dst[num]값으로
				db_query("UPDATE {$dbinfo['table_cate']} SET num=num-1 WHERE {$sql_where_cate} and num > ".db_escape($src_num)." and num <= ".db_escape($dst_num));
				db_query("UPDATE {$dbinfo['table_cate']} SET num=".db_escape($dst_num)." WHERE {$sql_where_srcuid_in}");
			}
		}
	}
} // end func.


// 카테고리 추가 부분($sql_set_cate 가져오는 것 필히 확인)
function cateWriteOK($dbinfo, $qs){
	global $sql_where_cate;

	global $SITE;
	
	$qs=check_value($qs);
	
	// num, re 값 결정
	if(isset($qs['cateuid']) && $qs['cateuid']){ // 서브카테고리 추가인경우
		$cateuid_safe = db_escape($qs['cateuid']);
		$sql = "SELECT * FROM {$dbinfo['table_cate']} WHERE uid='{$cateuid_safe}' AND {$sql_where_cate} ";
		$list = db_arrayone($sql) or back('해당 부모 카테고리가 없습니다.');
		$qs['num']=$list['num'] ?? null;
		$qs['re'] =getCateRe($dbinfo['table_cate'],$sql_where_cate,$qs['num'],$list['re'] ?? '');
		if(isset($dbinfo['cate_depth']) && $dbinfo['cate_depth'] && $dbinfo['cate_depth'] < strlen($qs['re'])) back('더 하부의 서브카테고리를 만드실 수 없습니다');
	} else { // 탑카테고리 추가인경우
		$max_num = db_resultone("SELECT MAX(num) AS num FROM {$dbinfo['table_cate']} WHERE {$sql_where_cate} ", 0, 'num');
		$qs['num'] = (int)$max_num + 1;
		$qs['re']	= '';
	}
	
	////////////////////////////////////////////
	// 추가되어 있는 테이블 필드 포함($sql_set)
	$skip_fields = array('uid', 'upfiles', 'upfiles_totalsize', 'hit', 'hitip', 'hitdownload', 'vote', 'voteip', 'rdate');
	$sql_set = '';
	if($fieldlist = userGetAppendFields($dbinfo['table_cate'], $skip_fields)){
		
		foreach($fieldlist as $value){
			// 해당 필드 데이터값 확정
			switch($value){
				case 'pern':
				case 'page_pern':
				case 'row_pern':
					if(!isset($_POST[$value]) || !strlen($_POST[$value])) $qs[$value] = 'null';
					break;
				case 'zip' :
					$qs['zip'] = (isset($_POST['zip1']) ? $_POST['zip1'] : '') . '-' . (isset($_POST['zip2']) ? $_POST['zip2'] : '');
					break;
				case 'ip' :
					$qs['ip'] = remote_addr();
					break;
				case 'bid' :
					$qs['bid']	= $_SESSION['seUid'] ?? 0;
					break;
				case 'userid' :
					if(isset($_SESSION['seUid'])){
						switch($dbinfo['enable_userid'] ?? 'userid'){
							case 'name'		: $qs['userid'] = $_SESSION['seName'] ?? ''; break;
							case 'nickname'	: $qs['userid'] = $_SESSION['seNickname'] ?? ''; break;
							default			: $qs['userid'] = $_SESSION['seUserid'] ?? ''; break;
						}
					}
					break;
				case 'email' :
					if(isset($_POST['email'])) $qs['email']	= check_email($_POST['email']);
					elseif(isset($_SESSION['seUid'])) $qs['email']	= $_SESSION['seEmail'] ?? '';
					break;
			} // end switch

			// sql_set 만듦
			if(isset($qs[$value])){
				if($value == 'passwd') $sql_set .= ", passwd	=PASSWORD('".db_escape($qs['passwd'])."') ";
				elseif($qs[$value] == 'null') $sql_set .= ", {$value} =NULL";
				else $sql_set .= ", {$value} ='".db_escape($qs[$value])."'";
			} elseif(isset($_POST[$value])) $sql_set .= ", {$value} ='".db_escape($_POST[$value])."'";
		} // end foreach
	} // end if

	$sql="INSERT INTO {$dbinfo['table_cate']} SET
				rdate	= UNIX_TIMESTAMP()
				{$sql_set}
		";
	db_query($sql);
	
	return db_insert_id();
}

// 카테고리 수정 부분
function cateModifyOK($dbinfo,$qs,$field){
	global $sql_where_cate;

	global $SITE;
	
	// $qs 추가,변경
	//$qs["{$field}"]	= "post,trim,notnull=" . urlencode("고유번호가 넘어오지 않았습니다");
	$qs=check_value($qs);
	$qs[$field] = $qs['cateuid'] ?? null;
	
	$field_val_safe = db_escape($qs[$field] ?? '');
	$sql = "SELECT * FROM {$dbinfo['table_cate']} WHERE {$field}='{$field_val_safe}' AND	{$sql_where_cate} ";
	$list = db_arrayone($sql) or back('수정하실 카테고리가 없습니다');

	////////////////////////////////////////////
	// 추가되어 있는 테이블 필드 포함($sql_set)
	$skip_fields = array('bid','num','re', 'uid', 'upfiles', 'upfiles_totalsize', 'hit', 'hitip', 'hitdownload', 'vote', 'voteip', 'rdate');
	$sql_set = '';
	if($fieldlist = userGetAppendFields($dbinfo['table_cate'], $skip_fields)){
		foreach($fieldlist as $value){
			// 해당 필드 데이터값 확정
			switch($value){
				/*
				case 'num' :
				case 're' :
					continue 2; // 다음 foreach 로...
				*/
				case 'pern':
				case 'page_pern':
				case 'row_pern':
					if(!isset($_POST[$value]) || !strlen($_POST[$value])) $qs[$value] = 'null';
					break;
				case 'zip' :
					$qs['zip'] = (isset($_POST['zip1']) ? $_POST['zip1'] : '') . '-' . (isset($_POST['zip2']) ? $_POST['zip2'] : '');
					break;
				case 'ip' :
					$qs['ip'] = remote_addr();
					break;
				case 'userid' :
					if(isset($list['bid']) && ($list['bid'] ?? '') == ($_SESSION['seUid'] ?? null)) { // 관리자권한으로 수정했으면 변경불가
						switch($dbinfo['enable_userid'] ?? 'userid'){
							case 'name'		: $qs['userid'] = $_SESSION['seName'] ?? ''; break;
							case 'nickname'	: $qs['userid'] = $_SESSION['seNickname'] ?? ''; break;
							default			: $qs['userid'] = $_SESSION['seUserid'] ?? ''; break;
						}
					}
					break;
				case 'email' :
					if(isset($_POST['email'])) $qs['email']	= check_email($_POST['email']);
					elseif(isset($list['bid']) && ($list['bid'] ?? '') == ($_SESSION['seUid'] ?? null)) // 관리자권한으로 수정했으면 변경불가
						$qs['email']	= $_SESSION['seEmail'] ?? '';
					break;
			} // end switch

			// sql_set 만듦
			if(isset($qs[$value])){
				if($value == 'passwd') $sql_set .= ", passwd	=PASSWORD('".db_escape($qs['passwd'])."') ";
				elseif($qs[$value] == 'null') $sql_set .= ", {$value} =NULL";
				else $sql_set .= ", {$value} ='".db_escape($qs[$value])."'";
			} elseif(isset($_POST[$value])) $sql_set .= ", {$value} ='".db_escape($_POST[$value])."'";
		} // end foreach
	} // end if

	////////////////////////////////////////////
	$sql="UPDATE {$dbinfo['table_cate']} SET
				rdate	=UNIX_TIMESTAMP()
				{$sql_set}
			WHERE
				{$field}='{$field_val_safe}'
		";
	db_query($sql);
	
	return true;
}

// 카테고리 삭제부분
function cateDeleteOK($dbinfo,$field,$goto){
	global $sql_where_cate;

	global $SITE;
	// $qs 추가,변경
	$qs=array(
			//"$field" =>	"request,trim,notnull=" . urlencode("고유넘버가 넘어오지 않았습니다."),
			'passwd' =>	'request,trim',
			'cateuid' =>	'request,trim,notnull=' . urlencode('고유넘버가 넘어오지 않았습니다.'),
		);
	$qs=check_value($qs);
	$qs[$field] = $qs['cateuid'] ?? null;
	
	$field_val_safe = db_escape($qs[$field] ?? '');
	$sql = "SELECT * FROM {$dbinfo['table_cate']} WHERE {$field}='{$field_val_safe}' AND {$sql_where_cate} ";
	$list = db_arrayone($sql) or back('수정하실 카테고리가 없습니다');

	// 자신과 하위 카테고리 uid 구함($subcate_uid)
	$subcate_uid = array(); // init
	$list_num_safe = db_escape($list['num'] ?? '');
	$list_re_safe = db_escape($list['re'] ?? '');
	$sql="SELECT * FROM {$dbinfo['table_cate']} WHERE {$sql_where_cate} AND num={$list_num_safe} AND re LIKE '{$list_re_safe}%\'";
	$rs2 = db_query($sql);
	if ($rs2) {
		for($i=0;$i<db_count($rs2);$i++){
			$subcate_uid[] = db_result($rs2,$i,'uid');
		}
		db_free($rs2);
	}
	
	// SQL문 where부분 만들기
	$sql_cate_where = ' ( uid in (' . implode(',',$subcate_uid) . ') ) ';
	$sql_where = ' ( cateuid in (' . implode(',',$subcate_uid) . ') ) ';

	// 해당 카테고리의 DB 데이터가 있다면 삭제못함
	$sql="SELECT count(*) as count FROM {$dbinfo['table']} WHERE	$sql_where ";
	$count_result = db_resultone($sql,0,'count');
	if($count_result){
		back("해당 카테고리와 관련된 DB 데이터가 있습니다.\n해당 데이터를 먼저 삭제하시기 바랍니다.");
	}

	// 해당 카테고리 삭제
	$sql="DELETE FROM {$dbinfo['table_cate']} WHERE {$sql_cate_where}";
	db_query($sql);
	
	// 카테고리값 시프트
	$list_re = $list['re'] ?? '';
	$list_num = $list['num'] ?? 0;
	
	if(strlen($list_re)){
		$list_re_safe = db_escape($list_re);
		$list_num_safe = db_escape($list_num);
		
		$re_length = strlen($list_re);
		$re_length_minus_1 = $re_length - 1;
		$re_length_plus_1 = $re_length + 1;
		
		$sql="UPDATE
					{$dbinfo['table_cate']}
				SET
						re=concat( substring(re,1,{$re_length_minus_1}),
						char(ord(substring(re,{$re_length},1))-1 ),
						substring(re,{$re_length_plus_1}) )
				WHERE
						 {$sql_where_cate}	
				AND
						num='{$list_num_safe}'
				AND
						re like '" . db_escape(substr($list_re,0,-1)) . "%'
				AND
						re > '{$list_re_safe}'
			";
	} else {
		$sql="UPDATE
					{$dbinfo['table_cate']}
				SET
					num=num-1
				WHERE
					 {$sql_where_cate}	
				AND
					num > ".db_escape($list_num)."
			";
	}
	db_query($sql);
	
	return true;
}

function getCateRe($table_cate, $sql_where_cate, $num, $re) { // 05/01/27 박선민

	global $SITE;
	if(trim($sql_where_cate) == '') $sql_where_cate=' 1 ';

	$num_safe = db_escape($num);
	$re_safe = db_escape($re);
	
	$sql="SELECT re, right(re,1) as last_char FROM {$table_cate} WHERE {$sql_where_cate} and num='{$num_safe}' AND length(re)=length('{$re_safe}')+1 AND locate('{$re_safe}', re)=1 ORDER BY re DESC LIMIT 1";

	$result = db_query($sql);
	$row = $result ? db_array($result) : null;
	if($row){
		$ord_head = substr($row['re'],0,-1);
		$ord_foot = chr(ord($row['last_char']) + 1);
		$re = $ord_head . $ord_foot;
	} else {
		$re .= '1';
	}
	if($result) db_free($result);
	return $re;
}

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

	$safe_table = db_escape($table);
	$result = db_query("SHOW COLUMNS FROM `{$safe_table}`");

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