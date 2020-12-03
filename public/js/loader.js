$(document).ready(function () {
    hideLoader();
})
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("form").addEventListener('submit', showLoader());
});
$('#es-ES > a,#en-GB > a,.btnanim v1').click(function () {
    showLoader();
})
function showLoader() {
    $("#css-loader").show();
}
function hideLoader() {
    $("#css-loader").hide();
}