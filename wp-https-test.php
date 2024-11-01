<?php
/*
Plugin Name: WordPress HTTPS Test
Plugin URI: http://www.fanpageconnect.com
Version: v1.0
Author: Pat Friedl, Chris Friedl, Bryan Batson
Description: WordPress HTTPS Test will test your WordPress pages for non HTTPS elements. If any unsecure components are found, they're highlighted and you'll be able to see a listing of the unsecure items. NOTE: Best used in IE9, Opera, FireFox or Chrome. This plugin looks nasty in IE8 or less!

Copyright 2011  fanpageconnect.com  email: support@fanpageconnect.com
*/

if(!class_exists("WPHTTPSTest")){

	class WPHTTPSTest {

		function WPHTTPSTest() { //constructor

			global $post;

			add_action('init', array(&$this, 'enqueue_jquery'));
			add_action('wp_footer', array(&$this, 'add_https_test_js'), 100);

		} // end function WPHTTPSTest

		function enqueue_jquery(){
			if (!is_admin()) {
				wp_enqueue_script('jquery');
			}
		} // end enqueue jquery

		function add_https_test_js(){
			global $post;
?>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('body').append('<div id="sslControlButtons"></div>');
	jQuery('#sslControlButtons').append('<input type="button" id="sslTestButton" value="Test My Site\'s SSL" />');
	jQuery('#sslControlButtons').append('<input type="button" id="sslResultButton" value="Show Results" />');
	jQuery('#sslControlButtons').append('<div id="sslTestOutput"></div>');

	var ctrlCss = {
		'position' : 'fixed',
		'top' : '10px',
		'left' : '10px'
	}
	var btnCss = {
		'position' : 'relative',
		'float' : 'left',
		'font-size' : '14px',
		'font-family' : 'arial,helvetica,sans-serif',
		'color' : '#fff',
		'background-color' : '#58A618',
		'z-index' : '10000000',
		'border' : '2px solid #427731',
		'border-radius' : '8px',
		'cursor' : 'pointer',
		'padding' : '8px',
		'font-weight' : 'bold',
		'-moz-box-shadow' : '3px 3px 3px #000',
		'-webkit-box-shadow' : '3px 3px 3px #000',
		'box-shadow' : '3px 3px 3px #000',
		'text-shadow' : '#000 0px 1px 0px',
		'margin-right' : '10px',
		'margin-bottom' : '12px'
	}
	var divCss = {
		'clear' : 'both',
		'position' : 'relative',
		'font-size' : '12px',
		'font-weight' : 'normal',
		'font-family' : 'arial,helvetica,sans-serif',
		'color' : '#000',
		'background-color' : '#e1e1e1',
		'z-index' : '10000000',
		'border' : '2px solid #999',
		'border-radius' : '8px',
		'padding' : '0px 8px 8px 8px',
		'-moz-box-shadow' : '3px 3px 3px #000',
		'-webkit-box-shadow' : '3px 3px 3px #000',
		'box-shadow' : '3px 3px 3px #000',
		'text-shadow' : '#fff 0px 1px 0px',
		'display' : 'none',
		'max-width' : '95%',
		'max-height' : '450px',
		'overflow' : 'auto'
	}
	var h4Css = {
		'color' : '#000',
		'font-size' : '13px',
		'margin-bottom' : '4px',
		'margin-top' : '4px'
	}

	jQuery('#sslControlButtons').css(ctrlCss);

	jQuery('#sslResultButton').css(btnCss);
	jQuery('#sslResultButton').css('display','none');
	jQuery('#sslTestButton').css(btnCss);

	jQuery('#sslTestOutput').css(divCss);

	if(navigator.appName.toLowerCase() == 'microsoft internet explorer'){
		jQuery('#sslControlButtons').offset({ top: 10, left: 10 });
		jQuery('#sslResultButton').css('margin-bottom','10px');
		jQuery('#sslTestButton').css('margin-bottom','10px');
	}

	jQuery('#sslResultButton').click(function(){
		jQuery('#sslTestOutput h4').css(h4Css);
		if(jQuery('#sslTestOutput').is(':visible')) {
			jQuery(this).val('Show Results');
			jQuery('#sslTestOutput').slideUp('fast');
		} else {
			jQuery(this).val('Hide Results');
			jQuery('#sslTestOutput').slideDown('fast');
		}
	});

	jQuery('#sslTestButton').click(function(){
		testHTTPS();
	});

});

function testHTTPS(){
	jQuery('#sslResultButton').val('Show Results');
	jQuery('#sslTestOutput').hide();
	jQuery('#sslTestOutput').html('');
	var $formCollection = jQuery('form');
	var $aCollection = jQuery('a');
	var $imgCollection = jQuery('img');
	var $allElements = jQuery('*');

	var unsecureSite = false;
	var unsecureForms = false;
	var unsecureA = false;
	var unsecureImg = false;
	var unsecureCSS = false;

	var siteOutput = '<p><h4>Unsecured URL:</h4>';
	var formOutput = '<p><h4>Unsecured Forms:</h4>';
	var aOutput = '<p><h4>Unsecured Links:</h4>';
	var imgOutput = '<p><h4>Unsecured Images:</h4>';
	var cssOutput = '<p><h4>Unsecured CSS/Scripts/Style Sheets:</h4>';

	var alertCSS = { 'border' : '2px dashed #f00' };

	if(document.location.protocol != 'https:'){
		unsecureSite = true;
	}
	if(unsecureSite){
		jQuery('#sslTestOutput').append(siteOutput + 'Your base URL is not secure - it needs to be HTTPS.</p>');
	} else {
		jQuery('#sslTestOutput').append(siteOutput + 'Congrats, your base URL is secure!</p>');
	}

	$formCollection.each(function(idx){
		if(jQuery(this).attr('action') != undefined){
			if(jQuery(this).attr('action').indexOf('http:') != -1 && jQuery(this).attr('target') == undefined){
				unsecureForms = true;

				formOutput += '&lt;form ';
				formOutput += (jQuery(this).attr('id') != undefined) ? 'id="' + jQuery(this).attr('id') + '" ' : '';
				formOutput += 'action="' + jQuery(this).attr('action') + '" ';
				formOutput += '&gt;<br/>';

				jQuery(this).css(alertCSS);
			}
		}
	});
	if(unsecureForms){
		jQuery('#sslTestOutput').append(formOutput + '</p>');
	} else {
		jQuery('#sslTestOutput').append(formOutput + 'Congrats, no unsecure forms!</p>');
	}

	$aCollection.each(function(idx){
		if(jQuery(this).attr('href') != undefined){

			if(jQuery(this).attr('href').indexOf('http:') != -1 && jQuery(this).attr('target') == undefined){
				unsecureA = true;

				aOutput += '&lt;a ';
				aOutput += (jQuery(this).attr('id') != undefined) ? 'id="' + jQuery(this).attr('id') + '" ' : '';
				aOutput += 'href="' + jQuery(this).attr('href') + '" ';
				aOutput += '&gt;<br />';

				jQuery(this).css(alertCSS);
			}
		}
	});
	if(unsecureA){
		jQuery('#sslTestOutput').append(aOutput + '</p>');
	} else {
		jQuery('#sslTestOutput').append(aOutput + 'Congrats, no unsecure anchors!</p>');
	}

	$imgCollection.each(function(idx){
		if(jQuery(this).attr('src').indexOf('http:') != -1){
			unsecureImg = true;

			imgOutput += '&lt;img ';
			imgOutput += (jQuery(this).attr('id') != undefined) ? 'id="' + jQuery(this).attr('id') + '" ' : '';
			imgOutput += 'src="' + jQuery(this).attr('src') + '" ';
			imgOutput += '&gt;<br />';

			jQuery(this).css(alertCSS);
		}
	});
	if(unsecureImg){
		jQuery('#sslTestOutput').append(imgOutput + '</p>');
	} else {
		jQuery('#sslTestOutput').append(imgOutput + 'Congrats, no unsecure images!</p>');
	}

	$allElements.each(function(idx){
		var tagType = jQuery(this)[0].tagName.toLowerCase();

		if(
			(jQuery(this).css('background') != undefined &&
			 jQuery(this).css('background').indexOf('http:') != -1)
			||
			(jQuery(this).css('background-image') != undefined &&
			 jQuery(this).css('background-image').indexOf('http:') != -1)
			||
			(tagType == 'link' && jQuery(this).attr('rel') == 'stylesheet' && jQuery(this).attr('href').indexOf('http:') != -1) ||
			(tagType == 'scr'+'ipt' && jQuery(this).attr('src') != undefined && jQuery(this).attr('src').indexOf('http:') != -1)
		){
			unsecureCSS = true;

			cssOutput += '&lt;' + tagType + ' ';
			cssOutput += (jQuery(this).attr('id') != undefined) ? 'id="' + jQuery(this).attr('id') + '" ' : '';
			cssOutput += (jQuery(this).attr('class') != undefined) ? 'class="' + jQuery(this).attr('class') + '" ' : '';

			if(tagType == 'link'){
				cssOutput += 'href="' + jQuery(this).attr('href') + '" ';
				cssOutput += '&gt;<br />';
			} else if(tagType == 'scr'+'ipt'){
				cssOutput += 'src="' + jQuery(this).attr('src') + '" ';
				cssOutput += '&gt;<br />';
			} else {
				if(
				   jQuery(this).css('background') != undefined &&
				   jQuery(this).css('background').indexOf('http:') != -1
				   ){
					cssOutput += 'background="' + jQuery(this).css('background') + '" ';
				}
				if(
				   jQuery(this).css('background-image') != undefined &&
				   jQuery(this).css('background-image').indexOf('http:') != -1
				   ){
					cssOutput += 'background-image="' + jQuery(this).css('background-image') + '" ';
				}
				cssOutput += '&gt;<br />';
			}
			jQuery(this).css(alertCSS);
		}
	});
	if(unsecureCSS){
		jQuery('#sslTestOutput').append(cssOutput + '</p>');
	} else {
		jQuery('#sslTestOutput').append(cssOutput + 'Congrats, no unsecure CSS, scripts or style sheets!</p>');
	}
	jQuery('#sslTestOutput').append('<p><h4>This SSL test provided by <a href="http://www.fanpageconnect.com/pro/" target="_blank">Fanpage Connect Pro</a></h4></p>');

	jQuery('#sslResultButton').show();
}
</script>
<?php
		} // end add_https_test_js

	} // end class WPHTTPSTest

} // end if class exists

// initialize the WPHTTPSTest class
if (class_exists("WPHTTPSTest")) {
	$wp_wphttps = new WPHTTPSTest();
}
?>