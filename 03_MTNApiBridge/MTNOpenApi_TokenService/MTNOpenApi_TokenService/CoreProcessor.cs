using StackExchange.Redis;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace MTNOpenApi_TokenService
{
    internal class CoreProcessor
    {
        #region ... VARIABLES
        AppLogger applogger = new AppLogger();
        LogMessage logmsg = new LogMessage();
        MTNOpenApiHelper moah = new MTNOpenApiHelper();
        #endregion


        #region ...01: RefreshAccessToken_COLLECTIONS
        public void RefreshAccessToken_COLLECTIONS()
        {
            string message_type = "";
            string message = "";
            try
            {
                
                // ... 000: Logging Method
                logmsg.CLASS = "CoreProcessor";
                logmsg.LOG_LEVEL = LogInfo.INFO;
                logmsg.FUNCTION = System.Reflection.MethodBase.GetCurrentMethod().Name;
                #region ... <logging />
                message_type = "STEP 01";
                message = "Preparing to refresh COLLECTIONS access token.";
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);

                message_type = "STEP 01";
                message = "";
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);
                #endregion

                // ... 001: Get Api User and Subscription Key
                string apiUser_Collections = RedisHelper.ReadData_HASH(AppConfig.API_USER_COLLECTIONS_KEY, AppConfig.API_USER_COLLECTIONS_KEY_FIELD);
                string subKey_Primary_Collections = RedisHelper.ReadData_HASH(AppConfig.SUB_KEY_PRIMARY_COLLECTIONS_KEY, AppConfig.SUB_KEY_PRIMARY_COLLECTIONS_KEY_FIELD);
                #region ... <logging />
                message_type = "STEP 02";
                message = "COLLECTIONS API USER: " + apiUser_Collections;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);

                message_type = "STEP 02";
                message = "COLLECTIONS SUB KEYY: " + subKey_Primary_Collections;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);

                message_type = "STEP 02";
                message = "";
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);
                #endregion

                // ... 002: Process the API key
                string api_key = moah.FetchAuthApiKey(logmsg, apiUser_Collections, subKey_Primary_Collections);
                #region ... <logging />
                message_type = "STEP 03";
                message = "COLLECTIONS API KEY: " + api_key;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);

                message_type = "STEP 03";
                message = "";
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);
                #endregion


                string auth_header_value = moah.CreateAuthHeaderValue(apiUser_Collections, api_key);
                #region ... <logging />
                message_type = "STEP 04";
                message = "COLLECTIONS AUTH HEADER: " + auth_header_value;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);

                message_type = "STEP 04";
                message = "";
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);
                #endregion

                string access_token = moah.GetTranToken(logmsg, auth_header_value, subKey_Primary_Collections);

                #region ... <logging />
                message_type = "STEP 05";
                message = "Proceeding to get COLLECTIONS api access token";
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);

                message_type = "STEP 05";
                message = "";
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);
                #endregion


                // ... 003: Update the key to redis
                if (access_token.Equals(""))
                {
                    // ... failed
                    #region ... <logging />
                    message_type = "STEP 06";
                    message = "FAILED TO GET API ACCESS TOKEN";
                    applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);

                    message_type = "STEP 06";
                    message = "";
                    applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);

                    applogger.LogFileSeparatorInternal();
                    #endregion
                }
                else
                {
                    // ... success
                    List<HashEntry> hash_entry_list = new List<HashEntry>();
                    hash_entry_list.Add(new HashEntry(AppConfig.ACCESS_TOKEN_COLLECTIONS_KEY_FIELD, access_token));
                    RedisHelper.SaveData_HASH(AppConfig.API_USER_COLLECTIONS_KEY, hash_entry_list);

                    #region ... <logging />
                    message_type = "STEP 06";
                    message = "ACCESS TOKEN RETRIEVED AND UPDATED SUCCESSFULLY";
                    applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);

                    message_type = "STEP 06";
                    message = "";
                    applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);

                    applogger.LogFileSeparatorInternal();
                    #endregion
                }//...end


            }
            catch (Exception ex)
            {
                logmsg.LOG_LEVEL = LogInfo.ERROR;
                string msg = ex.Message;
                string stack_trace = ex.StackTrace;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.CLASS + "." + logmsg.FUNCTION, msg);
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.CLASS + "." + logmsg.FUNCTION, stack_trace);
                applogger.LogFileSeparator();
            }
        }
        #endregion



        #region ...02: RefreshAccessToken_DISBURSEMENTS
        public void RefreshAccessToken_DISBURSEMENTS()
        {
            string message_type = "";
            string message = "";
            try
            {

                // ... 000: Logging Method
                logmsg.CLASS = "CoreProcessor";
                logmsg.LOG_LEVEL = LogInfo.INFO;
                logmsg.FUNCTION = System.Reflection.MethodBase.GetCurrentMethod().Name;
                #region ... <logging />
                message_type = "STEP 01";
                message = "Preparing to refresh DISBURSEMENTS access token.";
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);

                message_type = "STEP 01";
                message = "";
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);
                #endregion

                // ... 001: Get Api User and Subscription Key
                string apiUser_Disbursements = RedisHelper.ReadData_HASH(AppConfig.API_USER_DISBURSEMENTS_KEY, AppConfig.API_USER_DISBURSEMENTS_KEY_FIELD).Trim();
                string subKey_Primary_Disbursements = RedisHelper.ReadData_HASH(AppConfig.SUB_KEY_PRIMARY_DISBURSEMENTS_KEY, AppConfig.SUB_KEY_PRIMARY_DISBURSEMENTS_KEY_FIELD).Trim();
                #region ... <logging />
                message_type = "STEP 02";
                message = "DISBURSEMENTS API USER: " + apiUser_Disbursements;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);

                message_type = "STEP 02";
                message = "DISBURSEMENTS SUB KEYY: " + subKey_Primary_Disbursements;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);

                message_type = "STEP 02";
                message = "";
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);
                #endregion

                // ... 002: Process the API key
                string api_key = moah.FetchAuthApiKey(logmsg, apiUser_Disbursements, subKey_Primary_Disbursements);
                #region ... <logging />
                message_type = "STEP 03";
                message = "DISBURSEMENTS API KEY: " + api_key;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);

                message_type = "STEP 03";
                message = "";
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);
                #endregion

                string auth_header_value = moah.CreateAuthHeaderValue(apiUser_Disbursements, api_key);
                #region ... <logging />
                message_type = "STEP 04";
                message = "DISBURSEMENTS AUTH HEADER: " + auth_header_value;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);

                message_type = "STEP 04";
                message = "";
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);
                #endregion

                string access_token = moah.GetTranTokenDisbursement(logmsg, auth_header_value, subKey_Primary_Disbursements);
                #region ... <logging />
                message_type = "STEP 05";
                message = "Proceeding to get DISBURSEMENTS api access token";
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);

                message_type = "STEP 05";
                message = "";
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);
                #endregion


                // ... 003: Update the key to redis
                if (access_token.Equals(""))
                {
                    // ... failed
                    #region ... <logging />
                    message_type = "STEP 06";
                    message = "FAILED TO GET API ACCESS TOKEN";
                    applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);

                    message_type = "STEP 06";
                    message = "";
                    applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);

                    applogger.LogFileSeparatorInternal();
                    #endregion
                }
                else
                {
                    // ... success
                    List<HashEntry> hash_entry_list = new List<HashEntry>();
                    hash_entry_list.Add(new HashEntry(AppConfig.ACCESS_TOKEN_DISBURSEMENTS_KEY_FIELD, access_token));
                    RedisHelper.SaveData_HASH(AppConfig.ACCESS_TOKEN_DISBURSEMENTS_KEY, hash_entry_list);

                    #region ... <logging />
                    message_type = "STEP 06";
                    message = "ACCESS TOKEN RETRIEVED AND UPDATED SUCCESSFULLY";
                    applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);

                    message_type = "STEP 06";
                    message = "";
                    applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, message_type, message);

                    applogger.LogFileSeparatorInternal();
                    #endregion
                }//...end


            }
            catch (Exception ex)
            {
                logmsg.LOG_LEVEL = LogInfo.ERROR;
                string msg = ex.Message;
                string stack_trace = ex.StackTrace;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.CLASS + "." + logmsg.FUNCTION, msg);
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.CLASS + "." + logmsg.FUNCTION, stack_trace);
                applogger.LogFileSeparator();
            }
        }
        #endregion





    }


}
