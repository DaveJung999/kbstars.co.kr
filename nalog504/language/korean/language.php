<?php
####################################################################################
/*
				navyism@log analyzer 5
				대한민국어 언어팩

* 주의사항:

	' 와 " 또는 \ 와 같은 특수 문자는 특별한 경우를 제외하고는 사용할 수 없습니다.
	위 문자들의 사용으로 인하여 에러가 발생 될 수 있으니 주의 하세요.
	' 와 같은 문자는 ` 등으로 대체 하여 사용 하시기 바랍니다.

*/
####################################################################################


####################################################################################
//			Include Version Info File (required)
####################################################################################
include "nalog_info.php";


####################################################################################
//			Language Information (naming in English only)
####################################################################################
$lang['name']		= "Korean (utf-8)";
$lang['english_name'] 	= "Korean";


####################################################################################
//			Page Header (please do not modify)
####################################################################################
$lang['head']		= "<!-----------------------------------------------------------------------------------------------------


								 〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓
								 프로그램명 : navyism@log analyzer
								 배포버전	: {$nalog_info['version']}
								 배포일자	: {$nalog_info['date']}
								 제 작 자	: navyism
								 e-mail	 : navyism@navyism.com
								 homepage	: http://navyism.com
								 〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓
								 언 어 팩	: 한국어 (utf-8)
								 배포버전	: v1.0.2 for n@log 5.0.2
								 배포일자	: 2003.02.27
								 번 역 자	: navyism
								 e-mail	 : navyism@navyism.com
								 homepage	: http://navyism.com
								 〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓



n@series는 PHP와 mySQL을 기반으로 하는 CGI형태의 웹 프로그램으로,
모든 사용자에게는 다음과 같은 제약 사항이 적용 됩니다.

n@series의 저작권 및 배포권은 제작자(navyism)에게 있으며, 
저작권 표기후 누구나 사용 및 수정 할수 있습니다.
단 제작자와의 사전 합의 없이 저작권 표기를 수정하거나 삭제 할 수 없습니다.

n@series의 사용으로 인한 손실 및 손해에 대해서 제작자 및 배포자에게는 책임이 없으며, 
제작자 및 배포자에게는 유지 및 보수의 의무가 없습니다.

n@series는 개인, 기업 및 공공 단체 사이트 등에서 자유롭게 설치 및 사용 할 수 있지만, 
제작자와의 합의 없이 n@series를 목적으로 하는 유료 대여 및 판매와 같은 상업적인 행위는 할 수 없습니다.

n@series는 누구나 자유롭게 자신의 사이트에서 배포 할 수 있지만, 
원 제작자를 표기하지 않은 수정배포는 허용하지 않습니다.

제작자와의 합의 없이 저작권 표기를 수정 및 삭제 할 경우 
저작권법 (제97조의 5항)에 명시된 사항에 의해 처벌 될 수 있습니다.
'재산권 권리의 복제, 공연, 방송, 전시, 전송(인터넷), 2차적 저작물 작성의 방법으로 침해한 자에 대해 
5년이하 징역 또는 5천만원 이하의 벌금형에 처해 집니다.'
------------------------------------------------------------------------------------------------------>

<html>
<head>
<title>n@log analyzer {$nalog_info['version']}</title>
<meta charset=\"utf-8\">
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
<meta name=\"Description\" content=\"navyism@log\">
<meta name=\"Keywords\" content=\"navyism@log,n@log\">
<meta name=\"Author\" content=\"navyism\">
<link rel=\"stylesheet\" type=\"text/css\" href=\"language/$language/style.css\">
</head>
";
$lang['copy']	= "<font size=1>n@log analyzer {$nalog_info['version']} &copy;2001-2003 </font><a href=http://navyism.com target=_blank><font size=1><b>navyism</b></font></a>";


