<?php
####################################################################################
/*
			navyism@log analyzer 5	
			English Language Pack

* WARNING:

	You are advised to avoid using special characters such as ' , " , \ within
	variables. If you do so, it would cause unpredictable error on execution.
	You should use other character like ` as a replacement to quotation marks.

*/
####################################################################################


####################################################################################
//			Include Version Info File (required)
####################################################################################
include"nalog_info.php";


####################################################################################
//			Language Information (naming in English only)
####################################################################################
$lang['name']		= "English (iso-8859-1)";
$lang['english_name'] 	= "English";


####################################################################################
//			Page Header (please do not modify)
####################################################################################
$lang['head']		= "<!-----------------------------------------------------------------------------------------------------


								 ========================================
								 Program	: navyism@log analyzer
								 Version	: {$nalog_info['version']}
								 Date		: {$nalog_info['date']}
								 Programmer : navyism
								 E-mail	 : navyism@navyism.com
								 Homepage	: http://english.navyism.com
								 ========================================
								 Language	: English (iso-8859-1)
								 Version	: v1.0.2 for n@log 5.0.2
								 Date		: 2003.02.27
								 Translator : kiddiken(+navyism)
								 E-mail	 : webmaster@kiddiken.net
								 Homepage	: http://kiddiken.net
								 ========================================



The copyright owner of this program is belonging to navyism only.
You can use and modify this program, provided that you reserve to display the copyright notice.

navyism has no responsibility for failures or loss of data on your using of this program.
Thus, navyism is not responsible for repairing of any problems occurred or give you promptly help.

This program is freeware. Please DO NOT use it for any kind of business.

If you want to modify any file of this program (or any skin),
or to distribute the modified program (or any skin) on the internet,
you MUST retain the names and web site addresses of original program author,
skin designer(s) and language pack maker(s) in the copyright notice.
------------------------------------------------------------------------------------------------------>

<html>
<head>
<title>n@log analyzer {$nalog_info['version']}</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<meta name=\"Description\" content=\"navyism@log\">
<meta name=\"Keywords\" content=\"navyism@log,n@log\">
<meta name=\"Author\" content=\"navyism\">
<link rel=\"stylesheet\" type=\"text/css\" href=\"language/$language/style.css\">
</head>
";
$lang['copy']	= "<span style='font-size:7pt'>n@log analyzer {$nalog_info['version']} &copy;2001-2003 <a href=http://english.navyism.com target=_blank><span style='font-size:7pt'><b>navyism</b></span></a><br>English LangPack by <a href=http://kiddiken.net target=_blank><span style='font-size:7pt'><b>kiddiken</b></span></a>+<a href=http://english.navyism.com target=_blank><span style='font-size:7pt'><b>navyism</b></span></a></span>";


###################################################################################
//			Displaying License Agreement (install.php)
###################################################################################
$lang['install_license_textarea_rows']	= 11;
$lang['install_license_title']		= "Software License Agreement";
$lang['install_license_agreement']	= "<b>Please read the following License Agreement carefully.</b>";
$lang['install_license_text']		= "The copyright owner of this program is belonging to navyism only. You can use and modify this program, provided that you reserve to display the copyright notice.

navyism has no responsibility for failures or loss of data on your using of this program. Thus, navyism is not responsible for repairing of any problems occurred or give you promptly help.

This program is freeware. Please DO NOT use it for any kind of business.

If you want to modify any file of this program (or any skin), or to distribute the modified program (or any skin) on the internet, you MUST retain the names and web site addresses of original program author, skin designer(s) and language pack maker(s) in the copyright notice.";
$lang['install_license_ask']		= "<center>Do you understand and accept all the terms of this agreement?</center><br>";
$lang['install_license_agree']		= "Yes, I accept";
$lang['install_license_decline']		= "No, I decline";


