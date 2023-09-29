<?php 
// ABOUT PAGE
// SHOWS INFO ABOUT THE SYSTEM AND WHERE TO FIND IT ON GITLAB ETC.

// include 'http-headers.php'; // $_SERVER['HTTP_X_*'] 
?> 

<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?> - About</title>
</head>
<body>
    <div class="content">

        <?php include 'nav.php'; ?>

        <div class="container">
            <h2 class="header-small">About</h2>
        </div>

        <div class="container" style="margin-top:25px">
            <h3 style="font-size:22px">About Information</h3>
            <div style="padding-top: 20px;margin-left:25px">
            </div>

        </div>
    </div>
        
<?php include 'foot.php'; ?>

</body>