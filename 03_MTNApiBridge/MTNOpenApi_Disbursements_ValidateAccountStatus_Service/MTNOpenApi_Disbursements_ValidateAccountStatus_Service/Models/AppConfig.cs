namespace MTNOpenApi_Disbursements_ValidateAccountStatus_Service.Models
{
    public class AppConfig
    {

        #region ... VARIABLES
        public static IConfiguration appconfig = new ConfigurationBuilder().AddJsonFile("appsettings.json").AddEnvironmentVariables().Build();
        #endregion


        #region ... GENERAL APP SETTINGS

        // ... Application Info
        public static string APP_CODE = appconfig["APP:APP_CODE"];
        public static string APP_NAME = appconfig["APP:APP_NAME"];
        public static string APP_AUTHOR = appconfig["APP:APP_AUTHOR"];
        public static string BILLER = appconfig["APP:BILLER"];
        public static string SERVICE = appconfig["APP:SERVICE"];
        public static string SERVICE_UNIT = appconfig["APP:SERVICE_UNIT"];
        public static string SERVICE_ENVIRONMENT = appconfig["APP:SERVICE_ENVIRONMENT"];

        // ... Logging
        public static string LOG_FILE_EXXT = appconfig["LOGGING:LOG_FILE_EXXT"];
        public static string LOG_FILE_PRFX = appconfig["LOGGING:LOG_FILE_PRFX"];
        public static string LOG_BASE_PATH = appconfig["LOGGING:LOG_FILE_PATH"];

        // ... Logging Info
        public static string DEBUG = appconfig["LOG_INFO:DEBUG"];
        public static string INFO = appconfig["LOG_INFO:INFO"];
        public static string SUCCESS = appconfig["LOG_INFO:SUCCESS"];
        public static string WARNING = appconfig["LOG_INFO:WARNING"];
        public static string ERROR = appconfig["LOG_INFO:ERROR"];

        // ... Redis Host
        public static string REDIS_HOST = appconfig["REDIS:HOST"];
        public static string REDIS_PORT = appconfig["REDIS:PORT"];


        #endregion


        #region ... TOKEN SETTINGS

        #region ... 01: API USERS

        public static string API_USER_DISBURSEMENTS_KEY = appconfig["TOKEN_SETTINGS:API_USER_DISBURSEMENTS:KEY"];
        public static string API_USER_DISBURSEMENTS_KEY_FIELD = appconfig["TOKEN_SETTINGS:API_USER_DISBURSEMENTS:KEY_FIELD"];

        #endregion


        #region ... 02: SUBSCRIPTION KEYS (PRIMARY)

        public static string SUB_KEY_PRIMARY_DISBURSEMENTS_KEY = appconfig["TOKEN_SETTINGS:SUB_KEY_PRIMARY_DISBURSEMENTS:KEY"];
        public static string SUB_KEY_PRIMARY_DISBURSEMENTS_KEY_FIELD = appconfig["TOKEN_SETTINGS:SUB_KEY_PRIMARY_DISBURSEMENTS:KEY_FIELD"];

        #endregion


        #region ... 03: SUBSCRIPTION KEYS (SECONDARY)

        public static string SUB_KEY_SECONDARY_DISBURSEMENTS_KEY = appconfig["TOKEN_SETTINGS:SUB_KEY_SECONDARY_DISBURSEMENTS:KEY"];
        public static string SUB_KEY_SECONDARY_DISBURSEMENTS_KEY_FIELD = appconfig["TOKEN_SETTINGS:SUB_KEY_SECONDARY_DISBURSEMENTS:KEY_FIELD"];

        #endregion


        #region ... 04: SERVICE ACCESS TOKENS

        public static string ACCESS_TOKEN_DISBURSEMENTS_KEY = appconfig["TOKEN_SETTINGS:ACCESS_TOKEN_DISBURSEMENTS:KEY"];
        public static string ACCESS_TOKEN_DISBURSEMENTS_KEY_FIELD = appconfig["TOKEN_SETTINGS:ACCESS_TOKEN_DISBURSEMENTS:KEY_FIELD"];

        #endregion


        #endregion


        #region ... URL SETTINGS

        public static string DISBURSEMENTS_VALIDATEACCOUNTSTATUS_URL_KEY = appconfig["URL_SETTINGS:DISBURSEMENTS_VALIDATEACCOUNTSTATUS_URL:KEY"];
        public static string DISBURSEMENTS_VALIDATEACCOUNTSTATUS_URL_KEY_FIELD = appconfig["URL_SETTINGS:DISBURSEMENTS_VALIDATEACCOUNTSTATUS_URL:KEY_FIELD"];

        #endregion



    }
}
