/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
/*	config.allowedContent = 'area[!shape,!coords,!href,!target,alt];' + 'map[!name];';
	config.extraAllowedContent = 'area[!shape,!coords,!href,!target,alt];' + 'map[!name];';*/
	
	config.enterMode = CKEDITOR.ENTER_BR;
	CKEDITOR.dtd.$removeEmpty['i'] = false;
	config.fillEmptyBlocks = false;
	
	config.allowedContent = true;
	config.extraAllowedContent = true;
	
	config.height = 700;
	
	config.toolbarCanCollapse = true;
	config.font_names = '맑은 고딕/Malgun Gothic;굴림/Gulim;돋움/Dotum;바탕/Batang;궁서/Gungsuh;' + config.font_names;
	
	config.filebrowserUploadUrl = '/editor/upload.php';
};
