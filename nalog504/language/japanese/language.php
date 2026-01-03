<?php
####################################################################################
/*
				navyism@log analyzer 5
				볷?뚭붎

* 뭾댰럷?:

	' 궴 " 뼌궼 \ 궻귝궎궶딯뜂궼벫빶궶뤾뜃귩룣궖갂럊뾭뢯뿀귏궧귪갃
	뤵딯궻빒럻궼긄깋?귩덙궖딳궞궥뽦묋궕궇귡궻궳갂뭾댰궢궲돷궠궋갃
	' 궻귝궎궶딯뜂궼궩궻묆귦귟궸 ` 귩럊궯궲돷궠궋갃

*/
####################################################################################


####################################################################################
//			Include Version Info File (required)
####################################################################################
include"nalog_info.php";


####################################################################################
//			Language Information (naming in English only)
####################################################################################
$lang['name']		= "Japanese (shift-jis)";
$lang['english_name'] 	= "Japanese";


####################################################################################
//			Page Header (please do not modify)
####################################################################################
$lang['head']		= "<!-----------------------------------------------------------------------------------------------------


								 ========================================
								 긵깓긐깋?뼹 : navyism@log analyzer
								 긫?긙깈깛	: {$nalog_info['version']}
								 봹븓볷	?? : {$nalog_info['date']}
								 띿롌	??? : navyism
								 e-mail ?	: navyism@navyism.com
								 homepage ?	: http://navyism.com
								 ========================================
								 뙻 뚭	??	: 볷?뚭?(shift-jis)
								 긫?긙깈깛	: v1.0.2 for n@log 5.0.3
								 봹븓볷	?? : 2003.03.05
								 ?뽷롌		: uklife
								 e-mail		: webmaster@uk-life.com
								 ========================================



n@series궼 PHP궴 mySQL귩긹?긚궸궥귡CGI똭긂긃긳갋긵깓긐깋?궳갂
궥귊궲궻뿕뾭롌궸렅궻귝궎궶딮믦궕밙뾭궠귢귏궥갃

n@series궻뮊띿뙛땩귂봹븓뙛궼띿롌(navyism)궸궇귟갂
뮊띿뙛?딯궻뤵갂묿귖궕뿕뾭갂둂몾뢯뿀귏궥갃
궫궬궢갂띿롌궴궻럷멟궻떐땉궕궶궋귏귏갂뮊띿뙛?딯귩믷맫뼌궼랁룣뢯뿀귏궧귪갃

n@series궻럊뾭궸귝귡갂궋궔궶귡뫗둙궸뫮궢궲귖띿롌땩귂봹븓롌궼먖봀귩븠궋귏궧귪갃
귏궫갂띿롌땩귂봹븓롌궸댸렃갋뺚뢇궻?뼮궼궇귟귏궧귪갃

n@series궼뙿릐갂딃떾땩귂뭖뫬궶궵궻긖귽긣궳렔뾕궸먠뭫궢갂럊뾭뢯뿀귏궥궕갂
띿롌궴떐땉귩궧궦갂n@series귩뽞밒궴궢궫뾎뿿뫾궢땩귂붛봽궻귝궎궶룮떾뛱댴궼뢯뿀귏궧귪갃

n@series궼묿귖궕렔뾕궸렔빁궻긖귽긣궳봹븓뢯뿀귏궥궕갂
뙱띿롌귩?딯궢궶궋띋봹븓궼뢯뿀귏궧귪갃

------------------------------------------------------------------------------------------------------>

<html>
<head>
<title>n@log analyzer {$nalog_info['version']}</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=shift_jis\">
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
$lang['install_license_title']		= "뮊띿뙛 귽깛긚긣?깑 벏댰";
$lang['install_license_agreement']	= "<b>긵깓긐깋?귩귽깛긚긣?깑궥귡멟갂뷠궦돷딯궻딮뽵귩궓벶귒돷궠궋갃</b>";
$lang['install_license_text']		= "n@series궼 PHP궴 mySQL귩긹?긚궸궥귡CGI똭긂긃긳갋긵깓긐깋?궳갂
궥귊궲궻뿕뾭롌궸렅궻귝궎궶딮믦궕밙뾭궠귢귏궥갃

n@series궻뮊띿뙛땩귂봹븓뙛궼띿롌(navyism)궸궇귟갂
뮊띿뙛?딯궻뤵갂묿귖궕뿕뾭갂둂몾뢯뿀귏궥갃
궫궬궢갂띿롌궴궻럷멟궻떐땉궕궶궋귏귏갂뮊띿뙛?딯귩믷맫뼌궼랁룣뢯뿀귏궧귪갃

n@series궻럊뾭궸귝귡갂궋궔궶귡뫗둙궸뫮궢궲귖띿롌땩귂봹븓롌궼먖봀귩븠궋귏궧귪갃
귏궫갂띿롌땩귂봹븓롌궸댸렃갋뺚뢇궻?뼮궼궇귟귏궧귪갃

