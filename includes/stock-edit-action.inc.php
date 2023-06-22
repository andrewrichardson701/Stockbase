<?php
function image_upload($field, $stock_id, $redirect_url) {
    $timedate = date("dmyHis");

    $uploadDirectory = "../assets/img/stock/";
    $errors = [];                                                   // Store errors here
    $fileExtensionsAllowed = ['png', 'gif', 'jpg', 'jpeg', 'ico'];  // These will be the only file extensions allowed 
    $fileName = $_FILES[$field]['name'];                            // Get uploaded file name
    $fileSize = $_FILES[$field]['size'];                            // Get uploaded file size
    $fileTmpName  = $_FILES[$field]['tmp_name'];                    // Get uploaded file temp name
    $fileType = $_FILES[$field]['type'];                            // Get uploaded file type
    $explode = explode('.',$fileName);                              // Get file extension explode
    $fileNameShort = str_replace(" ", "_", implode('.', array_slice(explode('.', $fileName), 0, -1)));                             
    $fileExtension = strtolower(end($explode));                     // Get file extension

    if ($_FILES[$field]['name'] !== '') {
        if (!isset($_FILES[$field]))                          { $errors[] = "notSet-File";          }
        if ($_FILES[$field]['name'] == '')                    { $errors[] = "notSet-File-name";     }
        if ($_FILES[$field]['size'] == '')                    { $errors[] = "notSet-File-size";     }
        if ($_FILES[$field]['tmp_name'] == '')                { $errors[] = "notSet-File-tmp_name"; }
        if ($_FILES[$field]['type'] == '')                    { $errors[] = "notSet-File-type";     }
        if (!in_array($fileExtension,$fileExtensionsAllowed)) { $errors[] = "wrongFileExtension";   } // File extenstion match?
        if ($fileSize > 10000000)                             { $errors[] = "above10MB";            } // Within Filesize limits?
        
        if (empty($errors)) { // IF file is existing and all fields exist:
            $moveName = "stock-$stock_id-img-$timedate.$fileExtension";
            $uploadPath = $uploadDirectory.$moveName;
            $uploadFileName = $moveName;
            $didUpload = move_uploaded_file($fileTmpName, $uploadPath);
            if ($didUpload) {
                include 'dbh.inc.php';
                $sql = "INSERT INTO stock_img (stock_id, image) VALUES (?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    header("Location: ".$redirect_url."&error=imageSQL");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt, "ss", $stock_id, $uploadFileName);
                    mysqli_stmt_execute($stmt);
                }

            } else {
                $errors[] = "uploadFailed";
                print_r($errors);
                header("Location: ".$redirect_url."&error=imageUpload");
                exit();
                return $errors;
            }
        } else {
            print_r($errors);
            header("Location: ".$redirect_url."&error=imageUpload");
            exit();
            return $errors;
        } 
    }
}


