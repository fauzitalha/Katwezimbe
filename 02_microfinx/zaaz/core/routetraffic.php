<?php

// ... Utility Files
include_once "conf/conf.php";



// ... the class
class SaasRouter{

    // ... 001: RouteTraffic
    public function RouteTraffic($DomainName){
        $db = new DBOperations();

        // ... A: Check for Domain Details
        $DomainNameDetails = $db->FetchDomainNameDetails($DomainName);
        $cnt_data = sizeof($DomainNameDetails);
        if(!($cnt_data>0)){

            // ... ERR:Unknown Domain Name
            $api_response = array(
                "code" => "401",
                "message" => "Domain Name details not found",
                "domain" => $DomainName,
                "condetails" => null
            );
            http_response_code(401);
            return json_encode($api_response);
        } else {

            // ... B: Check if Organization is still active
            $ORGCODE = $DomainNameDetails['ORGCODE'];
            $SCHEME = $DomainNameDetails['SCHEME'];
            $DOMAIN_NAME = $DomainNameDetails['DOMAIN_NAME'];
            $APP_URI = $SCHEME."://".$DOMAIN_NAME;
            $OrgDetails = $db->FetchOrganization($ORGCODE);
            $cnt_org = sizeof($OrgDetails);
            if(!($cnt_data>0)){

                // ... ERR Organization is Disabled
                $api_response = array(
                    "code" => "402",
                    "message" => "Organization details not found",
                    "domain" => $DomainName,
                    "condetails" => null
                );
                http_response_code(402);
                return json_encode($api_response);
            } else {

                // ... C:  Get Organization Tenant
                $TenantDetails = $db->FetchTenant($ORGCODE);
                $cnt_ten = sizeof($TenantDetails);
                if(!($cnt_data>0)){

                    // ... C.ERR Tenant is Disabled
                    $api_response = array(
                        "code" => "403",
                        "message" => "Tenant details not found",
                        "domain" => $DomainName,
                        "condetails" => null
                    );
                    http_response_code(403);
                    return json_encode($api_response);
                } else {

                    $TenantCode = $TenantDetails['CODE'];

                    // ... D: Get the tenant connection details
                    $TenantConnectionDetails = $db->FetchTenantConnectionDetails($TenantCode);
                    $cnt_ten_con = sizeof($TenantConnectionDetails);
                    if(!($cnt_ten_con>0)){

                        // ... ERR Tenant is Disabled
                        $api_response = array(
                            "code" => "404",
                            "message" => "Tenant connection details not found",
                            "domain" => $DomainName,
                            "condetails" => null
                        );
                        http_response_code(404);
                        return json_encode($api_response);
                    } else {

                        $ORGHOST = $TenantConnectionDetails['HOST'];
                        $ORGRSSU = $TenantConnectionDetails['RSSU'];
                        $ORGDWWP = $TenantConnectionDetails['DWWP'];
                        $ORGBANK = $TenantConnectionDetails['BANK'];

                        $dbdetails = array(
                            "HOST" => $ORGHOST,
                            "RSSU" => $ORGRSSU,
                            "DWWP" => $ORGDWWP,
                            "BANK" => $ORGBANK
                        );

                        // ... ERR Tenant is Disabled
                        $api_response = array(
                            "code" => "200",
                            "message" => "Success",
                            "domain" => $DomainName,
                            "condetails" => $dbdetails
                        );
                        http_response_code(200);
                        return json_encode($api_response);
                    }
                }// ... end..iff..else..03
            }// ... end..iff..else..02
        }// ... end..iff..else..01
    }// ... end..func

    // ... 004: NavigateToNextPage
    public function NavigateToNextPage($next_page){
        echo '<meta http-equiv="refresh" content="0; URL='.$next_page.'" />';
    }
}   // ... end class



?>