###################################################################################
//			Displaying License Agreement (install.php)
###################################################################################
$lang['install_license_textarea_rows']	= 21;
$lang['install_license_title']		= "저작권 설치 동의";
$lang['install_license_agreement']	= "<b>프로그램 설치를 시작하기 전에 아래 사항을 꼭 읽어 보시기 바랍니다.</b>";
$lang['install_license_text']		= "n@series는 PHP와 mySQL을 기반으로 하는 CGI형태의 웹 프로그램으로,
모든 사용자에게는 다음과 같은 제약 사항이 적용 됩니다.

n@series의 저작권 및 배포권은 제작자(navyism)에게 있으며, 
저작권 표기후 누구나 사용 및 수정 할수 있습니다.
단 제작자와의 사전 합의 없이 저작권 표기를 수정하거나 삭제 할 수 없습니다.

n@series의 사용으로 인한 손실 및 손해에 대해서 제작자 및 배포자에게는 책임이 없으며, 제작자 및 배포자에게는 유지 및 보수의 의무가 없습니다.

n@series는 개인, 기업 및 공공 단체 사이트 등에서 자유롭게 설치 및 사용 할 수 있지만, 제작자와의 합의 없이 n@series를 목적으로 하는 유료 대여 및 판매와 같은 상업적인 행위는 할 수 없습니다.

n@series는 누구나 자유롭게 자신의 사이트에서 배포 할 수 있지만, 
원 제작자를 표기하지 않은 수정배포는 허용하지 않습니다.

제작자와의 합의 없이 저작권 표기를 수정 및 삭제 할 경우 저작권법 (제97조의 5항)에 명시된 사항에 의해 처벌 될 수 있습니다.
'재산권 권리의 복제, 공연, 방송, 전시, 전송(인터넷), 2차적 저작물 작성의 방법으로 침해한 자에 대해 5년이하 징역 또는 5천만원 이하의 벌금형에 처해 집니다.'";
$lang['install_license_ask']		= "<center>위의 내용을 모두 읽어 보셨으며, 동의 하시겠습니까?</center><br>";
$lang['install_license_agree']		= "예, 동의 합니다.";
$lang['install_license_decline']		= "아니오, 동의 하지 않습니다.";


###################################################################################
//			Setup MySQL Connection (install_er.php)
###################################################################################
$lang['install_mysql_title']		= "MySQL 접속정보 설정";
$lang['install_mysql_text']		= "n@log 5는 <b>MySQL database</b>를 이용하여 정보를 저장 하며, <b>MySQL database</b>를 이용하려면 사용자의 <b>MySQL계정</b>이 필요 합니다.<br>
MySQL계정에 대한 자세한 정보는 서버 관리자에게 문의 하시기 바랍니다.<br><br>
<font color=tomato>(MySQL계정은 FTP계정과는 다른것입니다)</font>";

$lang['install_mysql_account_mysql']	= "MySQL 사용자 계정정보 입력";
$lang['install_mysql_account_nalog']	= "n@log 5 관리자 계정생성";

$lang['install_mysql_input_db_host']	= "호스트이름";
$lang['install_mysql_input_db_id']	= "DB 아이디";
$lang['install_mysql_input_db_pass']	= "DB 패스워드";
$lang['install_mysql_input_db_name']	= "DB 이름";
$lang['install_mysql_input_admin_id']	= "관리자 ID";
$lang['install_mysql_input_admin_pass']	= "비밀번호";
$lang['install_mysql_input_admin_repass']	= "비밀번호 재입력";

$lang['install_mysql_error_db_host']	= "호스트이름을 입력하세요";
$lang['install_mysql_error_db_id']	= "DB 아이디를 입력하세요";
$lang['install_mysql_error_db_pass']	= "DB 패스워드를 입력하세요";
$lang['install_mysql_error_db_name']	= "DB 이름을 입력하세요";
$lang['install_mysql_error_admin_id']	= "관리자 아이디를 입력하세요";
$lang['install_mysql_error_admin_pass']	= "관리자 비밀번호를 입력하세요";
$lang['install_mysql_error_admin_repass']	= "관리자 비밀번호를 한번더 입력하세요";
$lang['install_mysql_error_admin_match']	= "관리자 비밀번호가 서로 일치하지 않습니다";


