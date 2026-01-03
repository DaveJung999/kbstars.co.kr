var step=0;
function onChange(term_id){
	var f = document.SearchForm;
	var years, months, days, sdate, edate;

	var edate = f.edate.value;

	var today =	new Date();
	var today_years	= today.getFullYear();
	var today_months = today.getMonth() + 1;
	var today_days	= today.getDate();

	if (today_months.toString().length <= 1) today_months = "0" + today_months.toString();
	if (today_days.toString().length <= 1) var today_days = "0" + today_days.toString();
	today = today_years.toString()+"-"+today_months.toString()+"-"+today_days.toString();
	edate = today;

	var YearStr	= today.substring(0,4);
	var MonthStr = today.substring(5,7);
	var DayStr	= today.substring(8,10);

	if (term_id == '1'){
		//금일 셋팅
		sdate = today;
	} else if (term_id == '2'){
		//1개월 셋팅
		today = new Date(YearStr, MonthStr-2, DayStr);
		today.setDate(today.getDate()+1);
		years	= today.getFullYear();
		months = today.getMonth() + 1;
		days	= today.getDate();
		if (months.toString().length <= 1) months = "0" + months.toString();
		if (days.toString().length <= 1) days = "0" + days.toString();
		sdate = years.toString()+"-"+months.toString()+"-"+days.toString();
	} else if (term_id == '3'){
		//1년 셋팅
		today = new Date(YearStr-1, MonthStr-1, DayStr);
		today.setDate(today.getDate()+1);
		years	= today.getFullYear();
		months = today.getMonth() + 1;
		days	= today.getDate();
		if (months.toString().length <= 1) months = "0" + months.toString();
		if (days.toString().length <= 1) days = "0" + days.toString();
		sdate = years.toString()+"-"+months.toString()+"-"+days.toString();

	} else if (term_id == '4'){
		//3년 셋팅
		today = new Date(YearStr-3, MonthStr-1, DayStr);
		today.setDate(today.getDate()+1);
		years	= today.getFullYear();
		months = today.getMonth() + 1;
		days	= today.getDate();
		if (months.toString().length <= 1) months = "0" + months.toString();
		if (days.toString().length <= 1) days = "0" + days.toString();
		sdate = years.toString()+"-"+months.toString()+"-"+days.toString();

	}
	f.sdate.value = sdate;
	f.edate.value = edate;
	f.term_id.value = term_id;
}
function on_view(userid, priv)
{
	location.href="index_detail.php?priv="+priv+"&userid="+userid;
}
function select_all()
{
	for( var i=0; i<document.ListForm.elements.length; i++)
	{
		var ele = document.ListForm.elements[i];
		var Re = /code_.*/;
		if( ele.name.search(Re)!=-1 )
			ele.checked = !ele.checked;
	}
}
function delete_selected(priv)
{
	if( !confirm('[주의]\\n\\n삭제 처리가 완료 되면 회원에 대한 정보는 완전히 삭제 되며\\n관련된 이전 모든 정보는 복구 할 수 없게 됩니다.\\n\\n그래도, 회원 정보를 삭제하시겠습니까?') ) return;
	var Form=document.ListForm;
	if( get_checked(Form)[0]==0 )
	{
		alert('회원을 선택해 주십시오.');
		return;
	}
	Form.target="_self";
	Form.action="ok.php?priv="+priv+"&mode=index_delete&uid=1&total_num="+get_checked(Form)[0];
	Form.submit();
}
function withdraw_selected(priv)
{
	if( !confirm('선택한 회원 데이타를 모두 탈퇴 처리 합니다.') ) return;
	var Form=document.ListForm;
	if( get_checked(Form)[0]==0 )
	{
		alert('회원을 선택해 주십시오.');
		return;
	}
	Form.target="_self";
	Form.action="ok.php?priv="+priv+"&mode=index_draw&uid=1&total_num="+get_checked(Form)[0];
	Form.submit();
}
function gradeset_selected(priv)
{
	var Form=document.ListForm;
	if( get_checked(Form)[0]==0 )
	{
		alert('회원을 선택해 주십시오.');
		return;
	}
	window.open('','GradeAdjustWin','width=300,height=160');
	Form.target="GradeAdjustWin";
	Form.action="level_edit.php?priv="+priv+"&total_num="+get_checked(Form)[0];
	Form.submit();
}
function get_checked(Form)
{
	num=0;
	var total_num ="";
	for( var i=0; i<Form.elements.length;	i++)
	{
		var ele = Form.elements[i];
		var Re	= /code_.*/
		if(ele.name.search(Re)!=-1)
			if(ele.checked){
				num++;
			total_num = total_num +";"+ ele.value;
		}
	}
	return [num, total_num];
}
function on_search(priv) {
	var form = document.SearchForm;
	var start_day = form.sdate.value;
	var end_day = form.edate.value;
	if(start_day.length < 3) start_day = "";
	if(end_day.length < 3) end_day = "";
	var name_vl = form.srch_order_name.value;
	var email_vl = form.srch_order_email.value;
	var idnum_vl = form.srch_order_idnum.value;
	var userid_vl = form.srch_order_userid.value;

	form.target = "_self";
	form.action = "index.php?priv="+priv+"&act=OrderList&sdate="+start_day+"&edate="+end_day+"&name="+name_vl+"&email="+email_vl+"&idnum="+idnum_vl+"&userid="+userid_vl;
	form.submit();
}
