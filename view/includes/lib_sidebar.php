<!-- Mobile toggle button -->
<button class="toggle-btn" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
  ☰
</button>

<!-- Desktop Sidebar -->
<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav class="sidebar d-none d-md-flex flex-column h-100">
  <div class="sidebar-title-container">
    <span class="sidebar-title fw-bold">LIBRARIAN</span>
  </div>
  <ul class="nav flex-column mt-2">
    <li class="nav-item">
      <a class="nav-link <?php echo ($currentPage == 'lib_dashboard.php') ? 'active' : ''; ?>" href="lib_dashboard.php">Dashboard</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?php echo ($currentPage == 'lib_managebooks.php') ? 'active' : ''; ?>" href="lib_managebooks.php">Manage Books</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?php echo ($currentPage == 'lib_managecat.php') ? 'active' : ''; ?>" href="lib_managecat.php">Manage Categories</a>
    </li>
  </ul>
  <div class="sidebar-bottom mt-auto">
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link" href="#">Help</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="../controller/AuthController.php?action=logout">Log Out</a>
      </li>
    </ul>
  </div>
</nav>


<!-- Mobile Offcanvas Sidebar -->
<div class="offcanvas offcanvas-start offcanvas-sidebar d-md-none" tabindex="-1" id="mobileSidebar">
  <div class="offcanvas-header d-flex align-items-center justify-content-between w-100">
    <div class="d-flex align-items-center flex-grow-1">
      <button class="toggle-btn me-2" type="button" disabled style="opacity:0; pointer-events:none;">☰</button>
      <span class="sidebar-title fw-bold mb-0">LIBRARIAN</span>
    </div>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body d-flex flex-column">
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link <?php echo ($currentPage == 'lib_dashboard.php') ? 'active' : ''; ?>" href="lib_dashboard.php">Dashboard</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo ($currentPage == 'lib_managebooks.php') ? 'active' : ''; ?>" href="lib_managebooks.php">Manage Books</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo ($currentPage == 'lib_managecat.php') ? 'active' : ''; ?>" href="lib_managecat.php">Manage Categories</a>
      </li>
    </ul>

    <!-- Bottom Links for Mobile -->
    <div class="offcanvas-bottom-links mt-auto">
      <hr class="bg-light">
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link" href="#">Help</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../controller/AuthController.php?action=logout">Log Out</a>
        </li>
      </ul>
    </div>
  </div>
</div>
