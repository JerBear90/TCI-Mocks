jQuery(document).ready( function($) {
    var $select = $('select[name=cars_orderby]');
    $select.find('option[value=name]').remove();
    setTimeout( function() {
        var $el = $('select[name=cars_orderby]').next('.nice-select');
        $el.find('li').each( function() {
            var val = $(this).text().trim();
            if( val == 'Sort by Name' ) $(this).remove();
        })
    },1000);

    var $mobile_sticky_status = (cardealer_header_js.sticky_header_mobile == true) ? true : false;
    var $desktop_sticky_status = (cardealer_header_js.sticky_header_desktop == true) ? true : false;
    jQuery('#menu-1').megaMenu({
        // DESKTOP MODE SETTINGS
        logo_align: 'left',		// align the logo left or right. options (left) or (right)
        links_align: 'left',      	// align the links left or right. options (left) or (right)
        socialBar_align: 'left',     	// align the socialBar left or right. options (left) or (right)
        searchBar_align: 'right',    	// align the search bar left or right. options (left) or (right)
        trigger: 'hover',    	// show drop down using click or hover. options (hover) or (click)
        effect: 'fade',     	// drop down effects. options (fade), (scale), (expand-top), (expand-bottom), (expand-left), (expand-right)
        effect_speed: 400,        	// drop down show speed in milliseconds
        sibling: true,       	// hide the others showing drop downs if this option true. this option works on if the trigger option is "click". options (true) or (false)
        outside_click_close: true,       	// hide the showing drop downs when user click outside the menu. this option works if the trigger option is "click". options (true) or (false)
        top_fixed: false,      	// fixed the menu top of the screen. options (true) or (false)
        sticky_header: $desktop_sticky_status,// menu fixed on top when scroll down down. options (true) or (false)
        sticky_header_height: 250,  		// sticky header height top of the screen. activate sticky header when meet the height. option change the height in px value.
        menu_position: 'horizontal', // change the menu position. options (horizontal), (vertical-left) or (vertical-right)
        full_width: false,        // make menu full width. options (true) or (false)
        // MOBILE MODE SETTINGS
        mobile_settings: {
            collapse: true,     // collapse the menu on click. options (true) or (false)
            sibling: true,     // hide the others showing drop downs when click on current drop down. options (true) or (false)
            scrollBar: true,     // enable the scroll bar. options (true) or (false)
            scrollBar_height: 400,      // scroll bar height in px value. this option works if the scrollBar option true.
            top_fixed: false,    // fixed menu top of the screen. options (true) or (false)
            sticky_header: $mobile_sticky_status,     // menu fixed on top when scroll down down. options (true) or (false)
            sticky_header_height: 200       // sticky header height top of the screen. activate sticky header when meet the height. option change the height in px value.
        }
    });

    if (document.getElementById('mega-menu-wrap-primary-menu')) {
        jQuery('.menu-mobile-collapse-trigger').hide();
    }

    function hideReviewZeroDates() {
        $('.wprev_showdate_T6').each(function () {
            var val = $(this).text();
            if (val == '01/01/70') {
                // d('hide;',$(this)[0]);
                $(this).text('');
                $(this).hide();
            }
        });
    }
    hideReviewZeroDates();
    $(document).on('click', '#wprev_load_more_btn_1', hideReviewZeroDates );
})

