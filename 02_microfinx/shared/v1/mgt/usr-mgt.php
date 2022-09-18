<?php
# ... SYSTEM RESPONSE OBJECT
$_SESSION['ALERT_MSG'] = "";

# ... FETCHING CUSTOMER DETAILS
$core_username = $_SESSION['core_username'];
$core_userId = $_SESSION['core_userId'];
$core_base64EncodedAuthenticationKey = $_SESSION['core_base64EncodedAuthenticationKey'];
$core_authenticated = $_SESSION['core_authenticated'];
$core_officeId = $_SESSION['core_officeId'];
$core_officeName = $_SESSION['core_officeName'];
$core_roles = $_SESSION['core_roles'];
$core_permissions = $_SESSION['core_permissions'];
$core_shouldRenewPassword = $_SESSION['core_shouldRenewPassword'];

# ... Customer App User Details
$UPR_RECORD_ID = $_SESSION['UPR_RECORD_ID'];
$UPR_USER_ID = $_SESSION['UPR_USER_ID'];
$UPR_USER_CORE_ID = $_SESSION['UPR_USER_CORE_ID'];
$UPR_GENDER = $_SESSION['UPR_GENDER'];
$UPR_PHONE = $_SESSION['UPR_PHONE'];
$UPR_EMAIL_ADDRESS = $_SESSION['UPR_EMAIL_ADDRESS'];
$UPR_LOGGED_IN = $_SESSION['UPR_LOGGED_IN'];
$UPR_USER_ROLE_DETAILS = $_SESSION['UPR_USER_ROLE_DETAILS'];


# ... BreakDown of Roles
$core_role_name = $core_roles[0]["name"];
?>
