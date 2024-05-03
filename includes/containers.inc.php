<?php
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// USED FOR SUBMITTING FORMS AND DOING SQL CHANGES FOR THE CONTAINERS PAGE AND SOME OTHER PAGES WITH SIMILAR PROPERTIES

// USED BY: containers.php

// print_r($_POST);
//         exit();

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
} 

$redirect_url = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : "containers.php";
$queryChar = strpos($redirect_url, "?") !== false ? '&' : '?';

include 'changelog.inc.php'; // for updating the changelog table

if (isset($_POST['container_add_submit'])) { 
    if (isset($_POST['container_name'])) {
        if (isset($_POST['container_description'])) {
            // csrf_token management
            if (isset($_POST['csrf_token'])) {
                if (isset($_POST['csrf_token']) && ($_POST['csrf_token'] !== $_SESSION['csrf_token'])) {
                    header("Location: ../".$redirect_url.$queryChar."error=csrfMissmatch");
                    exit();
                }
            } else {
                header("Location: ../".$redirect_url.$queryChar."error=csrfMissmatch");
                exit();
            }
            if (isset($_POST['type'])) {
                $container_name = $_POST['container_name'];
                $container_description = $_POST['container_description'];
                $container_type = $_POST['type'];
                
                include 'dbh.inc.php';

                if ($container_type == "container") {
                    // Adding a new container, NOT making an item a container.

                    if (isset($_POST['shelf']) && is_numeric($_POST['shelf'])) {
                        $shelf_id = $_POST['shelf'];
                        
                        // check if the container name is already in use
                        $sql = "SELECT * 
                                FROM container
                                WHERE name=? AND deleted=0";
                        $stmt = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                            header("Location: ../".$redirect_url.$queryChar."error=sqlerror&table=container&file=".__FILE__."&line=".__LINE__."&purpose=getContainerByName");
                            exit();
                        } else {
                            mysqli_stmt_bind_param($stmt, "s", $container_name);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $rowCount = $result->num_rows;
                            
                            if ((int)$rowCount == 0) {
                                // not in use
                                // check if the shelf Id exists (just in case)

                                $sql_shelf = "SELECT * 
                                        FROM shelf
                                        WHERE id=?";
                                $stmt_shelf = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_shelf, $sql_shelf)) {
                                    header("Location: ../".$redirect_url.$queryChar."error=sqlerror&table=shelf&file=".__FILE__."&line=".__LINE__."&purpose=checkShelfExists");
                                    exit();
                                } else {
                                    mysqli_stmt_bind_param($stmt_shelf, "s", $shelf_id);
                                    mysqli_stmt_execute($stmt_shelf);
                                    $result_shelf = mysqli_stmt_get_result($stmt_shelf);
                                    $rowCount_shelf = $result_shelf->num_rows;

                                    if ($rowCount_shelf == 1) {
                                        // shelf exists, add container
                                        $sql_container = "INSERT INTO container (name, description, shelf_id) 
                                                            VALUES (?, ?, ?)";
                                        $stmt_container = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt_container, $sql_container)) {
                                            header("Location: ../".$redirect_url.$queryChar."error=optic_containeractionConnectionSQL");
                                            exit();
                                        } else {
                                            mysqli_stmt_bind_param($stmt_container, "sss", $container_name, $container_description, $shelf_id);
                                            mysqli_stmt_execute($stmt_container);
                                            $container_id = mysqli_insert_id($conn);
                                            addChangelog($_SESSION['user_id'], $_SESSION['username'], "Add record", "container", $container_id, "name", '', $container_name);
                                            header("Location: ../".$redirect_url.$queryChar."success=containerAdded&container_id=$container_id");
                                            exit();
                                        }  
                                    } else {
                                        // shelf does not exists, error out
                                        header("Location: ../".$redirect_url.$queryChar."error=shelfIssue");
                                        exit();
                                    }
                                }
                            } else {
                                // name matches exiting, error out
                                header("Location: ../".$redirect_url.$queryChar."error=nameMatchesExisting");
                                exit();
                            }
                        }
                    } else {
                        // shelf missing
                        header("Location: ../".$redirect_url.$queryChar."error=shelfIssue");
                        exit();
                    }
                } elseif ($container_type == "item") {
                     // not needed, handled in stock-modify.inc.php
                } else {
                    header("Location: ../".$redirect_url.$queryChar."error=unknownType");
                    exit();
                }

            } else {
                header("Location: ../".$redirect_url.$queryChar."error=missingType");
                exit();
            }
        } else {
            header("Location: ../".$redirect_url.$queryChar."error=missingDescription");
            exit();
        }
    } else {
        header("Location: ../".$redirect_url.$queryChar."error=missingName");
        exit();
    }

} elseif (isset($_POST['container_delete_submit'])) {
    if (isset($_POST['container_id'])) {

        include 'dbh.inc.php';
        $container_id = (int)$_POST['container_id'];

        // Get info from current container + check it exists
        $sql = "SELECT * 
                FROM container
                WHERE id=? AND deleted=0";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../".$redirect_url.$queryChar."error=sqlerror&table=container&file=".__FILE__."&line=".__LINE__."&purpose=getContainerInfo");
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $container_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $rowCount = $result->num_rows;
            
            if ((int)$rowCount == 0) {
                // doesnt exists, error out
                header("Location: ../".$redirect_url.$queryChar."error=noEntries");
                exit();
            } else {
                // Does exist
                // check if no links
                $sql_links = "SELECT * 
                        FROM item_container
                        WHERE container_id=? AND container_is_item=0;";
                $stmt_links = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_links, $sql_links)) {
                    header("Location: ../".$redirect_url.$queryChar."error=sqlerror&table=item_container&file=".__FILE__."&line=".__LINE__."&purpose=getContainerLinks");
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt_links, "s", $container_id);
                    mysqli_stmt_execute($stmt_links);
                    $result_links = mysqli_stmt_get_result($stmt_links);
                    $rowCount_links = $result_links->num_rows;

                    if ($rowCount_links > 0) {
                        // error, links exist
                        header("Location: ../".$redirect_url.$queryChar."error=dependenciesPresent");
                        exit();
                    } else {
                        // no links, continue 

                        $row = $result->fetch_assoc();

                        $current_container_id = (int)$row['id'];
                        $current_container_name = $row['name'];
                        $current_container_description = $row['description'];

                        if ($container_id === $current_container_id) {
                            // delete container
                            $sql_delete = "UPDATE container SET deleted=1 WHERE id=?;";
                            $stmt_delete = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_delete, $sql_delete)) {
                                header("Location: ../".$redirect_url.$queryChar."error=sqlerror&table=container&file=".__FILE__."&line=".__LINE__."&purpose=deleteContainer");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt_delete, "s", $container_id);
                                mysqli_stmt_execute($stmt_delete);
                                // update changelog
                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Delete record", "container", $container_id, "deleted", 0, 1);
                                header("Location: ../".$redirect_url.$queryChar."success=deleted&container_id=$container_id");
                                exit();
                            }
                        } else {
                            // somehow the IDs dont match...
                            header("Location: ../".$redirect_url.$queryChar."error=idMissmatch");
                            exit();
                        }
                    }
                }
            }
        }

    } else {
        // no container ID
        header("Location: ../".$redirect_url.$queryChar."error=missingID");
        exit();
    }
} elseif (isset($_POST['container_edit_submit'])) {
    if (isset($_POST['container_id'])) {
        if (isset($_POST['container_name'])) {
            include 'dbh.inc.php';
            $container_id = (int)$_POST['container_id'];
            $container_name = $_POST['container_name'];
            $container_description = isset($_POST['container_description']) ? $_POST['container_description'] : '';

            // Get info from current container + check it exists
            $sql = "SELECT * 
                    FROM container
                    WHERE id=?";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("Location: ../".$redirect_url.$queryChar."error=sqlerror&table=container&file=".__FILE__."&line=".__LINE__."&purpose=getContainerInfo");
                exit();
            } else {
                mysqli_stmt_bind_param($stmt, "s", $container_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $rowCount = $result->num_rows;
                
                if ((int)$rowCount == 0) {
                    // doesnt exists, error out
                    header("Location: ../".$redirect_url.$queryChar."error=noEntries");
                    exit();
                } else {
                    // Does exist, get info

                    $row = $result->fetch_assoc();

                    $current_container_id = (int)$row['id'];
                    $current_container_name = $row['name'];
                    $current_container_description = $row['description'];

                    if ($container_id === $current_container_id) {
                        if ($container_name != $current_container_name) {
                            // check if the new name is already in use
                            $sql = "SELECT * 
                                FROM container
                                WHERE name=? AND deleted=0";
                            $stmt = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt, $sql)) {
                                header("Location: ../".$redirect_url.$queryChar."error=sqlerror&table=container&file=".__FILE__."&line=".__LINE__."&purpose=getContainerByName");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt, "s", $container_name);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                $rowCount = $result->num_rows;
                                
                                if ((int)$rowCount == 0) {
                                    // not in use
                                    // update the name
                                    $sql_name = "UPDATE container SET name=? WHERE id=?;";
                                    $stmt_name = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_name, $sql_name)) {
                                        header("Location: ../".$redirect_url.$queryChar."error=sqlerror&table=container&file=".__FILE__."&line=".__LINE__."&purpose=updateContainerName");
                                        exit();
                                    } else {
                                        mysqli_stmt_bind_param($stmt_name, "ss", $container_name, $container_id);
                                        mysqli_stmt_execute($stmt_name);
                                        // update changelog
                                        addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "container", $container_id, "name", $current_container_name, $container_name);
                                    }
                                } else {
                                    // name in use, error out
                                    header("Location: ../".$redirect_url.$queryChar."error=nameMatchesExisting");
                                    exit();
                                }
                            }
                        }
                        if ($container_description != $current_container_description) {
                            // update description
                            $sql_desc = "UPDATE container SET description=? WHERE id=?;";
                            $stmt_desc = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_desc, $sql_desc)) {
                                header("Location: ../".$redirect_url.$queryChar."error=sqlerror&table=container&file=".__FILE__."&line=".__LINE__."&purpose=updateContainerName");
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt_desc, "ss", $container_description, $container_id);
                                mysqli_stmt_execute($stmt_desc);
                                // update changelog
                                addChangelog($_SESSION['user_id'], $_SESSION['username'], "Update record", "container", $container_id, "description", $current_container_description, $container_description);
                            }
                        }
                        header("Location: ../".$redirect_url.$queryChar."success=updated&container_id=$container_id");
                        exit();
                    } else {
                        // somehow the IDs dont match...
                        header("Location: ../".$redirect_url.$queryChar."error=idMissmatch");
                        exit();
                    }
                }
            }

        } else {
            // no container name
            header("Location: ../".$redirect_url.$queryChar."error=missingName");
            exit();
        }
    } else {
        // no container ID
        header("Location: ../".$redirect_url.$queryChar."error=missingID");
        exit();
    }
} else {
    header("Location: ../".$redirect_url.$queryChar."error=submitIssue");
    exit();
}