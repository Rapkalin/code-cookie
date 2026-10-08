/**
 * Handles theme general events
 * 
 * @package Newsmatic
 * @since 1.0.0
 */
jQuery(document).ready(function($) {
    "use strict"
    var ajaxUrl = newsmaticObject.ajaxUrl, _wpnonce = newsmaticObject._wpnonce, sttOption = newsmaticObject.stt, query_vars = newsmaticObject.query_vars, paged = newsmaticObject.paged,  stickeyHeader = newsmaticObject.stickey_header;
    
    setTimeout(function() {
        $('body .newsmatic_loading_box').hide();
    }, 3000);

    var nrtl = false
    var ndir = "left"
    if ($('body').hasClass("rtl")) {
        nrtl = true;
        ndir = "right";
    };
    
    // theme trigger modal close
    function newsmaticclosemodal( elm, callback ) {
        $(document).mouseup(function (e) {
            var container = $(elm);
            if (!container.is(e.target) && container.has(e.target).length === 0) callback();
        });
    }

    // ticker news slider events
    var tc = $( ".ticker-news-wrap" );
    if( tc.length ) {
        var tcM = tc.find( ".ticker-item-wrap" ).marquee({
            duration: 15000,
            gap: 0,
            delayBeforeStart: 0,
            direction: ndir,
            duplicated: true,
            startVisible: true,
            pauseOnHover: true,
        });
        tc.on( "click", ".newsmatic-ticker-pause", function() {
            $(this).find( "i" ).toggleClass( "fa-pause fa-play" )
            tcM.marquee( "toggle" );
        })
    }

    // top date time
    var timeElement = $( ".top-date-time .time" )
    if( timeElement.length > 0 ) {
        setInterval(function() {
            timeElement.html(new Date().toLocaleTimeString())
        },1000);
    }
    
    // search form and sidebar toggle trigger
    $( "#masthead" ).on( "click", ".sidebar-toggle-trigger", function() {
        // $(this).next().addClass('toggle_show');
        $(this).addClass('slideshow');
        $('body').addClass('body_show_sidetoggle');
    });
    $( "#masthead" ).on( "click", ".sidebar-toggle-trigger.slideshow, .sidebar-toggle .sidebar-toggle-close", function() {
        // $(this).next().removeClass('toggle_show');
        $('.sidebar-toggle-trigger').removeClass('slideshow');
        $('body').removeClass('body_show_sidetoggle');
    });

    // search form 
    $( "#masthead" ).on( "click", ".search-trigger", function() {
        $(this).next().slideDown('slow');
        $(this).addClass('slideshow');
        $('#masthead .search-wrap input[type="search"]').focus()
    });
    $( "#masthead" ).on( "click", ".search-trigger.slideshow", function() {
        $(this).next().slideUp('slow');
        $(this).removeClass('slideshow');
    });

    // live search
    if( newsmaticObject.livesearch ) {
        var searchContainer = $("#masthead .search-wrap")
        if( searchContainer.length > 0 ) {
            var searchFormContainer = searchContainer.find("form")
            searchContainer.on( 'change, keyup', 'input[type="search"]', function() {
                var searchKey = $(this).val()
                if(searchKey) {
                    $.ajax({
                        method: 'post',
                        url: ajaxUrl,
                        data: {
                            action: 'newsmatic_search_posts_content',
                            search_key : searchKey.trim(),
                            _wpnonce: _wpnonce
                        },
                        beforeSend: function() {
                            searchFormContainer.addClass( 'retrieving-posts' );
                            searchFormContainer.removeClass( 'results-loaded' )
                        },
                        success : function(res) {
                            var parsedRes = JSON.parse(res)
                                searchContainer.find(".search-results-wrap").remove()
                                searchFormContainer.after(parsedRes.posts)
                                searchFormContainer.removeClass( 'retrieving-posts' ).addClass( 'results-loaded' );
                        },
                        complete: function() {
                            // render search content here
                        }
                    })
                } else {
                    searchContainer.find(".search-results-wrap").remove()
                    searchFormContainer.removeClass( 'results-loaded' )
                }
            })
        }
    }

    newsmaticclosemodal( $( ".search-wrap" ), function () {
        $( ".search-wrap .search-trigger" ).removeClass( "slideshow" );
        $( ".search-form-wrap" ).slideUp();
        $("#masthead .search-wrap").find(".search-results-wrap").remove()
        $("#masthead .search-wrap").removeClass( 'results-loaded' )
    }); // trigger search close
    newsmaticclosemodal( $( ".sidebar-toggle-wrap" ), function () {
        $( ".sidebar-toggle-wrap .sidebar-toggle-trigger" ).removeClass( "slideshow" );
        $('body').removeClass('body_show_sidetoggle');
    }); // trigger htsidebar close

    // top header ticker news slider events
    var thtn = $( ".top-ticker-news" );
    if( thtn.length ) {
        var thtnitems = thtn.find( ".ticker-item-wrap" )
        thtnitems.slick({
            dots: false,
            infinite: true,
            rtl: nrtl,
            vertical: true,
            arrows: true,
            autoplay: true,
            nextArrow: `<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>`,
            prevArrow: `<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>`,
        });
    }

    // main banner slider events
    var bc = $( "#main-banner-section" );
    if( bc.length ) {
        var bic = bc.find( ".main-banner-slider" )
        var bAuto = bic.data( "auto" )
        var bArrows = bic.data( "arrows" )
        bic.slick({
            dots: false,
            infinite: true,
            rtl: nrtl,
            arrows: bArrows,
            autoplay: bAuto,
            nextArrow: `<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>`,
            prevArrow: `<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>`,
        });
    }

    // news carousel events
    var nc = $( ".newsmatic-section .news-carousel .news-carousel-post-wrap" );
    if( nc.length ) {
        nc.each(function() {
            var _this = $(this)
            var ncDots= _this.data("dots") == '1'
            var ncLoop= _this.data("loop") == '1'
            var ncArrows= _this.data("arrows") == '1'
            var ncAuto  = _this.data("auto") == '1'
            var ncColumns  = _this.data("columns")
            _this.slick({
                dots: ncDots,
                infinite: ncLoop,
                arrows: ncArrows,
                autoplay: ncAuto,
                rtl: nrtl,
                slidesToShow: ncColumns,
                nextArrow: `<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>`,
                prevArrow: `<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>`,
                responsive: [
                  {
                    breakpoint: 1100,
                    settings: {
                      slidesToShow: 3,
                    },
                  },
                  {
                    breakpoint: 768,
                    settings: {
                      slidesToShow: 2,
                    },
                  },
                  {
                    breakpoint: 640,
                    settings: {
                      slidesToShow: 1,
                    },
                  }
                ]
            });
        })
    }

    // Filter posts
     $( ".newsmatic-section .news-filter" ).each(function() {
        var $scope = $(this), $scopeOptions = $scope.data("args"), newTabs = $scope.find( ".filter-tab-wrapper" ), newTabsContent = $scope.find( ".filter-tab-content-wrapper" );
        newTabs.on( "click", ".tab-title", function() {
          var a = $(this), aT = a.data("tab")
          a.addClass( "isActive" ).siblings().removeClass( "isActive" );
          if( newTabsContent.find( ".tab-content.content-" + aT ).length < 1 ) {
            $scopeOptions.category_name = aT
            $.ajax({
                method: 'get',
                url: ajaxUrl,
                data: {
                    action: 'newsmatic_filter_posts_load_tab_content',
                    options : JSON.stringify( $scopeOptions ),
                    _wpnonce: _wpnonce
                },
                beforeSend: function() {
                    $scope.addClass( 'retrieving-posts' );
                },
                success : function(res) {
                    var parsedRes = JSON.parse(res)
                    if( parsedRes.loaded ) {
                        newTabsContent.append(parsedRes.posts)
                        $scope.removeClass( 'retrieving-posts' );
                    }
                },
                complete: function() {
                    newTabsContent.find( ".tab-content.content-" + aT ).show().siblings().hide()
                }
            })
          } else {
            newTabsContent.find( ".tab-content.content-" + aT ).show().siblings().hide()
          }
        })
    })

    // popular posts widgets
    var ppWidgets = $( ".newsmatic-widget-popular-posts" )
    ppWidgets.each(function() {
        var _this = $(this), parentWidgetContainerId = _this.parents( ".widget.widget_newsmatic_popular_posts_widget" ).attr( "id" ), parentWidgetContainer = $( "#" + parentWidgetContainerId )
        var ppWidget = parentWidgetContainer.find( ".popular-posts-wrap" );
        if( ppWidget.length > 0 ) {
            var ppWidgetAuto = ppWidget.data( "auto" )
            var ppWidgetArrows = ppWidget.data( "arrows" )
            var ppWidgetLoop = ppWidget.data( "loop" )
            var ppWidgetVertical = ppWidget.data( "vertical" )
            if( ppWidgetVertical == 'vertical' ) {
                ppWidget.slick({
                    vertical: true,
                    slidesToShow: 4,
                    dots: false,
                    infinite: ppWidgetLoop,
                    arrows: ppWidgetArrows,
                    autoplay: ppWidgetAuto,
                    nextArrow: `<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>`,
                    prevArrow: `<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>`
                })
            } else {
                ppWidget.slick({
                    dots: false,
                    infinite: ppWidgetLoop,
                    rtl: nrtl,
                    arrows: ppWidgetArrows,
                    autoplay: ppWidgetAuto,
                    nextArrow: `<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>`,
                    prevArrow: `<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>`
                })
            }  
        }
    })

    // carousel posts widgets
    var cpWidgets = $( ".newsmatic-widget-carousel-posts" )
    cpWidgets.each(function() {
        var _this = $(this), parentWidgetContainerId = _this.parents( ".widget.widget_newsmatic_carousel_widget" ).attr( "id" ), parentWidgetContainer
        if( typeof parentWidgetContainerId != 'undefined' ) {
            parentWidgetContainer = $( "#" + parentWidgetContainerId )
            var ppWidget = parentWidgetContainer.find( ".carousel-posts-wrap" );
        } else {
            var ppWidget = _this;
        }
        if( ppWidget.length > 0 ) {
            var ppWidgetAuto = ppWidget.data( "auto" )
            var ppWidgetArrows = ppWidget.data( "arrows" )
            var ppWidgetLoop = ppWidget.data( "loop" )
            var ppWidgetVertical = ppWidget.data( "vertical" )
            if( ppWidgetVertical == 'vertical' ) {
                ppWidget.slick({
                    vertical: true,
                    dots: false,
                    infinite: ppWidgetLoop,
                    arrows: ppWidgetArrows,
                    autoplay: ppWidgetAuto,
                    nextArrow: `<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>`,
                    prevArrow: `<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>`
                })
            } else {
                ppWidget.slick({
                    dots: false,
                    infinite: ppWidgetLoop,
                    rtl: nrtl,
                    arrows: ppWidgetArrows,
                    autoplay: ppWidgetAuto,
                    nextArrow: `<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>`,
                    prevArrow: `<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>`
                })
            }  
        }
    })

    // tabbed posts
    var tabpWidgets = $( ".newsmatic-tabbed-widget-tabs-wrap" )
    tabpWidgets.each(function() {
        var _this = $(this), parentWidgetContainerId = _this.parents( ".widget.widget_newsmatic_tabbed_posts_widget" ).attr( "id" ), parentWidgetContainer
        if( typeof parentWidgetContainerId != 'undefined' ) {
            parentWidgetContainer = $( "#" + parentWidgetContainerId )
            var tabpWidget = parentWidgetContainer.find( ".newsmatic-tabbed-widget-tabs-wrap" );
        } else {
            var tabpWidget = _this;
        }
        if( tabpWidget.length > 0 ) {
            tabpWidget.on( "click", ".tabbed-widget-tabs li.tabbed-widget", function() {
                var _this = $(this), tabItem = _this.attr( "tab-item" );
                _this.addClass( "active" ).siblings().removeClass( "active" );
                tabpWidget.find( '.widget-tabs-content div[tab-content="' + tabItem + '"]' ).addClass( "active" ).siblings().removeClass( "active" );
            })
        }
    })

    // handle theme mode toggle
    $( ".mode_toggle_wrap" ).on( "click", function() {
        var _this = $(this)
        $( "body" ).toggleClass( "newsmatic_dark_mode" )
        if( _this.find( "input:checked" ).length > 0 && $( "body" ).hasClass( "newsmatic_dark_mode" ) ) {
            $.cookie("themeMode", "dark", { path: '/' }); // Custom update for child
            $("body").removeClass("newsmatic_main_body");

            $(".custom-logo-link").remove(); // Custom update for child
            $(".site-branding").append(newsmaticObject.customLogoDark); // Custom update for child
        } else {
            $.cookie("themeMode", "light", { path: '/'}); // Custom update for child
            $("body").addClass("newsmatic_main_body");

            $(".custom-logo-link").remove(); // Custom update for child
            $(".site-branding").append(newsmaticObject.customLogoLight); // Custom update for child
        }
    });


    // header sticky
    if( stickeyHeader ) {
        var lastScroll = 0;
        $(window).on('scroll',function() {  
            var scroll = $(window).scrollTop();
            if(scroll > 50){        
                if(lastScroll - scroll > 0) {
                    $(".main-header .menu-section").addClass("fixed_header");
                } else {
                    $(".main-header .menu-section").removeClass("fixed_header");
                }
                lastScroll = scroll;
            }else{
                $(".main-header .menu-section").removeClass("fixed_header");
            }
        });
    }

    // back to top script
    if( sttOption && $( "#newsmatic-scroll-to-top" ).length ) {
        var scrollContainer = $( "#newsmatic-scroll-to-top" );
        $(window).scroll(function() {
            if ( $(this).scrollTop() > 800 ) {
                scrollContainer.addClass('show');
            } else {
                scrollContainer.removeClass('show');
            }
        });
        scrollContainer.click(function(event) {
            event.preventDefault();
            // Animate the scrolling motion.
            $("html, body").animate({scrollTop:0},"slow");
        });
    }

    // category archive hide featured post in list
    var featuredPost = $( ".archive.category .featured-post.is-sticky" )
    if( featuredPost.length > 0 ) {
        var postHide = "#post-" + featuredPost.data("id")
        $(postHide).addClass( "sticky-hide" );
    }
})