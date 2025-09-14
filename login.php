<!DOCTYPE html>
<html lang="en">
<?php 
session_start();
include('./db_connect.php');
ob_start();
$system = $conn->query("SELECT * FROM system_settings")->fetch_array();
foreach($system as $k => $v){
    $_SESSION['system'][$k] = $v;
}
ob_end_flush();
?>
<?php 
if(isset($_SESSION['login_id']))
header("location:index.php?page=home");
?>
<?php include 'header.php' ?>
<head>
  <style>
    .form-control, .custom-select {
      border-radius: 50px !important;
    }
    .input-group-text {
      border-radius: 50px !important;
    }
    .btn-primary {
      border-radius: 50px;
    }
  </style>
</head>
<body class="hold-transition login-page" style="background: url('home_image14.jpg') no-repeat center center fixed; background-size: cover;">
  <h2><b><?php echo $_SESSION['system']['name'] ?></b></h2>
<div class="login-box">
  <div class="login-logo">
    <a href="#" class="text-white"></a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <form action="" id="login-form">
        <div class="input-group mb-3">
          <input type="email" class="form-control" name="email" required placeholder="Email" value="admin@epes.com">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="password" required placeholder="Password" value="password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="form-group mb-3">
          <label for="">Login As</label>
          <select name="login" id="" class="custom-select custom-select-sm">
            <option value="0" selected>Admin</option>
            <option value="1">Evaluator</option>
            <option value="2">Employee</option>
          </select>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
                Remember Me
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->
<script>
  $(document).ready(function(){
    $('#login-form').submit(function(e){
      e.preventDefault()
      start_load()
      if($(this).find('.alert-danger').length > 0 )
        $(this).find('.alert-danger').remove();
      
      // Basic validation
      var email = $('input[name="email"]').val().trim();
      var password = $('input[name="password"]').val().trim();
      
      if(email === '' || password === '') {
        $('#login-form').prepend('<div class="alert alert-danger">Please fill in all required fields.</div>');
        end_load();
        return false;
      }
      
      $.ajax({
        url:'ajax.php?action=login',
        method:'POST',
        data:$(this).serialize(),
        dataType: 'json',
        error:function(xhr, status, error){
          console.log('AJAX Error:', error)
          $('#login-form').prepend('<div class="alert alert-danger">Network error. Please try again.</div>');
          end_load();
        },
        success:function(resp){
          console.log('Login Response:', resp); // Debug response
          if(resp && resp.status === 'success'){
            location.href ='index.php';
          } else {
            var errorMsg = resp && resp.message ? resp.message : 'Username or password is incorrect.';
            $('#login-form').prepend('<div class="alert alert-danger">' + errorMsg + '</div>');
            end_load();
          }
        }
      })
    })
  })
</script>
<?php include 'footer.php' ?>
</body>
</html>