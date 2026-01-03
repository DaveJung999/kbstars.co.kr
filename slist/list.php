<?php
//=======================================================
// 설	명 : 심플리스트
// 책임자 : 박선민 (), 검수: 05/11/20
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 05/11/20 박선민 마지막 수정
// 24/05/20 Gemini PHP 7 마이그레이션
// 24/05/20 Gemini 사용자 요청에 따라 정렬, 통계 계산, 디자인 로직 추가
//=======================================================
$HEADER = array(
	'modeok' => 1,
	'priv' => '', // 인증유무 (비회원,회원,운영자,서버관리자)
	'usedb2' => 1, // DB 커넥션 사용
	'useSkin' => 1, // 템플릿 사용
	'useBoard2' => 1, // board2Count()
	'useApp' => 1 // cut_string()
);
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
// 1 . 넘어온값 체크

// 2 . 기본 URL QueryString
$qs_basic	= 'mode=&limitno=&limitrows=&time=';
if(($_GET['getinfo'] ?? null) != 'cont')
	$qs_basic .= '&pern=&row_pern=&page_pern=&html_type=&html_skin=&skin=';
$qs_basic	= href_qs($qs_basic); // 해당값 초기화

// 3 . $dbinfo 가져오기
include_once('config.php');

// 4 . 권한 체크
if(!privAuth((isset($dbinfo) ? $dbinfo : null), 'priv_list',1)) back('페이지를 보실 권한이 없습니다.');

//======================
// 5 . SQL문 where절 정리
//======================
$sql_where = ''; // init
$sc_string = $_GET['sc_string'] ?? null;
$sc_column = $_GET['sc_column'] ?? null;
// 서치 게시물만..
if(trim($sc_string) and $sc_column){
	// sc_column으로 title,content이면, or로 두필드 검색하도록
	$aTemp = explode(',',$sc_column);
	$tmp = '';
	for($i=0;$i<count($aTemp);$i++){
		if(!preg_match('/^[a-z0-9_-]+$/i',$aTemp[$i])) continue;
		if($i>0) $tmp .= ' or ';
		switch($aTemp[$i]){
			case 'bid':
			case 'uid':
				$tmp .=' ('.$aTemp[$i].'="'.db_escape($sc_string).'") '; break;
			default : // bug - sc_column 장난 우려
				$tmp .=' ('.$aTemp[$i].' like "%'.db_escape($sc_string).'%") ';
		}
	} // end for
	if($tmp){
		if($sql_where) $sql_where .= ' and ';
		$sql_where .= ' ('.$tmp.') ';
	}
} // end if
if(!$sql_where) $sql_where= ' 1 '; // 값이 없다면

//===========================
// 6 . SQL문 order by..절 정리
//===========================
switch($_GET['sort'] ?? ''){
	// get 해킹을 막기 위해 특정 값에만 order by 생성
	case 'uid':
	case 'title':
	case 'rdate':
		$sql_orderby = $_GET['sort']; break;
	case '!uid':
	case '!title':
	case '!rdate':
		$sql_orderby = substr($_GET['sort'],1).' DESC'; break;
	default :
		$sql_orderby = (isset($dbinfo['orderby']) ? $dbinfo['orderby'] : ' 1 ');
}

// 7 . 페이지 나눔등 각종 카운트 구하기
$count['total']=db_resultone("SELECT count(*) FROM {$dbinfo['table']} WHERE $sql_where LIMIT 1", 0, 'count(*)'); // 전체 게시물 수
$pern = $dbinfo['pern'] ?? 10;
$page_pern = $dbinfo['page_pern'] ?? 5;
$count=board2Count($count['total'],$_GET['page'] ?? 1,$pern,$page_pern); // 각종 카운트 구하기
$count['today']=db_resultone("SELECT count(*) FROM {$dbinfo['table']} WHERE (rdate > unix_timestamp(curdate())) and $sql_where LIMIT 1", 0, 'count(*)');

// 8 . URL Link...
$thisUrl = $thisUrl ?? '';
$href['list']		= $thisUrl.'list.php?'.href_qs('page=',$qs_basic);
if(($count['nowpage'] ?? 1) > 1) { // 처음, 이전 페이지
	$href['firstpage']	='list.php?'.href_qs('page=1',$qs_basic);
	$href['prevpage']	='list.php?'.href_qs('page='.($count['nowpage']-1),$qs_basic);
} else {
	$href['firstpage']	='javascript: void(0);';
	$href['prevpage']	='javascript: void(0);';
}
if(($count['nowpage'] ?? 1) < ($count['totalpage'] ?? 1)){ // 다음, 마지막 페이지
	$href['nextpage']	='list.php?'.href_qs('page='.($count['nowpage']+1),$qs_basic);
	$href['lastpage']	='list.php?'.href_qs('page='.($count['totalpage'] ?? 1),$qs_basic);
} else {
	$href['nextpage']	='javascript: void(0);';
	$href['lastpage'] ='javascript: void(0);';
}
$href['prevblock']= (($count['nowblock'] ?? 1)>1)					? 'list.php?'.href_qs('page='.($count['firstpage']-1) ,$qs_basic): 'javascript: void(0)';// 이전 페이지 블럭
$href['nextblock']= (($count['totalpage'] ?? 1) > ($count['lastpage'] ?? 1))? 'list.php?'.href_qs('page='.($count['lastpage'] +1),$qs_basic) : 'javascript: void(0)';// 다음 페이지 블럭

$href['write']	= 'write.php?'	. href_qs('mode=write',$qs_basic);	// 글쓰기

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
// 템플릿 기반 웹 페이지 제작
$skinfile=basename(__FILE__,'.php').'.html';
$thisPath = $thisPath ?? '.';