###################################################################################
//			When Installing... (install_ing.php)
###################################################################################
$lang['install_ing_error_db_id']		= "DB에 접속할수 없습니다\\nDB 아이디와 패스워드를 확인하세요";
$lang['install_ing_error_db_name']	= "DB에 접속할수 없습니다\\nDB 이름을 확인하세요";
$lang['install_ing_error_permission1']	= "n@log의 설치를 계속 할 수 없습니다.\\n디렉토리의 퍼미션이 707 또는 777이 아니거나 n@log의 파일들이 적절하지 않은 방법으로 본 계정에 복제된 것 같습니다.\\n\\n디렉토리의 퍼미션을 확인해 보시고 nalog_connect.php를 삭제하시고 다시 설치를 시도해 보시기 바랍니다";
$lang['install_ing_error_permission2']	= "n@log의 설치를 계속 할 수 없습니다.\\n디렉토리의 퍼미션이 707 또는 777이 아니거나 n@log의 파일들이 적절하지 않은 방법으로 본 계정에 복제된 것 같습니다.\\n\\n디렉토리의 퍼미션을 확인해 보시고 nalog_language.php를 삭제하시고 다시 설치를 시도해 보시기 바랍니다";

$lang['install_ing_finish']		= "n@log analyzer의 설치가 완료되었습니다";


###################################################################################
//			Version Info Check (check.php)
###################################################################################
$lang['version_check_title']		= "최신버전 체크";
$lang['version_check_this_version']	= "현재 버전: ";
$lang['version_check_latest_version']	= "최신 버전: ";
$lang['version_check_update_button']	= "업데이트하기";
$lang['version_check_close_button']	= "창닫기";


###################################################################################
//			Change Administration Account (change.php)
###################################################################################
$lang['change_admin_title']		= "관리자 계정변경";
$lang['change_admin_text']		= "새 관리자 계정";
$lang['change_admin_change_button']	= "변경하기";
$lang['change_admin_close_button']	= "창닫기";

$lang['change_admin_id']			= "관리자 ID";
$lang['change_admin_pass']		= "비밀번호";
$lang['change_admin_repass']		= "비밀번호 재입력";

$lang['change_admin_error_admin_id']	= "변경할 관리자 아이디를 입력하세요";
$lang['change_admin_error_admin_pass']	= "변경할 관리자 비밀번호를 입력하세요";
$lang['change_admin_error_admin_repass']	= "변경할 관리자 비밀번호를 한번더 입력하세요";
$lang['change_admin_error_admin_match']	= "변경할 관리자 비밀번호가 서로 일치하지 않습니다";

$lang['change_admin_finish']		= "관리자 계정이 변경 되었습니다";


###################################################################################
//			Program Uninstallation (uninstall.php)
###################################################################################
$lang['uninstall_finish']			= "n@log analyzer의 모든 자료 및 테이블을 삭제 하였습니다.\\n\\n다시 사용하려면 install.php를 실행하여\\n재설치를 하셔야 합니다";


###################################################################################
//			Administrator Login Page (login.php)
###################################################################################
$lang['login_title']			= "n@log 관리자 로그인";
$lang['login_id']				= "아이디";
$lang['login_pass']			= "비밀번호";
$lang['login_auto']			= "자동로그인";

$lang['login_warning_auto']		= "자동로그인을 하시면 브라우져를 닫은 후에도\\n계속해서 로그인상태를 유지하기 때문에\\nPC방이나 학교등 공공 장소에서는 위험합니다\\n\\n자동로그인을 사용하시겠습니까?";
$lang['login_error_id']			= "아이디를 입력하세요";
$lang['login_error_pass']			= "비밀번호를 입력하세요";

$lang['login_error_id_wrong']		= "아이디가 정확하지 않습니다";
$lang['login_error_pass_wrong']		= "비밀번호가 정확하지 않습니다";


