<!DOCTYPE html>
<html lang="en">
<?php 
session_start();
if(!isset($_SESSION['login_id'])) {
    header('location:login.php');
    exit(); // Add exit to prevent further execution
}
include 'db_connect.php';
ob_start();

if(!isset($_SESSION['system'])){
    $system = $conn->query("SELECT * FROM system_settings")->fetch_array();
    if($system) { // Check if system settings exist
        foreach($system as $k => $v){
            $_SESSION['system'][$k] = $v;
        }
    }
}
ob_end_flush();

include 'header.php';

// Set title and sidebar per role
if(!isset($_SESSION['login_type'])) {
    header('location:login.php');
    exit();
}

$role = $_SESSION['login_type']; // 0 = admin, 1 = evaluator, 2 = employee
switch ($role) {
    case 0:
        $title = "Admin Dashboard";
        $sidebar = 'admin_sidebar.php';
        $dashboard = 'home_admin.php';
        break;
    case 1:
        $title = "Evaluator Dashboard";
        $sidebar = 'evaluator_sidebar.php';
        $dashboard = 'home_evaluator.php';
        break;
    case 2:
        $title = "Employee Dashboard";
        $sidebar = 'employee_sidebar.php';
        $dashboard = 'home_employee.php';
        break;
    default:
        // Invalid role, redirect to login
        session_destroy();
        header('location:login.php');
        exit();
}
?>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
  <?php 
  // Include the correct sidebar based on role
  if(file_exists($sidebar)) {
      include $sidebar;
  } else {
      // Fallback to default sidebar if specific one doesn't exist
      include 'sidebar.php';
  }
  ?>
  <?php include 'topbar.php' ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
     <div class="toast" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-body text-white">
      </div>
    </div>
    <div id="toastsContainerTopRight" class="toasts-top-right fixed"></div>
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><?php echo htmlspecialchars($title) ?></h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
            <hr class="border-primary">
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
         <?php 
            $page = isset($_GET['page']) ? $_GET['page'] : $dashboard;
            // Security check to prevent directory traversal
            $page = basename($page);
            
            // Define routing for pages
            switch($page) {
                // Attendance pages
                case 'new_attendance_record':
                    include 'new_attendance.php';
                    break;
                case 'edit_attendance':
                    include 'edit_attendance.php';
                    break;
                case 'attendance_records':
                    include 'attendance_records.php';
                    break;
                    
                // Pending Evaluation pages
                case 'new_pending_evaluation_record':
                    include 'new_pending_evaluation.php';
                    break;
                case 'edit_pending_evaluation':
                    include 'edit_pending_evaluation.php';
                    break;
                case 'view_pending_evaluation':
                    include 'view_pending_evaluation.php';
                    break;
                case 'pending_evaluations':
                    include 'pending_evaluations.php';
                    break;
                    
                default:
                    // Check if file has .php extension, add if needed
                    if(!file_exists($page) && !file_exists($page.".php")){
                        include '404.html';
                    } else {
                        $pageFile = file_exists($page) ? $page : $page.'.php';
                        include $pageFile;
                    }
                    break;
            }
          ?>
      </div><!--/. container-fluid -->
    </section>
    <!-- /.content -->
    <div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Confirmation</h5>
      </div>
      <div class="modal-body">
        <div id="delete_content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='confirm' onclick="">Continue</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal_right" role='dialog'>
    <div class="modal-dialog modal-full-height  modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="fa fa-arrow-right"></span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="viewer_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
              <button type="button" class="btn-close" data-dismiss="modal"><span class="fa fa-times"></span></button>
              <img src="" alt="">
      </div>
    </div>
  </div>
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline-block">
      <b><?php echo isset($_SESSION['system']['name']) ? htmlspecialchars($_SESSION['system']['name']) : 'Employee Performance System' ?></b>
    </div>
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<!-- Bootstrap -->
<?php include 'footer.php' ?>
</body>
</html>