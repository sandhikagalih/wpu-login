<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <div class="row">
        <div class="col-lg">

            <?php if (validation_errors()) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= validation_errors(); ?>
                </div>
            <?php endif; ?>

            <?php foreach ($this->session->flashdata() as $key => $val) : ?>
                <div class="alert <?= $key; ?>" role="alert">
                    <?= $val; ?>
                </div>
            <?php endforeach; ?>

            <a href="" class="btn btn-primary mb-3 float-right" data-toggle="modal" data-target="#newSubMenuModal">Add New Sub Menu</a>

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Title</th>
                            <th scope="col">Menu</th>
                            <th scope="col">Url</th>
                            <th scope="col">Icon</th>
                            <th scope="col" class="text-center">Active</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        foreach ($subMenuManage as $sm) : ?>
                            <tr>
                                <th scope="row"><?= $i++; ?></th>
                                <td class="text-capitalize"><?= $sm['title']; ?></td>
                                <td class="text-capitalize"><?= $sm['menu']; ?></td>
                                <td class="text-capitalize"><?= $sm['url']; ?></td>
                                <td><?= $sm['icon']; ?></td>
                                <td class="text-center">
                                    <?php if ($sm['is_active'] == 1) : ?>
                                        <i class='fas fa-check-circle text-success'></i>
                                    <?php else : ?>
                                        <i class='fas fa-times-circle text-danger'></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('menu/edit/?type=submenu&id=' . $sm['id']); ?>" class="badge badge-primary">Edit</a>
                                    <a href="<?= base_url('menu/delete/?type=sub&id=' . $sm['id']); ?>" class="badge badge-danger">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<!-- Modal -->
<div class="modal fade" id="newSubMenuModal" tabindex="-1" aria-labelledby="newSubMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newSubMenuModalLabel">Add New Sub Menu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('menu/submenu'); ?>" method="POST">
                    <div class="form-group">
                        <input type="text" class="form-control" id="title" name="title" placeholder="Sub menu title">
                    </div>
                    <div class="form-group">
                        <select name="menu_id" id="menu_id" class="form-control custom-select">
                            <option value="">Select Menu</option>
                            <?php foreach ($menuManage as $mm) : ?>
                                <option value="<?= $mm['id']; ?>"><?= $mm['menu']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="url" name="url" placeholder="Sub menu url">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="icon" name="icon" placeholder="Sub menu icon">
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                            <label class="custom-control-label" for="is_active">Active?</label>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add</button>
            </div>
            </form>
        </div>
    </div>
</div>