###################################################################################
//			Root Manager (root.php)
###################################################################################
$lang['root_title']			= "최고 관리자 메뉴";
$lang['root_alt_counter_manager']		= "카운터 관리";
$lang['root_alt_version_check']		= "최신버전 확인";
$lang['root_alt_navyism_com']		= "n@log 5 공식홈페이지";
$lang['root_alt_change_admin']		= "관리자 계정 변경";
$lang['root_alt_uninstall']		= "n@log 5 제거";
$lang['root_warning_uninstall']		= "n@log analyzer를 제거 하면, \\n모든 카운터의 로그 기록과 설정이 삭제 됩니다.\\n\\n 제거 하시겠습니까?";

$lang['root_change_language_button']	= "언어변경";


###################################################################################
//			Counter Manager (admin.php)
###################################################################################
$lang['counter_manager_title']		= "카운터 관리";
$lang['counter_manager_paging1']		= "&nbsp;&nbsp;총 ";
$lang['counter_manager_paging2']		= "개의 생성된 카운터, 현재 ";
$lang['counter_manager_paging3']		= "페이지, 총 ";
$lang['counter_manager_paging4']		= "페이지";
$lang['counter_manager_view']		= "표시갯수";
$lang['counter_manager_view_button']	= "보기";
$lang['counter_manager_view_error']	= "숫자를 입력하셔야 합니다";

$lang['counter_manager_table_no']		= "번호";
$lang['counter_manager_table_name']	= "카운터 이름";
$lang['counter_manager_table_config']	= "환경설정";
$lang['counter_manager_table_example']	= "예제보기";
$lang['counter_manager_table_drop']	= "삭제";
$lang['counter_manager_table_clean']	= "비우기";
$lang['counter_manager_table_total']	= "전체";
$lang['counter_manager_table_today']	= "오늘";
$lang['counter_manager_table_today_peak'] = "최대";
$lang['counter_manager_table_peak']	= "최대동시접속수";
$lang['counter_manager_tablecell_view']	= "예제보기";
$lang['counter_manager_tablecell_drop']	= "삭제";
$lang['counter_manager_tablecell_clean']	= "비우기";

$lang['counter_manager_warning_drop']	= "선택된 카운터를 제거 합니다.\\n제거된 자료는 되돌릴 수 없습니다.\\n\\n작업을 계속하시겠습니까?";
$lang['counter_manager_warning_clean']	= "선택된 카운터의 자료를 삭제 합니다.\\n카운터의 설정은 지워지지 않습니다.\\n\\n작업을 계속하시겠습니까?";

$lang['counter_manager_create_button']	= "카운터생성";
$lang['counter_manager_error_create']	= "생성할 카운터이름을 입력하세요";


###################################################################################
//			Creating Counter (admin_ing.php)
###################################################################################
$lang['counter_create_error_name']	= "생성할 카운터이름을 입력하세요";
$lang['counter_create_error_char']	= "카운터이름에는 영문 숫자 _ 를 제외한 특수 문자를 사용 할 수 없습니다";
$lang['counter_create_error_exist']	= "존재하는 카운터이름 입니다";
$lang['counter_create_error_blank']	= "카운터이름에는 공백이 포함될 수 없습니다";


###################################################################################
//			Counter Manager - Overall (admin_counter.php)
###################################################################################
$lang['counter_main_plug_in']		= "플러그인을 선택하세요";

$lang['counter_main_date_format1']	= "Y-m-d H:i:s (D)";
$lang['counter_main_not_exist']		= "존재하지 않는 카운터 입니다";

$lang['counter_main_title']		= "카운터확인";
$lang['counter_main_title_hour']		= "시간별통계";
$lang['counter_main_title_day']		= "날짜별통계";
$lang['counter_main_title_week']		= "요일별통계";
$lang['counter_main_title_month']		= "월별통계";
$lang['counter_main_title_year']		= "년도별통계";
$lang['counter_main_title_refer']		= "링크주소통계 (서버명)";
$lang['counter_main_title_refer_detail']	= "링크주소통계 (URL)";
$lang['counter_main_title_os']		= "운영체제 & 웹브라우저";
$lang['counter_main_title_visitor']	= "방문자정보확인";
$lang['counter_main_title_config']	= "카운터설정";

