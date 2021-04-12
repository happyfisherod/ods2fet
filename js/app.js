function _(el) {
  return document.getElementById(el);
}

function uploadFile() {
  var file = _("file1").files[0];
  // alert(file.name+" | "+file.size+" | "+file.type);
  var formdata = new FormData();
  formdata.append("file1", file);
  var ajax = new XMLHttpRequest();
  ajax.upload.addEventListener("progress", progressHandler, false);
  ajax.addEventListener("load", completeHandler, false);
  ajax.addEventListener("error", errorHandler, false);
  ajax.addEventListener("abort", abortHandler, false);
  ajax.open("POST", "file_upload_parser.php"); // http://www.developphp.com/video/JavaScript/File-Upload-Progress-Bar-Meter-Tutorial-Ajax-PHP
  //use file_upload_parser.php from above url
  ajax.send(formdata);
}

function progressHandler(event) {
  _("loaded_n_total").innerHTML = "Uploaded " + event.loaded + " bytes of " + event.total;
  var percent = (event.loaded / event.total) * 100;
  _("progressBar").value = Math.round(percent);
  _("status").innerHTML = Math.round(percent) + "% uploaded... please wait";
}
var uploaded_file = "";
function completeHandler(event) {
  _("status").innerHTML = event.target.responseText;
  uploaded_file = event.target.responseText;
  _("progressBar").value = 0; //wil clear progress bar after successful upload
  document.getElementById('btnConvert').disabled = false;
}

function errorHandler(event) {
  _("status").innerHTML = "Upload Failed";
}

function abortHandler(event) {
  _("status").innerHTML = "Upload Aborted";
}

function fileUploadBtnClicked() {
  document.getElementById('btnConvert').disabled = true;
  document.getElementById('downloadBtn').disabled = true;
}

function convertClicked() {
  var ajax = new XMLHttpRequest();
  ajax.upload.addEventListener("progress", convertProgressHandler, false);
  ajax.addEventListener("load", convertCompleteHandler, false);
  ajax.addEventListener("error", convertErrorHandler, false);
  ajax.addEventListener("abort", convertAbortHandler, false);
  ajax.open("POST", "ods_php_parser_usage.php");
  var formdata = new FormData();
  formdata.append("ods", uploaded_file);
  ajax.send(formdata);
}

function convertProgressHandler(event) {
  var percent = (event.loaded / event.total) * 100;
  _("convertProgressBar").value = Math.round(percent);
  _("convertStatus").innerHTML = Math.round(percent) + "% converted... please wait";
}

function convertCompleteHandler(event) {
  _("convertStatus").innerHTML = event.target.responseText;
  _("convertProgressBar").value = 0; //wil clear progress bar after successful convert
  document.getElementById('downloadLink').href = "./converted/"+uploaded_file+".fet";
  document.getElementById('downloadBtn').disabled = false;
}

function convertErrorHandler(event) {
  _("convertStatus").innerHTML = "Convert Failed";
}

function convertAbortHandler(event) {
  _("convertStatus").innerHTML = "Convert Aborted";
}