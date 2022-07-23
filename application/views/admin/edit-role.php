<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800 text-center"><?= $title; ?></h1>

    <?php if (validation_errors()) : ?>
        <div class="row justify-content-center mb-1">
            <div class="col-6 alert alert-danger text-center" role="alert">
                <?= validation_errors(); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-6">
            <form action="<?= base_url('admin/editrole/' . $role['id']); ?>" method="POST">
                <div class="form-group d-none">
                    <label for="title">Role ID</label>
                    <input type="text" class="form-control" id="title" name="id" value="<?= $role['id']; ?>">
                </div>
                <div class="form-group">
                    <label for="title">Role Name</label>
                    <input type="text" class="form-control" id="title" name="role" value="<?= ucfirst($role['role']); ?>">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Submit</button>
                </div>
            </form>
        </div>
    </div>

</div>