$lang['counter_main_menu_hour']		= "시간별";
$lang['counter_main_menu_day']		= "일별";
$lang['counter_main_menu_week']		= "요일별";
$lang['counter_main_menu_month']		= "월별";
$lang['counter_main_menu_year']		= "년도별";
$lang['counter_main_menu_refer']		= "링크된서버";
$lang['counter_main_menu_refer_detail']	= "링크된페이지";
$lang['counter_main_menu_os']		= "운영체제&브라우저";
$lang['counter_main_menu_visitor']	= "방문자";
$lang['counter_main_menu_config']		= "환경설정";

$lang['counter_main_year']		= "년";
$lang['counter_main_month']		= "월";
$lang['counter_main_day']			= "일";

$lang['counter_main_button_view']		= "보기";
$lang['counter_main_button_view_all']	= "전체보기";
$lang['counter_main_button_print']	= "인쇄하기";
$lang['counter_main_button_back']		= "뒤로이동";
$lang['counter_main_button_check_all']	= "전체선택";
$lang['counter_main_button_cancel_all']	= "선택취소";
$lang['counter_main_button_search']	= "검색";
$lang['counter_main_button_delete']	= "선택된 로그를 삭제";


###################################################################################
//			Counter Manager - Part 1 (by Hour)
###################################################################################
$lang['counter_main_1_date_format']	= "Y년 n월 j일";
$lang['counter_main_1_date']		= "날짜: ";
$lang['counter_main_1_today']		= "오늘";
$lang['counter_main_1_sum']		= "누적";
$lang['counter_main_1_total']		= " , 총: ";
$lang['counter_main_1_total_visitor']	= "명의 방문자";
$lang['counter_main_1_hour_format']	= "H시";
$lang['counter_main_1_hour']		= "시";
$lang['counter_main_1_visitor']		= "명";
$lang['counter_main_1_view_visitor']	= "{yy}년 {mm}월 {dd}일 {hh}시의 방문자 목록 확인";


###################################################################################
//			Counter Manager - Part 2 (by Day)
###################################################################################
$lang['counter_main_2_date_format']	= "Y년 n월";
$lang['counter_main_2_month']		= "월: ";
$lang['counter_main_2_this_month']	= "현재달";
$lang['counter_main_2_sum']		= "누적";
$lang['counter_main_2_total']		= " , 총: ";
$lang['counter_main_2_total_visitor']	= "명의 방문자";
$lang['counter_main_2_day_format']	= "j일";
$lang['counter_main_2_visitor']		= "명";
$lang['counter_main_2_view_visitor']	= "{yy}년 {mm}월 {dd}일의 시간통계 확인";


###################################################################################
//			Counter Manager - Part 3 (by Week)
###################################################################################
$lang['counter_main_3_sum']		= "누적";
$lang['counter_main_3_total']		= " , 총: ";
$lang['counter_main_3_total_visitor']	= "명의 방문자";
$lang['counter_main_3_average']		= " , 평균 1주일당: ";
$lang['counter_main_3_average_visitor']	= "명의 방문자";
$lang['counter_main_3_visitor']		= "명";

$lang['counter_main_3_day_name0']		= "일요일";
$lang['counter_main_3_day_name1']		= "월요일";
$lang['counter_main_3_day_name2']		= "화요일";
$lang['counter_main_3_day_name3']		= "수요일";
$lang['counter_main_3_day_name4']		= "목요일";
$lang['counter_main_3_day_name5']		= "금요일";
$lang['counter_main_3_day_name6']		= "토요일";


###################################################################################
//			Counter Manager - Part 4 (by Month)
###################################################################################
$lang['counter_main_4_year']		= "년도: ";
$lang['counter_main_4_this_year']		= "금년";
$lang['counter_main_4_sum']		= "누적";
$lang['counter_main_4_total']		= ", 총: ";
$lang['counter_main_4_total_visitor']	= "명의 방문자";
$lang['counter_main_4_month_format']	= "n월";
$lang['counter_main_4_visitor']		= "명";
$lang['counter_main_4_view_visitor']	= "{yy}년 {mm}월의 일일통계 확인";


