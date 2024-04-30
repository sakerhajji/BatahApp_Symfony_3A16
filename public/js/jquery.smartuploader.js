/*== File Upload and delete Style1 Start Add Listing Page ==*/
function readURL(input, imgControlName) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      $(imgControlName).attr('src', e.target.result);
    }
    reader.readAsDataURL(input.files[0]);
  }
}

$("#imag").change(function() {
  // add your logic to decide which image control you'll use
  var imgControlName = "#ImgPreview";
  readURL(this, imgControlName);
  $('.preview1').addClass('it');
  $('.btn-rmv1').addClass('remove_add_file');
});
$("#imag2").change(function() {
  // add your logic to decide which image control you'll use
  var imgControlName = "#ImgPreview2";
  readURL(this, imgControlName);
  $('.preview2').addClass('it');
  $('.btn-rmv2').addClass('remove_add_file');
});
$("#imag3").change(function() {
  // add your logic to decide which image control you'll use
  var imgControlName = "#ImgPreview3";
  readURL(this, imgControlName);
  $('.preview3').addClass('it');
  $('.btn-rmv3').addClass('remove_add_file');
});
$("#imag4").change(function() {
  // add your logic to decide which image control you'll use
  var imgControlName = "#ImgPreview4";
  readURL(this, imgControlName);
  $('.preview4').addClass('it');
  $('.btn-rmv4').addClass('remove_add_file');
});

$("#removeImage1").on('click',function(e) {
  e.preventDefault();
  $("#imag").val("");
  $("#ImgPreview").attr("src", "");
  $('.preview1').removeClass('it');
  $('.btn-rmv1').removeClass('remove_add_file');
});
$("#removeImage2").on('click',function(e) {
  e.preventDefault();
  $("#imag2").val("");
  $("#ImgPreview2").attr("src", "");
  $('.preview2').removeClass('it');
  $('.btn-rmv2').removeClass('remove_add_file');
});
$("#removeImage3").on('click',function(e) {
  e.preventDefault();
  $("#imag3").val("");
  $("#ImgPreview3").attr("src", "");
  $('.preview3').removeClass('it');
  $('.btn-rmv3').removeClass('remove_add_file');
});
$("#removeImage4").on('click',function(e) {
  e.preventDefault();
  $("#imag4").val("");
  $("#ImgPreview4").attr("src", "");
  $('.preview4').removeClass('it');
  $('.btn-rmv4').removeClass('remove_add_file');
});
/*== File Upload and delete Style1 End Add Listing Page ==*/