n@series궼뙿릐갂딃떾땩귂뭖뫬궶궵궻긖귽긣궳렔뾕궸먠뭫궢갂럊뾭뢯뿀귏궥궕갂
띿롌궴떐땉귩궧궦갂n@series귩뽞밒궴궢궫뾎뿿뫾궢땩귂붛봽궻귝궎궶룮떾뛱댴궼뢯뿀귏궧귪갃

n@series궼묿귖궕렔뾕궸렔빁궻긖귽긣궳봹븓뢯뿀귏궥궕갂
뙱띿롌귩?딯궢궶궋띋봹븓궼뢯뿀귏궧귪갃";


$lang['install_license_ask']		= "<center>뿕뾭딮뽵궸벏댰궢귏궥궔갎</center><br>";
$lang['install_license_agree']		= "궼궋갂벏댰궢귏궥";
$lang['install_license_decline']		= "궋궋궑갂벏댰뢯뿀귏궧귪";


###################################################################################
//			Setup MySQL Connection (install_er.php)
###################################################################################
$lang['install_mysql_title']		= "MySQL 먝뫏륃뺪먠믦";
$lang['install_mysql_text']		= "n@log 5궼 <b>MySQL database</b>귩뿕뾭궢궲륃뺪귩뺎뫔궢귏궥갃<br><b>MySQL database</b>귩뿕뾭궥귡궸궼<b>MySQL귺긇긂깛긣</b>궕뷠뾴궳궥갃<br>
MySQL귺긇긂깛긣궸듫궥귡뤬궢궋볙뾢궸궰궋궲궼갂륃뺪궼긖?긫?듖뿚롌궸뽦궋뜃귦궧돷궠궋갃<br><br>
<font color=tomato>(MySQL귺긇긂깛긣궼FTP귺긇긂깛긣궴궼댾궋귏궥갃)</font>";

$lang['install_mysql_account_mysql']	= "MySQL 깇?긗?귺긇긂깛긣륃뺪볺쀍";
$lang['install_mysql_account_nalog']	= "n@log 5 듖뿚롌귺긇긂깛긣띿맟";

$lang['install_mysql_input_db_host']	= "긼긚긣뼹";
$lang['install_mysql_input_db_id']	= "DB ID";
$lang['install_mysql_input_db_pass']	= "DB 긬긚깗?긤";
$lang['install_mysql_input_db_name']	= "DB뼹";
$lang['install_mysql_input_admin_id']	= "듖뿚롌 ID";
$lang['install_mysql_input_admin_pass']	= "긬긚깗?긤";
$lang['install_mysql_input_admin_repass']	= "긬긚깗?긤띋볺쀍";

$lang['install_mysql_error_db_host']	= "긼긚긣뼹귩볺쀍궢궲돷궠궋";
$lang['install_mysql_error_db_id']	= "DB ID귩볺쀍궢궲돷궠궋";
$lang['install_mysql_error_db_pass']	= "DB 긬긚깗?긤귩볺쀍궢궲돷궠궋";
$lang['install_mysql_error_db_name']	= "DB뼹귩볺쀍궢궲돷궠궋";
$lang['install_mysql_error_admin_id']	= "듖뿚롌 ID귩볺쀍궢궲돷궠궋";
$lang['install_mysql_error_admin_pass']	= "듖뿚롌 ID귩볺쀍궢궲돷궠궋";
$lang['install_mysql_error_admin_repass']	= "듖뿚롌 ID귩귖궎덇뱗볺쀍궢궲돷궠궋";
$lang['install_mysql_error_admin_match']	= "듖뿚롌긬긚깗?긤궕덇뭭궢귏궧귪";


###################################################################################
//			When Installing... (install_ing.php)
###################################################################################
$lang['install_ing_error_db_id']		= "DB궸먝뫏뢯뿀귏궧귪\\nDB ID궴긬긚깗?긤귩둴봃궢궲돷궠궋";
$lang['install_ing_error_db_name']	= "DB궸먝뫏뢯뿀귏궧귪\\nDB뼹귩둴봃궢궲돷궠궋";
$lang['install_ing_error_permission1']	= "n@log궻귽깛긚긣?깑귩뭷?궠귢귏궢궫\\n긲긅깑??궻긬??긘깈깛궕707뼌궼777궳궶궋궔갂n@log궻긲?귽깑궕밙먛궳궶궋뺴?궳긓긯?궠귢궫궴럙귦귢귏궥\\n\\n귏궦갂긲긅깑??궻긬??긘깈깛귩둴봃궢갂nalog_connect.php귩랁룣궢궲궔귞띋귽깛긚긣?깑궢궲돷궠궋";
$lang['install_ing_error_permission2']	= "n@log궻귽깛긚긣?깑궕뭷?궠귢귏궢궫\\n긲긅깑??궻긬??긘깈깛궕707뼌궼777궳궶궋궔갂n@log궻긲?귽깑궕밙먛궳궶궋뺴?궳긓긯?궠귢궫궴럙귦귢귏궥\\n\\n귏궦갂긲긅깑??궻긬??긘깈깛귩둴봃궢갂nalog_connect.php귩랁룣궢궲궔귞띋귽깛긚긣?깑궢궲돷궠궋";

