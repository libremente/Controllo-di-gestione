/**
*   VGallery: CMS based on FormsFramework
    Copyright (C) 2004-2015 Alessandro Stucchi <wolfgan@gmail.com>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

 * @package VGallery
 * @subpackage console
 * @author Alessandro Stucchi <wolfgan@gmail.com>
 * @copyright Copyright (c) 2004, Alessandro Stucchi
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link https://github.com/wolfgan43/vgallery
 */
var titleFollowDim = false;
jQuery(function() {
	var thumbTitle = jQuery("#Thumbs_dim_x").val() + "x" + jQuery("#Thumbs_dim_y").val();
	if(thumbTitle == "0x0")
		thumbTitle = "";

	if(thumbTitle == jQuery("#Thumbs_display_name").val())
		titleFollowDim = true;

	jQuery("#Thumbs_dim_x, #Thumbs_dim_y").keyup(function(event) {
		jQuery(this).val(isNaN(parseInt(jQuery(this).val())) ? 0 : parseInt(jQuery(this).val()));

		var thumbTitle = parseInt(jQuery("#Thumbs_dim_x").val()) + "x" + parseInt(jQuery("#Thumbs_dim_y").val());
		if(thumbTitle == "0x0")
			thumbTitle = "";

		if(thumbTitle == jQuery("#Thumbs_display_name").val()) {
			titleFollowDim = true;
		}

		if(titleFollowDim) {
			jQuery("#Thumbs_display_name").val(thumbTitle).keyup();
		}
	}).focus(function() {
        jQuery(this).select();
    });
	jQuery("#Thumbs_display_name").keyup(function() {
		var thumbTitle = jQuery("#Thumbs_dim_x").val() + "x" + jQuery("#Thumbs_dim_y").val();
		if(thumbTitle == "0x0")
			thumbTitle = "";
		if(thumbTitle == jQuery("#Thumbs_display_name").val()) {
			titleFollowDim = true;
		} else {
			titleFollowDim = false;
		}


	});

	jQuery("#Thumbs_frame_size").on( "spin", function( event, ui ) {
		if(ui.value > 0) {
			jQuery("#Thumbs_frame_color").closest("DIV.colorpicker").fadeIn();
		} else {
			jQuery("#Thumbs_frame_color").closest("DIV.colorpicker").hide();
		}
	});
	jQuery("#Thumbs_frame_size").on( "keyup", function() {
		if(jQuery(this).val() > 0) {
			jQuery("#Thumbs_frame_color").closest("DIV.colorpicker").fadeIn();
		} else {
			jQuery("#Thumbs_frame_color").closest("DIV.colorpicker").hide();
		}
	});




	jQuery("#Thumbs_word_size").on( "spin", function( event, ui ) {
		if(ui.value > 0) {
			jQuery("#Thumbs_word_color").closest("DIV.colorpicker").fadeIn();
		} else {
			jQuery("#Thumbs_word_color").closest("DIV.colorpicker").hide();
		}
	});
	jQuery("#Thumbs_word_size").on( "keyup", function() {
		if(jQuery(this).val() > 0) {
			jQuery("#Thumbs_word_color").closest("DIV.colorpicker").fadeIn();
		} else {
			jQuery("#Thumbs_word_color").closest("DIV.colorpicker").hide();
		}
	});


	jQuery("#Thumbs_mode").change(function() {
		switch(jQuery(this).val()) {
			case "crop":
			case "stretch":
				jQuery(".mode-dep").slideUp();
				break;
			default:
				jQuery("#Thumbs_resize").change();
				jQuery(".mode-dep:not(.resize-dep)").slideDown();
		}
	});
	jQuery("#Thumbs_resize").change(function() {
		if(jQuery(this).is(":checked"))
			jQuery(".resize-dep").slideUp();
		else
			jQuery(".resize-dep").slideDown();
	});

	jQuery("#Thumbs_mode").change();
	jQuery("#Thumbs_resize").change();
	jQuery("#Thumbs_frame_size").keyup();
	jQuery("#Thumbs_word_size").keyup();
});