###################################################################################
//			Setup MySQL Connection (install_er.php)
###################################################################################
$lang['install_mysql_title']		= "Setup MySQL Connection";
$lang['install_mysql_text']		= "n@log 5 requires a <b>MySQL Database</b> to store your site statistics information.<br><br>
If you want to access MySQL, you must have both <b>MySQL user account (DB User)</b> and <b>MySQL database name (DB Name).</b><br><br>
For further details, please contact the administrator of your web hosting provider.<br>
<font color=tomato>(NOTE: MySQL user account and FTP user account are two different things!)</font>";

$lang['install_mysql_account_mysql']	= "MySQL Account Information";
$lang['install_mysql_account_nalog']	= "Setup n@log 5 Administration Account";

$lang['install_mysql_input_db_host']	= "DB Host Name";
$lang['install_mysql_input_db_id']	= "DB User ID";
$lang['install_mysql_input_db_pass']	= "DB User Password";
$lang['install_mysql_input_db_name']	= "DB Name";
$lang['install_mysql_input_admin_id']	= "Admin ID";
$lang['install_mysql_input_admin_pass']	= "Admin Password";
$lang['install_mysql_input_admin_repass']	= "Re-enter Password";

$lang['install_mysql_error_db_host']	= "Please input DB Host Name";
$lang['install_mysql_error_db_id']	= "Please input DB User ID";
$lang['install_mysql_error_db_pass']	= "Please input DB User Password";
$lang['install_mysql_error_db_name']	= "Please input DB Name";
$lang['install_mysql_error_admin_id']	= "Please input Admin ID";
$lang['install_mysql_error_admin_pass']	= "Please input Admin Password";
$lang['install_mysql_error_admin_repass']	= "Please re-enter Admin Password in the specified field for confirmation.";
$lang['install_mysql_error_admin_match']	= "The re-entered Admin Password does not match, please try again.";


###################################################################################
//			When Installing... (install_ing.php)
###################################################################################
$lang['install_ing_error_db_id']		= "Connection failed.\\nPlease check your DB host name, user ID and password before you try again.";
$lang['install_ing_error_db_name']	= "Connection failed.\\nPlease check your DB name before you try again.\\n(or you did not create this DB?)";
$lang['install_ing_error_permission1']	= "Permission denied.\\nPlease make sure the permission of n@log folder is 707 or 777\\n(or delete \\'nalog_connect.php\\') and try again.";
$lang['install_ing_error_permission2']	= "Permission denied.\\nPlease make sure the permission of n@log folder is 707 or 777\\n(or delete \\'nalog_language.php\\') and try again.";

$lang['install_ing_finish']		= "Congratulations!\\nThe installation of n@log analyzer is finished.";


###################################################################################
//			Version Info Check (check.php)
###################################################################################
$lang['version_check_title']		= "Version Info Check";
$lang['version_check_this_version']	= "Your n@log version: ";
$lang['version_check_latest_version']	= "Latest n@log version: ";
$lang['version_check_update_button']	= "Download Latest Version";
$lang['version_check_close_button']	= "Close";


###################################################################################
//			Change Administration Account (change.php)
###################################################################################
$lang['change_admin_title']		= "Change Administration Account";
$lang['change_admin_text']		= "Administration Account Info Update";
$lang['change_admin_change_button']	= "Change";
$lang['change_admin_close_button']	= "Close";

$lang['change_admin_id']			= "New Admin ID (or remain unchanged)";
$lang['change_admin_pass']		= "New Admin Password";
$lang['change_admin_repass']		= "Re-type Admin Password";

$lang['change_admin_error_admin_id']	= "Please input New Admin ID";
$lang['change_admin_error_admin_pass']	= "Please input New Admin Password";
$lang['change_admin_error_admin_repass']	= "Please re-enter New Admin Password in the specified field for confirmation.";
$lang['change_admin_error_admin_match']	= "The re-entered Admin Password does not match, please try again.";

$lang['change_admin_finish']		= "The information of n@log administration account is successfully changed.";


###################################################################################
//			Program Uninstallation (uninstall.php)
###################################################################################
$lang['uninstall_finish']			= "The uninstallation of n@log analyzer is finished.\\nIf you would like to re-install this program again,\\nplease execute install.php in the program\\'s root folder.";


