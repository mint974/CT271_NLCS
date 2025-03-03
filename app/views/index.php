<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>


<?php $this->start("page") ?>
<div class="container-fluid homepage homepage_index mt-3">
  <!-- carousel-box -->
  <div class=" container-fluid carousel-box">
    <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="/assets/image/index/1.png" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="/assets/image/index/2.png" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="/assets/image/index/3.png" class="d-block w-100" alt="...">
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying"
        data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying"
        data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </div>

  <!-- sale-box -->
  <div class="container-fluid product-box sale-box d-flex flex-column align-items-center justify-content-center">
    <h1><b>TRÁI CÂY KHUYẾN MÃI</b></h1>

    <div class="row row-cols-2 row-cols-md-4 g-6" id="discounted-product-list">
      <?php foreach ($discountedProducts as $index1 => $product1): ?>
        <?php
        // Xử lý hình ảnh
        $images1 = explode(',', $product1['images'] ?? '');
        $firstImage1 = !empty($images1[0]) ? $images1[0] : '/assets/image/default.jpg';

        // Tính toán giá giảm
        $discountedPrice = $product1['price'] * (1 - $product1['discount_rate'] / 100);
        ?>
        <div class="col product-item1" style="display: <?php echo $index1 < 4 ? 'block' : 'none'; ?>;">
          <div class="card h-100">
            <img src="<?php echo htmlspecialchars($firstImage1); ?>" class="card-img-top"
              alt="<?php echo htmlspecialchars($product1['name']); ?>">
            <div class="discount_rate">
              <p class="fs-5 m-1"><?php echo htmlspecialchars($product1['discount_rate']); ?>%</p>
            </div>
            <div class="font-card">
              <h2 class="mt-3 mx-2"><b><?php echo htmlspecialchars($product1['name']); ?></b></h2>
              <div class="hidden">
                <div class="d-flex align-items-center">
                  <p class="mx-2 new-price"><?php echo number_format($discountedPrice, 0, ',', '.'); ?> đ</p>
                  <p class="mx-2 old-price"><?php echo number_format($product1['price'], 0, ',', '.'); ?> đ</p>
                </div>
                <button type="button" class="btn my-btn btn-outline-success">
                  <i class="fa fa-plus orange-color"></i> Thêm vào giỏ hàng
                </button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <button type="button" class="btn my-btn btn-outline-success mt-3" id="load-more-discounted">
      <i class="fas fa-angle-down"></i> XEM THÊM
    </button>
  </div>


  <!-- TRÁI CÂY VIỆT NAM -->
  <div class=" container-fluid product-box VN-box d-flex flex-column align-items-center justify-content-center pb-4">
    <h1><b>TRÁI CÂY VIỆT NAM</b></h1>

    <div class="row row-cols-2 row-cols-md-5 g-4 product-list">
      <?php foreach ($products1 as $index => $product2): ?>
        <div class="col product-card p-3 product-item" style="display: <?php echo $index < 10 ? 'block' : 'none'; ?>;">
          <div class="card h-100 position-relative">
            <?php
            // Lấy danh sách ảnh, chọn ảnh đầu tiên nếu có, nếu không dùng ảnh mặc định
            $images2 = explode(',', $product2['images'] ?? '');
            $firstImage2 = !empty($images2[0]) ? $images2[0] : '/assets/image/default.jpg';
            ?>
            <img src="<?php echo htmlspecialchars($firstImage2); ?>" class="card-img-top"
              alt="<?php echo htmlspecialchars($product2['name']); ?>">
            <div class="card-body">
              <h5 class="card-title fs-4" style="height: 40px;"><?php echo htmlspecialchars($product2['name']); ?></h5>
              <p class="card-text fs-5"><?php echo number_format($product2['price'], 0, ',', '.'); ?> đ</p>
              <button type="button" class="btn my-btn btn-outline-success">
                <i class="fa fa-plus orange-color"></i> Thêm vào giỏ hàng
              </button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <button type="button" class="btn my-btn btn-outline-success mt-3 load-more">
      <i class="fas fa-angle-down"></i> XEM THÊM
    </button>
  </div>


  <!-- TRÁI CÂY NƯỚC NGOÀI -->
  <div class="container-fluid product-box other-box d-flex flex-column align-items-center justify-content-center">
    <h1><b>TRÁI CÂY NHẬP KHẨU</b></h1>

    <div class="row row-cols-2 row-cols-md-5 g-4 product-list">
      <?php foreach ($products2 as $index => $product3): ?>
        <div class="col product-card p-3 product-item" style="display: <?php echo $index < 10 ? 'block' : 'none'; ?>;">
          <div class="card h-100 position-relative">
            <?php
            // Lấy danh sách ảnh, chọn ảnh đầu tiên nếu có, nếu không dùng ảnh mặc định
            $images3 = explode(',', $product3['images'] ?? '');
            $firstImage3 = !empty($images3[0]) ? $images3[0] : '/assets/image/default.jpg';
            ?>
            <img src="<?php echo htmlspecialchars($firstImage3); ?>" class="card-img-top"
              alt="<?php echo htmlspecialchars($product3['name']); ?>">
            <div class="card-body">
              <h5 class="card-title fs-4" style="height: 40px;"><?php echo htmlspecialchars($product3['name']); ?></h5>
              <p class="card-text fs-5"><?php echo number_format($product3['price'], 0, ',', '.'); ?> đ</p>
              <button type="button" class="btn my-btn btn-outline-success">
                <i class="fa fa-plus orange-color"></i> Thêm vào giỏ hàng
              </button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <button type="button" class="btn my-btn btn-outline-success mt-3 load-more">
      <i class="fas fa-angle-down"></i> XEM THÊM
    </button>
  </div>


  <div class="quality-commitment pb-4">
    <div class="logo">
      <img src="/assets/image/full-logo.png" class="logo" alt="">
    </div>
    <div class="row mt-3 m-1">
      <div class="image-box col-12 col-lg-6 p-2">
        <div class="row">
          <div class="col-12 col-sm-6 d-flex flex-column align-items-center justify-content-center">
            <img src="/assets/image/index/quality1.jpg" alt="Quality 1">
          </div>
          <div class="col-12 col-sm-6 d-flex flex-column align-items-center">
            <img src="/assets/image/index/quality2.jpg" class="m-3" alt="Quality 2">
            <img src="/assets/image/index/quality3.jpg" class="m-3" alt="Quality 3">
          </div>
        </div>
        <div class="organic">
          <img src="/assets/image/index/quality4.png" alt="Quality 4">
        </div>
      </div>

      <div
        class="quality-commitment-box text-center col-12 col-lg-6 d-flex flex-column justify-content-evenly align-items-center">
        <h2 class="fs-1 mt-3 mb-3"><b>CAM KẾT CHẤT LƯỢNG</b></h2>
        <p class="fs-4 mt-3 mb-3">MINT tuyển chọn kỹ lưỡng trái cây từ các nông trại uy tín, đảm bảo sản phẩm luôn
          tươi
          ngon và an toàn cho sức khỏe người tiêu dùng. Chúng tôi không sử dụng hóa chất bảo quản hay thuốc trừ sâu,
          và
          tự hào mang đến cho bạn những trái cây tốt nhất.</p>
        <h3 class="mt-3 mb-3"><b>Cung cấp 100% thực phẩm hữu cơ và lành mạnh.</b></h3>
        <img class="mt-3 mb-3" src="/assets/image/index/MinhTan.png" alt="Minh Tan">
      </div>
    </div>

  </div>
