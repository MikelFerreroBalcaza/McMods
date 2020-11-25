$('#tblcodetranslate').on('click', 'button', function (e) {
    let txtarea = $(this).parent().prev().children();
    let tr = $(this).parent().parent();
    let txtarea2 = $(this).parent().prev().prev().children();
    let lang = '';
    if ($('#langtranslate').length) {
        lang = $('#langtranslate').val()
    }
    e.preventDefault();
    $.ajax({
        type: "POST",
        url: 'php/funciones_AJAX.php',
        data: {
            lang: lang,
            text: $(this).parent().prev().prev().children().text()
        },
        success: function (response) {
            let arr = JSON.parse(response);
            if (arr['LANGUAGEPLACEHOLDER'] == false) {
                alert(arr['TRANSLATOR_LANGUAGEALERTSETLANG'])
            } else {
                txtarea.val(arr['RESULT']);
                txtarea.removeClass();
                txtarea.addClass('review');
                txtarea2.removeClass();
                txtarea2.addClass('review');
                tr.removeClass();
                tr.addClass('tabletr review');
            }

        }
    });

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