###################################################################################
//			Administrator Login Page (login.php)
###################################################################################
$lang['login_title']			= "n@log Admin Login";
$lang['login_id']				= "Admin ID";
$lang['login_pass']			= "Admin Password";
$lang['login_auto']			= "Auto Login?";

$lang['login_warning_auto']		= "If you choose Auto Login, the \\'logged in\\' status will be remained after you\\nclose the browser, so that you don\\'t need to logout and you will be\\nautomatically logged in every time you enter the counter manager.\\n\\nPlease DO NOT use this feature if you are using computer in public places\\ne.g. net-cafe, school, etc. \\n\\nAre you sure to use Auto Login?";
$lang['login_error_id']			= "Please input Admin ID";
$lang['login_error_pass']			= "Please input Admin Password";

$lang['login_error_id_wrong']		= "The Admin ID you entered is not correct.";
$lang['login_error_pass_wrong']		= "The Admin Password you entered is not correct.";


###################################################################################
//			Root Manager (root.php)
###################################################################################
$lang['root_title']			= "ROOT Manager";
$lang['root_alt_counter_manager']		= "Manage your n@log counter(s)";
$lang['root_alt_version_check']		= "Check online if there is a new version of n@log";
$lang['root_alt_navyism_com']		= "Visit navyism&#39;s homepage [Korean']";
$lang['root_alt_change_admin']		= "Change the information of n@log administration account";
$lang['root_alt_uninstall']		= "Uninstall n@log";
$lang['root_warning_uninstall']		= "Are you sure to uninstall n@log analyzer?";

$lang['root_change_language_button']	= "Change";


###################################################################################
//			Counter Manager (admin.php)
###################################################################################
$lang['counter_manager_title']		= "Counter Manager";
$lang['counter_manager_paging1']		= "<font size=1>&nbsp;&nbsp;";
$lang['counter_manager_paging2']		= " counter(s), ";
$lang['counter_manager_paging3']		= " of ";
$lang['counter_manager_paging4']		= " page(s)</font>";
$lang['counter_manager_view']		= "View how many counters at one page: ";
$lang['counter_manager_view_button']	= "View";
$lang['counter_manager_view_error']	= "This is not a number";

$lang['counter_manager_table_no']		= "No.";
$lang['counter_manager_table_name']	= "Counter Name";
$lang['counter_manager_table_config']	= "Config";
$lang['counter_manager_table_example']	= "Usage Example";
$lang['counter_manager_table_drop']	= "Delete?";
$lang['counter_manager_table_clean']	= "Clean?";
$lang['counter_manager_table_total']	= "Total";
$lang['counter_manager_table_today']	= "Today";
$lang['counter_manager_table_today_peak'] = "Day Peak";
$lang['counter_manager_table_peak']	= "Online Peak";
$lang['counter_manager_tablecell_view']	= "View";
$lang['counter_manager_tablecell_drop']	= "Delete";
$lang['counter_manager_tablecell_clean']	= "Clean";

$lang['counter_manager_warning_drop']	= "Are you sure to delete this counter?";
$lang['counter_manager_warning_clean']	= "Are you sure to clean up this counter?";

$lang['counter_manager_create_button']	= "Create Counter";
$lang['counter_manager_error_create']	= "Please input the counter\\'s name.";


###################################################################################
//			Creating Counter (admin_ing.php)
###################################################################################
$lang['counter_create_error_name']	= "Please input the counter\\'s name.";
$lang['counter_create_error_char']	= "Please use lowercase alphabet (a-z) or numbers (0-9)\\nas well as _ characters only.";
$lang['counter_create_error_exist']	= "This counter already exists.";
$lang['counter_create_error_blank']	= "Please do not use space in the counter\\'s name.";


###################################################################################
//			Counter Manager - Overall (admin_counter.php)
###################################################################################
$lang['counter_main_plug_in']		= "Please select a Plug-in";

$lang['counter_main_date_format1']	= "n/j/Y H:i:s D";
$lang['counter_main_not_exist']		= "This counter does not exist";

