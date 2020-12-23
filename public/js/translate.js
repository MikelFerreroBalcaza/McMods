$('#tblcodetranslate').on('click', 'button', function (e) {
    let txtarea = $(this).parent().prev().children();
    let tr = $(this).parent().parent();
    let txtarea2 = $(this).parent().prev().prev().children();
    let source = $('#langaTranslate').text().trim();
    console.log(source);
    let target = '';
    if ($('#langtoTranslate')) {
        target = $('#langtoTranslate').text().trim();
    } else if ($('#langtranslate')) {
        target = $('#langtranslate').val();
    }
    console.log(target);
    if (target != '') {
        e.preventDefault();
        showLoader();
        $.ajax({
            type: "POST",
            url: '/ajax',
            data: {
                source: source,
                target: target,
                text: $(this).parent().prev().prev().children().text()
            },
            success: function (response) {
                let arr = JSON.parse(response);
                txtarea.val(arr['result']);
                txtarea.removeClass();
                txtarea.addClass('review');
                txtarea2.removeClass();
                txtarea2.addClass('review');
                tr.removeClass();
                tr.addClass('tabletr review');
                hideLoader();
            }
        });
    }
});
$('#tblcodetranslate').on('click', 'input', function (e) {
    let tr = $(this).parent().parent();
    let txtarea1 = $(this).parent().prev().prev().children();
    let txtarea2 = $(this).parent().prev().prev().prev().children();
    if (this.value == 'checked') {
        txtarea1.removeClass();
        txtarea1.addClass('checked');
        txtarea2.removeClass();
        txtarea2.addClass('checked');
        tr.removeClass();
        tr.addClass('tabletr checked');
    }
    if (this.value == 'review') {
        txtarea1.removeClass();
        txtarea1.addClass('review');
        txtarea2.removeClass();
        txtarea2.addClass('review');
        tr.removeClass();
        tr.addClass('tabletr review');
    }
    if (this.value == 'delete') {
        txtarea1.removeClass();
        txtarea1.addClass('delete');
        txtarea1.val('');
        txtarea2.removeClass();
        txtarea2.addClass('delete');
        tr.removeClass();
        tr.addClass('tabletr delete');
    }
});