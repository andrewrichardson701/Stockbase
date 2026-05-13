<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SSOController extends Controller
{
    public function saveSettings(Request $request)
    {
        $tenantId = $request->input('tenant_id');
        $extractedCert = $this->extractMicrosoftCert($tenantId);

        if (!$extractedCert) {
            return back()->withErrors(['tenant_id' => 'Could not extract certificate. Check Tenant ID.']);
        }

        // Fixed table name to 'config'
        DB::table('config')->where(['id' => 1])->update([
            'saml_tenant_id' => $tenantId,
            'saml_enabled' => true,
            'saml_settings' => json_encode(['idp_cert' => $extractedCert]),
        ]);

        return back()->with('success', 'SSO Configured successfully.');
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