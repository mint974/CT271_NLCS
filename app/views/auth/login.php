<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>
<?php 
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<div class="container">
  <div class="row">
    <div class="col-md-8 offset-md-2">

      <!-- FLASH MESSAGES -->

      <div class="card mt-3">
        <div class="card-header fw-bold text-uppercase">Login</div>
        <div class="card-body bg-body-tertiary">
          <?php if (isset($errors['account'])): ?>
            <div class="mb-3 row">
              <div class="alert alert-warning">
                <strong><?= htmlspecialchars($errors['account']) ?></strong>
                <a href="/account/reactivate" class="btn btn-outline-primary">Khôi phục tài khoản</a>
              </div>
            </div>
          <?php endif ?>
          <form method="POST" action="/login">

            <?php if (isset($success)): ?>
              <div class="mb-3 row">
                <div class="alert alert-success">
                  <strong>Success!</strong><?= $success ?>
                </div>
              </div>
            <?php endif ?>

            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="mb-3 row">
              <label for="email" class="offset-md-2 col-md-2 col-form-label">E-Mail Address</label>
              <div class="col-md-6">
                <input id="email" type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                  name="email" value="<?= isset($old['email']) ? htmlspecialchars($old['email']) : '' ?>" required autofocus>

                <?php if (isset($errors['email'])): ?>
                  <span class="invalid-feedback">
                    <strong><?= htmlspecialchars($errors['email']) ?></strong>
                  </span>
                <?php endif ?>
              </div>
            </div>

            <div class="mb-3 row">
              <label for="password" class="offset-md-2 col-md-2 col-form-label">Password</label>
              <div class="col-md-6">
                <input id="password" type="password"
                  class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" name="password" required>

                <?php if (isset($errors['password'])): ?>
                  <span class="invalid-feedback">
                    <strong><?= htmlspecialchars($errors['password']) ?></strong>
                  </span>
                <?php endif ?>
              </div>
            </div>

            <div class="mb-3 row">
              <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-primary">
                  Login
                </button>

                <a class="btn btn-link" href="/register">
                  You are a new user?
                </a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->stop() ?>