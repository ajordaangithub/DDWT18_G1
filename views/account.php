<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/DDWT18/final/css/bootstrap.min.css">

    <!-- Own CSS -->
    <link rel="stylesheet" href="/DDWT18/final/css/main.css">

    <title><?= $page_title ?></title>
</head>
<body>
<!-- Menu -->
<?= $navigation ?>

<!-- Content -->
<div class="container">
    <!-- Breadcrumbs -->
    <div class="pd-15">&nbsp</div>
    <?= $breadcrumbs ?>

    <div class="row">

        <div class="col-md-12">
            <!-- Error message -->
            <?php if (isset($error_msg)){echo $error_msg;} ?>

            <h1><?= $page_title ?></h1>
            <h5><?= $page_subtitle ?></h5>
            <p><?= $page_content ?></p>
        </div>

    </div>


    <div class="pd-15">&nbsp;</div>

    <div class="row">

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Welcome, <?= $user ?>
                </div>
                <div class="card-body">
                    <p>You're logged in.</p>
                    <a href="/DDWT18/final/logout/" class="btn btn-primary">Logout</a>
                    <br>
                    <br>
                    <a href="/DDWT18/final/removeaccount/" class="btn btn-primary" onclick="return confirm('Are you sure you want to remove your account?');">Remove account</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Add room
                </div>
                <div class="card-body">
                    <p>Add new residence to rent out</p>
                    <a href="/DDWT18/final/add/" class="btn btn-primary">Add room</a>
                </div>
            </div>
        </div>

    </div>
    <div class="optin">
        <?php if($display_optins) {?>
            <?php if(isset($left_content)){?>
                <?php if ($role == 2) {?>
                <h1 id="header">Current Opt-ins</h1>
                <?php echo $left_content;} else {?>
                    <h1 id="header">Current submitted rooms</h1>
                    <?php echo $left_content;}?>
                <?php }?>
        <?php }?>
    </div>
</div>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>