<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8"/>
<title>CKEditor 4 설치하기</title>
<script type="text/javascript" src="./ckeditor/ckeditor.js"></script>
<script type="text/javascript">
//<![CDATA[
function LoadPage() {
	CKEDITOR.replace('contents');
}

function FormSubmit(f) {
	CKEDITOR.instances.contents.updateElement();
	if(f.contents.value == "") {
		alert("내용을 입력해 주세요.");
		return false;
	}
	alert(f.contents.value);
	
	// 전송은 하지 않습니다.
	return false;
}
//]]>
</script>
</head>
<body onload="LoadPage();">
<form id="EditorForm" name="EditorForm" onsubmit="return FormSubmit(this);">
<div>
	<label for="title">제목</label>
	<input type="text" id="title" name="title" size="40" />
</div>
<div>
	<label for="contents">내용</label>
	<textarea id="contents" name="contents"></textarea>
</div>
<div><input type="submit" value="전송" /></div>
</form>
</body>
</html>