$lang['counter_main_title']		= "Counter Manager";
$lang['counter_main_title_hour']		= "Analyze by Hour";
$lang['counter_main_title_day']		= "Analyze by Day";
$lang['counter_main_title_week']		= "Analyze by Week";
$lang['counter_main_title_month']		= "Analyze by Month";
$lang['counter_main_title_year']		= "Analyze by Year";
$lang['counter_main_title_refer']		= "Analyze by Referers (Host)";
$lang['counter_main_title_refer_detail']	= "Analyze by Referers (URL)";
$lang['counter_main_title_os']		= "Analyze by Visitors' OS&Browser";
$lang['counter_main_title_visitor']	= "Analyze by Visitors' Info.";
$lang['counter_main_title_config']	= "Configuration";

$lang['counter_main_menu_hour']		= "Hour";
$lang['counter_main_menu_day']		= "Day";
$lang['counter_main_menu_week']		= "Week";
$lang['counter_main_menu_month']		= "Month";
$lang['counter_main_menu_year']		= "Year";
$lang['counter_main_menu_refer']		= "Referer Host";
$lang['counter_main_menu_refer_detail']	= "Referer URL";
$lang['counter_main_menu_os']		= "OS+Browser";
$lang['counter_main_menu_visitor']	= "Visitors";
$lang['counter_main_menu_config']		= "Config";

$lang['counter_main_year']		= "<span style=font-size:6pt>&#9664; </span>Y";
$lang['counter_main_month']		= "<span style=font-size:6pt>&#9664; </span>M";
$lang['counter_main_day']			= "<span style=font-size:6pt>&#9664; </span>D";

$lang['counter_main_button_view']		= "View";
$lang['counter_main_button_view_all']	= "View All";
$lang['counter_main_button_print']	= "Print";
$lang['counter_main_button_back']		= "Back";
$lang['counter_main_button_check_all']	= "Select All";
$lang['counter_main_button_cancel_all']	= "Select None";
$lang['counter_main_button_search']	= "Search";
$lang['counter_main_button_delete']	= "Delete Selected Log";


###################################################################################
//			Counter Manager - Part 1 (by Hour)
###################################################################################
$lang['counter_main_1_date_format']	= "n/j/Y";
$lang['counter_main_1_date']		= "Date: ";
$lang['counter_main_1_today']		= "Today";
$lang['counter_main_1_sum']		= "Cumulative";
$lang['counter_main_1_total']		= ", Total: ";
$lang['counter_main_1_total_visitor']	= " visitor(s)";
$lang['counter_main_1_hour_format']	= "H\h\\r";
$lang['counter_main_1_visitor']		= " visitor(s)";
$lang['counter_main_1_view_visitor']	= "View all visitors&#39; ID+IP who connected within {mm}/{dd}/{yy} at the hour {hh}";


###################################################################################
//			Counter Manager - Part 2 (by Day)
###################################################################################
$lang['counter_main_2_date_format']	= "F Y";
$lang['counter_main_2_month']		= "Month: ";
$lang['counter_main_2_this_month']	= "This month";
$lang['counter_main_2_sum']		= "Cumulative";
$lang['counter_main_2_total']		= ", Total: ";
$lang['counter_main_2_total_visitor']	= " visitor(s)";
$lang['counter_main_2_day_format']	= "jS";
$lang['counter_main_2_visitor']		= " visitor(s)";
$lang['counter_main_2_view_visitor']	= "View by hour within the day of {mm}/{dd}/{yy}";


###################################################################################
//			Counter Manager - Part 3 (by Week)
###################################################################################
$lang['counter_main_3_sum']		= "Cumulative";
$lang['counter_main_3_total']		= ", Total: ";
$lang['counter_main_3_total_visitor']	= " visitor(s)";
$lang['counter_main_3_average']		= ", at an average of ";
$lang['counter_main_3_average_visitor']	= " visitor(s) per week";
$lang['counter_main_3_visitor']		= " visitor(s)";

$lang['counter_main_3_day_name0']		= "Sunday";
$lang['counter_main_3_day_name1']		= "Monday";
$lang['counter_main_3_day_name2']		= "Tuesday";
$lang['counter_main_3_day_name3']		= "Wednesday";
$lang['counter_main_3_day_name4']		= "Thursday";
$lang['counter_main_3_day_name5']		= "Friday";
$lang['counter_main_3_day_name6']		= "Saturday";


