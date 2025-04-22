<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $this->e($title) ?></title>
  <link rel="icon" href="/assets/image/icon-logo.png" type="image/x-icon">

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
  <?= $this->section("page_specific_css") ?>
  <?php
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  ?>
  <style>
    :root {
      --bg-dark-green-color: #08a045;
      --bg-green-color: #29bf12;
      --red-color: #f21b3f;
      --orange-color: #ff9914;
      --bg-1-color: #f8fdf6;
    }

    body {
      background-color: #e8fccf;
      color: #333;
    }

    .sidebar {
      background-color: var(--bg-dark-green-color);
      color: white;
      height: 100vh;
      width: 200px;
      padding-top: 1rem;
      position: fixed;
      z-index: 1000;
    }

    .sidebar .logo {
      text-align: center;
      margin-bottom: 1rem;
    }

    .sidebar .logo img {
      height: 50px;
    }

    .sidebar .logo h6 {
      font-size: 20px;
      margin-top: 5px;
      color: #fff;
    }

    .nav-item {
      display: flex;
      align-items: center;
      padding: 0.75rem 1.5rem;
      color: white;
      text-decoration: none;
      transition: background 0.3s;
    }

    .nav-item:hover {
      background-color: var(--bg-green-color);
    }

    .nav-item i {
      font-size: 18px;
      margin-right: 10px;
    }

    .main-header {
      position: sticky;
      top: 0;
      z-index: 10;
      background-color: #f8fdf6;
      display: flex;
      justify-content: space-between;
      /* căn đều 2 bên */
      align-items: center;
      /* căn giữa theo chiều dọc */
      padding: 0.75rem 1rem;
      border-radius: 12px;
      box-shadow: 0 1px 6px rgba(0, 0, 0, 0.05);
      margin-bottom: 1.5rem;
    }


    .user-info {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .user-info img {
      width: 40px;
      height: 40px;
      object-fit: cover;
      border-radius: 50%;
    }

    .btn-logout {
      width: 100%;
      text-align: left;
    }

    .avatar {
      border-radius: 50%;
      width: 35px;
      height: 35px;
      object-fit: cover;
      object-position: center;
      clip-path: circle(30px at 50% 50%);
    }

    .main-header a {
      text-decoration: none;
      color: black;
    }

    .sidebar a.nav-item {
      font-size: 15px;
      border-radius: 0 20px 20px 0;
      margin: 2px 0;
      font-weight: 500;
    }

    .sidebar a.nav-item.active,
    .sidebar a.nav-item:hover {
      background-color: var(--bg-green-color);
      color: #fff;
      text-decoration: none;
    }

    .sidebar a.nav-item i {
      width: 20px;
      text-align: center;
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-auto sidebar d-flex flex-column">
        <div class="logo">
          <img src="/assets/image/icon-logo.png" alt="Logo">
          <h6>Mint Fresh <span style="color: var(--orange-color);">Fruit</span></h6>
        </div>

        <div class="flex-grow-1 d-flex flex-column">
          <a href="/adminhome" class="nav-item"><i class="fas fa-home"></i> Tổng quan</a>
          <a href="/orders/admin" class="nav-item"><i class="fas fa-shopping-cart"></i> Đơn hàng</a>
          <!-- Nút Sản phẩm: dùng để mở / đóng -->
          <a class="nav-item d-flex justify-content-between align-items-center" data-bs-toggle="collapse"
            href="#productMenu" role="button" aria-expanded="false" aria-controls="productMenu">
            <div><i class="fas fa-apple-alt me-2"></i> Sản phẩm</div>
            <i class="bi bi-caret-down-fill collapse-icon"></i>
          </a>

          <!-- Submenu: collapse Bootstrap -->
          <div class="collapse ps-4" id="productMenu">
            <a href="/products/admin" class="nav-item">Danh sách sản phẩm</a>
            <a href="/suppliers/admin" class="nav-item">Nhà cung cấp</a>
            <a href="/catalogs/admin" class="nav-item">Danh mục sản phẩm</a>
            <a href="/receipt" class="nav-item">Nhập hàng</a>
          </div>



          <a href="/account/admin" class="nav-item "><i class="fas fa-users"></i> Khách hàng </a>
          <a href="/promotion/admin" class="nav-item"><i class="fas fa-tags"></i> Khuyến mãi</a>
          <a href="/contacts/admin" class="nav-item"><i class="fas fa-headset"></i> Liên hệ</a>
          <a href="/home/report" class="nav-item"><i class="fas fa-chart-line"></i> Báo cáo</a>
        </div>

        <!-- Đăng xuất nằm cuối -->
        <a href="/logout" class="btn btn-danger btn-sm btn-logout mt-auto mb-3"
          onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <i class="bi bi-box-arrow-right"></i> Đăng xuất
        </a>
        <form id="logout-form" action="/logout" method="POST" style="display: none;"></form>
      </div>


      <!-- Main Area -->
      <div class="col" style="margin-left: 220px;">
        <!-- Top Header -->
        <div class="main-header mt-1">
          <p class="my-auto fw-bold ">
            MINT FRESH <span style="color: #ff9914;">FRUIT</span> | Chuyên cung cấp thực phẩm sạch
          </p>


          <a href="<?= '/account/detail/' . $this->e(AUTHGUARD()->user()->id_account) ?>">
            <div class="user-info d-flex align-items-center gap-2">
              <div class="text-end">
                <strong class="fs-6"><?= $this->e(AUTHGUARD()->user()->username); ?></strong><br>
                <small><?= $this->e(AUTHGUARD()->user()->role); ?></small>
              </div>
              <img src="/<?= $this->e(AUTHGUARD()->user()->url); ?>" alt="Avatar" class="rounded-circle"
                style="width: 40px; height: 40px; object-fit: cover;">

            </div>
          </a>

        </div>


        <!-- Main Content -->
        <main class="main">
          <?= $this->section("page") ?>
        </main>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script src="/assets/js/js.js"></script>

</body>

<script>
  const collapseEl = document.getElementById('productMenu');
  const toggleIcon = document.querySelector('[href="#productMenu"] .collapse-icon');

  collapseEl.addEventListener('show.bs.collapse', function () {
    toggleIcon.classList.remove('bi-caret-down-fill');
    toggleIcon.classList.add('bi-caret-up-fill');
  });

  collapseEl.addEventListener('hide.bs.collapse', function () {
    toggleIcon.classList.remove('bi-caret-up-fill');
    toggleIcon.classList.add('bi-caret-down-fill');
  });
</script>



</html>