<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>
<div class="container-fluid homepage mt-3">
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
  <div class="container-fluid sale-box d-flex flex-column align-items-center justify-content-center">
    <h1><b>TRÁI CÂY KHUYẾN MÃI</b></h1>

    <div class="row row-cols-2 row-cols-md-4 g-5">
      <div class="col">
        <div class="card h-100">
          <img src="/assets/image/products/OT01A-cheri.jpg" class="card-img-top" alt="...">
          <div class="discount_rate">
            <p class="fs-5 m-1">10%</p>
          </div>
          <div class="font-card">
            <h2 class="mt-3 mx-2"><b>ANH ĐÀO</b></h2>
            <div class="hidden">
              <div class="d-flex align-items-center">
                <p class="mx-2 new-price">48.000 đ</p>
                <p class="mx-2 old-price">60.000 đ</p>
              </div>
              <button type="button" class="btn my-btn btn-outline-success">
                <i class="fa fa-plus orange-color"></i> Thêm vào giỏ hàng
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100">
          <img src="/assets/image/products/OT01A-cheri.jpg" class="card-img-top" alt="...">
          <div class="font-card">
            <h2 class="mt-3 mx-2"><b>ANH ĐÀO</b></h2>
            <div class="hidden">
              <div class="d-flex align-items-center">
                <p class="mx-2 new-price">48.000 đ</p>
                <p class="mx-2 old-price">60.000 đ</p>
              </div>
              <button type="button" class="btn my-btn btn-outline-success">
                <i class="fa fa-plus orange-color"></i> Thêm vào giỏ hàng
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100">
          <img src="/assets/image/products/OT01A-cheri.jpg" class="card-img-top" alt="...">
          <div class="font-card">
            <h2 class="mt-3 mx-2"><b>ANH ĐÀO</b></h2>
            <div class="hidden">
              <div class="d-flex align-items-center">
                <p class="mx-2 new-price">48.000 đ</p>
                <p class="mx-2 old-price">60.000 đ</p>
              </div>
              <button type="button" class="btn my-btn btn-outline-success">
                <i class="fa fa-plus orange-color"></i> Thêm vào giỏ hàng
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100">
          <img src="/assets/image/products/OT01A-cheri.jpg" class="card-img-top" alt="...">
          <div class="font-card">
            <h2 class="mt-3 mx-2"><b>ANH ĐÀO</b></h2>
            <div class="hidden">
              <div class="d-flex align-items-center">
                <p class="mx-2 new-price">48.000 đ</p>
                <p class="mx-2 old-price">60.000 đ</p>
              </div>
              <button type="button" class="btn my-btn btn-outline-success">
                <i class="fa fa-plus orange-color"></i> Thêm vào giỏ hàng
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <button type="button" class="btn my-btn btn-outline-success mt-3 ">
      <i class="fas fa-angle-down"></i> XEM THÊM
    </button>
  </div>


  <div class=" container-fluid VN-box d-flex flex-column align-items-center justify-content-center pb-4">
    <h1><b>TRÁI CÂY VIỆT NAM</b></h1>
    <div class="row row-cols-sm-2 row-cols-md-5 g-4">

      <div class="col product-card p-3">
        <div class="card h-100 position-relative">
          <img src="/assets/image/na.jpg" class="card-img-top" alt="quả na">
          <div class="card-body">
            <h5 class="card-title fs-4">Quả na</h5>
            <p class="card-text fs-5">30.000 đ</p>
            <button type="button" class="btn my-btn btn-outline-success">
              <i class="fa fa-plus orange-color"></i> Thêm vào giỏ hàng
            </button>
          </div>
        </div>
      </div>


      <div class="col product-card p-3">
        <div class="card h-100 position-relative">
          <img src="/assets/image/na.jpg" class="card-img-top" alt="quả na">
          <div class="card-body">
            <h5 class="card-title fs-4">Quả na</h5>
            <p class="card-text fs-5">30.000 đ</p>
            <button type="button" class="btn my-btn btn-outline-success">
              <i class="fa fa-plus orange-color"></i> Thêm vào giỏ hàng
            </button>
          </div>
        </div>
      </div>

      <div class="col product-card p-3">
        <div class="card h-100 position-relative">
          <img src="/assets/image/na.jpg" class="card-img-top" alt="quả na">
          <div class="card-body">
            <h5 class="card-title fs-4">Quả na</h5>
            <p class="card-text fs-5">30.000 đ</p>
            <button type="button" class="btn my-btn btn-outline-success">
              <i class="fa fa-plus orange-color"></i> Thêm vào giỏ hàng
            </button>
          </div>
        </div>
      </div>


      <div class="col product-card p-3">
        <div class="card h-100 position-relative">
          <img src="/assets/image/na.jpg" class="card-img-top" alt="quả na">
          <div class="card-body">
            <h5 class="card-title fs-4">Quả na</h5>
            <p class="card-text fs-5">30.000 đ</p>
            <button type="button" class="btn my-btn btn-outline-success">
              <i class="fa fa-plus orange-color"></i> Thêm vào giỏ hàng
            </button>
          </div>
        </div>
      </div>


      <div class="col product-card p-3">
        <div class="card h-100 position-relative">
          <img src="/assets/image/na.jpg" class="card-img-top" alt="quả na">
          <div class="card-body">
            <h5 class="card-title fs-4">Quả na</h5>
            <p class="card-text fs-5">30.000 đ</p>
            <button type="button" class="btn my-btn btn-outline-success">
              <i class="fa fa-plus orange-color"></i> Thêm vào giỏ hàng
            </button>
          </div>
        </div>
      </div>

      <div class="col product-card p-3">
        <div class="card h-100 position-relative">
          <img src="/assets/image/na.jpg" class="card-img-top" alt="quả na">
          <div class="card-body">
            <h5 class="card-title fs-4">Quả na</h5>
            <p class="card-text fs-5">30.000 đ</p>
            <button type="button" class="btn my-btn btn-outline-success">
              <i class="fa fa-plus orange-color"></i> Thêm vào giỏ hàng
            </button>
          </div>
        </div>
      </div>
    </div>
    <button type="button" class="btn my-btn btn-outline-success mt-3 ">
      <i class="fas fa-angle-down"></i> XEM THÊM
    </button>
  </div>

  <div class=" container-fluid other-box d-flex flex-column align-items-center justify-content-center">
    <h1><b>TRÁI CÂY NHẬP KHẨU</b></h1>
    <div class="row row-cols-sm-2 row-cols-md-5 g-4">
      <div class="col product-card p-3">
        <div class="card h-100 position-relative">
          <img src="/assets/image/na.jpg" class="card-img-top" alt="quả na">
          <div class="card-body">
            <h5 class="card-title fs-4">Quả na</h5>
            <p class="card-text fs-5">30.000 đ</p>
            <button type="button" class="btn my-btn btn-outline-success">
              <i class="fa fa-plus orange-color"></i> Thêm vào giỏ hàng
            </button>
          </div>
        </div>
      </div>


      <div class="col product-card p-3">
        <div class="card h-100 position-relative">
          <img src="/assets/image/na.jpg" class="card-img-top" alt="quả na">
          <div class="card-body">
            <h5 class="card-title fs-4">Quả na</h5>
            <p class="card-text fs-5">30.000 đ</p>
            <button type="button" class="btn my-btn btn-outline-success">
              <i class="fa fa-plus orange-color"></i> Thêm vào giỏ hàng
            </button>
          </div>
        </div>
      </div>

      <div class="col product-card p-3">
        <div class="card h-100 position-relative">
          <img src="/assets/image/na.jpg" class="card-img-top" alt="quả na">
          <div class="card-body">
            <h5 class="card-title fs-4">Quả na</h5>
            <p class="card-text fs-5">30.000 đ</p>
            <button type="button" class="btn my-btn btn-outline-success">
              <i class="fa fa-plus orange-color"></i> Thêm vào giỏ hàng
            </button>
          </div>
        </div>
      </div>


      <div class="col product-card p-3">
        <div class="card h-100 position-relative">
          <img src="/assets/image/na.jpg" class="card-img-top" alt="quả na">
          <div class="card-body">
            <h5 class="card-title fs-4">Quả na</h5>
            <p class="card-text fs-5">30.000 đ</p>
            <button type="button" class="btn my-btn btn-outline-success">
              <i class="fa fa-plus orange-color"></i> Thêm vào giỏ hàng
            </button>
          </div>
        </div>
      </div>


      <div class="col product-card p-3">
        <div class="card h-100 position-relative">
          <img src="/assets/image/na.jpg" class="card-img-top" alt="quả na">
          <div class="card-body">
            <h5 class="card-title fs-4">Quả na</h5>
            <p class="card-text fs-5">30.000 đ</p>
            <button type="button" class="btn my-btn btn-outline-success">
              <i class="fa fa-plus orange-color"></i> Thêm vào giỏ hàng
            </button>
          </div>
        </div>
      </div>

      <div class="col product-card p-3">
        <div class="card h-100 position-relative">
          <img src="/assets/image/na.jpg" class="card-img-top" alt="quả na">
          <div class="card-body">
            <h5 class="card-title fs-4">Quả na</h5>
            <p class="card-text fs-5">30.000 đ</p>
            <button type="button" class="btn my-btn btn-outline-success">
              <i class="fa fa-plus orange-color"></i> Thêm vào giỏ hàng
            </button>
          </div>
        </div>
      </div>
    </div>

    <button type="button" class="btn my-btn btn-outline-success mt-3 ">
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
        <p class="fs-4 mt-3 mb-3">MINT tuyển chọn kỹ lưỡng trái cây từ các nông trại uy tín, đảm bảo sản phẩm luôn tươi
          ngon và an toàn cho sức khỏe người tiêu dùng. Chúng tôi không sử dụng hóa chất bảo quản hay thuốc trừ sâu, và
          tự hào mang đến cho bạn những trái cây tốt nhất.</p>
        <h3 class="mt-3 mb-3"><b>Cung cấp 100% thực phẩm hữu cơ và lành mạnh.</b></h3>
        <img class="mt-3 mb-3" src="/assets/image/index/MinhTan.png" alt="Minh Tan">
      </div>
    </div>

  </div>
</div>
<?php $this->stop() ?>