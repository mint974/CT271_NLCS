<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>
<style>
  :root {
    --bg-dark-green-color: #08a045;
    --bg-green-color: #29bf12;
    --bg-light-green-color: #abff4f;
    --yellow-color: #f3de2c;
    --blue-color: #08bdbd;
    --light-blue-color: #a8d5e2;
    --red-color: #f21b3f;
    --orange-color: #ff9914;
    --white-color: #FFFFFF;
    --bg-1-color: #e8fccf;
    --bg-2-color: #f5fdc6;
  }

  body {
    background: var(--bg-1-color);
    font-family: Arial, sans-serif;
  }

  .register-container {
    min-height: 70vh;
  }

  .register-card {
    background: var(--white-color);
    border-radius: 10px;
    overflow: hidden;
    display: flex;
    max-width: 900px;
    width: 100%;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
  }

  .register-left {
    width: 40%;
    height: 100%;
    border-radius: 10px 0 0 10px;
    /* Bo góc bên trái */
    overflow: hidden;
  }

  .card-img {
    object-fit: cover;
    width: auto;
    height: 550px;
  }

  .card-img-overlay{
    background-color: rgba(0, 0, 0, 0.3); 
  }


  .register-right {
    padding: 40px;
    width: 60%;
  }

  .register-title {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
    color: var(--bg-dark-green-color);
    text-align: center;
  }

  .btn-custom {
    background: var(--bg-dark-green-color);
    color: var(--white-color);
    border: none;
    transition: 0.3s;
  }

  .btn-custom:hover {
    background: var(--bg-green-color);
    color: white;
  }



  .address-row .form-control {
    flex: 1;
  }

  @media (max-width: 992px) {
    .register-card {
      flex-direction: column;
    }

    .register-left,
    .register-right {
      width: 100%;
      padding: 30px;
    }
  }
</style>

<div class="container d-flex align-items-center justify-content-center register-container">
  <div class="register-card">
    <div class="card register-left text-white">
      <img src="/assets/image/register-image.jpg" class="card-img" alt="Register Image">
      <div class="card-img-overlay d-flex flex-column justify-content-center text-center">
        <h3> <strong>Chào mừng đến với Mint Fresh Fruit!</strong> </h3>
        <p class="px-3">

          Hãy đăng ký ngay để khám phá những loại trái cây tươi ngon, sạch và bổ dưỡng nhất.
          Đặt hàng dễ dàng và nhận nhiều ưu đãi hấp dẫn!
        </p>
        <p><a href="/login" class="text-white fw-bold">Đăng nhập ngay</a></p>
      </div>
    </div>

    <div class="register-right">
      <h2 class="register-title">Đăng ký tài khoản</h2>
      <form method="POST" action="/register">
        <div class="mb-3">
          <label for="username" class="form-label">Tên đăng nhập</label>
          <input id="username" type="text" class="form-control form-control-sm <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
            name="username" value="<?= isset($old['username']) ? htmlspecialchars($old['username']) : '' ?>" required autofocus>
          <?php if (isset($errors['username'])): ?>
            <span class="invalid-feedback">
              <strong><?= htmlspecialchars($errors['username']) ?></strong>
            </span>
          <?php endif ?>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">E-Mail</label>
          <input id="email" type="email" class="form-control form-control-sm  <?= isset($errors['email']) ? ' is-invalid' : '' ?>"
            name="email" value="<?= isset($old['email']) ? htmlspecialchars($old['email']) : '' ?>" required>
          <?php if (isset($errors['email'])): ?>
            <span class="invalid-feedback">
              <strong><?= htmlspecialchars($errors['email']) ?></strong>
            </span>
          <?php endif ?>
        </div>

        <div class="mb-3">
          <label for="receiver_phone" class="form-label">Số điện thoại</label>
          <input id="receiver_phone" type="text"
            class="form-control form-control-sm  <?= isset($errors['receiver_phone']) ? ' is-invalid' : '' ?>" name="receiver_phone"
            value="<?= isset($old['receiver_phone']) ? htmlspecialchars($old['receiver_phone']) : '' ?>" required>

          <?php if (isset($errors['receiver_phone'])): ?>
            <span class="invalid-feedback">
              <strong><?= htmlspecialchars($errors['receiver_phone']) ?></strong>
            </span>
          <?php endif ?>
        </div>


        <div class="mb-3">
          <label class="form-label">Địa chỉ</label>
          <div class="row g-2">
            <div class="col-md-3">
              <input type="text" class="form-control form-control-sm " name="house_number" placeholder="Số nhà" required>
            </div>
            <div class="col-md-3">
              <input type="text" class="form-control form-control-sm " name="ward" placeholder="Phường/Xã" required>
            </div>
            <div class="col-md-3">
              <input type="text" class="form-control form-control-sm " name="district" placeholder="Quận/Huyện" required>
            </div>
            <div class="col-md-3">
              <input type="text" class="form-control form-control-sm " name="city" placeholder="Tỉnh/TP" required>
            </div>
          </div>
        </div>

        <div class="row mb-3">

          <div class="col-6">
            <label for="password" class="form-label">Mật khẩu</label>
            <input id="password" type="password"
              class="form-control form-control-sm  <?= isset($errors['password']) ? ' is-invalid' : '' ?>" name="password" required>
            <?php if (isset($errors['password'])): ?>
              <span class="invalid-feedback">
                <strong><?= htmlspecialchars($errors['password']) ?></strong>
              </span>
            <?php endif ?>
          </div>
          <div class="col-6">
            <label for="password-confirm" class="form-label">Xác nhận mật khẩu</label>
            <input id="password-confirm" type="password"
              class="form-control form-control-sm  <?= isset($errors['password_confirm']) ? ' is-invalid' : '' ?>"
              name="password_confirm" required>

            <?php if (isset($errors['password_confirm'])): ?>
              <span class="invalid-feedback">
                <strong><?= htmlspecialchars($errors['password_confirm']) ?></strong>
              </span>
            <?php endif ?>
          </div>
        </div>

        <button type="submit" class="btn btn-custom w-100">Đăng ký</button>
      </form>
    </div>
  </div>
</div>
<?php $this->stop() ?>