<?php

$errorPprefix = isset($errorPprefix) ? $errorPprefix : '<p class="red">Error: ';
$errorPsuffix = isset($errorPsuffix) ? $errorPsuffix : '</p>';
$successPprefix = isset($successPprefix) ? $successPprefix : '<p class="green">';
$successPsuffix = isset($successPsuffix) ? $successPsuffix : '</p>';

$errorPtext = '';
$sqlerrorPtext = '';
$successPtext = '';

if (isset($_GET['error'])) {
    // admin.inc.php
    if ($_GET['error'] == 'submitIssue') {
        $errorPtext = 'Submission issue. Check your form for any submit values required.';
    } elseif ($_GET['error'] == 'emptyFields') {
        $errorPtext = 'Empty fields present in the form.';
    } elseif ($_GET['error'] == 'noStockSelected') { // index
        $errorPtext = 'No stock item selected.';
    } elseif ($_GET['error'] == 'uploadedFileNameMissing') {
		$errorPtext = 'Uploaded file name missing.';
	} elseif ($_GET['error'] == 'fileNameMissing') {
		$errorPtext = 'File name missing.';
	} elseif ($_GET['error'] == 'submitMissing') {
		$errorPtext = 'Submit not set. Unauthorised.';
	} elseif ($_GET['error'] == 'sqlerror') {
        $errorPtext = 'SQL Error.';
        if (isset($_GET['table'])) {
            $errorPtext .= ' Table = '.$_GET['table'];
        }
        if (isset($_GET['file'])) {
            $errorPtext .= ' File = '.$_GET['file'];
        }
        if (isset($_GET['line'])) {
            $errorPtext .= ' Line = '.$_GET['line'];
        }
        if (isset($_GET['purpose'])) {
            $errorPtext .= ' Purpose = '.$_GET['purpose'];
        }
    } elseif ($_GET['error'] == 'passwordMatchesCurrent') {
        $errorPtext = 'New password matches current.';
    } elseif ($_GET["error"] == "invalidCredentials" || str_contains($_GET['error'], "invalidCredentials")) {
		$errorPtext = '<p class="red">Invalid Username / Password...</p>';
	} elseif ($_GET['error'] == 'invalidPermissions') {
        $errorPtext = 'Invalid permissions to complete.';
    } elseif ($_GET['error'] == 'roleMissing') {
        $errorPtext = 'Current user role missing.';
    } elseif ($_GET['error'] == 'userIdMissing') {
        $errorPtext = 'User ID missing.';
    } elseif ($_GET['error'] == 'passwordMismatch') {
        $errorPtext = 'Passwords do not match.';
    } elseif ($_GET['error'] == 'noSubmit') {
        $errorPtext = 'Form submit condition not met.';
    } elseif ($_GET['error'] == 'incorrectLocationType') {
        $errorPtext = 'Incorrect location type submitted.';
    } elseif ($_GET['error'] == 'missingLocationType') {
        $errorPtext = 'Missing location type.';
    } elseif ($_GET['error'] == 'missingLocationDescription') {
        $errorPtext = 'Location Description missing.';
    } elseif ($_GET['error'] == 'missingLocationName') {
        $errorPtext = 'Location Name missing.';
    } elseif ($_GET['error'] == 'missingLocationId') {
        $errorPtext = 'Location ID missing.';
    } elseif ($_GET['error'] == 'dependenciesPresent') {
        $errorPtext = 'Dependencies in place on this object. No changes made.';
    } elseif ($_GET['error'] == 'linksExist') {
        $errorPtext = 'Links in place on this object. No changes made.';
    } elseif ($_GET['error'] == 'missingFileLinks') {
        $errorPtext = 'File Links unknown.';
    } elseif ($_GET['error'] == 'missingFileName') {
        $errorPtext = 'File Name missing.';
    } elseif ($_GET['error'] == 'unknwonType') {
        $errorPtext = 'Unkown type submitted.';
    } elseif ($_GET['error'] == 'missingType') {
        $errorPtext = 'Missing type.';
    } elseif ($_GET['error'] == 'missingAttributeType') {
        $errorPtext = 'Missing attribute type.';
    } elseif ($_GET['error'] == 'incorrectAttributeType') {
        $errorPtext = 'Incorrect attribute type.';
    } elseif ($_GET['error'] == 'missingAttributeID') {
        $errorPtext = 'Missing attribute ID.';
    } elseif ($_GET['error'] == 'sessionRoleMissing') {
        $errorPtext = 'Role missing from session details. Please logout and back in.';
    } elseif ($_GET['error'] == 'incorrectRole') {
        $errorPtext = 'Incorrect user permissions to perform this action.';
    } elseif ($_GET['error'] == 'userExists') {
		$errorPtext = 'Matching user already exists.';
	} elseif ($_GET['error'] == 'multipleEntries') {
		$errorPtext = 'Multiple matching users already exists.';
	} elseif ($_GET['error'] == 'submitNotSet') {
		$errorPtext = 'Form submit condition not met.';
	} elseif ($_GET['error'] == 'emailFormat') {
		$errorPtext = 'Invalid email format.';
	} elseif ($_GET['error'] == 'missingFields') {
		$errorPtext = 'Missing fields present in the form.';
	} elseif ($_GET['error'] == 'idMissmatch') {
		$errorPtext = 'ID missmatch found.';
	} elseif ($_GET['error'] == 'idMissing') {
		$errorPtext = 'ID missing.';
	} elseif ($_GET['error'] == 'cardNoMatch') {
		$errorPtext = 'Incorrect card number.';
	} elseif ($_GET['error'] == 'cardNumberNotNumeric') {
		$errorPtext = 'Card ID not numeric.';
	} elseif ($_GET['error'] == 'missingCardData') {
		$errorPtext = 'Missing card data.';
	} elseif ($_GET['error'] == 'missingCard') {
		$errorPtext = 'Missing card.';
	} elseif ($_GET["error"] == "resubmit") {
		$errorPtext = 'Error occured, please re-submit.';
	} elseif ($_GET["error"] == "resubmitDate") {
		$errorPtext = 'Error occured with the date, please re-submit.';
	} elseif ($_GET["error"] == "resubmitToken") {
		$errorPtext = 'Error occured with the token, please re-submit.';
	} elseif ($_GET["error"] == "resubmitResults") {
		$errorPtext = 'Error occured with the results, please re-submit.';
	} elseif ($_GET["error"] == "selectorMissing") {
		$errorPtext = 'Error occured: selector missing.';
	} elseif ($_GET["error"] == "validatorMissing") {
		$errorPtext = 'Error occured: validator missing.';
	} else {
        $errorPtext = $_GET['error'];
    }
    
}

