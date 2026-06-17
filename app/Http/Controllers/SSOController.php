<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\GeneralModel;
use App\Models\ChangelogModel;

class SSOController extends Controller
{
    public function saveSettings(Request $request)
    {
        $tenantId = $request->input('saml_tenant_id');
        
        $extractedCert = $this->extractMicrosoftCert($tenantId);

        if (!$extractedCert) {
            return back()->withErrors(['tenant_id' => 'Could not automatically retrieve the certificate. Check your Tenant ID.']);
        }

        $user = GeneralModel::getUser();
        $changelog_info = [
                'user' => $user,
                'table' => 'config',
                'record_id' => 1,
                'action' => 'Update record',
            ];
        
        $find_config = DB::table('config')->where('id', 1)->first();

        // 1. Build the real Microsoft URLs using the Tenant ID
        $entityId = "https://sts.windows.net/{$tenantId}/";
        $loginUrl = "https://login.microsoftonline.com/{$tenantId}/saml2";
        $logoutUrl = "https://login.microsoftonline.com/{$tenantId}/saml2"; // Usually identical to login URL in Azure AD
        
        $tenant_find = DB::table('saml2_tenants')->where('uuid', 'm365')->first();
        // 2. Save the REAL data directly to the package's table
        $tenant_update = DB::table('saml2_tenants')->updateOrInsert(
            ['uuid' => 'm365'], // The identifier used in your routes
            [
                'key'             => 'm365',
                'idp_entity_id'   => $entityId,
                'idp_login_url'   => $loginUrl,
                'idp_logout_url'  => $logoutUrl,
                'idp_x509_cert'   => $extractedCert,
                'metadata'        => json_encode([]),
                'name_id_format'  => 'persistent',
                'updated_at'      => now(),
                // Only set created_at if it's a new record
                'created_at'      => DB::raw('COALESCE(created_at, NOW())') 
            ]
        );

        if ($tenant_update) {
            // changelog
            $changelog_info['field'] = 'idp_entity_id';
            $changelog_info['previous_value'] = $tenant_find ? $tenant_find->idp_entity_id : null;
            $changelog_info['new_value'] = $entityId;
            ChangelogModel::addChangelog($changelog_info);
        } else {
            return back()->withErrors(['update' => 'Failed to update SSO settings.']);
        }


        // 3. Save just your UI toggles/Tenant ID to your custom configs table
        $update = DB::table('config')->where('id', 1)->update([
            'saml_tenant_id' => $tenantId,
        ]);

        // Explicitly check for false (in case of a raw query error), 
        // otherwise, if it's 0 or 1, it was successful.
        if ($update !== false) {
            // Only log to changelog if a row was actually changed
            if ($update > 0) {
                $changelog_info['field'] = 'saml_tenant_id';
                $changelog_info['previous_value'] = $find_config ? $find_config->saml_tenant_id : null;
                $changelog_info['new_value'] = $tenantId;
                ChangelogModel::addChangelog($changelog_info);
            }
        } else {
            return back()->withErrors(['update' => 'Failed to update SSO settings.']);
        }

        return redirect()->to(route('admin', ['setting' => 'authentication']) . '#sso-settings')->with('success', 'Updated fields: saml_tenant_id');
    }

    public function extractMicrosoftCert($tenantId)
    {
        $url = "https://login.microsoftonline.com/{$tenantId}/federationmetadata/2007-06/federationmetadata.xml";
        
        try {
            $xmlContent = file_get_contents($url);
            if (!$xmlContent) throw new \Exception("Could not reach Microsoft Metadata URL.");

            $dom = new \DOMDocument();
            $dom->loadXML($xmlContent);
            $xpath = new \DOMXPath($dom);

            $xpath->registerNamespace('md', 'urn:oasis:names:tc:SAML:2.0:metadata');
            $xpath->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');

            $query = "//md:IDPSSODescriptor/md:KeyDescriptor[@use='signing']/ds:KeyInfo/ds:X509Data/ds:X509Certificate";
            $entries = $xpath->query($query);

            if ($entries->length > 0) {
                return trim($entries->item(0)->nodeValue);
            }

            throw new \Exception("X509Certificate not found in metadata.");
        } catch (\Exception $e) {
            return null;
        }
    }
}