$lang['install_ing_finish']		= "n@log analyzer궻귽깛긚긣?깑궕뒶뿹궢귏궢궫";


###################################################################################
//			Version Info Check (check.php)
###################################################################################
$lang['version_check_title']		= "띍륷긫?긙깈깛륃뺪";
$lang['version_check_this_version']	= "뙸띪궻긫?긙깈깛: ";
$lang['version_check_latest_version']	= "띍륷긫?긙깈깛: ";
$lang['version_check_update_button']	= "귺긞긵긢?긣";
$lang['version_check_close_button']	= "빧궣귡";


###################################################################################
//			Change Administration Account (change.php)
###################################################################################
$lang['change_admin_title']		= "듖뿚롌귺긇긂깛긣빾뛛";
$lang['change_admin_text']		= "륷궢궋듖뿚롌귺긇긂깛긣";
$lang['change_admin_change_button']	= "빾뛛";
$lang['change_admin_close_button']	= "빧궣귡";

$lang['change_admin_id']			= "듖뿚롌 ID";
$lang['change_admin_pass']		= "긬긚깗?긤";
$lang['change_admin_repass']		= "긬긚깗?긤띋볺쀍";

$lang['change_admin_error_admin_id']	= "빾뛛궥귡듖뿚롌ID귩볺쀍궢궲돷궠궋";
$lang['change_admin_error_admin_pass']	= "빾뛛궥귡듖뿚롌긬긚깗?긤귩볺쀍궢궲돷궠궋";
$lang['change_admin_error_admin_repass']	= "빾뛛궥귡듖뿚롌ID귩볺쀍궢궲돷궠궋귩귖궎괦뱗볺쀍궢궲돷궠궋";
$lang['change_admin_error_admin_match']	= "빾뛛궥귡듖뿚롌긬긚깗?긤궕덇뭭궢귏궧귪";

$lang['change_admin_finish']		= "듖뿚롌귺긇긂깛긣궕빾뛛궠귢귏궢궫";


###################################################################################
//			Program Uninstallation (uninstall.php)
###################################################################################
$lang['uninstall_finish']			= "n@log analyzer궻궥귊궲궻륃뺪궴긡?긳깑귩랁룣궢귏궢궫\\n\\n띋뱗럊궎뤾뜃궼 install.php귩렳뛱궢궲돷궠궋";


###################################################################################
//			Administrator Login Page (login.php)
###################################################################################
$lang['login_title']			= "n@log 듖뿚롌 깓긐귽깛";
$lang['login_id']				= "ID";
$lang['login_pass']			= "긬긚깗?긤";
$lang['login_auto']			= "렔벍깓긐귽깛";

$lang['login_warning_auto']		= "렔벍깓긐귽깛귩럊궎궴긳깋긂긗귩빧궣궫뚣궳귖\\n깓긐귽깛륉뫴귩댸렃궥귡궫귕\\n긬?긓깛귩떎뾎궥귡뤾뜃궼뭾댰궢궲돷궠궋\\n\\n렔벍깓긐귽깛귩럊궋귏궥궔갎";
$lang['login_error_id']			= "ID귩볺쀍궢궲돷궠궋";
$lang['login_error_pass']			= "긬긚깗?긤귩볺쀍궢궲돷궠궋";

$lang['login_error_id_wrong']		= "ID궕맫궢궘궇귟귏궧귪";
$lang['login_error_pass_wrong']		= "긬긚깗?긤궕맫궢궘궇귟귏궧귪";


###################################################################################
//			Root Manager (root.php)
###################################################################################
$lang['root_title']			= "듖뿚롌긽긦깄?";
$lang['root_alt_counter_manager']		= "긇긂깛?듖뿚";
$lang['root_alt_version_check']		= "띍륷긫?긙깈깛둴봃";
$lang['root_alt_navyism_com']		= "n@log 5 긆긲귻긘긿깑긖귽긣";
$lang['root_alt_change_admin']		= "듖뿚롌귺긇긂깛긣빾뛛";
$lang['root_alt_uninstall']		= "n@log 5 랁룣";
$lang['root_warning_uninstall']		= "n@log analyzer귩랁룣궥귡궴갂\\n궥귊궲궻긇긂깛?궻깓긐딯?궴먠믦궕랁룣궠귢귏궥\\n\\n 랁룣궢귏궥궔갎";