if (isset($_POST['submit']) && ($_POST['submit'] == 'Save')) {
    session_start();
    // print_r($_POST);
    if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['sku'])) {
        $stock_id = $_POST['id'];
        $stock_name = $_POST['name'];
        $stock_sku = $_POST['sku'];
        $stock_description = isset($_POST['description'])? $_POST['description'] : '';
        $stock_min_stock = isset($_POST['min_stock'])? $_POST['min_stock'] : 0;

        // echo ("<br>id: $stock_id<br>name: $stock_name<br>sku: $stock_sku<br>description: $stock_description<br>min stock: $stock_min_stock<br>");
        
        
        // check if SKU is used - if it is error back to page
        include 'dbh.inc.php';
        $sql_stock = "SELECT * FROM stock WHERE sku=? AND id!=?";
        $stmt_stock = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt_stock, $sql_stock)) {
            echo("ERROR getting entries");
        } else {
            mysqli_stmt_bind_param($stmt_stock, "ss", $stock_sku, $stock_id);
            mysqli_stmt_execute($stmt_stock);
            $result_stock = mysqli_stmt_get_result($stmt_stock);
            $rowCount_stock = $result_stock->num_rows;
            if ($rowCount_stock > 0) {
                // SKU exists, issue.
                header("Location: ".$_SERVER['REQUEST_URI']."&error=duplicateSKU");
                exit();
            } else {
                //SKU not found, continue.

                //update the content
                $sql_update = "UPDATE stock SET name=?, description=?, sku=?, min_stock=? WHERE id=?";
                $stmt_update = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_update, $sql_update)) {
                    header("Location: ".$_SERVER['REQUEST_URI']."&error=updateStockSQL");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt_update, "sssss", $stock_name, $stock_description, $stock_sku, $stock_min_stock, $stock_id);
                    mysqli_stmt_execute($stmt_update);
                    header("Location: ../stock.php?stock_id=$stock_id&modify=edit&success=changesSaved");
                    exit();
                }

            }
        }
    }
    
    
} elseif (isset($_POST['submit']) && ($_POST['submit'] == 'image-delete')) {
    echo('Delete<br>');
    print_r($_POST);

    $redi_url = 'https://inventory.arpco.xyz/stock.php?stock_id='.$_POST['stock_id'].'&modify=edit&images=edit';

    if (isset($_POST['stock_id'])) {
        if (isset($_POST['img_id'])) {
            $stock_id = $_POST['stock_id'];
            $img_id = $_POST['img_id'];

            include 'dbh.inc.php';
            $sql_delete_stock_img = "DELETE FROM stock_img WHERE stock_id=? AND id=?";
            $stmt_delete_stock_img = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_delete_stock_img, $sql_delete_stock_img)) {
                header("Location: ".$redi_url."&error=stock_imgTableSQLConnection");
                exit();
            } else {
                mysqli_stmt_bind_param($stmt_delete_stock_img, "ss", $stock_id, $img_id);
                mysqli_stmt_execute($stmt_delete_stock_img);
                $rows_delete_stock_img = $conn->affected_rows;
                if ($rows_delete_stock_img == 0) {
                    // No Rows deleted.
                    header("Location: ".$redi_url."&error=noImgRemoved");
                    exit();
                } else {
                    // Rows Deleted.
                    header("Location: ".$redi_url."&success=ImgRemoved&count=".$rows_delete_stock_img);
                    exit();
                }
                
            }
        } else {
            // no img_id set
            header("Location: ".$redi_url."&error=no-img_id");
            exit();  
        }
    } else {
        // no stock_id set
        header("Location: ".$redi_url."&error=no-stock_id");
        exit();
    }
} elseif (isset($_POST['submit']) && ($_POST['submit'] == 'Add Image')) {
    echo('Add<br>');
    print_r($_POST);

    session_start();
    $redi_url = 'https://inventory.arpco.xyz/stock.php?stock_id='.$_POST['stock_id'].'&modify=edit&images=edit';

    if (isset($_POST['img-file-name'])) {
        if (isset($_POST['stock_id'])) {
            
            // Check if the relationship already exists in the DB (if image is already linked)

            include 'dbh.inc.php';
            $sql_imgCheck = "SELECT * FROM stock_img WHERE image=? AND stock_id=?";
            $stmt_imgCheck = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_imgCheck, $sql_imgCheck)) {
                echo("ERROR getting entries");
            } else {
                mysqli_stmt_bind_param($stmt_imgCheck, "ss", $_POST['img-file-name'], $_POST['stock_id']);
                mysqli_stmt_execute($stmt_imgCheck);
                $result_imgCheck = mysqli_stmt_get_result($stmt_imgCheck);
                $rowCount_imgCheck = $result_imgCheck->num_rows;
                if ($rowCount_imgCheck > 0) {
                    // Image already linked, throw error and return.
                    header("Location: ".$redi_url."&error=imageAlreadyLinked");
                    exit();
                } else {
                    // Can be linked - continue.

                    $sql = "INSERT INTO stock_img (image, stock_id) VALUES (?, ?)";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        header("Location: ".$redi_url."&error=stock_imgTableSQLConnection");
                        exit();
                    } else {
                        mysqli_stmt_bind_param($stmt, "ss", $_POST['img-file-name'], $_POST['stock_id']);
                        mysqli_stmt_execute($stmt);
                        $modified_rows = $conn->affected_rows;
                        if ($modified_rows == 0) {
                            // No rows changed - error
                            header("Location: ".$redi_url."&error=stock_imgNoRowsChanged");
                            exit();
                        } else if ($modified_rows == 1) {
                            // correct number of rows change - success
                            header("Location: ".$redi_url."&success=stock_imgAdded");
                            exit();
                        } else {
                            // Too many rows changed - error
                            header("Location: ".$redi_url."&error=stock_imgTooManyRowsChanged_".$modified_rows);
                            exit();
                        }
                    }

                }
            }

        } else {
            // redirect and error for no stock_id
            // echo('<or class="red">Error: no stock_id.</or><br>');
            header("Location: ".$redi_url."&error=no-stock_id");
            exit();
        }   
    } else {
        // redirect and error for no file name
        // echo('<or class="red">Error: no img-file-name.</or><br>');
        header("Location: ".$redi_url."&error=no-img-file-name");
        exit();
    }

} elseif (isset($_POST['submit']) && ($_POST['submit'] == 'Upload')) {
    echo('Upload<br>');
    print_r($_POST);
    print_r($_FILES);

    session_start();

    $redi_url = 'https://inventory.arpco.xyz/stock.php?stock_id='.$_POST['stock_id'].'&modify=edit&images=edit';
    if (isset($_POST['stock_id'])) {
        if (isset($_FILES['image'])) {
            image_upload('image', $_POST['stock_id'], $redi_url);
            header("Location: ".$redi_url."&success=fileUploaded");
            exit();
        } else {
            // no $_FILES['image']
            header("Location: ".$redi_url."&error=no-uploadedImage");
            exit();
        }
    } else {
        // no stock_id
        header("Location: ".$redi_url."&error=no-stock_id");
        exit();
    }
    

} else {
    session_start();
    header("Location: ../".$_SESSION['redirect_url']."&error=noSubmit");
    exit();
}






?>
