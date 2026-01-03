/* 인쇄 */
var initBody   
function beforePrint(){   
	initBody = document.body.innerHTML;   
	document.body.innerHTML = contents.innerHTML;   
}

function afterPrint(){   
	document.body.innerHTML = initBody;   
} 

function printArea() {   
	 
	if (typeof window.parent.booking != "undefined")
	{
		parent.booking.focus();
		window.print();
	}
	else
	{
		window.onbeforeprint = beforePrint;   
		window.onafterprint = afterPrint;
		window.print();
	}
}


/********************************************************************
Note : 메인페이지 개편할 때 추가한 JS 
*********************************************************************/

var j$ = jQuery.noConflict();

j$(function(){
	var isiPad = navigator.userAgent.match(/iPad/i) != null;

	var headerOriHeight = j$(".header_outwrap").height();//header 영역 닫힘 height
	var headerHeight = j$(".header_wrap").height(); //header영역 펼침 height
	var gnbState = 0; //0 close , 1 open 
	
	j$(window).bind("load orientationchange resize", function(event){
	
	});

	
	//GNB
	var gnbTimeout  = 300;
	var gnbTimer = 0;
	var gnbDropmenu = 0;

	j$(window).load(function(){
		j$(".gnb_list .d3_list").after("<span class=\"d3_arrow\"></span>");
		j$(".gnb_list .prd .d2_list>li").each(function(n){
			j$(this).addClass("d2_"+(n+1));
		});
	});

	j$(".gnb_list .d1_link").bind("hover click focus",function(e){ //20130329 수정 
		if ( gnbState == 0){
			showGnbAll();
			e.preventDefault();
		}
		if (isiPad){
			if (j$(this).hasClass("hover")){
				j$(this).removeClass("hover");
			}
		}
	});

	j$(".gnb_list .d2_link:last").focusout(function(){ //20130329 추가
		hideGnbAll();
	});

	j$(".header_outwrap").mouseleave(function(){
		hideGnbAll();
	});

	j$(".btn_gnb_close").click(function(){
		j$(".btn_gnb_close").removeClass("btn_gnb_open");
		hideGnbAll();
	});

	j$(".gnb_list a.d1_link").hover(function(){ hide3Depth(); changeMenuOver(j$(this)); },function(){ changeMenuOut(j$(this)); }); //1depth over



	j$(".gnb_list a.d2_link").click( function(e){
		if (isiPad){
			if (j$(this).hasClass("ahover")){
				j$(this).removeClass("ahover");
				e.preventDefault();
			} //iPad에서 hover와 click 구분, dbclick 효과 
		}		
	});


	j$(".gnb_list a.d2_link").hover( function(e){

			if( j$(this).next('.d3_list').length > 0 ){  
				showGnb(j$(this));cancelTimer();
				if (isiPad){
					j$(this).addClass("ahover");
				}
			} 
			if( j$(this).next('.d3_list').length <= 0 && !isiPad ) { changeMenuOver(j$(this)); }
			

		},function(){ 

			if( j$(this).next('.d3_list').length <= 0 && !isiPad ){ changeMenuOut(j$(this)); } 
			doTimer();
	
	}); //2depth over


	j$(".gnb_list .d3_list li").bind("hover click",function(){ cancelTimer(); });

	if (!isiPad){
		j$(".gnb_list .d3_list a").hover( function(){ changeMenuOver(j$(this)); },function(){ changeMenuOut(j$(this));});//3depth over
	}


	function showGnb(obj){
		cancelTimer();
		hideGnb();
		changeMenuOver(obj);
		gnbDropmenu = obj.parent().find('.d3_list, .d3_arrow');
		gnbDropmenu.show();
	}

	function hideGnb(){	
		if(gnbDropmenu!=0 && gnbDropmenu.is('.d3_list')){
			var linkobj = gnbDropmenu.parent().find('a.d2_link');
			if (!linkobj.parent().is('.current')){
				changeMenuOut(linkobj);
				gnbDropmenu.hide();
			}
		}
	}

	function hide3Depth(){
		var hideobj = j$(".gnb_list .d3_list, .gnb_list .d3_arrow")
		hideobj.hide();
	}

	function showGnbAll(){ //20120712 수정 
		j$(".header_outwrap").animate({height: headerHeight + "px"}, { duration: 300,complete: function() { j$(".btn_gnb_close").addClass("btn_gnb_open"); },queue:false });
		gnbState=1;
	}

	function hideGnbAll(){
		j$(".header_outwrap").animate({height: headerOriHeight + "px"}, {
			duration: 200, complete: function() { 				
				gnbState=0;
				j$(".gnb_list a.d2_link").each(function(){
					changeMenuOut(j$(this));
				});
			
			},
			queue:false
		});
	}

	function doTimer(){
		gnbTimer = window.setTimeout(hideGnb, gnbTimeout);
	}

	function cancelTimer(){
		if(gnbTimer){  
			window.clearTimeout(gnbTimer);
			gnbTimer = null;
		}
	}	

	//메뉴 이미지 변경
	function changeMenuOver(obj){
		if (!obj.parent().is('.current')){
			if (obj.find('img').length > 0){
				var currentSrc = obj.find('img').attr('src');
				var changeSrc = currentSrc.replace('_off.','_on.');	
				obj.find('img').attr('src',changeSrc);
			} else {
				obj.addClass('hover');
			}
		}	
	}

	function changeMenuOut(obj){
		if (!obj.parent().is('.current')) {	
			if (obj.find('img').length > 0){
				var currentSrc = obj.find('img').attr('src');
				var changeSrc = currentSrc.replace('_on.', '_off.');
				obj.find('img').attr('src', changeSrc);	
			} else {
				obj.removeClass('hover');
			}
		}
	}



	//Combo - Footer 
	j$(".link_menu a.hd").click(function(e){
		j$(this).toggleClass("open");
		j$(this).siblings("ul").slideToggle();
		e.preventDefault();
	});
	
	//Combo - Player
	j$(".link_menu1 a.hd").click(function(e){
		j$(this).toggleClass("open");
		j$(this).siblings("ul").slideToggle();
		e.preventDefault();
	});
	
	j$(".stripe tr:odd").addClass('odd');
		

	//ETC
	if (j$("body").attr("id") != "KDB-Main" && isiPad ){ j$("body").addClass("sub_ipad"); }


	/* Main ********************************************* */
	//메인 팝업 생성
	try{
		j$("#main_layer_01 .con").carouFredSel({
			items: { visible:1,minimum:null,start:0},
			circular: true,
			//scroll:{ pauseOnHover : true }, //20130401 수정
			auto : { pauseDuration :5000 },
			pagination: {
				container: "#main_layer_01 .pagination",
				anchorBuilder: function( i ) {
					var alttxt = j$(this).find("img").attr("alt");
					return '<a href="#" alt="'+ alttxt +'"></a>';
				},
				event : "hover click"
			}
		});
	}catch(e){}

	//메인 롤링 배너 콘트롤 
	j$("#main_layer_01 .pause").toggle(function() {
	  j$("#main_layer_01 .con").trigger("pause"); //20130401 수정
	  j$(this).addClass("play");
	  j$(this).find("span").html("재생");
	}, function() {
	  j$("#main_layer_01 .con").trigger("play"); //20130401 수정
	   j$(this).removeClass("play");
	   j$(this).find("span").html("일시정지");
	});
	
	
	
	//메인 배경 비주얼 생성
	try{
		j$("#main_bgvisual .main_bgvisual_wrapper").carouFredSel({
			responsive: true,
			items: 1,
			scroll: {
				fx: 'crossfade'
			},
			auto : { pauseDuration :7000 },
			pagination: {
				container: "#main_bgvisual_control .pagination",
				anchorBuilder: function( i ) {
					var alttxt = j$(this).find(".alt").text();
					return '<a href="#" title="'+ alttxt +'"></a>';
				},
				event : "hover click"
			}		
		});
	}catch(e){}


	//메인 배경 비주얼 콘트롤 
	j$("#main_bgvisual_control .pause").toggle(function() {
	  j$("#main_bgvisual .main_bgvisual_wrapper").trigger("pause");
	  j$(this).addClass("play");
	  j$(this).find("span").html("재생");
	}, function() {
	  j$("#main_bgvisual .main_bgvisual_wrapper").trigger("play");
	   j$(this).removeClass("play");
	   j$(this).find("span").html("일시정지");
	});


	//메인 롤링 배너 생성
	try{
		j$("#main_ban4 .con").carouFredSel({
			items: { visible:1,minimum:null,start:0},
			circular: true,
			//scroll:{ pauseOnHover : true }, //20130401 수정
			auto : { pauseDuration :5000 },
			pagination: {
				container: "#main_ban4 .pagination",
				anchorBuilder: function( i ) {
					var alttxt = j$(this).find("img").attr("alt");
					return '<a href="#" alt="'+ alttxt +'"></a>';
				},
				event : "hover click"
			}
		});
	}catch(e){}

	//메인 롤링 배너 콘트롤 
	j$("#main_ban4 .pause").toggle(function() {
	  j$("#main_ban4 .con").trigger("pause"); //20130401 수정
	  j$(this).addClass("play");
	  j$(this).find("span").html("재생");
	}, function() {
	  j$("#main_ban4 .con").trigger("play"); //20130401 수정
	   j$(this).removeClass("play");
	   j$(this).find("span").html("일시정지");
	});

	function changeImg(obj,b,a){
		var currentSrc = obj.attr('src');
		var changeSrc = currentSrc.replace(b,a);
		obj.attr('src',changeSrc);
	}

	//Layer popup 
	
	j$(".main_pop .btn_pop_close").click(function(e){
		j$(".main_pop").hide();
		e.preventDefault();
	});

});


