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

    $('#menuIcon').on('click', function (e) {
        $(this).siblings('nav').slideToggle();
    });

    $window.on('resize', function () {
        if ($window.width() > 1280) {
            $('#topWrapper nav').show();
        }
    });

    function adjustInfoHeight() {
        var $info = $('#infoWrapper'),
            infoHeight = $info.height(),
            windowHeight = $window.height();

        if (body.hasClass('homePage')) {
            var postHeight = $('#posts').outerHeight(true);
            //console.log(postHeight);
            var availableContentHeight = windowHeight - 60 - postHeight - 15; // the -15 is for measuring
            $info.css('height', availableContentHeight);
        }
        //
        $('.sideBackground').height($info.height());
    }

    adjustInfoHeight();

    //$('#infoWrapper').backstretch(baseUrl + 'public/images/home-background.jpg');
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
    /*                   Gallery Page                   */
    var $imagePreview = $('#imagePreview');
    if($imagePreview.length > 0){
        var $displayedImage = $imagePreview.find('img');
        $displayedImage.css('max-width',$displayedImage.get(0).naturalWidth);
    }


    $('#images').find('ul li').click(function(){
        var $element = $(this);
        if(!$element.hasClass('active')){
            var $imagePreview = $('#imagePreview'),
                $image = $imagePreview.find('img');

            $image.css('height',$image.height()).attr('src',baseUrl + '/public/images/loading.gif');

            $element.siblings('.active').removeClass('active');
            $element.addClass('active');

            var splitImg = $element.children('img').attr('src').split('/'),
                name = splitImg[splitImg.length-1].slice(0,-12),
                path = baseUrl + '/public/images/gallery/' + name + '.png';

            // we use a temp image to get the natural width of the image
            var tempImage = $('<img />', {
                src: path
            }).hide().appendTo(body).load(function(){
                $image.css('max-width',tempImage.width());
                tempImage.remove();
                $image.css('height','auto').attr('src',path);
                $imagePreview.children('span').text($element.children('span').text());
            });


        }
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