<?php 
include 'session.php'; // Session setup and redirect if the session is not active 
include 'http-headers.php'; // $_SERVER['HTTP_X_*'] 
?> 

<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title>Inventory - Profile</title>
</head>
<body>
    <?php // dependency PHP
    

    ?>

    <a href="links.php" class="skip-nav-link-inv">show links</a>

    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->

    <div class="container">
        <h2 class="header-small">Profile</h2>
    </div>
    <?php

    include 'includes/dbh.inc.php';

    $sql_users = "SELECT * FROM users WHERE username=?";
    $stmt_users = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt_users, $sql_users)) {
        header("Location: ../index.php?error=sqlerror_getUsersList");
        exit();
    } else {
        mysqli_stmt_bind_param($stmt_users, "s", $_SESSION['username']);
        mysqli_stmt_execute($stmt_users);
        $result = mysqli_stmt_get_result($stmt_users);
        $rowCount = $result->num_rows;
        if ($rowCount < 1) {
            $userFound = 0;

        } elseif ($rowCount == 1) {
            while ($row = $result->fetch_assoc()){
                $profile_username = $row['username'];
                $profile_first_name = $row['first_name'];
                $profile_last_name = $row['last_name'];
                $profile_email = $row['email'];
                $profile_role = ucwords($row['role']);
            }  
        } else { // if there are somehow too many rows matching the sql
            header("Location: ../index.php?sqlerror=multipleentries");
            exit();
        }
    }
    ?>

    <div class="container" style="margin-top:25px">
        <h3 style="font-size:22px">User Information</h3>
        <div style="padding-top: 20px;margin-left:25px">
            <form action="/action_page.php">
                <table>
                    <tbody>
                        <tr class="nav-row" id="username">
                            <td id="username_header" style="width:200px">
                                <!-- Custodian Colour: #72BE2A -->
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Username:</p>
                            </td>
                            <td id="username_info">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle"><?php echo($profile_username); ?></p>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="firstname">
                            <td id="firstname_header" style="width:200px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">First Name:</p>
                            </td>
                            <td id="firstname_info">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle"><?php echo($profile_first_name); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="lastname">
                            <td id="lastname_header" style="width:200px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle">Last Name:</p>
                            </td>
                            <td id="lastname_info">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle"><?php echo($profile_last_name); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="email">
                            <td id="email_header" style="width:200px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle"" for="admin-banner-color"">Email:</p>
                            </td>
                            <td id="email_info">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle"><?php echo($profile_email); ?></p>
                            </td>
                        </tr>
                        <tr class="nav-row" style="margin-top:20px" id="role">
                            <td id="role_header" style="width:200px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle"" for="admin-banner-color"">Role:</p>
                            </td>
                            <td id="role_info">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle"><?php echo($profile_role); ?></p>
                            </td>
                        </tr>
                        <!-- <tr class="nav-row" style="margin-top:20px" id="banner-color">
                            <td id="banner-color-label" style="width:200px">
                                <p style="min-height:max-content;margin:0" class="nav-v-c align-middle" for="admin-banner-color">Banner Colour:</p>
                            </td>
                            <td id="banner-color-picker">
                                <label class="label-color">
                                    <input class="form-control" name="banner-color" type="text" value=""/>
                                </label>
                            </td>
                        </tr> -->
                        <!-- <tr class="nav-row" style="margin-top:20px">
                            <td colspan=3>
                                <input id="form-submit" type="submit" class="btn btn-secondary" style="margin-left:25px" value="Save" />
                            </td>
                        </tr> -->
                    </tbody>
                </table>
            </form>
        </div>

    </div>

    <!-- Modal Image Div -->
    <div id="modalDiv" class="modal" onclick="modalClose()">
        <span class="close" onclick="modalClose()">&times;</span>
        <img class="modal-content bg-trans" id="modalImg">
        <div id="caption" class="modal-caption"></div>
    </div>
    <!-- End of Modal Image Div -->

    <script> // color-picker box json
        $("input.color").each(function() {
            var that = this;
            $(this).parent().prepend($("<i class='fa fa-paint-brush color-icon'></i>").click(function() {
                that.type = (that.type == "color") ? "text" : "color";
            }));
        }).change(function() {
            $(this).attr("data-value", this.value);
            this.type = "text";
        });
    </script>
    <script> // MODAL SCRIPT
        // Get the modal
        function modalLoad(element) {
            var modal = document.getElementById("modalDiv");

            // Get the image and insert it inside the modal - use its "alt" text as a caption
            var img = document.getElementById(element);
            var modalImg = document.getElementById("modalImg");
            var captionText = document.getElementById("caption");
            modal.style.display = "block";
            modalImg.src = element.src;
            captionText.innerHTML = element.alt;
        }

        // When the user clicks on <span> (x), close the modal or if they click the image.
        modalClose = function() { 
            var modal = document.getElementById("modalDiv");
            modal.style.display = "none";
        }
    </script>
    <script> // site selection <select> page navigation (area one below)
        function siteChange(element) {
            var selectElement = document.getElementById(element);
            var newSiteValue = selectElement.value;

            if (newSiteValue) {
                var updatedUrl = updateQueryParameter('', 'site', newSiteValue);
                updatedUrl = updateQueryParameter(updatedUrl, 'area', '0');
                window.location.href = updatedUrl;
            }
        }
        function areaChange(element) {
            var selectElement = document.getElementById(element);
            var newAreaValue = selectElement.value;

            if (newAreaValue) {
                var updatedUrl = updateQueryParameter('', 'area', newAreaValue);
                window.location.href = updatedUrl;
            }
        }
    </script>
    <script>
        function updateQueryParameter(url, query, newQueryValue) {
            // Get the current URL
            if (url === '') {
                var currentUrl = window.location.href;
            } else {
                var currentUrl = url;
            }
            
            // Get the index of the "?" character in the URL
            var queryStringIndex = currentUrl.indexOf('?');

            // If there is no "?" character in the URL, return the URL with the new $query query parameter value
            if (queryStringIndex === -1) {
                return currentUrl + '?' + query + '=' + newQueryValue;
            }

            // Get the query string portion of the URL
            var queryString = currentUrl.slice(queryStringIndex + 1);

            // Split the query string into an array of key-value pairs
            var queryParams = queryString.split('&');

            // Create a new array to hold the updated query parameters
            var updatedQueryParams = [];

            // Loop through the query parameters and update the query parameter if it exists
            for (var i = 0; i < queryParams.length; i++) {
                var keyValue = queryParams[i].split('=');
                if (keyValue[0] === query) {
                updatedQueryParams.push(query + '=' + newQueryValue);
                } else {
                updatedQueryParams.push(queryParams[i]);
                }
            }

            // If the query parameter does not exist, add it to the array of query parameters
            if (updatedQueryParams.indexOf(query + '=' + newQueryValue) === -1) {
                updatedQueryParams.push(query + '=' + newQueryValue);
            }

            // Join the updated query parameters into a string and append them to the original URL
            var updatedQueryString = updatedQueryParams.join('&');
            return currentUrl.slice(0, queryStringIndex + 1) + updatedQueryString;
        }
    </script>
    <script>
        function navPage(url) {
            window.location.href = url;
        }
    </script>

</body>