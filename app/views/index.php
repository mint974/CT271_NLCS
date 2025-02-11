<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>
<div class="container homepage mt-3">
  <div class="carousel-box">
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

  <div class="sale-box">
    <h1><b>TRÁI CÂY KHUYẾN MÃI</b></h1>
    <div class="container row justify-content-around">

      <div class="card col-12 col-sm-6 col-md-4 col-lg-3 mt-1 px-1">
        <img src="/assets/image/cheri.jpg" class="card-img-top" alt="">
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
      <div class="card col-12 col-sm-6 col-md-4 col-lg-3 mt-1 px-1">
        <img src="/assets/image/cheri.jpg" class="card-img-top" alt="">
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
      <div class="card col-12 col-sm-6 col-md-4 col-lg-3 mt-1 px-1">
        <img src="/assets/image/cheri.jpg" class="card-img-top" alt="">
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
      <div class="card col-12 col-sm-6 col-md-4 col-lg-3 mt-1 px-1">
        <img src="/assets/image/cheri.jpg" class="card-img-top" alt="">
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
</div>
<?php $this->stop() ?>