###################################################################################
//			Counter Manager - Part 5 (by Year)
###################################################################################
$lang['counter_main_5_sum']		= "누적";
$lang['counter_main_5_total']		= ", 총: ";
$lang['counter_main_5_total_visitor']	= "명의 방문자";
$lang['counter_main_5_year_format']	= "Y년";
$lang['counter_main_5_visitor']		= "명";
$lang['counter_main_5_view_visitor']	= "{yy}년의 월별통계 확인";


###################################################################################
//			Counter Manager - Part 6 (by Referers - Host & URL)
###################################################################################
$lang['counter_main_6_date_format']	= "Y년 m월 d일 H시 i분 s초";
$lang['counter_main_6_total']		= "총: ";
$lang['counter_main_6_total_url']		= "개의 URL, ";
$lang['counter_main_6_total_visitor']	= "명의 방문자";
$lang['counter_main_6_total_zero']	= "로그 기록이 없습니다";
$lang['counter_main_6_total_delete']	= "선택된 로그를 삭제 하시겠습니까?";

$lang['counter_main_6_today_only']	= "금일기록만";
$lang['counter_main_6_sort_by']		= "정렬방법";

$lang['counter_main_6_sort_1']		= "방문자순";
$lang['counter_main_6_sort_2']		= "방문자역순";
$lang['counter_main_6_sort_3']		= "시간순";
$lang['counter_main_6_sort_4']		= "시간역순";
$lang['counter_main_6_sort_5']		= "URL순";
$lang['counter_main_6_sort_6']		= "URL역순";

$lang['counter_main_6_search_negative']	= "부정어";
$lang['counter_main_6_search_and']	= "그리고";
$lang['counter_main_6_search_or'] 	= "또는";

$lang['counter_main_6_table_url']		= "접속서버 (호스트 이름 또는 URL, 마지막 접속시간)";
$lang['counter_main_6_table_hit']		= "접속자수";

$lang['counter_main_6_url_remember']	= "URL 기억하기";
$lang['counter_main_6_url_forget']	= "URL 기억취소";

$lang['counter_main_6_url_remember_button']="<span lang=ko style=font-family:돋움,Dotum;font-size:8pt>[기억하기']</font>";
$lang['counter_main_6_url_forget_button']	= "<span lang=ko style=font-family:돋움,Dotum;font-size:8pt;color:#F7418C>[기억취소']</span>";

$lang['counter_main_6_direct_connect']	= "주소직접입력 또는 즐겨찾기를 이용한 방문";
$lang['counter_main_6_view_detail_url']	= "상세접속경로";
$lang['counter_main_6_delete_button']	= "로그삭제";
$lang['counter_main_6_delete_question']	= "정말 삭제 하시겠습니까?";

$lang['counter_main_6_error_pagenum']	= "숫자를 입력하셔야 합니다";


###################################################################################
//			Counter Manager - Part 7 (by Visitors' OS & Browser)
###################################################################################
$lang['counter_main_7_total']		= "총: ";
$lang['counter_main_7_total_os']		= "개의 운영체제, ";
$lang['counter_main_7_total_browser']	= "개의 브라우저, ";
$lang['counter_main_7_total_visitor']	= "명의 방문자";
$lang['counter_main_7_visitor']		= "명";
$lang['counter_main_7_total_zero']	= "로그 기록이 없습니다";

$lang['counter_main_7_title_os']		= "방문자의 운영체제";
$lang['counter_main_7_title_browser']	= "방문자의 웹브라우저";

$lang['counter_main_7_error_pagenum']	= "숫자를 입력하셔야 합니다";


###################################################################################
//			Counter Manager - Part 8 (by Visitors' Information)
###################################################################################
$lang['counter_main_8_date_format']	= "Y년 m월 d일 H시 i분 s초";
$lang['counter_main_8_total']		= "총: ";
$lang['counter_main_8_total_visitor']	= "명의 방문자";
$lang['counter_main_8_total_zero']	= "로그 기록이 없습니다";
$lang['counter_main_8_today_only']	= "금일기록만";
$lang['counter_main_8_member_only']	= "회원기록만";
$lang['counter_main_8_sort_by']		= "정렬방법";