$lang['root_change_language_button']	= "뙻뚭빾뛛";


###################################################################################
//			Counter Manager (admin.php)
###################################################################################
$lang['counter_manager_title']		= "긇긂깛?듖뿚";
$lang['counter_manager_paging1']		= "&nbsp;&nbsp;똶 ";
$lang['counter_manager_paging2']		= "뙿궻긇긂깛?, 뙸띪 ";
$lang['counter_manager_paging3']		= "긻?긙, 똶 ";
$lang['counter_manager_paging4']		= "긻?긙";
$lang['counter_manager_view']		= "?렑궥귡긇긂깛?궻릶";
$lang['counter_manager_view_button']	= "둴봃";
$lang['counter_manager_view_error']	= "뵾둷릶럻궳볺쀍궢궲돷궠궋";

$lang['counter_manager_table_no']		= "붥뜂";
$lang['counter_manager_table_name']	= "긇긂깛?뼹";
$lang['counter_manager_table_config']	= "듏떕먠믦";
$lang['counter_manager_table_example']	= "긖깛긵깑";
$lang['counter_manager_table_drop']	= "랁룣";
$lang['counter_manager_table_clean']	= "룊딖돸";
$lang['counter_manager_table_total']	= "뜃똶";
$lang['counter_manager_table_today']	= "뜞볷";
$lang['counter_manager_table_today_peak'] = "띍묈";
$lang['counter_manager_table_peak']	= "띍묈벏렄먝뫏";
$lang['counter_manager_tablecell_view']	= "긖깛긵깑";
$lang['counter_manager_tablecell_drop']	= "랁룣";
$lang['counter_manager_tablecell_clean']	= "룊딖돸";

$lang['counter_manager_warning_drop']	= "멗묖궠귢궫긇긂깛?귩랁룣궢귏궥\\n덇뱗랁룣궠귢궫륃뺪궼뙰궸뽣궧귏궧귪\\n\\n뫏궚귏궥궔갎";
$lang['counter_manager_warning_clean']	= "멗묖궠귢궫긇긂깛?귩룊딖돸궢귏궥\\n긇긂깛?궻먠믦궼빾귦귟귏궧귪\\n\\n뫏궚귏궥궔갎";

$lang['counter_manager_create_button']	= "긇긂깛?띿맟";
$lang['counter_manager_error_create']	= "띿맟궥귡긇긂깛?뼹귩볺쀍궢궲돷궠궋";


###################################################################################
//			Creating Counter (admin_ing.php)
###################################################################################
$lang['counter_create_error_name']	= "띿맟궥귡긇긂깛?뼹귩볺쀍궢궲돷궠궋";
$lang['counter_create_error_char']	= "긇긂깛?뼹궸궼깓??럻갂릶럻갂 _ ?귩룣궋궫딯뜂궶궵궼럊궑귏궧귪";
$lang['counter_create_error_exist']	= "뫔띪궥귡긇긂깛?뼹궳궥";
$lang['counter_create_error_blank']	= "긇긂깛?뼹궸궼뗴뵏궕궇궯궲궼궋궚귏궧귪";


###################################################################################
//			Counter Manager - Overall (admin_counter.php)
###################################################################################
$lang['counter_main_plug_in']		= "긵깋긐귽깛귩멗묖궢궲돷궠궋";

$lang['counter_main_date_format1']	= "Y-m-d H:i:s (D)";
$lang['counter_main_not_exist']		= "뫔띪궢궶궋긇긂깛?궳궥";

$lang['counter_main_title']		= "긇긂깛?둴봃";
$lang['counter_main_title_hour']		= "렄듩빶뱷똶";
$lang['counter_main_title_day']		= "볷빶";
$lang['counter_main_title_week']		= "뾧볷빶";
$lang['counter_main_title_month']		= "뙉빶";
$lang['counter_main_title_year']		= "봏빶";
$lang['counter_main_title_refer']		= "깏깛긏귺긤깒긚뱷똶 (긖?긫?뼹)";
$lang['counter_main_title_refer_detail']	= "깏깛긏귺긤깒긚뱷똶 (URL)";
$lang['counter_main_title_os']		= "OS & 긳깋긂긗";
$lang['counter_main_title_visitor']	= "뻂뽦롌륃뺪둴봃";
$lang['counter_main_title_config']	= "긇긂깛?먠믦";

$lang['counter_main_menu_hour']		= "렄듩빶";
$lang['counter_main_menu_day']		= "볷빶";
$lang['counter_main_menu_week']		= "뾧볷빶";
$lang['counter_main_menu_month']		= "뙉빶";
$lang['counter_main_menu_year']		= "봏빶";
$lang['counter_main_menu_refer']		= "깏깛긏궠귢궫긖?긫?";
$lang['counter_main_menu_refer_detail']	= "깏깛긏궠귢궫긻?긙";
$lang['counter_main_menu_os']		= "OS & 긳깋긂긗";
$lang['counter_main_menu_visitor']	= "뻂뽦롌";
$lang['counter_main_menu_config']		= "듏떕먠믦";

