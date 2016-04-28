/* global $, smoothScroll, ga */
/* eslint new-cap: [2, { capIsNewExceptions: ["Stickyfill"] }] */

// on ready tbh
$(function () {
    var scrollLimiter = { 25: false, 50: false, 75: false, 100: false }

    // function to resize grids
    // function resizeGrids () {

    //     // cycle through each grid and set a height
    //     $('.grid').each(function () {
    //         var $grid = $(this);
    //         var $cells = $grid.find('[class*="col-"]');
    //         var maxHeight = 370;

    //         $cells.css('height', '');

    //         // get the max height to set
    //         $cells.each(function () {
    //             var $cell = $(this);
    //             var cellHeight = $cell.height();

    //             if (cellHeight > maxHeight) {
    //                 maxHeight = cellHeight;
    //             }
    //         });

    //         $cells.height(maxHeight);
    //     });
    // }

    // resizeGrids();

    function resizeSpotlights () {
        $('.spotlights').each(function () {
            var $row = $(this);
            var $cols = $row.find('[class*="col-"]');
            var $spotlights = $cols.find('.spotlight');
            var maxHeight = 370;

            $spotlights.css('min-height', '');

            $cols.each(function () {
                var $col = $(this);
                var colHeight = $col.height();

                if (colHeight > maxHeight) {
                    maxHeight = colHeight;
                }
            });

            console.log(maxHeight);

            $spotlights.css('min-height', (maxHeight + 'px'));
        });
    }

    resizeSpotlights();

    $('.nav--tabs').each(function () {
        var $tabs = $(this).find('li');
        var $close = $(this).find('.modal__close');

        $tabs.click(function () {
            $tabs.removeClass('active');
            $(this).addClass('active');
        });

        $close.click(function (e) {
            var $parentTab = $(this).parent().parent();

            $parentTab.removeClass('active');

            e.stopPropagation();
        });
    });

    //$('.navbar').Stickyfill();

    smoothScroll.init({
        updateURL: false
        // offset: 88
    });

    // resize grids on window resize
    $(window).resize(function () {
        // resizeGrids();
        resizeSpotlights();
    });

    $('.social__link').not('.social__link--email').click(function (e) {
        e.preventDefault();
        window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=600');
    });

    /*
        GOOGLE ANALYTICS
    */

    // embedded video (iframe) play trigger
    $(window).focus(); // trigger focus
    $(window).blur(function (){
        var page = window.location.pathname.split('/')[0] || '/';
        var active = document.activeElement;
        if(/iframe/i.test(active.tagName)) {
            ga('send', {
                hitType: 'event',
                eventCategory: page,
                eventAction: 'Play Video',
                eventLabel: $(active).attr('src')
            });
        }
    });
    // form submission trigger
    $(document).on('submit', 'form', function(e){
        e.preventDefault();
        var page = window.location.pathname.split('/')[0] || '/';
        var formLabel = $(this).closest('.section').attr('id') || 'Sign up form';
        var form = this;
        var submitted = false;
        var _submit = function() {
            if (submitted) {
                return;
            }
            submitted = true;
            form.submit();
        };
        setTimeout(_submit, 1000); // ensure initial action proceeds
        ga('send', {
            hitType: 'event',
            eventCategory: page,
            eventAction: 'Form Submitted',
            eventLabel: formLabel,
            hitCallback: _submit
        });
    });
    // call to action button (not submit) click trigger
    $(document).on('click', '.btn:not([type="submit"])', function(){
        var page = window.location.pathname.split('/')[0] || '/';
        var buttonLabel = $(this).text();
        ga('send', {
            hitType: 'event',
            eventCategory: page,
            eventAction: 'CTA Clicked',
            eventLabel: buttonLabel
        });
    });
    // like/share click trigger
    $(document).on('click', 'a.social__link', function(){
        var page = window.location.pathname.split('/')[0] || '/';
        var socialNetwork = /social__link--(\w+)/gi.exec(this.className)[1] || 'generic'; // i.e. facebook, twitter etc.
        ga('send', {
            hitType: 'event',
            eventCategory: page,
            eventAction: 'Like/Share Clicked',
            eventLabel: socialNetwork
        });
    });
    // external link click trigger
    $(document).on('click', 'a:not(.social__link)', function(e){
        e.preventDefault();
        var page = window.location.pathname.split('/')[0] || '/';
        var href = $(this).attr('href') || '';
        var _route = function() {
            window.location = href;
        };
        if (/^#/.test(href)) {
            var hashLink = window.location.hash = href;

            return hashLink; // return if href is hash link
        }
        setTimeout(_route, 1000); // ensure initial action proceeds
        ga('send', {
            hitType: 'event',
            eventCategory: page,
            eventAction: 'External Link Clicked',
            eventLabel: href,
            hitCallback: _route
        });
    });
    // scroll trigger
    $(document).scroll(function(){
        var page = window.location.pathname.split('/')[0] || '/';
        var percent = Math.round((($(document).scrollTop() + $(window).height()) / $(document).height()) * 100);
        if(percent === 25 || percent === 50 || percent === 75 || percent === 100) {
            if(scrollLimiter[percent] === true) {
                return;
            }
            ga('send', {
                hitType: 'event',
                eventCategory: page,
                eventAction: 'Window Scrolled To',
                eventLabel: percent + '%'
            });
            scrollLimiter[percent] = true;
        }
    });
});
