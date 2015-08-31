$(function () {
    var body = $('body'),
        flash = $('#flash'),
        $window = $(window),
        focusedDiv;
    if (flash.is(':visible')) {
        flash.setRemoveTimeout(3000);
    }
    //var gt = new Gettext({ 'domain': 'messages' });

    $('.printTrigger').on('click', function (e) {
        e.preventDefault();
        window.print();
    });

    // if the shadow div exists, when you click outside of the
    // focused box it should be reverted to normal
    $(document).on('click', function (event) {
        if (event.target.id == 'shadow') {
            focusedDiv.unfocusLight();

            $('body').removeClass('unscrollable');
//            if (focusedDiv.attr('id') == 'stage') {
//                focusedDiv.find('iframe').detach();
//                focusedDiv.hide();
//            } else {
            focusedDiv.detach();
//            }
        }
    });

        function adjustInfoHeight() {
            var $info = $('#infoWrapper');

            if(body.hasClass('homePage')) {
                var availableContentHeight = $window.height() - $("header").outerHeight() - $('#posts').outerHeight(true);
                $info.css('height', availableContentHeight);
            }
            $info.backstretch(baseUrl + '/images/home-background.jpg');

            $('.sideBackground').height($info.height());
        }

        adjustInfoHeight();

        $window.on('resize', adjustInfoHeight);


        //var $bg              = $("#homeBg"),
        //    aspectRatio      = $bg.width() / $bg.height();
        //
        //console.log("edw");
        //function resizeBg() {
        //
        //    if ( ($window.width() / $window.height()) < aspectRatio ) {
        //        $bg
        //            .removeClass()
        //            .addClass('bgheight');
        //    } else {
        //        $bg
        //            .removeClass()
        //            .addClass('bgwidth');
        //    }
        //
        //}
        //
        //$window.resize(resizeBg).trigger("resize");



    /* ===================================================== */
    /*                       Tab Related                     */
    var tabs = $('.tabs span');

    tabs.each(function () {
        var span = $(this);
        var id = span.attr('class').split(' ')[0].substr(4);
        if (span.hasClass('activeTab')) {
            $('#' + id).show();
        } else {
            $('#' + id).hide();
        }
    });

    tabs.on('click', function () {
        var span = $(this);
        if (!span.hasClass('activeTab')) {
            var activeTab = span.siblings('.activeTab').removeClass('activeTab');
            $('#' + activeTab.attr('class').split(' ')[0].substr(4)).fadeOut("normal", function () {
                $('#' + span.attr('class').split(' ')[0].substr(4)).fadeIn();
            });
            span.addClass('activeTab');
        }
    });

    /* ===================================================== */
    /*                 Product Category Search               */

    /**
     * @description The category search function.
     */
    var isSearching = false;
    var categories = $('#categories').html();
    $(".search input").on('keyup', function (event) {
        var value = $(this).val().trim().toLowerCase();
        delay(function () {
            event.preventDefault();
            if (!isSearching) {
                isSearching = true;
                var resultList = $('#categories');
                if (value.length == 0) {
                    resultList.html(categories);
                } else if (value.length > 1) {
                    $.ajax({
                        url: baseUrl + '/products/search/' + value,
                        type: "GET"
                    }).success(function (data) {
                        if ($.trim(data) != "") {
                            resultList.html(data);
                        }
                    }).error(function () {
                        addMessage("Something with wrong, please try again.");
                    });
                }
                isSearching = false;
            }
        }, 150);
    });

    /* ===================================================== */
    /*                   Product View Page                   */


    $('.magnifiable').on('click', function () {
        var img = $(this),
            imgSrc = img.attr('src'),
            windowHeight = $(window).height();

        // we use a temp image to get the natural height of the image
        var tempImage = $('<img />', {
            src: imgSrc,
            css: {
                "display": "none"
            }
        }).appendTo(body);

        var imgHeight = tempImage.height();
        tempImage.remove();

        if (imgHeight < windowHeight - 100) {
            var css = {
                top: "50%",
                marginTop: -(parseInt(imgHeight / 2))
            }
        } else {
            var css = {
                top: 0,
                marginTop: "50px"
            }
        }

        var stageWrapper = $('<div/>', {
            id: "stageWrapper",
            css: css
        }).prependTo($('body'));

        var stage = $('<div/>', {
            'id': 'stage'
        }).appendTo(stageWrapper);

        var imgWrapper = $('<div/>', {
            'id': 'imageWrapper'
        }).appendTo(stage);

        var newImg = $('<img/>', {
            'src': imgSrc,
            'css': {
                "max-height": $(window).height() - 100,
                "width": "auto"
            }
        }).appendTo(imgWrapper);

        focusedDiv = stageWrapper;
        stageWrapper.focusLight();
        stageWrapper.css({
            "width": newImg.width(),
            "margin-left": -(newImg.width() / 2)
        });
        body.addClass('unscrollable');
    });
});