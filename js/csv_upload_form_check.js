$(document).ready(function () {
  $("#frmCSVImport").on("submit", function () {
    $("#response").attr("class", "");
    $("#response").html("");
    var fileType = ".csv";
    var regex = new RegExp("([a-zA-Z0-9s_\\.-:])+(" + fileType + ")$");
    if (!regex.test($("#file").val().toLowerCase())) {
      $("#response").addClass("error");
      $("#response").addClass("display-block");
      $("#response").html(
        "Érvénytelen fájlfeltöltés : <b>" + fileType + "</b> Files."
      );
      return false;
    }
    return true;
  });
});
