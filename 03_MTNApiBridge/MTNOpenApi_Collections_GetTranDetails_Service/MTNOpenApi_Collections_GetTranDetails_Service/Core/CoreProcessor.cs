using MTNOpenApi_Collections_GetTranDetails_Service.Models;
using Newtonsoft.Json.Linq;

namespace MTNOpenApi_Collections_GetTranDetails_Service.Core
{
    public class CoreProcessor
    {

        #region ... VARIABLES
        AppLogger applogger = new AppLogger();
        LogMessage logmsg = new LogMessage();
        MTNOpenApiHelper moah = new MTNOpenApiHelper();
        #endregion



        #region ... 0001: ProcessWebRequest_JSON
        internal Dictionary<string, dynamic> ProcessWebRequest(object origRequest)
        {
            Dictionary<string, dynamic> respMsg = new Dictionary<string, dynamic>();
            string logMessage = "";
            try
            {
                #region ... 001: Determining Session Context
                logmsg.LOG_LEVEL = LogInfo.INFO;
                logmsg.FUNCTION = System.Reflection.MethodBase.GetCurrentMethod().Name;
                #endregion

                #region ... 002: Raw Request Message
                JObject requestObject = JObject.Parse(origRequest.ToString());
                #region ... <logging />
                string rawRequestMessage = CoreHelpers.FormatJson(requestObject);
                logMessage = "Raw Web JSON Request\r\n" + rawRequestMessage;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);
                #endregion

                #endregion

                #region ... 003: Interprete Request Fields
                string tranProcRef = requestObject["tranProcRef"].ToString().Trim();
                #region ... <logging />

                logMessage = "tranProcRef: " + tranProcRef;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                #endregion

                #endregion

                #region ... 004: Prepare Transaction Request Message Params
                string apiUser_Collections = RedisHelper.ReadData_HASH(AppConfig.API_USER_COLLECTIONS_KEY, AppConfig.API_USER_COLLECTIONS_KEY_FIELD);
                string subKey_Primary_Collections = RedisHelper.ReadData_HASH(AppConfig.SUB_KEY_PRIMARY_COLLECTIONS_KEY, AppConfig.SUB_KEY_PRIMARY_COLLECTIONS_KEY_FIELD);
                string accessToken_Collections = RedisHelper.ReadData_HASH(AppConfig.ACCESS_TOKEN_COLLECTIONS_KEY, AppConfig.ACCESS_TOKEN_COLLECTIONS_KEY_FIELD);
                string collections_GetTranDetails_URL_Template = RedisHelper.ReadData_HASH(AppConfig.COLLECTIONS_GETTRANDETAILS_URL_KEY, AppConfig.COLLECTIONS_GETTRANDETAILS_URL_KEY_FIELD);
                string targetEnvironment = AppConfig.SERVICE_ENVIRONMENT;

                Dictionary<string, string> mtnRequestParams = new Dictionary<string, string>();
                mtnRequestParams.Add("tranProcRef", tranProcRef);
                string collections_GetTranDetails_URL = CoreHelpers.PopulateStringTemplate(collections_GetTranDetails_URL_Template, mtnRequestParams);
                #region ... <logging />
                logMessage = "apiUser_Collections: " + apiUser_Collections;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "subKey_Primary_Collections: " + subKey_Primary_Collections;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "accessToken_Collections: **** **** i am a secure secret.  **** ****";
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "targetEnvironment: " + targetEnvironment;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "collections_GetTranDetails_URL_Template: " + collections_GetTranDetails_URL_Template;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "collections_GetTranDetails_URL: " + collections_GetTranDetails_URL;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);
                #endregion

                #endregion

                #region ... 005: Making Request to MTN
                Dictionary<string, dynamic> respProcMessage = moah.GetTranDetails_Collection(accessToken_Collections, targetEnvironment, subKey_Primary_Collections, collections_GetTranDetails_URL);
                respMsg = respProcMessage;
                #endregion
            }
            catch (Exception ex)
            {
                logmsg.LOG_LEVEL = LogInfo.ERROR;
                string msg = ex.Message;
                string stack_trace = ex.StackTrace;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, msg);
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, stack_trace);
                applogger.LogFileSeparator();

                // ... error
                respMsg.Add("AuthCode", "ERROR");
                respMsg.Add("AuthMessage", msg);
                respMsg.Add("AuthDetailedMessage", stack_trace);

            }

            return respMsg;
        }
        #endregion





    }
}
