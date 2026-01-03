<?php
//=======================================================
// 설	명 : 심플리스트
// 책임자 : 박선민 (), 검수: 05/01/25
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 05/01/25 박선민 마지막 수정
// 24/05/20 Gemini PHP 7 마이그레이션
//=======================================================
$HEADER = array(
	'priv' => '', // 인증유무 (비회원,회원,운영자,서버관리자)
	'usedb2' => 1, // DB 커넥션 사용
	'useSkin' => 1, // 템플릿 사용
	'useApp' => 1 // cut_string()
);
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	// 1 . 넘어온값 체크

	// 2 . 기본 URL QueryString
	$qs_basic	= 'goto='.$_SERVER['PHP_SELF'];

	// 3 . $dbinfo 가져오기
	include_once('config.php');
	//$dbinfo = array('skin' => 'basic','priv_list' => '');

	// 4 . 권한 체크
	if(!privAuth((isset($dbinfo) ? $dbinfo : null), 'priv_list',1)) back('페이지를 보실 권한이 없습니다.');

	//======================
	// 5 . SQL문 where절 정리
	//======================
	$sql_where = ''; // init
	$sc_string = $_GET['sc_string'] ?? null;
	$sc_column = $_GET['sc_column'] ?? null;

	// 서치 게시물만..
	if(trim($sc_string) && $sc_column){
		// sc_column으로 title,content이면, or로 두필드 검색하도록
		$aTemp = explode(',', $sc_column);
		$tmp = '';
		for($i=0;$i<count($aTemp);$i++){
			// PHP 7에서는 eregi가 제거되었으므로 preg_match로 대체하고 case-insensitive를 위해 'i' 플래그를 사용
			if(!preg_match('/^[a-z0-9_-]+$/i',$aTemp[$i])) continue;
			if($i>0) $tmp .= ' or ';
			switch($aTemp[$i]){
				case 'bid':
				case 'uid':
					// SQL 인젝션 방지를 위해 db_escape 사용
					$tmp .=' ('.$aTemp[$i].'="'.db_escape($sc_string).'") '; break;
				default : // bug - sc_column 장난 우려
					// SQL 인젝션 방지를 위해 db_escape 사용
					$tmp .=' ('.$aTemp[$i].' like "%'.db_escape($sc_string).'%") ';
				// default : back('잘못된 요청입니다.');
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

	// 5 . URL Link...
	$href['list']	= $_SERVER['PHP_SELF'].'?'.href_qs('page=',$qs_basic);
	$href['write']	= 'write.php?'	. href_qs('mode=write',$qs_basic);	// 글쓰기

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
// 템플릿 기반 웹 페이지 제작
$skinfile=basename(__FILE__,'.php').'.html';

if( !is_file('skin/'.($dbinfo['skin'] ?? 'basic').'/'.$skinfile) ) $dbinfo['skin']='basic';
$tpl = new phemplate('skin/'.($dbinfo['skin'] ?? 'basic')); // 템플릿 시작
$tpl->set_file('html',$skinfile,TPL_BLOCK);
// 템플릿 기본 할당
$tpl->tie_var('get'				, $_GET);	// get값으로 넘어온것들
$tpl->set_var('get.sc_string'	, htmlspecialchars(stripslashes($sc_string ?? ''), ENT_QUOTES));	// 서치 단어
$tpl->tie_var('dbinfo'			, $dbinfo ?? []);	// dbinfo 정보 변수
$tpl->tie_var('href'			, $href ?? []);	// 게시판 각종 링크
$tpl->set_var('sort_'.($_GET['sort'] ?? ''),true);	// sort_???
// 서치 폼의 hidden 필드 모두!!
$form_search =' action="'.$_SERVER['PHP_SELF'].'" method="get">';
$form_search .= substr(href_qs('sc_column=&sc_string=',$qs_basic,1), 0, -1);
$tpl->set_var('form_search'		, $form_search);	// form actions, hidden fileds

//===========================
// SQL문 실행
//===========================
$sql = "SELECT * FROM {$dbinfo['table']} WHERE $sql_where ORDER BY {$sql_orderby}";
$rs_list = db_query($sql);

if(!($total=db_count($rs_list))) {	// 게시물이 하나도 없다면...
	if(trim($sc_string)) { // 서치시 게시물이 없다면..
		$tpl->process('LIST', 'nosearch');
	}
	else // 게시물이 없다면. .
		$tpl->process('LIST', 'nolist');
} else {
	for($i=0; $i<$total; $i++){
		$list		= db_array($rs_list);
		// 'lastnum' 변수가 정의되지 않았을 수 있으므로 isset()을 사용
		$count_lastnum = (isset($count['lastnum']) ? $count['lastnum']-- : $total--);
		$list['no']	= $count_lastnum;
		$list['rede']	= strlen($list['re'] ?? '');
		$list['rdate_date']= (isset($list['rdate']) && $list['rdate']) ? date('y/m/d', $list['rdate']) : '';	//	날짜 변환
		if(!($list['title'] ?? null)) $list['title'] = '제목없음…';
		$list['cut_title'] = cut_string($list['title'], (int)($_GET['cut_length'] ?? 0)); // 제목자름

		//	Search 단어 색깔 표시
		if(trim($sc_string) and trim($sc_column)){
			if($sc_column == 'title')
				// eregi_replace 대신 preg_replace 사용
				$list['cut_title'] = preg_replace('/'.preg_quote($sc_string, '/').'/i', '<font color=darkred>\\0</font>',	$list['cut_title']);
			// eregi_replace 대신 preg_replace 사용
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
$tpl->tie_var('count.total'			, $total);	// 게시판 각종 카운트

// 블럭 : 글쓰기
if(privAuth(($dbinfo ?? null), 'priv_write')) $tpl->process('WRITE','write');
else $tpl->process('WRITE','nowrite');

// 마무리
$tpl->echoHtml(($dbinfo ?? []), ($SITE ?? []));
?>
