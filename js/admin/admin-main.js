$(function () {
    var flash = $(".flash");
    if (flash.is(":visible")) {
        flash.setRemoveTimeout(5000)
    }

    /**
     * Created by Josh on 13/7/2015.
     */
    var bTable = $('.tableWrapper').betterTable({
        unsortableCells: [0, 13],
        hiddenCells: [0],
        tableName: "Users",
        lengthMenu: [1, 5, 7, 10, 20, 30, -1],
        defaultLength: 10,
        columnNumber: 50
    });

    $('.formWrapper textarea').ckeditor({
        customConfig: betterTableUrl + '/custom-config.js'
    });

    $('.formWrapper .element select').each(function(){
        var select = $(this);
        if(!select.hasClass('joinList')) select.selectBox();
    });

    var $galleryForm = $("#galleryForm");

    if ($galleryForm.length > 0) {
        var tableJoin = new TableJoin($('.joinSelect'));

        $galleryForm.on('submit', function (e) {
            //e.preventDefault();
            var encodedAttributes = tableJoin.getEncodedAttributes();

            var joinInput = $galleryForm.children('input[name="gallery[images]"]');
            if(joinInput.length <= 0) {
                joinInput = $('<input />', {
                    name: "gallery[images]"
                }).hide().appendTo($galleryForm);
            }
            joinInput.val(encodedAttributes);
            //console.log(encodedAttributes);
            //console.log($galleryForm.serialize());
            //return false;
        });

    }
});