if (isset($_GET['sqlerror'])) {
    // admin.inc.php
    if ($_GET['sqlerror'] == 'tooManyConfigRows') {
        $sqlerrorPtext = 'Too many config rows in table. Please correct this.';
    } elseif ($_GET['sqlerror'] == 'noEntries') {
        $sqlerrorPtext = 'No entries found in table. Please correct this.';
        if (isset($_GET['field'])) {
            $sqlerrorPtext .= ' Field = '.$_GET['field'];
        }
        if (isset($_GET['table'])) {
            $sqlerrorPtext .= ' Table = '.$_GET['table'];
        }
    } elseif ($_GET['sqlerror'] == 'matchingThemeFound') {
		$sqlerrorPtext = 'Theme already exists.';
	} elseif ($_GET['sqlerror'] == 'multipleEntries') {
		$sqlerrorPtext = 'Multiple entries found.';
	} elseif ($_GET['sqlerror'] == 'noID1') {
        $sqlerrorPtext = 'No row found with ID 1.';
    } elseif ($_GET['sqlerror'] == 'noUserFound') {
        $sqlerrorPtext = 'No user found in table.';
    } elseif ($_GET['sqlerror'] == 'tooManyUserFound') {
        $sqlerrorPtext = 'Multiple users found in table.';
    } elseif ($_GET['sqlerror'] == 'failedToChangeSkuPrefix') {
        $sqlerrorPtext = 'Failed to update SKU prefixes in stock table.';
    } elseif ($_GET['sqlerror'] == 'noRowsFound') {
        $sqlerrorPtext = 'No rows found.';
    } elseif ($_GET['sqlerror'] == 'emailExists') {
		$sqlerrorPtext = 'Email already in use.';
		if (isset($_GET['email'])) {
			$sqlerrorPtext .= ' Email: '.$_GET['email'];
		}
	} elseif ($_GET['sqlerror'] == 'incorrectRowCount') {
		$sqlerrorPtext = 'Incorrect row count in table.';
		if (isset($_GET['email'])) {
			$sqlerrorPtext .= ' Email: '.$_GET['email'];
		}
	} else {
        $sqlerrorPtext = $_GET['sqlerror'];
    }
    if (isset($_GET['table'])) {
        $sqlerrorPtext .= ' Table = '.$_GET['table'];
    }
    if (isset($_GET['file'])) {
        $sqlerrorPtext .= ' File = '.$_GET['file'];
    }
    if (isset($_GET['line'])) {
        $sqlerrorPtext .= ' Line = '.$_GET['line'];
    }
    if (isset($_GET['purpose'])) {
        $sqlerrorPtext .= ' Purpose = '.$_GET['purpose'];
    }
}

