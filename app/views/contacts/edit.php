<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>
<div class="container">
    <!-- SECTION HEADING -->
    <h2 class="text-center animate__animated animate__bounce">Contacts</h2>
    <div class="row">
        <div class="col-md-6 offset-md-3 text-center">
            <p class="animate__animated animate__fadeInLeft">Update your contacts here.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            <form action="<?= '/contacts/' . htmlspecialchars($contact['id']) ?>" method="POST" class="col-md-6 offset-md-3">

                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>" maxlen="255" id="name" placeholder="Enter Name" value="<?= htmlspecialchars($contact['name']) ?>" />

                    <?php if (isset($errors['name'])) : ?>
                        <span class="invalid-feedback">
                            <strong><?= htmlspecialchars($errors['name']) ?></strong>
                        </span>
                    <?php endif ?>
                </div>

                <!-- Phone -->
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control<?= isset($errors['phone']) ? ' is-invalid' : '' ?>" maxlen="255" id="phone" placeholder="Enter Phone" value="<?= htmlspecialchars($contact['phone']) ?>" />

                    <?php if (isset($errors['phone'])) : ?>
                        <span class="invalid-feedback">
                            <strong><?= htmlspecialchars($errors['phone']) ?></strong>
                        </span>
                    <?php endif ?>
                </div>

                <!-- Notes -->
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes </label>
                    <textarea name="notes" id="notes" class="form-control<?= isset($errors['notes']) ? ' is-invalid' : '' ?>" placeholder="Enter notes (maximum character limit: 255)"><?= htmlspecialchars($contact['notes']) ?></textarea>

                    <?php if (isset($errors['notes'])) : ?>
                        <span class="invalid-feedback">
                            <strong><?= htmlspecialchars($errors['notes']) ?></strong>
                        </span>
                    <?php endif ?>
                </div>

                <!-- Submit -->
                <button type="submit" name="submit" id="submit" class="btn btn-primary">Update Contact</button>
            </form>

        </div>
    </div>
</div>
<?php $this->stop() ?>