/********************************************************************
Note : 회사소개
*********************************************************************/
j$(function(){
	//연혁 관련 추가 
	if (j$("#kdbHistory").length > 0) { 
	
		j$("#kdbHistory .history_wrap").each(function(index){
			if (index > 1){ j$(this).hide(); }
		});

		j$("#kdbHistory ul.history_tab li:first a").addClass("on");
 
		j$("#kdbHistory ul.history_tab a").click(function(e){
			var linkTxt = j$(this).attr("href");
			j$("#kdbHistory ul.history_tab a").each(function(){
				j$(this).removeClass("on");
				var chYear = j$(this).find("img")[0];
				changeImg(j$(chYear),'_on.','_off.');
			});
			j$(this).addClass("on");
			var chYear = j$(this).find("img")[0];
			changeImg(j$(chYear),'_off.','_on.');
			j$("#kdbHistory .history_wrap").each(function(index){
				if (index > -1){ j$(this).hide(); }
			});
			j$(linkTxt).show();
			e.preventDefault();
		});

	}
	
	function changeImg(obj,b,a){
		var currentSrc = obj.attr('src');
		var changeSrc = currentSrc.replace(b,a);
		obj.attr('src',changeSrc);
	}
	
	//리틀위너스 게시판 농구공
	j$(".board_list02").append('<img src="/share/images/board/icon_ball.png" alt="" class="board_ball" />');
	j$(".qna_list02").append('<img src="/share/images/board/icon_ball.png" alt="" class="board_ball" />');

});

 
var m_bn_total=0;
var m_bn_cnt=0;
var pointer=0;
function m_bn_init(){
	var j$=jQuery;
	m_bn_total = j$(".history_tab > li").size();
	
	j$(".history_tab_area").css({"height":"29px","overflow":"hidden"});

	j$(".next").click(function(){
		if(m_bn_cnt< (m_bn_total%9)){
			m_bn_cnt++;
			m_bn_r_click();
		}
		return false;
	});
	j$(".prev").click(function(){
		if(m_bn_cnt>0){
			m_bn_cnt--;
			m_bn_l_click();
		}
		return false;
	});
}


function m_bn_r_click(){
	var j$=jQuery;
	j$(".history_tab").css("left",pointer-=72);
}
function m_bn_l_click(){
	var j$=jQuery;
	j$(".history_tab").css("left",pointer+=72);
}