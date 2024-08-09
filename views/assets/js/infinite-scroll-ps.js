jQuery(document).ready(function($){

    if( typeof InfiniteAjaxScroll === 'undefined' ) {
        console.error('InfiniteScroll not found');
        return;
    }

    // extends default params
    infinite_scroll_ps = $.extend({
        sel_container: '.products',
        sel_items: '.product',
        sel_pagination: '.pagination',
        btn_text: 'Load more',
        btn_color: '#007bff',
    }, infinite_scroll_ps);

    var getNextPage = function() {
        var next_page = $(infinite_scroll_ps.sel_pagination).find('.current').next();
        if( next_page.length ) {
            return next_page.find('a').addClass('next__page');
        }
        return null;
    }

    var getPrevPage = function() {
        var prev_page = $(infinite_scroll_ps.sel_pagination).find('.current').prev();
        if( prev_page.length ) {
            return prev_page.find('a').addClass('prev__page');
        }
        return null;
    }

    getNextPage();
    getPrevPage();

    // Infinite Scroll
    var infiniteScroll = new InfiniteAjaxScroll(infinite_scroll_ps.sel_container, {
        item: infinite_scroll_ps.sel_items,
        pagination: infinite_scroll_ps.sel_pagination,
        next: '.next__page',
        prev: '.prev__page',
        loadOnScroll: (infinite_scroll_ps.scroll_type == 1) ? true : false,
        spinner: {
            element: document.createElement('div'),
            delay: 600,
            show: function(element) {
                element.innerHTML =
                    '<div class="spinner-container">' +
                    '<div class="spinner">' +
                    '<div class="bounce1"></div>' +
                    '<div class="bounce2"></div>' +
                    '<div class="bounce3"></div>' +
                    '</div>' +
                    '</div>';
                $(element).appendTo(infinite_scroll_ps.sel_container);
            },
            hide: function(element) {
                $(element).remove();
            }
        },
        trigger: {
            element:document.createElement('div'),
            when: function(pageIndex) {
                if(getNextPage()) {
                    getPrevPage();
                    return true;
                }
                return false;
            },
            show: function(element) {
                element.innerHTML =
                    '<div class="trigger">' +
                    '<button id="infinite-scroll-ps-load-more" style="background-color:'+ infinite_scroll_ps.btn_color +'" >'+ infinite_scroll_ps.btn_text +'</button>' +
                    '</div>';
                $(element).appendTo(infinite_scroll_ps.sel_container);
            },
            hide: function(element) {
                $(element).remove();
            }
        }
    });

});