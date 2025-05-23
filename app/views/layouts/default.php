<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title><?= htmlspecialchars($title) ?></title>
  <link rel="icon" href="/assets/image/icon-logo.png" type="image/x-icon">
  <!-- Styles -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <link href="/assets/css/style.css" rel="stylesheet">

  <?= $this->section("page_specific_css") ?>
</head>

<body>
  <?php
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  ?>
  <header class="fixed-top">
    <div class="container-fluid bg-nav row ">
      <div class="col-lg-4"></div>
      <div class=" mt-2 mb-1 search-group col-lg-4 d-flex justify-content-center align-items-center">
        <form action="/search" method="post" class="d-flex form-search">
          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
          <input class="form-control" type="text" placeholder="Search" name="search">
          <button class="button" type="submit"><i class="fas fa-search"></i></button>
        </form>
      </div>
      <div class="content col-lg-4 d-flex justify-content-center align-items-center">
        <p class="text-light my-auto"><b>MINT FRESH <span class="orange-color">FRUIT</span> | Chuyên cung cấp thực phẩm
            sạch</b></p>
      </div>
    </div>

    <nav class="navbar navbar-expand-sm p-0 m-0">
      <div class="container-fluid row nav">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse col-sm-5" id="collapsibleNavbar">
          <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="/home">TRANG CHỦ</a></li>
            <li class="nav-item"><a class="nav-link" href="/products">SẢN PHẨM</a></li>
            <li class="nav-item"><a class="nav-link" href="/introduction">GIỚI THIỆU</a></li>
            <li class="nav-item"><a class="nav-link" href="/contacts">LIÊN HỆ</a></li>
          </ul>
        </div>
        <a class="navbar-brand col-sm-2 d-flex justify-content-center align-items-center" href="#">
          <img src="/assets/image/full-logo.png" style="height: 50px;" alt="">
        </a>
        <div class="right-div mb-2 col-sm-5 d-flex justify-content-end align-items-center ">
          <?php if (!AUTHGUARD()->isUserLoggedIn()): ?>
            <div class="border mx-2 rounded d-flex justify-content-center align-items-center">
              <i class="fas fa-user"></i>
              <a href="/login" class="mx-2">ĐĂNG NHẬP</a> /
              <a href="/register" class="orange-color mx-2">ĐĂNG KÍ</a>
            </div>
          <?php else: ?>

            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <!-- Avatar and Username -->
                <img class="avatar rounded-circle me-2" src="/<?= htmlspecialchars(AUTHGUARD()->user()->url) ?>" alt="Avatar"
                  style="width: 30px; height: 30px;">
                <span><?= htmlspecialchars(AUTHGUARD()->user()->username) ?></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <!-- User management link -->
                <li><a class="dropdown-item" href="/orders/index">
                    <i class="bi bi-boxes"></i> Quản lý đơn hàng
                  </a></li>
                <li><a class="dropdown-item" href="<?= '/account/detail/' .htmlspecialchars(AUTHGUARD()->user()->id_account) ?>">
                    <i class="bi bi-person"></i> Quản lý tài khoản
                  </a></li>
                <li>
                  <a class="dropdown-item" href="/logout"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                  </a>
                  <form id="logout-form" class="d-none" action="/logout" method="POST"></form>
                </li>
              </ul>
            </li>


          <?php endif ?>
          <a href="/products/shoppingcard" class="border mx-2 rounded d-flex justify-content-center align-items-center">
            <i class="fas fa-shopping-cart position-relative">
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?= htmlspecialchars(AUTHGUARD()->isUserLoggedIn() ? AUTHGUARD()->showQuantity() : 0) ?>
              </span>
            </i>
            <p class="mx-2 my-auto">Giỏ Hàng</p>
          </a>
        </div>

      </div>
    </nav>
    <?php
    if (isset($_SESSION['error'])) {
      echo '<script>alert("' . $_SESSION['error'] . '")</script>';
      unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
      echo '<script>alert("' . $_SESSION['success'] . '")</script>';
      unset($_SESSION['success']);
    }
    ?>


  </header>
  <div class="container-fluid main">
    <?= $this->section("page") ?>
  </div>

  <button id="back-to-top" title="Go to top">Top</button>

  <footer class="container-fluid">

    <img src="/assets/image/footer-car.png" class="footer-car" alt="">
    <div class="container-fluid text-center row mx-auto footer">

      <div class="general col-sm-12 col-lg-4 mt-2">
        <img src="/assets/image/full-logo.png" alt="">
        <p class="fs-4"><span style="color: #ff9914;"><b>MINT FRESH FRUIT</b></span>, thành lập năm 2024, cam kết mang
          đến sản phẩm chất lượng cao và tươi ngon. Mint là địa chỉ tin cậy với trái cây tươi ngon và an toàn. Trải
          nghiệm dịch vụ thân thiện và chuyên nghiệp tại Mint.</p>
      </div>
      <div class="policy col-sm-12 col-lg-4 mt-2">
        <h2><b>THÔNG TIN CHÍNH SÁCH</b></h2>
        <ul class="nav flex-column">
          <li class="nav-item fs-4"><a class="nav-link" href="#">Chính sách bảo mật</a></li>
          <li class="nav-item fs-4"><a class="nav-link" href="#">Hướng dẫn mua hàng</a></li>
          <li class="nav-item fs-4"><a class="nav-link" href="#">Điều khoản sử dụng</a></li>
          <li class="nav-item fs-4"><a class="nav-link" href="#">Chính sách vận chuyển</a></li>
          <li class="nav-item fs-4"><a class="nav-link" href="#">Chính sách đổi trả hàng</a></li>
        </ul>
      </div>
      <div class="contact-info col-sm-12 col-lg-4 mt-2">
        <h2><b>THÔNG TIN KẾT NỐI</b></h2>
        <div class="address d-flex justify-content-center align-items-center">
          <i class="fas fa-map-marker" style="color: red;"></i>
          <p class="fs-4">Huyện Phong Điền, TP Cần Thơ</p>
        </div>
        <div class="phone d-flex justify-content-center align-items-center">
          <i class="fa fa-phone" style="color: black;"></i>
          <p class="fs-4">0775097409</p>
        </div>
        <div class="email d-flex justify-content-center align-items-center">
          <i class="fas fa-envelope" style="color: red;"></i>
          <p class="fs-4">mint1224@gmail.com</p>
        </div>
      </div>
      <div class="store col-sm-12 col-lg-4 mt-2">
        <h2><b>THÔNG TIN CỬA HÀNG</b></h2>
        <ul class="nav flex-column">
          <li class="nav-item fs-4"><a class="nav-link" href="#">TRANG CHỦ</a></li>
          <li class="nav-item fs-4"><a class="nav-link" href="#">SẢN PHẨM</a></li>
          <li class="nav-item fs-4"><a class="nav-link" href="#">GIỚI THIỆU</a></li>
          <li class="nav-item fs-4"><a class="nav-link" href="#">LIÊN HỆ</a></li>
        </ul>
      </div>
      <div class="branch col-sm-12 col-lg-4 mt-2">
        <h2><b>HỆ THỐNG CHI NHÁNH</b></h2>
        <ul class="nav flex-column">
          <li class="nav-item fs-4"><a class="nav-link" href="#">Cần Thơ</a></li>
          <li class="nav-item fs-4"><a class="nav-link" href="#">Hậu Giang</a></li>
        </ul>
      </div>
      <div class="follow col-sm-12 col-lg-4 mt-2">
        <h2><b>THEO DỖI CHÚNG TÔI</b></h2>
        <div class="follow-icon d-flex justify-content-center align-items-center">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-youtube"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
        </div>
      </div>
      <img src="/assets/image/footer-flower.png" class="footer-flower" alt="">

    </div>

  </footer>


  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <script src="/assets/js/js.js"></script>

  <?= $this->section("page_specific_js") ?>

</body>

</html>