$lang['counter_main_year']		= "봏";
$lang['counter_main_month']		= "뙉";
$lang['counter_main_day']			= "볷";

$lang['counter_main_button_view']		= "둴봃";
$lang['counter_main_button_view_all']	= "멣븫";
$lang['counter_main_button_print']	= "덐랛";
$lang['counter_main_button_back']		= "뽣귡";
$lang['counter_main_button_check_all']	= "궥귊궲멗묖";
$lang['counter_main_button_cancel_all']	= "멗묖긌긿깛긜깑";
$lang['counter_main_button_search']	= "뙚랊";
$lang['counter_main_button_delete']	= "멗묖궠귢궫깓긐귩랁룣";


###################################################################################
//			Counter Manager - Part 1 (by Hour)
###################################################################################
$lang['counter_main_1_date_format']	= "Y봏 n뙉 j볷";
$lang['counter_main_1_date']		= "볷븊: ";
$lang['counter_main_1_today']		= "뜞볷";
$lang['counter_main_1_sum']		= "긣??깑";
$lang['counter_main_1_total']		= " , 뜃똶: ";
$lang['counter_main_1_total_visitor']	= "릐궻뻂뽦롌";
$lang['counter_main_1_hour_format']	= "H렄";
$lang['counter_main_1_hour']		= "렄";
$lang['counter_main_1_visitor']		= "릐";
$lang['counter_main_1_view_visitor']	= "{yy}봏 {mm}뙉 {dd}볷 {hh}렄궻뻂뽦롌깏긚긣둴봃";


###################################################################################
//			Counter Manager - Part 2 (by Day)
###################################################################################
$lang['counter_main_2_date_format']	= "Y봏 n뙉";
$lang['counter_main_2_month']		= "뙉: ";
$lang['counter_main_2_this_month']	= "뜞뙉";
$lang['counter_main_2_sum']		= "긣??깑";
$lang['counter_main_2_total']		= " , 뜃똶: ";
$lang['counter_main_2_total_visitor']	= "릐궻뻂뽦롌";
$lang['counter_main_2_day_format']	= "j볷";
$lang['counter_main_2_visitor']		= "릐";
$lang['counter_main_2_view_visitor']	= "{yy}봏 {mm}뙉 {dd}볷궻렄듩뱷똶둴봃";


###################################################################################
//			Counter Manager - Part 3 (by Week)
###################################################################################
$lang['counter_main_3_sum']		= "긣??깑";
$lang['counter_main_3_total']		= " , 뜃똶: ";
$lang['counter_main_3_total_visitor']	= "릐궻뻂뽦롌";
$lang['counter_main_3_average']		= " , 빟뗉괦뢙듩: ";
$lang['counter_main_3_average_visitor']	= "릐궻뻂뽦롌";
$lang['counter_main_3_visitor']		= "릐";

$lang['counter_main_3_day_name0']		= "볷뾧볷";
$lang['counter_main_3_day_name1']		= "뙉뾧볷";
$lang['counter_main_3_day_name2']		= "됌뾧볷";
$lang['counter_main_3_day_name3']		= "릣뾧볷";
$lang['counter_main_3_day_name4']		= "뽜뾧볷";
$lang['counter_main_3_day_name5']		= "뗠뾧볷";
$lang['counter_main_3_day_name6']		= "뱘뾧볷";


###################################################################################
//			Counter Manager - Part 4 (by Month)
###################################################################################
$lang['counter_main_4_year']		= "봏: ";
$lang['counter_main_4_this_year']		= "뜞봏";
$lang['counter_main_4_sum']		= "긣??깑";
$lang['counter_main_4_total']		= ", 뜃똶: ";
$lang['counter_main_4_total_visitor']	= "릐궻뻂뽦롌";
$lang['counter_main_4_month_format']	= "n뙉";
$lang['counter_main_4_visitor']		= "릐";
$lang['counter_main_4_view_visitor']	= "{yy}봏 {mm}뙉궻볷빶뱷똶둴봃";


###################################################################################
//			Counter Manager - Part 5 (by Year)
###################################################################################
$lang['counter_main_5_sum']		= "긣??깑";
$lang['counter_main_5_total']		= ", 뜃똶: ";
$lang['counter_main_5_total_visitor']	= "릐궻뻂뽦롌";
$lang['counter_main_5_year_format']	= "Y봏";
$lang['counter_main_5_visitor']		= "릐";
$lang['counter_main_5_view_visitor']	= "{yy}봏궻뙉빶뱷똶둴봃";


