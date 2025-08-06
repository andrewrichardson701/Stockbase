<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LdapModel;

class LdapController extends Controller
{
    //

    static public function testLdap(Request $request)
    {
        $results = [];
        // return ($request);
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'ldap_username' => 'string|required',
                'ldap_password' => 'string|required',
                'ldap_domain' => 'string|required',
                'ldap_host' => 'string|required',
                'ldap_host_secondary' => 'string|nullable',
                'ldap_port' => 'integer|required',
                'ldap_basedn' => 'string|required',
                'ldap_usergroup' => 'string|nullable',
                'ldap_userfilter' => 'string|nullable',
            ]);

            $results = LdapModel::ldapTest(
                "1. Connection test on host: ".$request['ldap_host'],
                $request['ldap_username'],
                $request['ldap_password'],
                $request['ldap_domain'],
                $request['ldap_host'],
                $request['ldap_port'],
                $request['ldap_basedn'],
                $request['ldap_usergroup'],
                $request['ldap_userfilter']
            );
        
            if ($request['ldap_host_secondary'] !== null) {
                $results2 = LdapModel::ldapTest(
                    "1. Connection test on host: ".$request['ldap_host_secondary'],
                    $request['ldap_username'],
                    $request['ldap_password'],
                    $request['ldap_domain'],
                    $request['ldap_host_secondary'],
                    $request['ldap_port'],
                    $request['ldap_basedn'],
                    $request['ldap_usergroup'],
                    $request['ldap_userfilter']
                );
                $results = array_merge($results, $results2);
            }

            echo(json_encode($results));
        } else {
            return 'CSRF Missmatch';
        }
    }
}