###################################################################################
//			Counter Manager - Part 4 (by Month)
###################################################################################
$lang['counter_main_4_year']		= "Year: ";
$lang['counter_main_4_this_year']		= "This year";
$lang['counter_main_4_sum']		= "Cumulative";
$lang['counter_main_4_total']		= ", Total: ";
$lang['counter_main_4_total_visitor']	= " visitor(s)";
$lang['counter_main_4_month_format']	= "F";
$lang['counter_main_4_visitor']		= " visitor(s)";
$lang['counter_main_4_view_visitor']	= "View by day within the month of {mm}/{yy}";


###################################################################################
//			Counter Manager - Part 5 (by Year)
###################################################################################
$lang['counter_main_5_sum']		= "Cumulative";
$lang['counter_main_5_total']		= ", Total: ";
$lang['counter_main_5_total_visitor']	= " visitor(s)";
$lang['counter_main_5_year_format']	= "\Y\e\\a\\r Y";
$lang['counter_main_5_visitor']		= " visitor(s)";
$lang['counter_main_5_view_visitor']	= "View by month within the year of {yy}";


###################################################################################
//			Counter Manager - Part 6 (by Referers - Host & URL)
###################################################################################
$lang['counter_main_6_date_format']	= "m/d/Y H:i:s";
$lang['counter_main_6_total']		= "Total: ";
$lang['counter_main_6_total_url']		= " URL(s), ";
$lang['counter_main_6_total_visitor']	= " visitor(s)";
$lang['counter_main_6_total_zero']	= "There is no data";
$lang['counter_main_6_total_delete']	= "Are you sure to delete the selected log record(s)?";

$lang['counter_main_6_today_only']	= "Today only";
$lang['counter_main_6_sort_by']		= "Sort by";

$lang['counter_main_6_sort_1']		= "Hits, in ascending order";
$lang['counter_main_6_sort_2']		= "Hits, in descending order";
$lang['counter_main_6_sort_3']		= "Time, in ascending order";
$lang['counter_main_6_sort_4']		= "Time, in descending order";
$lang['counter_main_6_sort_5']		= "URL, in ascending order";
$lang['counter_main_6_sort_6']		= "URL, in descending order";

$lang['counter_main_6_search_negative']	= "Excluding";
$lang['counter_main_6_search_and']	= "and";
$lang['counter_main_6_search_or'] 	= "or";

$lang['counter_main_6_table_url']		= "Referrers' details (URL & last connection time)";
$lang['counter_main_6_table_hit']		= "Hit(s)";

$lang['counter_main_6_url_remember']	= "Remember this URL";
$lang['counter_main_6_url_forget']	= "Forget this URL";

$lang['counter_main_6_url_remember_button']="<span style=font-size:7pt>[REMEMBER']</span>";
$lang['counter_main_6_url_forget_button']	= "<span style=font-size:7pt;color:#F7418C>[FORGET']</span>";

$lang['counter_main_6_direct_connect']	= "The user either typed the URL directly, or use Favorites to visit.";
$lang['counter_main_6_view_detail_url']	= "View Full URL(s)";
$lang['counter_main_6_delete_button']	= "Delete";
$lang['counter_main_6_delete_question']	= "Are you sure to delete this log record?";

$lang['counter_main_6_error_pagenum']	= "Please use numbers only for the page number";


###################################################################################
//			Counter Manager - Part 7 (by Visitors' OS & Browser)
###################################################################################
$lang['counter_main_7_total']		= "Total: ";
$lang['counter_main_7_total_os']		= " OS type(s), ";
$lang['counter_main_7_total_browser']	= " browser type(s), ";
$lang['counter_main_7_total_visitor']	= " visitor(s)";
$lang['counter_main_7_visitor']		= " visitor(s)";
$lang['counter_main_7_total_zero']	= "There is no data";