###################################################################################
//			Counter Manager - Part 6 (by Referers - Host & URL)
###################################################################################
$lang['counter_main_6_date_format']	= "Y봏 m뙉 d볷 H렄 i빁 s뷳";
$lang['counter_main_6_total']		= "뜃똶: ";
$lang['counter_main_6_total_url']		= "궰궻 URL, ";
$lang['counter_main_6_total_visitor']	= "릐궻뻂뽦롌";
$lang['counter_main_6_total_zero']	= "깓긐긲?귽깑궕궇귟귏궧귪";
$lang['counter_main_6_total_delete']	= "멗묖궢궫깓긐긲?귽깑귩랁룣궢귏궥궔갎";

$lang['counter_main_6_today_only']	= "뜞볷궻깓긐궻귒";
$lang['counter_main_6_sort_by']		= "빥귊듂궑";

$lang['counter_main_6_sort_1']		= "뻂뽦롌룈";
$lang['counter_main_6_sort_2']		= "뻂뽦롌땤룈";
$lang['counter_main_6_sort_3']		= "렄듩룈";
$lang['counter_main_6_sort_4']		= "렄듩땤룈";
$lang['counter_main_6_sort_5']		= "URL룈";
$lang['counter_main_6_sort_6']		= "URL땤룈";

$lang['counter_main_6_search_negative']	= "뙚랊뾭";
$lang['counter_main_6_search_and']	= "and";
$lang['counter_main_6_search_or'] 	= "or";

$lang['counter_main_6_table_url']		= "먝뫏긖?긫? (긼긚긣뼹 뼌궼 URL갂띍뚣궻먝뫏렄듩)";
$lang['counter_main_6_table_hit']		= "먝뫏롌릶";

$lang['counter_main_6_url_remember']	= "URL 딯?";
$lang['counter_main_6_url_forget']	= "URL 딯?긌긿깛긜깑";

$lang['counter_main_6_url_remember_button']="<span lang=ja style=font-size:8pt>[ 딯? ']</font>";
$lang['counter_main_6_url_forget_button']	= "<span lang=ja style=font-size:8pt;color:#F7418C>[긌긿깛긜깑']</span>";

$lang['counter_main_6_direct_connect']	= "귺긤깒긚뮳먝볺쀍 뼌궼 궓딠궸볺귟귩뿕뾭궢궫뻂뽦";
$lang['counter_main_6_view_detail_url']	= "뤬띢먝뫏깑?긣";
$lang['counter_main_6_delete_button']	= "깓긐긲?귽깑랁룣";
$lang['counter_main_6_delete_question']	= "?뱰궸랁룣궢궲귝귣궢궋궳궥궔갎";

$lang['counter_main_6_error_pagenum']	= "릶럻귩볺쀍궢궲돷궠궋";


###################################################################################
//			Counter Manager - Part 7 (by Visitors' OS & Browser)
###################################################################################
$lang['counter_main_7_total']		= "뜃똶: ";
$lang['counter_main_7_total_os']		= "롰쀞궻OS, ";
$lang['counter_main_7_total_browser']	= "롰쀞궻긳깋긂긗, ";
$lang['counter_main_7_total_visitor']	= "릐궻뻂뽦롌";
$lang['counter_main_7_visitor']		= "릐";
$lang['counter_main_7_total_zero']	= "깓긐긲?귽깑궕궇귟귏궧귪";

$lang['counter_main_7_title_os']		= "뻂뽦롌궻OS";
$lang['counter_main_7_title_browser']	= "뻂뽦롌궻긳깋긂긗";

$lang['counter_main_7_error_pagenum']	= "릶럻궳볺쀍궢궲돷궠궋";


###################################################################################
//			Counter Manager - Part 8 (by Visitors' Information)
###################################################################################
$lang['counter_main_8_date_format']	= "Y봏 m뙉 d볷 H렄 i빁 s뷳";
$lang['counter_main_8_total']		= "뜃똶: ";
$lang['counter_main_8_total_visitor']	= "릐궻뻂뽦롌";
$lang['counter_main_8_total_zero']	= "깓긐긲?귽깑궕궇귟귏궧귪";
$lang['counter_main_8_today_only']	= "뜞볷궻깓긐궻귒";
$lang['counter_main_8_member_only']	= "됵덒딯?궻귒";
$lang['counter_main_8_sort_by']		= "빥귊듂궑";

$lang['counter_main_8_sort_1']		= "렄듩룈";
$lang['counter_main_8_sort_2']		= "렄듩땤룈";
$lang['counter_main_8_sort_3']		= "됵덒ID룈";
$lang['counter_main_8_sort_4']		= "됵덒ID땤룈";

