using MTNOpenApi_Disbursements_ValidateAccountStatus_Service.Models;
using Newtonsoft.Json.Linq;

namespace MTNOpenApi_Disbursements_ValidateAccountStatus_Service.Core
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
                string accountHolderIdType = requestObject["accountHolderIdType"].ToString().Trim();
                string accountHolderId = requestObject["accountHolderId"].ToString().Trim();
                #region ... <logging />
                logMessage = "accountHolderIdType: " + accountHolderIdType;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "accountHolderId: " + accountHolderId;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);
                #endregion

                #endregion

                #region ... 004: Prepare Transaction Request Message Params
                string apiUser_Disbursements = RedisHelper.ReadData_HASH(AppConfig.API_USER_DISBURSEMENTS_KEY, AppConfig.API_USER_DISBURSEMENTS_KEY_FIELD);
                string subKey_Primary_Disbursements = RedisHelper.ReadData_HASH(AppConfig.SUB_KEY_PRIMARY_DISBURSEMENTS_KEY, AppConfig.SUB_KEY_PRIMARY_DISBURSEMENTS_KEY_FIELD);
                string accessToken_Disbursements = RedisHelper.ReadData_HASH(AppConfig.ACCESS_TOKEN_DISBURSEMENTS_KEY, AppConfig.ACCESS_TOKEN_DISBURSEMENTS_KEY_FIELD);
                string disbursements_ValidateAccountStatus_URL_Template = RedisHelper.ReadData_HASH(AppConfig.DISBURSEMENTS_VALIDATEACCOUNTSTATUS_URL_KEY, AppConfig.DISBURSEMENTS_VALIDATEACCOUNTSTATUS_URL_KEY_FIELD);
                string targetEnvironment = AppConfig.SERVICE_ENVIRONMENT;

                Dictionary<string, string> mtnRequestParams = new Dictionary<string, string>();
                mtnRequestParams.Add("accountHolderIdType", accountHolderIdType);
                mtnRequestParams.Add("accountHolderId", accountHolderId);
                string disbursements_ValidateAccountStatus_URL = CoreHelpers.PopulateStringTemplate(disbursements_ValidateAccountStatus_URL_Template, mtnRequestParams);
                #region ... <logging />
                logMessage = "apiUser_Disbursements: " + apiUser_Disbursements;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "subKey_Primary_Disbursements: " + subKey_Primary_Disbursements;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "accessToken_Collections: **** **** i am a secure secret.  **** ****";
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "targetEnvironment: " + targetEnvironment;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "disbursements_ValidateAccountStatus_URL_Template: " + disbursements_ValidateAccountStatus_URL_Template;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "disbursements_ValidateAccountStatus_URL: " + disbursements_ValidateAccountStatus_URL;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);
                #endregion

                #endregion

                #region ... 005: Making Request to MTN
                Dictionary<string, dynamic> respProcMessage = moah.DisbursementsValidateAccountStatus(accessToken_Disbursements, targetEnvironment, subKey_Primary_Disbursements, disbursements_ValidateAccountStatus_URL);
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
