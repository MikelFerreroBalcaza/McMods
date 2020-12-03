let form = document.querySelector("form");
form.addEventListener(
  "invalid",
  function (event) {
    event.preventDefault();
  },
  true
);
let button = form.querySelector("#sub");
button.addEventListener("click", function () {
  if (document.querySelectorAll(".error").length == 0) {
    let invalid = form.querySelectorAll(":invalid");
    for (let i = 0; i < invalid.length; i++) {
      let error = document.createElement("div");
      let label = invalid[i].parentNode.parentNode;
      error.className = "error";
      error.textContent = invalid[i].validationMessage;
      label.append(error);
    }
    window.setTimeout(function () {
      var allErrors = document.querySelectorAll(".error");
      for (var i = 0; i < allErrors.length; i++) {
        allErrors[i].remove();
      }
    }, 2000);
  }
  hideLoader();
});

$(function () {
  $("#userfile").on("change", function () {
    if ($(this)[0].files[0]) {
      $(this).prev().text($(this)[0].files[0].name);
    }
  });
});