$lang['counter_main_8_sort_1']		= "시간순";
$lang['counter_main_8_sort_2']		= "시간역순";
$lang['counter_main_8_sort_3']		= "회원ID순";
$lang['counter_main_8_sort_4']		= "회원ID역순";

$lang['counter_main_8_title_1']		= "방문자의 ID / 링크된페이지 / 운영체제 / 브라우저";
$lang['counter_main_8_title_2']		= "방문자의 IP / 방문시각";

$lang['counter_main_8_right_arrow']	= "<span style=font-size:6pt>▶</span> ";
$lang['counter_main_8_direct_connect']	= "주소직접입력 또는 즐겨찾기를 이용한 방문";
$lang['counter_main_8_not_login']		= "미확인";
$lang['counter_main_8_unknown_os']	= "알수없는 운영체제";
$lang['counter_main_8_unknown_browser']	= "알수없는 브라우저";
$lang['counter_main_8_search']		= "방문기록 검색";

$lang['counter_main_8_error_pagenum']	= "숫자를 입력하셔야 합니다";


###################################################################################
//			Counter Manager - Part 9 (Configuration)
###################################################################################
$lang['counter_config_total']		= "총 방문자수";
$lang['counter_config_skin']		= "스킨 설정";
$lang['counter_config_skin_pattern']	= "스킨 패턴 파일 사용";
$lang['counter_config_skin_pattern_use']	= "스킨 패턴 파일을 사용함";
$lang['counter_config_reconnect']		= "재접속 설정";
$lang['counter_config_reconnect_always']	= "항상 카운터 증가 (쿠키를 사용하지 않음)";
$lang['counter_config_reconnect_new_open']= "웹 브라우져를 다시 시작하면 카운터 증가 (쿠키 시간 : 0 sec)";
$lang['counter_config_reconnect_by_time1']= "지정된 시간 이후에 카운터 증가 (쿠키 시간 : ";
$lang['counter_config_reconnect_by_time2']= " sec)";
$lang['counter_config_reconnect_once']	= "하루에 한번만 카운터 증가";
$lang['counter_config_time_zone1']	= "기록에 참고할 시간대 설정";
$lang['counter_config_time_zone2']	= "시간 + 서버의 현지 시간 [권장하지 않음']";
$lang['counter_config_admin_check']	= "관리자 접속 체크";
$lang['counter_config_admin_check_not']	= "관리자의 접속을 통계에 포함하지 않음";
$lang['counter_config_now_check']		= "현재 접속자 체크";
$lang['counter_config_now_check_use']	= "현재 접속자수를 체크함";
$lang['counter_config_now_time']		= "접속 유지 시간";
$lang['counter_config_now_time_use1']	= "";
$lang['counter_config_now_time_use2']	= " 초 동안 접속하고 있는 것으로 간주함 (10초 이상)";
$lang['counter_config_admin_data']	= "통계 자료 관리";
$lang['counter_config_admin_data_delete1']= "통계 기록 삭제";
$lang['counter_config_admin_data_delete2']= " 일별,요일별,월별,연도별 통계를 삭제";
$lang['counter_config_admin_os']		= "OS & Browser 자료 관리";
$lang['counter_config_admin_os_delete1']	= "OS & Browser 기록 삭제";
$lang['counter_config_admin_os_delete2']	= " OS & Browser 통계를 삭제";
$lang['counter_config_visitor_check']	= "접속 자료 체크";
$lang['counter_config_visitor_check_use']	= "방문자 기록을 체크함";
$lang['counter_config_visitor_limit']	= "접속 자료 제한";
$lang['counter_config_visitor_delete1']	= "접속 기록 삭제";
$lang['counter_config_visitor_delete2']	= " 접속 기록을 삭제";
$lang['counter_config_visitor_limit_set1']= "";
$lang['counter_config_visitor_limit_set2']= " 명의 방문자 기록만을 보관함 (0일때 무제한)";
$lang['counter_config_log_check']		= "로그 자료 체크";
$lang['counter_config_log_check_use']	= "방문자의 로그를 체크함";
$lang['counter_config_log_limit']		= "로그 자료 제한";
$lang['counter_config_log_delete1']	= "로그 기록 삭제";
$lang['counter_config_log_delete2']	= " 로그 기록을 삭제";
$lang['counter_config_log_limit_set1']	= "";
$lang['counter_config_log_limit_set2']	= " 개의 로그 기록만을 보관함 (0일때 무제한)";
$lang['counter_config_member_cookie']	= "회원 구분 쿠키 이름";
$lang['counter_config_member_cookie_is']	= "(<b>n@board 3:</b> na3_member, <b>Zeroboard:</b> <a href=patch/zboard_login/README.txt target=_blank>로그인 연동 패치 필요</a>)";
$lang['counter_config_permission']	= "권한 설정";
$lang['counter_config_permission1']	= "관리자만 시간별 통계 확인 하기";
$lang['counter_config_permission2']	= "관리자만 날짜별 통계 확인 하기";
$lang['counter_config_permission3']	= "관리자만 요일별 통계 확인 하기";
$lang['counter_config_permission4']	= "관리자만 월별 통계 확인 하기";
$lang['counter_config_permission5']	= "관리자만 년도별 통계 확인 하기";
$lang['counter_config_permission6']	= "관리자만 로그 통계 확인 하기";
$lang['counter_config_permission7']	= "관리자만 상세로그 통계 확인 하기";
$lang['counter_config_permission8']	= "관리자만 운영체제/웹 브라우저 통계 확인 하기";
$lang['counter_config_permission9']	= "관리자만 방문자 통계 확인 하기";

