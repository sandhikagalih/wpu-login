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
            <form action="<?= base_url('menu/edit/?type=submenu&id=' . $editSub['id']); ?>" method="POST">
                <input type="hidden" class="form-control" id="title" name="id" value="<?= $editSub['id']; ?>">
                <div class="form-group">
                    <label for="title">Submenu Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?= $editSub['title']; ?>">
                </div>
                <div class="form-group">
                    <label for="menu_id">Menu</label>
                    <select name="menu_id" id="menu_id" class="form-control custom-select">
                        <?php foreach ($menuManage as $mm) : ?>
                            <option value="<?= $mm['id']; ?>" <?php if ($editSub['menu_id'] == $mm['id']) echo 'selected'; ?>><?= $mm['menu']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="url">URL</label>
                    <input type="text" class="form-control" id="url" name="url" value="<?= $editSub['url']; ?>">
                </div>
                <div class="form-group">
                    <label for="icon">Icon</label>
                    <input type="text" class="form-control" id="icon" name="icon" value="<?= $editSub['icon']; ?>">
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" <?php if ($editSub['is_active'] == 1) echo 'checked'; ?>>
                        <label class="custom-control-label" for="is_active">Active?</label>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Submit</button>
                </div>
            </form>
        </div>
    </div>

</div>