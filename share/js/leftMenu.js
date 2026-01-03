
var j$ = jQuery.noConflict();
j$(document).ready(function(){	

	j$("#lm_menu a").each(function(){  //클래스 네임 변경				
		var imgObj = j$(this).children("img");
		var imgSrc = j$(imgObj).attr("src");
		var old_imgSrc = "";
		
		//add mouseOver
		j$(this).mouseover(function(){	
			imgSrc = j$(imgObj).attr("src");
			cur_imgSrc = imgSrc;
			var on = imgSrc.replace(/_1.jpg/,"_2.jpg"); 
			j$(imgObj).attr("src",on);
		});
		
		//add mouseOut
		j$(this).mouseout(function(){
			if (typeof(cur_imgSrc) != "undefined" )
				j$(imgObj).attr("src",cur_imgSrc);
		});
		
		//add click
	   j$(this).click(function(){
			
			// 전부 초기화
			j$("#lm_menu a").each(function(){  //클래스 네임 변경				
				var imgObj = j$(this).children("img");
				var imgSrc = j$(imgObj).attr("src");
				var allReset = imgSrc.replace(/_2.jpg/,"_1.jpg"); 
				j$(imgObj).attr("src",allReset);
			});
			
			// 클릭한 메뉴 변경
			var dn = imgSrc.replace(/_1.jpg/,"_2.jpg");
			j$(imgObj).attr("src",dn);
			
			cur_imgSrc = imgSrc = dn;
	   });
	});
	
});

// URL get 값 가져오기
function getHttpParam(name) {
	var regexS = "[\\?&]" + name + "=([^&#]*)";
	var regex = new RegExp(regexS);
	var results = regex.exec(window.location.href);
	if (results == null) {
		return "";
	} else {
		return results[1];
	}
}

var str = getHttpParam("mNum");
var obj = document.getElementById(str);

// 메뉴ID로 찾아 클릭 이미지 변경
if( obj != null){
	obj.src = obj.src.replace(/_1.jpg/,"_2.jpg");
}
