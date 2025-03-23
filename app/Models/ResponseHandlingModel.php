<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResponseHandlingModel extends Model
{
    //
    static public function responseHandling($request) 
    {
        $return = null;

        $defaultMessages = [
            'error' => [
                'submitIssue' => 'Submission issue. Check your form for any submit values required.',
                'csrfMissmatch' => 'CSRF Token Missmatch',
                'emptyFields' => 'Empty fields present in the form.',
                'noStockSelected' => 'No stock item selected.',
                'uploadedFileNameMissing' => 'Uploaded file name missing.',
                'fileNameMissing' => 'File name missing.',
                'submitMissing' => 'Submit not set. Unauthorized.',
                'sqlerror' => 'SQL Error.',
                'passwordMatchesCurrent' => 'New password matches current.',
                'invalidCredentials' => 'Invalid Username / Password...',
                'loginBlocked' => 'Login Blocked. Please try again later...',
                'invalidPermissions' => 'Invalid permissions to complete.',
                'roleMissing' => 'Current user role missing.',
                'userIdMissing' => 'User ID missing.',
                'passwordMismatch' => 'Passwords do not match.',
                'noSubmit' => 'Form submit condition not met.',
                'incorrectLocationType' => 'Incorrect location type submitted.',
                'missingLocationType' => 'Missing location type.',
                'missingLocationDescription' => 'Location Description missing.',
                'missingLocationName' => 'Location Name missing.',
                'missingLocationId' => 'Location ID missing.',
                'dependenciesPresent' => 'Dependencies in place on this object. No changes made.',
                'linksExist' => 'Links in place on this object. No changes made.',
                'missingFileLinks' => 'File Links unknown.',
                'missingFileName' => 'File Name missing.',
                'unknownType' => 'Unknown type submitted.',
                'missingType' => 'Missing type.',
                'missingAttributeType' => 'Missing attribute type.',
                'incorrectAttributeType' => 'Incorrect attribute type.',
                'missingAttributeID' => 'Missing attribute ID.',
                'sessionRoleMissing' => 'Role missing from session details. Please logout and back in.',
                'incorrectRole' => 'Incorrect user permissions to perform this action.',
                'userExists' => 'Matching user already exists.',
                'multipleEntries' => 'Multiple matching users already exist.',
                'submitNotSet' => 'Form submit condition not met.',
                'SKUexists' => 'SKU already exists. Please pick another, or leave empty to generate a new one.',
                'multipleItemsFound' => 'Multiple item rows found in the items table. Database correction needed.',
                'emailFormat' => 'Invalid email format.',
                'missingFields' => 'Missing fields present in the form.',
                'idMismatch' => 'ID mismatch found.',
                'idMissing' => 'ID missing.',
                'cardNoMatch' => 'Incorrect card number.',
                'cardNumberNotNumeric' => 'Card ID not numeric.',
                'missingCardData' => 'Missing card data.',
                'missingCard' => 'Missing card.',
                'resubmit' => 'Error occurred, please re-submit.',
                'resubmitDate' => 'Error occurred with the date, please re-submit.',
                'resubmitToken' => 'Error occurred with the token, please re-submit.',
                'resubmitResults' => 'Error occurred with the results, please re-submit.',
                'selectorMissing' => 'Error occurred: selector missing.',
                'validatorMissing' => 'Error occurred: validator missing.',
                'missingID' => 'Error: Missing ID',
                'missingItem' => 'Error: Missing Item',
                'missingName' => 'Error: Missing Name',
                'missingDescription' => 'Error: Missing Description',
                'noChangeNeeded' => 'No Changes Needed.',
                'nameMatchesExisting' => 'Entry matching this name already exists.',
                'shelfIssue' => 'Issue with shelf ID.',
                'noLinksFound' => 'No links found for this item.',
                'noRows' => 'No matching rows found.',
                'NaN' => 'Value is Not a Number.',
                'propertyExists' => 'Property already exists.'
            ],
            'sqlerror' => [
                'tooManyConfigRows' => 'Too many config rows in table. Please correct this.',
                'noEntries' => 'No entries found in table. Please correct this.',
                'matchingThemeFound' => 'Theme already exists.',
                'multipleEntries' => 'Multiple entries found.',
                'noID1' => 'No row found with ID 1.',
                'noUserFound' => 'No user found in table.',
                'tooManyUserFound' => 'Multiple users found in table.',
                'failedToChangeSkuPrefix' => 'Failed to update SKU prefixes in stock table.',
                'noRowsFound' => 'No rows found.',
                'emailExists' => 'Email already in use.',
                'incorrectRowCount' => 'Incorrect row count in table.'
            ],
            'success' => [
                'restored' => 'Successfully restored!',
                'changesSaved' => 'Changes Saved!',
                'passwordChanged' => 'Password Changed!',
                'profileUpdated' => 'Profile Updated Successfully.',
                'cardUpdated' => 'Card Updated!',
                'cardDeassigned' => 'Card Deassigned!',
                'enabled' => 'Enabled!',
                'disabled' => 'Disabled!',
                'deleted' => 'Deleted!',
                'updated' => 'Updated!',
                'added' => 'Added!',
                'locationAdded' => 'Location added!',
                'uploaded' => 'Successfully uploaded!',
                'stockRemoved' => 'Stock removed.',
                'stockAdded' => 'Stock added.',
                'fileUploaded' => 'File Uploaded Successfully.',
                'unlinked' => 'Unlinked Successfully.',
                'linked' => 'Linked Successfully.'
            ]
        ];

        $errorPprefix = '<p class="container red">Error: ';
        $errorPsuffix = '</p>';
        $successPprefix = '<p class="container green">';
        $successPsuffix = '</p>';

        if (isset($request['error'])) {
            $errorText = $defaultMessages['error'][$request['error']] ?? htmlspecialchars($request['error'] ?? '');
        }
        if (isset($request['sqlerror'])) {
            $sqlErrorText = $defaultMessages['sqlerror'][$request['sqlerror']] ?? htmlspecialchars($request['sqlerror'] ?? '');
        }
        if (isset($request['success'])) {
            $successText = $defaultMessages['success'][$request['success']] ?? htmlspecialchars($request['success'] ?? '');
        }

        if (isset($request['ajax']) && $request['ajax'] == 1) {
            return $errorText ?: $sqlErrorText ?: $successText;
        }

        if (isset($errorText)) {
            $return .= $errorPprefix . $errorText . $errorPsuffix;
        }
        if (isset($sqlErrorText)) {
            $return .= $errorPprefix . $sqlErrorText . $errorPsuffix;
        }
        if (isset($successText)) {
            $return .= $successPprefix . $successText . $successPsuffix;
        }

        return $return;
    }
}