</div>

<script>
  
//load thêm sản phẩm
document.addEventListener("DOMContentLoaded", function () {
    // Xử lý cho các sản phẩm thường
    document.querySelectorAll(".product-box").forEach(section => {
      let products = section.querySelectorAll(".product-item");
      let loadMoreBtn = section.querySelector(".load-more");
      let itemsPerPage = 10;
      let currentIndex = 10;

      if (loadMoreBtn) {
        loadMoreBtn.addEventListener("click", function () {
          let totalProducts = products.length;

          for (let i = currentIndex; i < currentIndex + itemsPerPage && i < totalProducts; i++) {
            products[i].style.display = "block";
          }

          currentIndex += itemsPerPage;

          if (currentIndex >= totalProducts) {
            loadMoreBtn.style.display = "none";
          }
        });
      }
    });

    // Xử lý cho sản phẩm khuyến mãi
    let discountedItems = document.querySelectorAll("#discounted-product-list .product-item1");
    let loadMoreBtn1 = document.getElementById("load-more-discounted");
    let itemsPerPage1 = 4;
    let currentIndex1 = 4;

    if (loadMoreBtn1) {
      loadMoreBtn1.addEventListener("click", function () {
        let totalItems = discountedItems.length;
        let nextIndex = currentIndex1 + itemsPerPage1;

        for (let i = currentIndex1; i < nextIndex && i < totalItems; i++) {
          discountedItems[i].style.display = "block";
        }

        currentIndex1 = nextIndex;

        if (currentIndex1 >= totalItems) {
          loadMoreBtn1.style.display = "none";
        }
      });
    }
  });
</script>
<?php $this->stop() ?>