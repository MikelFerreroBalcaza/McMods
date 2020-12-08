$(document).ready(function () {
    hideLoader();
})
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("form").addEventListener('submit', showLoader());
});
$('#es-ES > a,#en-GB > a,div.ficha:nth-child(1) > ul:nth-child(1) > li:nth-child(3) > label:nth-child(1)').click(function () {
    showLoader();
})
function showLoader() {
    $("#css-loader").show();
}
function hideLoader() {
    $("#css-loader").hide();
}