$lang['counter_main_7_title_os']		= "Visitors' OS type";
$lang['counter_main_7_title_browser']	= "Visitors' browser type";

$lang['counter_main_7_error_pagenum']	= "Please use numbers only for the page number";


###################################################################################
//			Counter Manager - Part 8 (by Visitors' Information)
###################################################################################
$lang['counter_main_8_date_format']	= "m/d/Y H:i:s";
$lang['counter_main_8_total']		= "Total: ";
$lang['counter_main_8_total_visitor']	= " visitor(s)";
$lang['counter_main_8_total_zero']	= "There is no data";
$lang['counter_main_8_today_only']	= "Today only";
$lang['counter_main_8_member_only']	= "Member only";
$lang['counter_main_8_sort_by']		= "Sort by";

$lang['counter_main_8_sort_1']		= "Time, in ascending order";
$lang['counter_main_8_sort_2']		= "Time, in descending order";
$lang['counter_main_8_sort_3']		= "ID, in ascending order";
$lang['counter_main_8_sort_4']		= "ID, in descending order";

$lang['counter_main_8_title_1']		= "Visitors' ID, referer URL, OS and browser type";
$lang['counter_main_8_title_2']		= "Visitors' IP and connection time";

$lang['counter_main_8_right_arrow']	= "<span style=font-size:6pt>&#9654;</span> ";
$lang['counter_main_8_direct_connect']	= "The user either typed the URL directly, or use Favorites to visit.";
$lang['counter_main_8_not_login']		= "Not logged in";
$lang['counter_main_8_unknown_os']	= "Unknown OS type";
$lang['counter_main_8_unknown_browser']	= "Unknown browser type";
$lang['counter_main_8_search']		= "Search";

$lang['counter_main_8_error_pagenum']	= "Please use numbers only for the page number";


