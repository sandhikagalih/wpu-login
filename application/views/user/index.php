<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <?php if ($this->session->flashdata('message') == 'edited') : ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="alert alert-success" role="alert">Your profile has been edited!</div>
            </div>
        </div>
    <?php endif; ?>

    <div class="card mb-3 border-0">
        <div class="row no-gutters">
            <div class="col-md-4 d-flex justify-content-center">
                <img src="<?= base_url('assets/img/profile/') . $user['image']; ?>" alt="<?= $user['name']; ?>">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h5 class="card-title"><?= $user['name']; ?></h5>
                    <p class="card-text"><?= $user['email']; ?></p>
                    <p class="card-text"><small class="text-muted">Member since <?= date('d F Y', $user['date_created']); ?></small></p>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->