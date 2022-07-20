<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <div class="row">
        <div class="col-lg-6">

            <?php if ($this->session->flashdata('message') == 'changed') : ?>
                <div class="alert alert-success" role="alert">
                    Access Changed!
                </div>
            <?php endif; ?>

            <h5>Role : <?= $role['role']; ?></h5>

            <table class="table table-hover table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Menu</th>
                        <th scope="col">Access</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    foreach ($menus as $m) : ?>
                        <?php if ($m['id'] == 1) continue; ?>
                        <tr>
                            <th scope="row"><?= $i++; ?></th>
                            <td class="text-capitalize"><?= $m['menu']; ?></td>
                            <td>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" <?= check_access($role['id'], $m['id']); ?> data-role="<?= $role['id']; ?>" data-menu="<?= $m['id']; ?>">
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->