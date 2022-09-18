<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// ... TIMEZONE
date_default_timezone_set("Africa/Kampala");

// ... APPLICATION NAME
$_SESSION['APP_NAME'] = GetSystemParameter("APP_NAME");
$_SESSION['APP_VERSION'] = GetSystemParameter("APP_VERSION");
$_SESSION['APP_SMALL_LOGO'] = GetSystemParameter("APP_SMALL_LOGO");
$_SESSION['APP_BIG_LOGO'] = GetSystemParameter("APP_BIG_LOGO");

// ... MIFOS CORE SETTINGS
$_SESSION['CORE_HOST_URL'] = GetSystemParameter("CORE_HOST_URL");
$_SESSION['CORE_TENANT_ID'] = GetSystemParameter("CORE_TENANT_ID");
$_SESSION['CORE_API_PROVIDER'] = GetSystemParameter("CORE_API_PROVIDER");
$_SESSION['CORE_API_USERNAME'] = GetSystemParameter("CORE_API_USERNAME");
$_SESSION['CORE_API_PASSWORD'] = GetSystemParameter("CORE_API_PASSWORD");

// ... MORE DETAILS
$_SESSION['APP_OWNER'] = GetSystemParameter("APP_OWNER");
$_SESSION['ORG_CODE'] = GetSystemParameter("ORGCODE");
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
												///////////////////////////////////
												///////////////////////////////////
												///////////////////////////////////
												///////////////////////////////////
												///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// ... APPLICATION NAME
$APP_NAME = $_SESSION['APP_NAME'];
$APP_VERSION = $_SESSION['APP_VERSION'];
$APP_SMALL_LOGO = $_SESSION['APP_SMALL_LOGO'];
$APP_BIG_LOGO = $_SESSION['APP_BIG_LOGO'];

$CORE_HOST_URL = $_SESSION['CORE_HOST_URL'];
$CORE_TENANT_ID = $_SESSION['CORE_TENANT_ID'];
$CORE_API_PROVIDER = $_SESSION['CORE_API_PROVIDER'];
$CORE_API_USERNAME = $_SESSION['CORE_API_USERNAME'];
$CORE_API_PASSWORD = $_SESSION['CORE_API_PASSWORD'];

$MIFOS_CONN_DETAILS = array();
$MIFOS_CONN_DETAILS[0] = $CORE_HOST_URL;
$MIFOS_CONN_DETAILS[1] = $CORE_API_PROVIDER;
$MIFOS_CONN_DETAILS[2] = $CORE_TENANT_ID;
$MIFOS_CONN_DETAILS[3] = $CORE_API_USERNAME;
$MIFOS_CONN_DETAILS[4] = $CORE_API_PASSWORD;

$BASEAESKEYFILEPATH = "/u01/apps/saas/orgkeys";
$_SESSION['BASEAESKEYFILEPATH'] = $BASEAESKEYFILEPATH;

// ... MIFOS CONNECTION DETAILS
$TRAN_INTERFACE = "http://mifosbridge.slankinit.com/api/v1/tran-data.php";
$DOCC_INTERFACE = "http://mifosbridge.slankinit.com/api/v1/multipart-data.php";
$IMMG_INTERFACE = "http://mifosbridge.slankinit.com/api/v1/image-data.php";
$_SESSION['TRAN_INTERFACE'] = $TRAN_INTERFACE;
$_SESSION['DOCC_INTERFACE'] = $DOCC_INTERFACE;
$_SESSION['IMMG_INTERFACE'] = $IMMG_INTERFACE;

// ... SACCO APPLICATION OWNER
$APP_OWNER = $_SESSION['APP_OWNER'];


// ... COPYRIGHT STATEMENT
$COPY_RIGHT_STMT = "&copy; ".date("Y", time()).". ".$APP_OWNER."";
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
