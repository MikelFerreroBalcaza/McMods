let btn = document.getElementById("btn_mode");
let css = '';

btn.onclick = function () {
    if (btn.checked) {
        css = 'css/style_dark.css';
    } else {
        css = 'css/style_light.css';
    }
    document.getElementById("link_mode").setAttribute("href", css);
}