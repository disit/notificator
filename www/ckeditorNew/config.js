/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
   
   config.placeholder_select = {
      placeholders: ['Widget title', 'Metric name',  'Dashboard name', 'Dashboard link']
      //format: '[%placeholder%]'
   };
   
   //config.extraPlugins = 'richcombo,placeholder_select';
};
