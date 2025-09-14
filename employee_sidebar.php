<style>
  .main-sidebar {
    background: linear-gradient(135deg, #D1DEE8, #B8CAD8) !important;
    color: #2C3E50 !important;
  }

  .main-sidebar .brand-link,
  .main-sidebar .nav-link,
  .main-sidebar .nav-icon,
  .main-sidebar p,
  .main-sidebar h3 {
    color: #2C3E50 !important;
  }

  .main-sidebar .nav-link.active {
    background-color: #B8CAD8 !important;
    color: #ffffff !important;
  }

  .main-sidebar .nav-treeview .nav-link {
    background-color: rgba(255, 255, 255, 0.2);
    color: #2C3E50 !important;
  }

  .main-sidebar .nav-link:hover {
    background-color: #B8CAD8 !important;
    color: #ffffff !important;
  }
</style>

<aside class="main-sidebar elevation-4">
  <div class="dropdown">
    <a href="./" class="brand-link">
      <h3 class="text-center p-0 m-0"><b>EMPLOYEE</b></h3>
    </a>
  </div>

  <div class="sidebar pb-4 mb-4">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">

        <li class="nav-item dropdown">
          <a href="./" class="nav-link nav-home">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>

<script>
  $(document).ready(function(){
    var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
    var s = '<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>';
    if (s != '') page = page + '_' + s;
    if ($('.nav-link.nav-' + page).length > 0) {
      $('.nav-link.nav-' + page).addClass('active');
      if ($('.nav-link.nav-' + page).hasClass('tree-item')) {
        $('.nav-link.nav-' + page).closest('.nav-treeview').siblings('a').addClass('active');
        $('.nav-link.nav-' + page).closest('.nav-treeview').parent().addClass('menu-open');
      }
      if ($('.nav-link.nav-' + page).hasClass('nav-is-tree')) {
        $('.nav-link.nav-' + page).parent().addClass('menu-open');
      }
    }
  });
</script>