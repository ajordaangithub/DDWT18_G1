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
        <div class="col-md-8">
            <!-- Error message -->
            <?php if (isset($error_msg)){echo $error_msg;} ?>
                        <h1><?= $page_title ?></h1>
                        <h5><?= $page_subtitle ?></h5>

        <div class="optin">
            <form action="<?= $form_action ?>" method="POST" enctype="multipart/form-data">
                <input class="btn btn-light" type="file" name="userfile[]" value="test" multiple="">
                <input class="btn btn-dark" type="submit" name="submit" value="Upload">
                <?php if(isset($room_id)){ ?> <input type="hidden" name="room_id" value=" <?php echo $room_id ?>"><?php } ?>
                <?php if(isset($room_owner)){ ?> <input type="hidden" name="room_owner" value=" <?php echo $room_owner ?>"><?php } ?>
            </form>
            <br>
        <div class="row">
            <?php get_images($room_id, 1) ?>
        </div>
        </div>





        <?php if(isset($left_content)){echo $left_content;} ?>