$lang['counter_config_warning_data']	= "통계 기록을 삭제 하면\\n일별,요일별,월별,연도별 통계가 모두 삭제 됩니다\\n\\n계속 하시겠습니까?";
$lang['counter_config_warning_os']	= "OS & Browser 기록을 삭제 하면\\n저장된 OS & Browser 관련 기록이 모두 삭제 됩니다\\n\\n계속 하시겠습니까?";
$lang['counter_config_warning_visitor']	= "방문자 기록을 삭제 하면\\n저장된 방문자 기록이 모두 삭제 됩니다\\n\\n계속 하시겠습니까?";
$lang['counter_config_warning_log']	= "로그 기록을 삭제 하면\\n서버 및 상세 로그 기록이 모두 삭제 됩니다\\n\\n계속 하시겠습니까?";

$lang['counter_config_button_save']	= "저장하기";
$lang['counter_config_button_reset']	= "저장취소";

$lang['counter_manager_error_not_exist']	= "존재하지 않는 카운터 입니다";
$lang['counter_manager_error_total_is']	= "총 방문자수는 숫자를 입력하셔야 합니다";
$lang['counter_manager_error_cookie_time']= "쿠키 시간은 숫자를 입력하셔야 합니다";
$lang['counter_manager_error_connect_time']="접속 유지 시간은 숫자를 입력하셔야 합니다";
$lang['counter_manager_error_log_limit']	= "로그 자료 제한은 숫자를 입력하셔야 합니다";

if (!isset($ip)) {
	$ip = '';
}
###################################################################################
//			IP Address Information Check (check_ip.php)
###################################################################################
$lang['check_ip_title']			= "IP정보조회 : ";
$lang['check_ip_support']			= "질문 및 관련자료";
$lang['check_ip_close']			= "창닫기";
$lang['check_ip_false_msg']		= "whois 서버와의 연결이 성공적으로 이루어지지 않았습니다.<br>잠시후 자동으로 http://www.apnic.net의 IP정보 확인 페이지로 이동합니다.<br>자동으로 이동되지 않으면 다음의 링크를 클릭하여 IP정보를 확인하세요.<br><br><a href=http://www.apnic.net/apnic-bin/whois.pl?searchtext=$ip>http://www.apnic.net/apnic-bin/whois.pl?searchtext=$ip</a>";
$lang['check_ip_right_arrow']		= "<span style=font-size:6pt>▶</span>";
?>
