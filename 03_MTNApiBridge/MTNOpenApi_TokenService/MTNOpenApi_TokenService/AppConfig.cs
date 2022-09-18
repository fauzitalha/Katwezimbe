using Microsoft.Extensions.Configuration;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace MTNOpenApi_TokenService
{
    internal class AppConfig
    {
        #region ... VARIABLES
        public static IConfiguration appconfig = new ConfigurationBuilder().AddJsonFile("appsettings.json").AddEnvironmentVariables().Build();
        #endregion

        // ... Application Info
        public static string APP_CODE = appconfig["APP:APP_CODE"];
        public static string APP_NAME = appconfig["APP:APP_NAME"];
        public static string APP_AUTHOR = appconfig["APP:APP_AUTHOR"];
        public static string BILLER = appconfig["APP:BILLER"];
        public static string SERVICE = appconfig["APP:SERVICE"];
        public static string SERVICE_UNIT = appconfig["APP:SERVICE_UNIT"];

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


        #region ... TOKEN SETTINGS

        #region ... 01: API USERS
        public static string API_USER_COLLECTIONS_WIDGET_KEY = appconfig["TOKEN_SETTINGS:API_USER_COLLECTIONS_WIDGET:KEY"];
        public static string API_USER_COLLECTIONS_WIDGET_KEY_FIELD = appconfig["TOKEN_SETTINGS:API_USER_COLLECTIONS_WIDGET:KEY_FIELD"];

        public static string API_USER_COLLECTIONS_KEY = appconfig["TOKEN_SETTINGS:API_USER_COLLECTIONS:KEY"];
        public static string API_USER_COLLECTIONS_KEY_FIELD = appconfig["TOKEN_SETTINGS:API_USER_COLLECTIONS:KEY_FIELD"];

        public static string API_USER_DISBURSEMENTS_KEY = appconfig["TOKEN_SETTINGS:API_USER_DISBURSEMENTS:KEY"];
        public static string API_USER_DISBURSEMENTS_KEY_FIELD = appconfig["TOKEN_SETTINGS:API_USER_DISBURSEMENTS:KEY_FIELD"];

        public static string API_USER_REMITTANCES_KEY = appconfig["TOKEN_SETTINGS:API_USER_REMITTANCES:KEY"];
        public static string API_USER_REMITTANCES_KEY_FIELD = appconfig["TOKEN_SETTINGS:API_USER_REMITTANCES:KEY_FIELD"];
        #endregion


        #region ... 02: SUBSCRIPTION KEYS (PRIMARY)
        public static string SUB_KEY_PRIMARY_COLLECTIONS_WIDGET_KEY = appconfig["TOKEN_SETTINGS:SUB_KEY_PRIMARY_COLLECTIONS_WIDGET:KEY"];
        public static string SUB_KEY_PRIMARY_COLLECTIONS_WIDGET_KEY_FIELD = appconfig["TOKEN_SETTINGS:SUB_KEY_PRIMARY_COLLECTIONS_WIDGET:KEY_FIELD"];

        public static string SUB_KEY_PRIMARY_COLLECTIONS_KEY = appconfig["TOKEN_SETTINGS:SUB_KEY_PRIMARY_COLLECTIONS:KEY"];
        public static string SUB_KEY_PRIMARY_COLLECTIONS_KEY_FIELD = appconfig["TOKEN_SETTINGS:SUB_KEY_PRIMARY_COLLECTIONS:KEY_FIELD"];

        public static string SUB_KEY_PRIMARY_DISBURSEMENTS_KEY = appconfig["TOKEN_SETTINGS:SUB_KEY_PRIMARY_DISBURSEMENTS:KEY"];
        public static string SUB_KEY_PRIMARY_DISBURSEMENTS_KEY_FIELD = appconfig["TOKEN_SETTINGS:SUB_KEY_PRIMARY_DISBURSEMENTS:KEY_FIELD"];

        public static string SUB_KEY_PRIMARY_REMITTANCES_KEY = appconfig["TOKEN_SETTINGS:SUB_KEY_PRIMARY_REMITTANCES:KEY"];
        public static string SUB_KEY_PRIMARY_REMITTANCES_KEY_FIELD = appconfig["TOKEN_SETTINGS:SUB_KEY_PRIMARY_REMITTANCES:KEY_FIELD"];
        #endregion


        #region ... 03: SUBSCRIPTION KEYS (SECONDARY)
        public static string SUB_KEY_SECONDARY_COLLECTIONS_WIDGET_KEY = appconfig["TOKEN_SETTINGS:SUB_KEY_SECONDARY_COLLECTIONS_WIDGET:KEY"];
        public static string SUB_KEY_SECONDARY_COLLECTIONS_WIDGET_KEY_FIELD = appconfig["TOKEN_SETTINGS:SUB_KEY_SECONDARY_COLLECTIONS_WIDGET:KEY_FIELD"];

        public static string SUB_KEY_SECONDARY_COLLECTIONS_KEY = appconfig["TOKEN_SETTINGS:SUB_KEY_SECONDARY_COLLECTIONS:KEY"];
        public static string SUB_KEY_SECONDARY_COLLECTIONS_KEY_FIELD = appconfig["TOKEN_SETTINGS:SUB_KEY_SECONDARY_COLLECTIONS:KEY_FIELD"];

        public static string SUB_KEY_SECONDARY_DISBURSEMENTS_KEY = appconfig["TOKEN_SETTINGS:SUB_KEY_SECONDARY_DISBURSEMENTS:KEY"];
        public static string SUB_KEY_SECONDARY_DISBURSEMENTS_KEY_FIELD = appconfig["TOKEN_SETTINGS:SUB_KEY_SECONDARY_DISBURSEMENTS:KEY_FIELD"];

        public static string SUB_KEY_SECONDARY_REMITTANCES_KEY = appconfig["TOKEN_SETTINGS:SUB_KEY_SECONDARY_REMITTANCES:KEY"];
        public static string SUB_KEY_SECONDARY_REMITTANCES_KEY_FIELD = appconfig["TOKEN_SETTINGS:SUB_KEY_SECONDARY_REMITTANCES:KEY_FIELD"];
        #endregion


        #region ... 04: SERVICE ACCESS TOKENS
        public static string ACCESS_TOKEN_COLLECTIONS_KEY = appconfig["TOKEN_SETTINGS:ACCESS_TOKEN_COLLECTIONS:KEY"];
        public static string ACCESS_TOKEN_COLLECTIONS_KEY_FIELD = appconfig["TOKEN_SETTINGS:ACCESS_TOKEN_COLLECTIONS:KEY_FIELD"];

        public static string ACCESS_TOKEN_DISBURSEMENTS_KEY = appconfig["TOKEN_SETTINGS:ACCESS_TOKEN_DISBURSEMENTS:KEY"];
        public static string ACCESS_TOKEN_DISBURSEMENTS_KEY_FIELD = appconfig["TOKEN_SETTINGS:ACCESS_TOKEN_DISBURSEMENTS:KEY_FIELD"];
        #endregion


        #endregion



        #region ... URL SETTINGS

        public static string AUTH_API_KEY_URL_KEY = appconfig["URL_SETTINGS:AUTH_API_KEY_URL:KEY"];
        public static string AUTH_API_KEY_URL_KEY_FIELD = appconfig["URL_SETTINGS:AUTH_API_KEY_URL:KEY_FIELD"];

        public static string COLLECTIONS_TOKEN_URL_KEY = appconfig["URL_SETTINGS:COLLECTIONS_TOKEN_URL:KEY"];
        public static string COLLECTIONS_TOKEN_URL_FIELD = appconfig["URL_SETTINGS:COLLECTIONS_TOKEN_URL:KEY_FIELD"];

        public static string DISBURSEMENTS_TOKEN_URL_KEY = appconfig["URL_SETTINGS:DISBURSEMENTS_TOKEN_URL:KEY"];
        public static string DISBURSEMENTS_TOKEN_URL_KEY_FIELD = appconfig["URL_SETTINGS:DISBURSEMENTS_TOKEN_URL:KEY_FIELD"];


        #endregion


    }
}