###################################################################################
//			Counter Manager - Part 9 (Configuration)
###################################################################################
$lang['counter_config_total']		= "Total visitors";
$lang['counter_config_skin']		= "Skin selection";
$lang['counter_config_skin_pattern']	= "Use skin pattern?";
$lang['counter_config_skin_pattern_use']	= "Yes (please refer to skin.php in the skin folder to customize skin pattern)";
$lang['counter_config_reconnect']		= "Counting behavior about<br>&nbsp;page reload by users";
$lang['counter_config_reconnect_always']	= "Always re-count (ignore cookie)";
$lang['counter_config_reconnect_new_open']= "Re-count when user open the browser again (cookie time: 0 sec)";
$lang['counter_config_reconnect_by_time1']= "Re-count after a specific time (cookie time: ";
$lang['counter_config_reconnect_by_time2']= " sec)";
$lang['counter_config_reconnect_once']	= "Count once in a day only";
$lang['counter_config_time_zone1']	= "Adjustment for your time zone";
$lang['counter_config_time_zone2']	= "hour(s) of difference when written to log data. [not recommend']<br>&nbsp;(e.g. If you set this to +16, the date & time info of the log being written<br>&nbsp;&nbsp;will be adjust to 16 hours added to the host server's time.)";
$lang['counter_config_admin_check']	= "Excluding admin connections?";
$lang['counter_config_admin_check_not']	= "Yes, do not count me";
$lang['counter_config_now_check']		= "Enable NOW connections?";
$lang['counter_config_now_check_use']	= "Yes, count for NOW connections";
$lang['counter_config_now_time']		= "Time allowed for NOW connections";
$lang['counter_config_now_time_use1']	= "After the visitor connect for ";
$lang['counter_config_now_time_use2']	= " seconds, it will be no longer marked as a<br>&nbsp;NOW connection. (recommend to set this value above 10)";
$lang['counter_config_admin_data']	= "Time statistics data maintenance";
$lang['counter_config_admin_data_delete1']= "Delete time statistics data";
$lang['counter_config_admin_data_delete2']= "<br>&nbsp;Click this button to delete the time statistics data by hours, days, weeks, months and years.";
$lang['counter_config_admin_os']		= "OS & browser data maintenance";
$lang['counter_config_admin_os_delete1']	= "Delete OS & browser data";
$lang['counter_config_admin_os_delete2']	= "<br>&nbsp;Click this button to delete the statistics data of OS & browser.";
$lang['counter_config_visitor_check']	= "Logging visitors' records?";
$lang['counter_config_visitor_check_use']	= "Yes, keep tracking of visitors' records (ID & IP)";
$lang['counter_config_visitor_limit']	= "Visitors' records maintenance";
$lang['counter_config_visitor_delete1']	= "Delete visitors&#39; records";
$lang['counter_config_visitor_delete2']	= "<br>&nbsp;Click this button to delete the log of visitors' records.";
$lang['counter_config_visitor_limit_set1']= "No. of visitors' record to be kept: ";
$lang['counter_config_visitor_limit_set2']= " (no limit if set to zero)";
$lang['counter_config_log_check']		= "Logging referrer data?";
$lang['counter_config_log_check_use']	= "Yes, keep tracking of referrer data (host & URL)";
$lang['counter_config_log_limit']		= "Referrer data maintenance";
$lang['counter_config_log_delete1']	= "Delete referrer data";
$lang['counter_config_log_delete2']	= "<br>&nbsp;Click this button to delete the log of referrer data.";
$lang['counter_config_log_limit_set1']	= "No. of referrer data to be kept: ";
$lang['counter_config_log_limit_set2']	= " (no limit if set to zero)";
$lang['counter_config_member_cookie']	= "Cookie name <br>&nbsp;of your board program";
$lang['counter_config_member_cookie_is']	= "(e.g. <b>n@board 3</b> 's member cookie name is 'na3_member')";
$lang['counter_config_permission']	= "Permission settings<br>&nbsp;for viewing data";
$lang['counter_config_permission1']	= "Only administrator can view the counter statistics by hours.";
$lang['counter_config_permission2']	= "Only administrator can view the counter statistics by days.";
$lang['counter_config_permission3']	= "Only administrator can view the counter statistics by weeks.";
$lang['counter_config_permission4']	= "Only administrator can view the counter statistics by months.";
$lang['counter_config_permission5']	= "Only administrator can view the counter statistics by years.";
$lang['counter_config_permission6']	= "Only administrator can view the log of referrer hosts.";
$lang['counter_config_permission7']	= "Only administrator can view the log of referrer URLs.";
$lang['counter_config_permission8']	= "Only administrator can view the statistics data of OS & browser types.";
$lang['counter_config_permission9']	= "Only administrator can view the log of visitors' data.";

$lang['counter_config_warning_data']	= "Are you sure to delete all the time statistics data (by hours, days, weeks, months, years)?";
$lang['counter_config_warning_os']	= "Are you sure to delete all the OS & browser data?";
$lang['counter_config_warning_visitor']	= "Are you sure to delete all the visitors\\' records?";
$lang['counter_config_warning_log']	= "Are you sure to delete all the referrer data?";

$lang['counter_config_button_save']	= "Save";
$lang['counter_config_button_reset']	= "Reset";

$lang['counter_manager_error_not_exist']	= "This counter does not exist";
$lang['counter_manager_error_total_is']	= "Please use numbers only for total visitors";
$lang['counter_manager_error_cookie_time']= "Please use numbers only for cookie time";
$lang['counter_manager_error_connect_time']="Please use numbers only for connection time";
$lang['counter_manager_error_log_limit']	= "Please use numbers only for the number of referrer data";

if (!isset($ip)) {
	$ip = '';
}
###################################################################################
//			IP Address Information Check (check_ip.php)
###################################################################################
$lang['check_ip_title']			= "IP Address Information Check: ";
$lang['check_ip_support']			= "More information or support";
$lang['check_ip_close']			= "Close";
$lang['check_ip_false_msg']		= "Failed to connect whois server.<br>Try visiting this URL to get the information you want:<br><br><a href=http://www.apnic.net/apnic-bin/whois.pl?searchtext=$ip>http://www.apnic.net/apnic-bin/whois.pl?searchtext=$ip</a>";
$lang['check_ip_right_arrow']		= "<span style=font-size:6pt>&#9654;</span>";
?>
