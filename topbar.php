<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand" style="background: linear-gradient(to bottom right, #D1DEE8, #B8CAD8); color: #2C3E50;">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <?php if(isset($_SESSION['login_id'])): ?>
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button" style="color: #2C3E50;">
        <i class="fas fa-bars" style="color: #2C3E50;"></i>
      </a>
    </li>
    <?php endif; ?>
    <li class="nav-item">
      <a class="nav-link" href="./" role="button" style="color: #2C3E50;">
        <large><b><?php echo isset($_SESSION['system']['name']) ? $_SESSION['system']['name'] : 'Employee Performance System' ?></b></large>
      </a>
    </li>
  </ul>

  <!-- Right navbar -->
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#" role="button" style="color: #2C3E50;">
        <i class="fas fa-expand-arrows-alt" style="color: #2C3E50;"></i>
      </a>
    </li>

    <?php if(isset($_SESSION['login_id'])): ?>
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" aria-expanded="true" href="javascript:void(0)" style="color: #2C3E50;">
        <span>
          <div class="d-flex badge-pill" style="align-items: center; gap: 5px;">
            <span>
              <img src="assets/uploads/<?php echo isset($_SESSION['login_avatar']) ? $_SESSION['login_avatar'] : 'no-image-available.png' ?>" 
                   alt="User Avatar" class="user-img border">
            </span>
            <span style="color: #2C3E50;">
              <b><?php echo isset($_SESSION['login_firstname']) ? ucwords($_SESSION['login_firstname']) : 'User' ?></b>
            </span>
            <span class="fa fa-angle-down ml-2" style="color: #2C3E50;"></span>
          </div>
        </span>
      </a>
      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="account_settings">
        <a class="dropdown-item" href="javascript:void(0)" id="manage_account" style="color: #2C3E50;">
          <i class="fa fa-cog" style="color: #2C3E50;"></i> Manage Account
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="javascript:void(0)" onclick="handleLogout()" style="color: #2C3E50;">
          <i class="fa fa-power-off" style="color: #2C3E50;"></i> Logout
        </a>
      </div>
    </li>
    <?php endif; ?>
  </ul>
</nav>
<!-- /.navbar -->

<script>
  $('#manage_account').click(function(){
    uni_modal('Manage Account','manage_user.php?id=<?php echo $_SESSION['login_id'] ?>')
  });
</script>

<style>
  .user-img {
    border-radius: 50%;
    height: 25px;
    width: 25px;
    object-fit: cover;
    border: 2px solid #B8CAD8 !important;
  }
  
  /* Ensure dropdown aligns to the right */
  .dropdown-menu-right {
    right: 0;
    left: auto;
  }
  
  /* Adjust positioning for the user menu */
  .navbar-nav .nav-item.dropdown:last-child {
    margin-right: 5px;
  }
  
  /* Custom hover effects for the new color scheme */
  .main-header .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
  }
</style>