$lang['counter_main_8_title_1']		= "뻂뽦롌궻 ID / 깏깛긏궠귢궫긻?긙 / OS / 긳깋긂긗";
$lang['counter_main_8_title_2']		= "뻂뽦롌궻 IP / 뻂뽦렄뜌";

$lang['counter_main_8_right_arrow']	= "<span style=font-size:6pt>&#9654;</span> ";
$lang['counter_main_8_direct_connect']	= "귺긤깒긚뮳먝볺쀍 뼌궼 궓딠궸볺귟귩뿕뾭궢궫뻂뽦";
$lang['counter_main_8_not_login']		= "뼟둴봃";
$lang['counter_main_8_unknown_os']	= "븉뼻궶OS";
$lang['counter_main_8_unknown_browser']	= "븉뼻궶긳깋긂긗";
$lang['counter_main_8_search']		= "뻂뽦딯?뙚랊";

$lang['counter_main_8_error_pagenum']	= "릶럻궳볺쀍궢궲돷궠궋";


###################################################################################
//			Counter Manager - Part 9 (Configuration)
###################################################################################
$lang['counter_config_total']		= "뜃똶?뻂뽦롌릶";
$lang['counter_config_skin']		= "긚긌깛먠믦";
$lang['counter_config_skin_pattern']	= "긚긌깛긬??깛긲?귽깑럊뾭";
$lang['counter_config_skin_pattern_use']	= "긚긌깛긬??깛긲?귽깑귩럊뾭궥귡";
$lang['counter_config_reconnect']		= "띋먝뫏먠믦";
$lang['counter_config_reconnect_always']	= "륂궸긇긂깛?궕몵궑귡 (긏긞긌?럊뾭뼰궢)";
$lang['counter_config_reconnect_new_open']= "긳깋긂긗띋딳벍렄갂긇긂깛?궕몵궑귡 (긏긞긌?렄듩 : 0 sec)";
$lang['counter_config_reconnect_by_time1']= "럚믦궠귢궫렄듩뚣갂긇긂깛?궕몵궑귡 (긏긞긌?렄듩 : ";
$lang['counter_config_reconnect_by_time2']= " sec)";
$lang['counter_config_reconnect_once']	= "덇볷궸덇됷궻귒몵궑귡";
$lang['counter_config_time_zone1']	= "딯?렄듩뫱빾뛛";
$lang['counter_config_time_zone2']	= "렄듩 + 긖?긫?궻뙸뭤렄듩 [궓뒰귕뢯뿀귏궧귪']";
$lang['counter_config_admin_check']	= "듖뿚롌먝뫏?긃긞긏";
$lang['counter_config_admin_check_not']	= "듖뿚롌궻먝뫏궼긇긂깛?궔귞둖궥";
$lang['counter_config_now_check']		= "뙸띪먝뫏롌?긃긞긏";
$lang['counter_config_now_check_use']	= "뙸띪먝뫏롌귩?긃긞긏궥귡";
$lang['counter_config_now_time']		= "먝뫏댸렃렄듩";
$lang['counter_config_now_time_use1']	= "";
$lang['counter_config_now_time_use2']	= " 뷳듩갂먝뫏궠귢궲궋귡궴먠믦 (10뷳댥뤵)";
$lang['counter_config_admin_data']	= "뱷똶럱뿿듖뿚";
$lang['counter_config_admin_data_delete1']= "뱷똶딯?랁룣";
$lang['counter_config_admin_data_delete2']= " 볷빶갂뾧볷빶갂뙉빶갂봏빶뱷똶딯?귩랁룣궥귡";
$lang['counter_config_admin_os']		= "OS & Browser 럱뿿듖뿚";
$lang['counter_config_admin_os_delete1']	= "OS & Browser 딯?랁룣";
$lang['counter_config_admin_os_delete2']	= " OS & Browser 뱷똶귩랁룣궥귡";
$lang['counter_config_visitor_check']	= "먝뫏럱뿿?긃긞긏";
$lang['counter_config_visitor_check_use']	= "뻂뽦롌딯??긃긞긏";
$lang['counter_config_visitor_limit']	= "먝뫏럱뿿맕뙽";
$lang['counter_config_visitor_delete1']	= "먝뫏럱뿿랁룣";
$lang['counter_config_visitor_delete2']	= " 먝뫏럱뿿귩랁룣궥귡";
$lang['counter_config_visitor_limit_set1']= "";
$lang['counter_config_visitor_limit_set2']= " 릐빁궻딯?궬궚뺎뫔 (0궳뼰맕뙽)";
$lang['counter_config_log_check']		= "깓긐긲?귽깑?긃긞긏";
$lang['counter_config_log_check_use']	= "뻂뽦롌궻깓긐귩?긃긏궥귡";
$lang['counter_config_log_limit']		= "깓긐긲?귽깑맕뙽";
$lang['counter_config_log_delete1']	= "깓긐긲?귽깑랁룣";
$lang['counter_config_log_delete2']	= " 깓긐긲?귽깑귩랁룣궥귡";
$lang['counter_config_log_limit_set1']	= "";
$lang['counter_config_log_limit_set2']	= " 뙿궻깓긐딯?궻귒뺎뫔 (0궳뼰맕뙽)";
$lang['counter_config_member_cookie']	= "됵덒뗦빁?긏긞긌?뼹";
$lang['counter_config_member_cookie_is']	= "(<b>n@board 3:</b> na3_member)";
$lang['counter_config_permission']	= "뙛뙽먠믦";
$lang['counter_config_permission1']	= "듖뿚롌궻귒갂렄듩빶뱷똶둴봃됀";
$lang['counter_config_permission2']	= "듖뿚롌궻귒갂볷빶뱷똶둴봃됀";
$lang['counter_config_permission3']	= "듖뿚롌궻귒갂뾧볷빶뱷똶둴됀";
$lang['counter_config_permission4']	= "듖뿚롌궻귒갂뙉빶뱷똶둴봃됀";
$lang['counter_config_permission5']	= "듖뿚롌궻귒갂봏빶뱷똶둴봃됀";
$lang['counter_config_permission6']	= "듖뿚롌궻귒갂깓긐뱷똶둴봃됀";
$lang['counter_config_permission7']	= "듖뿚롌궻귒갂뤬띢깓긐뱷똶둴봃됀";
$lang['counter_config_permission8']	= "듖뿚롌궻귒갂OS/긳깋긂긗 뱷똶둴봃됀";
$lang['counter_config_permission9']	= "듖뿚롌궻귒갂뻂뽦롌뱷똶둴봃됀";

