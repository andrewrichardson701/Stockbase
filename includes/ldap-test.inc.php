<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// TEST LDAP CONFIGURATION. THIS IS DONE VIA AN AJAX REQUEST ON THE ADMIN.PHP PAGE


// $ldap_username = "CN=ldapauth,CN=Users,DC=ajrich,DC=co,DC=uk";
// $ldap_password = '';
// $ldap_password_confirm = $ldap_password;
// $ldap_domain = 'ajrich.co.uk';
// $ldap_host = '10.0.2.5';
// $ldap2_host = '10.0.2.111';
// $ldap_port = 389;
// $ldap_basedn = 'DC=ajrich,DC=co,DC=uk';
// $ldap_usergroup = "cn=Users";
// $ldap_userfilter = "(objectClass=User)";



$ldap_username         = $_POST['ldap_username']; 
$ldap_password         = $_POST['ldap_password'];
$ldap_password_confirm = $_POST['ldap_password_confirm'];
$ldap_domain           = $_POST['ldap_domain'];
$ldap_host             = $_POST['ldap_host'];
$ldap_port             = $_POST['ldap_port'];
$ldap_basedn           = $_POST['ldap_basedn'];
$ldap_usergroup        = $_POST['ldap_usergroup'];
$ldap_userfilter       = $_POST['ldap_userfilter'];

$ldap2_username         = $_POST['ldap_username']; 
$ldap2_password         = $_POST['ldap_password'];
$ldap2_password_confirm = $_POST['ldap_password_confirm'];
$ldap2_domain           = $_POST['ldap_domain'];
$ldap2_host             = $_POST['ldap_host_secondary'];
$ldap2_port             = $_POST['ldap_port'];
$ldap2_basedn           = $_POST['ldap_basedn'];
$ldap2_usergroup        = $_POST['ldap_usergroup'];
$ldap2_userfilter       = $_POST['ldap_userfilter'];

$ldap_userlist = [];

function ldapTest($check, $ldap_username, $ldap_password, $ldap_password_confirm, $ldap_domain, $ldap_host, $ldap_port, $ldap_basedn, $ldap_usergroup, $ldap_userfilter) {
    global $ldap_userlist;
    $type_filter = array("ou", "sn", "givenname", "mail");
    $errors=[]; 

    array_push($ldap_userlist, $check);
    array_push($ldap_userlist, "");

    if ($ldap_password === $ldap_password_confirm) {
        $ldap_conn = ldap_connect($ldap_host, $ldap_port);
        if (!$ldap_conn) {
            $error = "Error: Could not connect to LDAP server: $ldap_host.";
            array_push($errors, $error);
        } else {
            ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

            $ldap_dn = $ldap_usergroup.",".$ldap_basedn;
            $ldap_bind = ldap_bind($ldap_conn, $ldap_username, $ldap_password);
            if (!$ldap_bind) {
                $error = "Error: Could not bind to LDAP server: $ldap_host.";
                array_push($errors, $error);
            } else {
                $ldap_search = ldap_search($ldap_conn, $ldap_dn, $ldap_userfilter, $type_filter);
                if (!$ldap_search) {
                    $error = "Error: Could not search LDAP server: $ldap_host.";
                    array_push($errors, $error);
                } else {
                    $ldap_info = ldap_get_entries($ldap_conn, $ldap_search);
                    if (!$ldap_info) {
                        $error = "Error: Could not get entries from LDAP server: $ldap_host.";
                        array_push($errors, $error);
                    } else {
                        for ($i=0; $i < $ldap_info['count']; $i++) {
                            array_push($ldap_userlist, $ldap_info[$i]['dn']);
                        }
                        array_push($ldap_userlist, "");
                        array_push($ldap_userlist, "Count: ".$ldap_info['count']);
                        array_push($ldap_userlist, "======================================================");
                        array_push($ldap_userlist, "");
                        
                    }
                }
            }
        }
    } else {
        $error = "Error: Confirm password does not match: $ldap_host.";
        array_push($errors, $error);
    }
    if (!is_null($errors) && !empty($errors)) { 
        foreach ($errors as $e) {
            array_push($ldap_userlist, $e);
        }
    }
}

$check = "1. Connection test on host: $ldap_host";
ldapTest($check, $ldap_username, $ldap_password, $ldap_password_confirm, $ldap_domain, $ldap_host, $ldap_port, $ldap_basedn, $ldap_usergroup, $ldap_userfilter);

$check = "2. Connection test on host: $ldap2_host";
ldapTest($check, $ldap_username, $ldap_password, $ldap_password_confirm, $ldap_domain, $ldap2_host, $ldap_port, $ldap_basedn, $ldap_usergroup, $ldap_userfilter);

if (!is_null($ldap_userlist) && !empty($ldap_userlist)) {
    echo(json_encode($ldap_userlist));
} else {
    array_push($ldap_userlist, "Error: Generic Error - No Output");
    echo(json_encode($ldap_userlist));
}


// print_r('<pre>');
// print_r($ldap_userlist);
// print_r('</pre>');





?>