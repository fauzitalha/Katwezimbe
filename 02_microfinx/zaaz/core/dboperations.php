<?php



class DBOperations{

	# ... ... ... F01: Execute Entity Insert ... ... ... .. ... ... ... ... ... ... ... ... ... ... ..
	public function ExecuteEntityInsert($QUERY){
		$exec_response = array();
		$q = mysql_query($QUERY) or die("ERROR 1: ".mysql_error());
		if ($q) {
			$exec_response["RESP"] = "EXECUTED";
			$exec_response["RECORD_ID"] = mysql_insert_id();
		}
		return $exec_response;
	}

	# ... ... ... F02: Execute Entity Update ... ... ... ... .. ... ... ... ... ... ... ... ... ... ..
	public function ExecuteEntityUpdate($QUERY){
		$update_response = "";
		$q = mysql_query($QUERY) or die("ERROR 1: ".mysql_error());
		if ($q) {
			$update_response = "EXECUTED";
		}
		return $update_response;
	}

	# ... ... ... F03: Return one Entry from DB ... ... ... .. ... ... ... ... ... ... ... ... ... ...
	public function ReturnOneEntryFromDB($DB_QUERY){
		$RTN_VALUE = "";

		$q = mysql_query($DB_QUERY) or die("ERROR 1: ".mysql_error());
		while ($row = mysql_fetch_array($q)) {
			$RTN_VALUE = trim($row['RTN_VALUE']);
		}

		return $RTN_VALUE;
    }
    
	# ... ... ... F04: Fetch Domain Name Details ... ... ... .. ... ... ... ... ... ... ... ... ... ...
    public function FetchDomainNameDetails($DomainName){
        $DomainNameDetails = array();
        $q = mysql_query("SELECT * FROM org_url WHERE DOMAIN_NAME='$DomainName' AND DOMAIN_STATUS='ACTIVE'") or die("ERR_UPR_LOG: ".mysql_error());
        while ($row = mysql_fetch_array($q)) {  
            $DomainNameDetails['RECORDID'] = trim($row['RECORDID']);
            $DomainNameDetails['ORGCODE'] = trim($row['ORGCODE']);
            $DomainNameDetails['DOMAIN_NAME'] = trim($row['DOMAIN_NAME']);
            $DomainNameDetails['SCHEME'] = trim($row['SCHEME']);
            $DomainNameDetails['VERSION'] = trim($row['VERSION']);
            $DomainNameDetails['CREATEDBY'] = trim($row['CREATEDBY']);
            $DomainNameDetails['CREATEDON'] = trim($row['CREATEDON']);
            $DomainNameDetails['APPROVEDBY'] = trim($row['APPROVEDBY']);
            $DomainNameDetails['APPROVEDON'] = trim($row['APPROVEDON']);
            $DomainNameDetails['DOMAIN_STATUS'] = trim($row['DOMAIN_STATUS']);
        }
        return $DomainNameDetails;
    }

    # ... ... ... F05: Fetch Organization ... ... ... .. ... ... ... ... ... ... ... ... ... ...
    public function FetchOrganization($OrgCode){
        $OrgDetails = array();
        $q = mysql_query("SELECT * FROM org WHERE CODE='$OrgCode' AND ORGSTATUS='ACTIVE'") or die("ERR_UPR_LOG_1: ".mysql_error());
        while ($row = mysql_fetch_array($q)) {  
            $OrgDetails['RECORDID'] = trim($row['RECORDID']);
            $OrgDetails['CODE'] = trim($row['CODE']);
            $OrgDetails['NAME'] = trim($row['NAME']);
            $OrgDetails['PLATFORMTYPECODE'] = trim($row['PLATFORMTYPECODE']);
            $OrgDetails['CREATEDBY'] = trim($row['CREATEDBY']);
            $OrgDetails['CREATEDON'] = trim($row['CREATEDON']);
            $OrgDetails['APPROVEDBY'] = trim($row['APPROVEDBY']);
            $OrgDetails['APPROVEDON'] = trim($row['APPROVEDON']);
            $OrgDetails['ORGSTATUS'] = trim($row['ORGSTATUS']);
        }
        return $OrgDetails;
    }

    # ... ... ... F06: Fetch Tenant ... ... ... .. ... ... ... ... ... ... ... ... ... ...
    public function FetchTenant($OrgCode){
        $TenantDetails = array();
        $q = mysql_query("SELECT * FROM tenant WHERE ORGCODE='$OrgCode' AND STATUS='ACTIVE'") or die("ERR_UPR_LOG: ".mysql_error());
        while ($row = mysql_fetch_array($q)) {  
            $TenantDetails['RECORDID'] = trim($row['RECORDID']);
            $TenantDetails['ORGCODE'] = trim($row['ORGCODE']);
            $TenantDetails['CODE'] = trim($row['CODE']);
            $TenantDetails['NAME'] = trim($row['NAME']);
            $TenantDetails['CREATEDBY'] = trim($row['CREATEDBY']);
            $TenantDetails['CREATEDON'] = trim($row['CREATEDON']);
            $TenantDetails['APPROVEDBY'] = trim($row['APPROVEDBY']);
            $TenantDetails['APPROVEDON'] = trim($row['APPROVEDON']);
            $TenantDetails['STATUS'] = trim($row['STATUS']);
        }
        return $TenantDetails;
    }

    # ... ... ... F07: FetchTenantConnectionDetails ... ... ... .. ... ... ... ... ... ... ... ... ... ...
    public function FetchTenantConnectionDetails($TenantCode){
        $TenantConnectionDetails = array();
        $q = mysql_query("SELECT * FROM tenant_connection WHERE TENANTCODE='$TenantCode' AND STATUS='ACTIVE'") or die("ERR_UPR_LOG: ".mysql_error());
        while ($row = mysql_fetch_array($q)) {  
            $TenantConnectionDetails['RECORDID'] = trim($row['RECORDID']);
            $TenantConnectionDetails['TENANTCODE'] = trim($row['TENANTCODE']);
            $TenantConnectionDetails['HOST'] = trim($row['HOST']);
            $TenantConnectionDetails['RSSU'] = trim($row['RSSU']);
            $TenantConnectionDetails['DWWP'] = trim($row['DWWP']);
            $TenantConnectionDetails['BANK'] = trim($row['BANK']);
            $TenantConnectionDetails['CREATEDBY'] = trim($row['CREATEDBY']);
            $TenantConnectionDetails['CREATEDON'] = trim($row['CREATEDON']);
            $TenantConnectionDetails['APPROVEDBY'] = trim($row['APPROVEDBY']);
            $TenantConnectionDetails['APPROVEDON'] = trim($row['APPROVEDON']);
            $TenantConnectionDetails['LASTCHANGEDBY'] = trim($row['LASTCHANGEDBY']);
            $TenantConnectionDetails['LASTCHANGEDON'] = trim($row['LASTCHANGEDON']);
            $TenantConnectionDetails['STATUS'] = trim($row['STATUS']);
        }
        return $TenantConnectionDetails;
    }



}

?>