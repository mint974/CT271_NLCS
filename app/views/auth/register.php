<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>
<div class="container">
  <div class="row">
    <div class="col-md-8 offset-md-2">

      <div class="card mt-3">
        <div class="card-header fw-bold text-uppercase">Register</div>
        <div class="card-body bg-body-tertiary">

          <form method="POST" action="/register">
            <div class="mb-3 row">
              <label for="username" class="offset-md-2 col-md-3 col-form-label">username</label>
              <div class="col-md-5">
                <input id="username" type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                  name="username" value="<?= isset($old['username']) ? $this->e($old['username']) : '' ?>" required autofocus>

                <?php if (isset($errors['username'])): ?>
                  <span class="invalid-feedback">
                    <strong><?= $this->e($errors['username']) ?></strong>
                  </span>
                <?php endif ?>
              </div>
            </div>

            <div class="mb-3 row">
              <label for="email" class="offset-md-2 col-md-3 col-form-label">E-Mail Address</label>
              <div class="col-md-5">
                <input id="email" type="email" class="form-control <?= isset($errors['email']) ? ' is-invalid' : '' ?>"
                  name="email" value="<?= isset($old['email']) ? $this->e($old['email']) : '' ?>" required>

                <?php if (isset($errors['email'])): ?>
                  <span class="invalid-feedback">
                    <strong><?= $this->e($errors['email']) ?></strong>
                  </span>
                <?php endif ?>
              </div>
            </div>

            <div class="mb-3 row">
              <label for="phone_number" class="offset-md-2 col-md-3 col-form-label">Phone Number</label>
              <div class="col-md-5">
                <input id="phone_number" type="number" class="form-control <?= isset($errors['phone_number']) ? ' is-invalid' : '' ?>"
                  name="phone_number" value="<?= isset($old['phone_number']) ? $this->e($old['phone_number']) : '' ?>" required>

                <?php if (isset($errors['phone_number'])): ?>
                  <span class="invalid-feedback">
                    <strong><?= $this->e($errors['phone_number']) ?></strong>
                  </span>
                <?php endif ?>
              </div>
            </div>

            <div class="mb-3 row">
              <label for="password" class="offset-md-2 col-md-3 col-form-label">Password</label>
              <div class="col-md-5">
                <input id="password" type="password"
                  class="form-control <?= isset($errors['password']) ? ' is-invalid' : '' ?>" name="password" required>

                <?php if (isset($errors['password'])): ?>
                  <span class="invalid-feedback">
                    <strong><?= $this->e($errors['password']) ?></strong>
                  </span>
                <?php endif ?>
              </div>
            </div>

            <div class="mb-3 row">
              <label for="password-confirm" class="offset-md-2 col-md-3 col-form-label">Confirm Password</label>
              <div class="col-md-5">
                <input id="password-confirm" type="password"
                  class="form-control <?= isset($errors['password_confirm']) ? ' is-invalid' : '' ?>"
                  name="password_confirm" required>

                <?php if (isset($errors['password_confirm'])): ?>
                  <span class="invalid-feedback">
                    <strong><?= $this->e($errors['password_confirm']) ?></strong>
                  </span>
                <?php endif ?>
              </div>
            </div>

            <div class="mb-3 row">
              <div class="col-md-5 offset-md-5">
                <button type="submit" class="btn btn-primary">
                  Register
                </button>
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->stop() ?>