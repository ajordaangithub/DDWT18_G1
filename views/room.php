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
            <table class="table">
                <tbody>
                <tr>
                    <th scope="row">Type</th>
                    <td><?= $room_type ?></td>
                </tr>
                <tr>
                    <th scope="row">Price</th>
                    <td><?= $room_price ?></td>
                </tr>
                <tr>
                    <th scope="row">Size</th>
                    <td><?= $room_size ?> m2</td>
                </tr>
                <tr>
                    <th scope="row">Added by user</th>
                    <td><?= $added_by ?></td>
                </tr>
                </tbody>
            </table>
            <div class="row">
                <?php get_images($room_id, 0) ?>
            </div>
            <?php if ($display_buttons) {?>
                <div class="row">
                    <div class="col-sm-2">
                        <a href="/DDWT18/final/edit/?room_id=<?= $room_id ?>&user_id=<?= $_GET['room_id']?>" role="button" class="btn btn-warning">Edit</a>
                    </div>
                    <div class="col-sm-2">
                        <a href="/DDWT18/final/img/?room_id=<?= $room_id ?>&user_id=<?= $_GET['room_id']?>" role="button" class="btn btn-warning">Images</a>
                    </div>
                    <div class="col-sm-2">
                        <form action="/DDWT18/final/remove/" method="POST">
                            <input type="hidden" value="<?= $room_id ?>" name="room_id">
                            <button type="submit" class="btn btn-danger">Remove</button>
                        </form>
                    </div>
                </div>
            <?php }?>
            <?php if ($display_optin) {?>
                <div class="optin">
                    <h5>Initiate an opt-in</h5>
                    <form action="/DDWT18/final/optin/" method="POST">
                        <div class="form-group row">
                            <label for="optinMessage" class="col-sm-2 col-form-label">Message</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="optinMessage" rows="3" name="Message" required></textarea>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <input type="hidden" value="<?= $room_id ?>" name="room_id">
                        </div>
                        <button type="submit" class="btn btn-primary">Opt-in!</button>
                    </form>
                </div>
            <?php }?>
            <?php if($display_optins) {?>
                <?php if(isset($left_content)){?>
                    <h1>Current Opt-ins</h1>
                    <?php echo $left_content;} ?>
            <?php }?>

        </div>

        <!-- Right column -->
        <div class="col-md-4">

            <?php include $right_column ?>

        </div>



</div>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>