if( !is_file($thisPath.'/skin/'.($dbinfo['skin'] ?? 'basic').'/'.$skinfile) ) $dbinfo['skin']='basic';
$tpl = new phemplate($thisPath.'/skin/'.($dbinfo['skin'] ?? 'basic')); // 템플릿 시작
$tpl->set_file('html',$skinfile,TPL_BLOCK);
// 템플릿 기본 할당
$tpl->tie_var('get'				, $_GET);	// get값으로 넘어온것들
$tpl->set_var('get.sc_string'	, htmlspecialchars(stripslashes($sc_string ?? ''), ENT_QUOTES));	// 서치 단어
$tpl->tie_var('dbinfo'			, $dbinfo ?? []);	// dbinfo 정보 변수
$tpl->tie_var('href'			, $href ?? []);	// 게시판 각종 링크
$tpl->set_var('sort_'.($_GET['sort'] ?? ''),true);	// sort_???
$tpl->tie_var('count'			, $count ?? []);	// 게시판 각종 카운트
// 서치 폼의 hidden 필드 모두!!
$form_search =' action="'.$_SERVER['PHP_SELF'].'" method="get">';
$form_search .= substr(href_qs('sc_column=&sc_string=',$qs_basic,1), 0, -1);
$tpl->set_var('form_search'		, $form_search);	// form actions, hidden fileds

$sql = "SELECT * FROM {$dbinfo['table']} WHERE $sql_where ORDER BY {$sql_orderby} LIMIT {$count['firstno']},{$count['pern']}";
$rs_list = db_query($sql);

if(!($total=db_count($rs_list))) {	// 게시물이 하나도 없다면...
	if(trim($sc_string)) { // 서치시 게시물이 없다면..
		$tpl->process('LIST', 'nosearch');
	}
	else // 게시물이 없다면. .
		$tpl->process('LIST', 'nolist');
} else {
	$num = $count['total'] - $count['firstno'];
	for($i=0; $i<$total; $i++){
		$list		= db_array($rs_list);
		$list['no']	= $num--;
		$list['rede']	= strlen($list['re'] ?? '');
		$list['rdate_date']= (isset($list['rdate']) && $list['rdate']) ? date('y/m/d', $list['rdate']) : '';	//	날짜 변환
		if(!($list['title'] ?? null)) $list['title'] = '제목없음…';
		$list['cut_title'] = cut_string($list['title'], (int)($_GET['cut_length'] ?? 0)); // 제목자름

		//	Search 단어 색깔 표시
		if(trim($sc_string) and trim($sc_column)){
			if($sc_column == 'title')
				$list['cut_title'] = preg_replace('/'.preg_quote($sc_string, '/').'/i', '<font color=darkred>\\0</font>',	$list['cut_title']);
			$list[$sc_column]	= preg_replace('/'.preg_quote($sc_string, '/').'/i', '<font color=darkred>\\0</font>', $list[$sc_column]);
		}

		// URL Link...
		$list['href']['read'] = 'read.php?'	. href_qs('uid='.($list['uid'] ?? ''),$qs_basic);

		// 템플릿 할당
		$tpl->set_var('list'		,$list);
		$tpl->set_var('blockloop'	,true);
		$tpl->process('LIST','list',TPL_OPTIONAL|TPL_APPEND);
	} // end for (i)
	//	템플릿내장값 지우기
	$tpl->drop_var('blockloop');
	$tpl->drop_var('list',$list);
} // end if (게시물이 있다면...)

// 템플릿 마무리 할당
// 블럭 : 첫페이지, 이전페이지
if(($count['nowpage'] ?? 1) > 1){
	$tpl->process('FIRSTPAGE','firstpage');
	$tpl->process('PREVPAGE','prevpage');
} else {
	$tpl->process('FIRSTPAGE','nofirstpage');
	$tpl->process('PREVPAGE','noprevpage');
}

// 블럭 : 페이지 블럭 표시
// <-- (이전블럭) 부분
if (($count['nowblock'] ?? 1)>1) $tpl->process('PREVBLOCK','prevblock');
else $tpl->process('PREVBLOCK','noprevblock');
// 1 2 3 4 5 부분
for ($i=($count['firstpage'] ?? 1);$i<=($count['lastpage'] ?? 1);$i++) {
	$tpl->set_var('blockcount',$i);
	if($i == ($count['nowpage'] ?? 1))
		$tpl->process('BLOCK','noblock',TPL_APPEND);
	else {
		$tpl->set_var('href.blockcount', 'list.php?'.href_qs('page='.$i,$qs_basic) );
		$tpl->process('BLOCK','block',TPL_APPEND);
	}
} // end for
// --> (다음블럭) 부분
if (($count['totalpage'] ?? 1) > ($count['lastpage'] ?? 1)) $tpl->process('NEXTBLOCK','nextblock');
else $tpl->process('NEXTBLOCK','nonextblock');

// 블럭 : 다음페이지, 마지막 페이지
if(($count['nowpage'] ?? 1) < ($count['totalpage'] ?? 1)){
	$tpl->process('NEXTPAGE','nextpage');
	$tpl->process('LASTPAGE','lastpage');
} else {
	$tpl->process('NEXTPAGE','nonextpage');
	$tpl->process('LASTPAGE','nolastpage');
}

// 블럭 : 글쓰기
if(privAuth(($dbinfo ?? null), 'priv_write')) $tpl->process('WRITE','write');
else $tpl->process('WRITE','nowrite');

// 마무리
$tpl->echoHtml(($dbinfo ?? []), ($SITE ?? []));
?>