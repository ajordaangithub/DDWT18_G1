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

        <!-- Left column -->
        <div class="col-md-12">
            <!-- Error message -->
            <?php if (isset($error_msg)){echo $error_msg;} ?>

            <h1><?= $page_title ?></h1>
            <h5><?= $page_subtitle ?></h5>

            <div class="pd-15">&nbsp;</div>

            <form action="<?= $form_action?>" method="POST">
                <div class="form-group">
                    <label for="inputUsername">Username</label>
                    <input type="text" class="form-control" id="inputUsername" placeholder="j.jansen" name="username" value="<?php if (isset($userinfo)){echo $userinfo['username'];}?>"required>
                </div>
                <div class="form-group">
                    <label for="inputUsername">Name</label>
                    <input type="text" class="form-control" id="inputUsername" placeholder="Jan Jansen" name="fullname" value="<?php if (isset($userinfo)){echo $userinfo['full_name'];}?>"required>
                </div>
                <div class="form-group">
                    <label for="inputUsername">Birthdate</label>
                    <input type="date" class="form-control" id="inputUsername" name="birthdate"  value="<?php if (isset($userinfo)){echo $userinfo['birth_date'];}?>"required>
                </div>
                <div class="form-group row">
                    <label for="inputAbstract" class="col-sm-2 col-form-label">Biography</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="inputAbstract" rows="3" name="biography" required><?php if (isset($userinfo)){echo $userinfo['biography'];}?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputUsername">Study/Profession</label>
                    <input type="text" class="form-control" id="inputUsername" placeholder="Economics" name="profession" value="<?php if (isset($userinfo)){echo $userinfo['profession'];}?>"required>
                </div>
                <div class="form-group">
                    <label for="inputUsername">Language</label>
                    <input type="text" class="form-control" id="inputUsername" placeholder="Dutch" name="language" value="<?php if (isset($userinfo)){echo $userinfo['language'];}?>"required>
                </div>
                <div class="form-group">
                    <label for="inputUsername">Email</label>
                    <input type="email" class="form-control" id="inputUsername" placeholder="janjansen@gmail.com" name="email" value="<?php if (isset($userinfo)){echo $userinfo['email'];}?>"required>
                </div>
                <div class="form-group">
                    <label for="inputUsername">Phone</label>
                    <input type="tel" class="form-control" id="inputUsername" placeholder="0612345678" name="phone" value="<?php if (isset($userinfo)){echo $userinfo['phonenumber'];}?>"required>
                </div>
                <?php if(isset($user_id)){ ?> <input type="hidden" name="user_id" value=" <?php echo $user_id ?>"><?php } ?>
                <button type="submit" class="btn btn-primary"><?= $submit_btn?></button>
            </form>

        </div>

    </div>
</div>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>