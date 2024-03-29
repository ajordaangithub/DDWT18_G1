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
            <p><?= $page_content ?></p>
            <form action="<?= $form_action ?>" method="POST">
                <div class="form-group row">
                    <label for="inputAddress" class="col-sm-2 col-form-label">Address</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputAddress" name="Address"
                               value="<?php if (isset($room_info)){echo $room_info['address'];} ?>" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputCity" class="col-sm-2 col-form-label">City</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputCity" name="City"
                               value="<?php if (isset($room_info)){echo $room_info['city'];} ?>" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputType" class="col-sm-2 col-form-label">Type</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputType" name="Type"
                               value="<?php if (isset($room_info)){echo $room_info['type'];} ?>" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPrice" class="col-sm-2 col-form-label">Price</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="inputPrice" name="Price"
                               value="<?php if (isset($room_info)){echo $room_info['price'];} ?>" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputSize" class="col-sm-2 col-form-label">Size (m<sup>2</sup>)</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="inputSize" name="Size"
                               value="<?php if (isset($room_info)){echo $room_info['size'];} ?>" required>
                    </div>
                </div>

                <?php if(isset($room_id)){ ?> <input type="hidden" name="room_id" value=" <?php echo $room_id ?>"><?php } ?>
                <?php if(isset($room_owner)){ ?> <input type="hidden" name="room_owner" value=" <?php echo $room_owner ?>"><?php } ?>
                
                <div class="form-group row">
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary"><?= $submit_btn ?></button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right column -->
        <div class="col-md-4">

            <?php include $right_column ?>

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