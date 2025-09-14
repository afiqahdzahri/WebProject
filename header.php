<?php
// header.php - Ensure no output before this PHP tag
// No spaces, no HTML, no echo statements before the opening PHP tag

date_default_timezone_set("Asia/Manila");
ob_start();

$title = isset($_GET['page']) ? ucwords(str_replace("_", ' ', $_GET['page'])) : "Home";
$page_title = $title . " | " . (isset($_SESSION['system']['name']) ? $_SESSION['system']['name'] : 'Employee Performance System');

ob_end_clean(); // Clean the buffer without outputting
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($page_title) ?></title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- DataTables -->
  <link rel="stylesheet" href="assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
   <!-- Select2 -->
  <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
   <!-- SweetAlert2 -->
  <link rel="stylesheet" href="assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="assets/plugins/toastr/toastr.min.css">
  <!-- dropzonejs -->
  <link rel="stylesheet" href="assets/plugins/dropzone/min/dropzone.min.css">
  <!-- DateTimePicker -->
  <link rel="stylesheet" href="assets/dist/css/jquery.datetimepicker.min.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Switch Toggle -->
  <link rel="stylesheet" href="assets/plugins/bootstrap4-toggle/css/bootstrap4-toggle.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="assets/dist/css/styles.css">
  
  <!-- jQuery -->
  <script src="assets/plugins/jquery/jquery.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="assets/plugins/jquery-ui/jquery-ui.min.js"></script>
  
  <!-- Custom JavaScript for logout and login handling -->
  <script>
  // Handle logout functionality
  function handleLogout() {
      if(confirm('Are you sure you want to logout?')) {
          $.ajax({
              url: 'ajax.php?action=logout',
              method: 'POST',
              dataType: 'json',
              success: function(response) {
                  if(response.status === 'success') {
                      window.location.href = response.redirect;
                  } else {
                      alert('Logout failed: ' + response.message);
                      // Fallback to traditional logout
                      window.location.href = 'logout.php';
                  }
              },
              error: function() {
                  // Fallback: redirect directly to logout.php
                  window.location.href = 'logout.php';
              }
          });
      }
  }

  // Direct logout without confirmation (optional)
  function directLogout() {
      $.ajax({
          url: 'ajax.php?action=logout',
          method: 'POST',
          dataType: 'json',
          success: function(response) {
              if(response.status === 'success') {
                  window.location.href = response.redirect;
              } else {
                  // Fallback to traditional logout
                  window.location.href = 'logout.php';
              }
          },
          error: function() {
              // Fallback: redirect directly to logout.php
              window.location.href = 'logout.php';
          }
      });
  }

  // Update login form handling to work with JSON responses
  $(document).ready(function(){
      $('#login-form').submit(function(e){
          e.preventDefault()
          start_load()
          if($(this).find('.alert-danger').length > 0 )
              $(this).find('.alert-danger').remove();
          $.ajax({
              url:'ajax.php?action=login',
              method:'POST',
              data:$(this).serialize(),
              dataType: 'json',
              error:function(err){
                  console.log(err)
                  end_load();
              },
              success:function(resp){
                  if(resp.status === 'success'){
                      location.href ='index.php';
                  } else {
                      $('#login-form').prepend('<div class="alert alert-danger">' + resp.message + '</div>')
                      end_load();
                  }
              }
          })
      })
  });
  </script>

  <!-- summernote -->
  <link rel="stylesheet" href="assets/plugins/summernote/summernote-bs4.min.css">
  
</head>