if (isset($_GET['success'])) {
    // admin.inc.php
    if ($_GET['success'] == 'restored') {
        $successPtext = 'Successfully restored!';
    } elseif ($_GET['success'] == 'passwordChanged' || $_GET['success'] == 'PasswordChanged') {
        $successPtext = 'Password Changed!';
    } elseif ($_GET['success'] == "profileUpdated") {
		$successPtext = 'Profile Updated Successfully.';
	} elseif ($_GET['success'] == 'cardUpdated') {
		$successPtext = 'Card Updated!';
		if (isset($_GET['type'])) {
			$successPtext .= ' Card '.$_GET['type'].'.';  
		}
		if (isset($_GET['card'])) {
			$successPtext .= ' Card: '.$_GET['card'].'.';
		}
		if (isset($_GET['card_number'])) {
			$successPtext .= ' Card number: '.$_GET['card_number'].'.';
		}
	} elseif ($_GET['success'] == 'cardDeassigned') {
		$successPtext = 'Card Updated!';
		if (isset($_GET['card'])) {
			$successPtext .= ' Card: '.$_GET['card'].'.';
		}
	} elseif ($_GET['success'] == 'enabled') {
        $successPtext = 'Enabled!';
    } elseif ($_GET['success'] == 'disabled') {
        $successPtext = 'Disabled!';
    } elseif ($_GET['success'] == 'deleted') {
        $successPtext = 'Deleted!';
        if (isset($_GET['type'])) {
            $successPtext .= ' Type = '.ucwords($_GET['type']);
        }
        if (isset($_GET['id'])) {
            $successPtext .= ' ID = '.ucwords($_GET['id']);
        }
    } elseif ($_GET['success'] == 'updated') {
        $successPtext = 'Updated!';
        if (isset($_GET['type'])) {
            $successPtext .= ' Type = '.ucwords($_GET['type']);
        }
        if (isset($_GET['id'])) {
            $successPtext .= ' ID = '.ucwords($_GET['id']);
        }
    } elseif ($_GET['success'] == 'locationAdded') {
        $successPtext = 'Location added!';
        if (isset($_GET['locationType'])) {
            if (isset($_GET['locationID'])) {
                $sqlerrorPtext .= ' '.ucwords($_GET['locationType']).' ID = '.$_GET['locationID'];
            }
            if (isset($_GET['site_id'])) {
                $successPtext .= ' Site ID = '.$_GET['site_id'];
            }
            if (isset($_GET['area_id'])) {
                $successPtext .= ' Area ID = '.$_GET['area_id'];
            }
            if (isset($_GET['shelf_id'])) {
                $successPtext .= ' Shelf ID = '.$_GET['shelf_id'];
            }
        }
    } elseif ($_GET['success'] == 'uploaded') {
		$successPtext = 'Successfully uploaded!';
	} else {
        $successPtext = $_GET['success'];
    }
}

function showResponse() {
    global $errorPtext, $errorPprefix, $errorPsuffix, $sqlerrorPtext, $successPprefix, $successPtext, $successPsuffix;
    if ($errorPtext !== '') {
        echo $errorPprefix.$errorPtext.$errorPsuffix;
    }
    if ($sqlerrorPtext !== '') {
        echo $errorPprefix.$sqlerrorPtext.$errorPsuffix;
    }
    if ($successPtext !== '') {
        echo $successPprefix.$successPtext.$successPsuffix;
    }
}


?>