$lang['counter_config_warning_data']	= "뱷똶딯?귩랁룣궥귡궴\\n볷빶갂뾧볷빶갂뙉빶갂봏빶뱷똶궕궥귊궲랁룣궠귢귏궥\\n\\n뫏궚귏궥궔갎";
$lang['counter_config_warning_os']	= "OS & Browser 딯?귩랁룣궥귡궴\\n뺎뫔궠귢궲궋귡 OS & Browser 듫쁀딯?궕궥귊궲랁룣궠귢귏궥\\n\\n뫏궚귏궥궔갎";
$lang['counter_config_warning_visitor']	= "뻂뽦롌딯?귩랁룣궥귡궴\\n뺎뫔궠귢궲궋귡뻂뽦롌딯?궕궥귊궲랁룣궠귢귏궥\\n\\n뫏궚귏궥궔갎";
$lang['counter_config_warning_log']	= "깓긐딯?귩랁룣궥귡궴\\n긖?긫?땩귂뤬띢먝뫏딯?궕궥귊궲랁룣궠귢귏궥\\n\\n뫏궚귏궥궔갎";

$lang['counter_config_button_save']	= " 뺎뫔 ";
$lang['counter_config_button_reset']	= "긌긿깛긜깑";

$lang['counter_manager_error_not_exist']	= "뫔띪궢궶궋긇긂깛?궳궥";
$lang['counter_manager_error_total_is']	= "뜃똶뻂뽦롌릶궼뵾둷릶럻궳볺쀍궢궲돷궠궋";
$lang['counter_manager_error_cookie_time']= "긏긞긌?렄듩궼뵾둷릶럻궳볺쀍궢궲돷궠궋";
$lang['counter_manager_error_connect_time']="먝뫏댸렃렄듩궼뵾둷릶럻궳볺쀍궢궲돷궠궋";
$lang['counter_manager_error_log_limit']	= "깓긐럱뿿맕뙽궼뵾둷릶럻궳볺쀍궢궲돷궠궋";

if (!isset($ip)) {
	$ip = '';
}
###################################################################################
//			IP Address Information Check (check_ip.php)
###################################################################################
$lang['check_ip_title']			= "IP륃뺪뤖됵 : ";
$lang['check_ip_support']			= "렲뽦 땩귂 듫쁀륃뺪";
$lang['check_ip_close']			= "빧궣귡";
$lang['check_ip_false_msg']		= "whois 긖?긫?궴궻먝뫏궕뢯뿀귏궧귪궳궢궫갃<br>궢궽귞궘궢궲궔귞렔벍궸 http://www.apnic.net궻 IP륃뺪둴봃긻?긙궸댷벍궢귏궥<br>렔벍궸댷귞궶궋뤾뜃궼렅궻깏깛긏귩긏깏긞긏궢갂IP륃뺪귩둴봃궢궲돷궠궋<br><br><a href=http://www.apnic.net/apnic-bin/whois.pl?searchtext=$ip>http://www.apnic.net/apnic-bin/whois.pl?searchtext=$ip</a>";
$lang['check_ip_right_arrow']		= "<span style=font-size:6pt>&#9654;</span>";
?>
