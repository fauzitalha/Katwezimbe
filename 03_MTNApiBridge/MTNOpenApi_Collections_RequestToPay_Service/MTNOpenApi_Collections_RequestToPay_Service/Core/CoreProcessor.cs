using MTNOpenApi_Collections_RequestToPay_Service.Models;
using Newtonsoft.Json.Linq;
using System.Collections;

namespace MTNOpenApi_Collections_RequestToPay_Service.Core
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
                string amount = requestObject["amount"].ToString().Trim();
                string currency = requestObject["currency"].ToString().Trim();
                string externalId = requestObject["externalId"].ToString().Trim();

                JObject payer = (JObject)requestObject["payer"];
                string partyIdType = payer["partyIdType"].ToString().Trim();
                string partyId = payer["partyId"].ToString().Trim();

                string payerMessage = requestObject["payerMessage"].ToString().Trim();
                string payeeNote = requestObject["payeeNote"].ToString().Trim();
                #region ... <logging />
                logMessage = "amount: " + amount;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "currency: " + currency;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "externalId: " + externalId;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "payer.partyIdType: " + partyIdType;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "payer.partyId: " + partyId;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "payerMessage: " + payerMessage;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "payeeNote: " + payeeNote;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);
                #endregion

                #endregion

                #region ... 004: Prepare Transaction Request Message Params
                string apiUser_Collections = RedisHelper.ReadData_HASH(AppConfig.API_USER_COLLECTIONS_KEY, AppConfig.API_USER_COLLECTIONS_KEY_FIELD);
                string subKey_Primary_Collections = RedisHelper.ReadData_HASH(AppConfig.SUB_KEY_PRIMARY_COLLECTIONS_KEY, AppConfig.SUB_KEY_PRIMARY_COLLECTIONS_KEY_FIELD);
                string accessToken_Collections = RedisHelper.ReadData_HASH(AppConfig.ACCESS_TOKEN_COLLECTIONS_KEY, AppConfig.ACCESS_TOKEN_COLLECTIONS_KEY_FIELD);
                string collections_Request2Pay_URL = RedisHelper.ReadData_HASH(AppConfig.COLLECTIONS_REQUEST2PAY_URL_KEY, AppConfig.COLLECTIONS_REQUEST2PAY_URL_KEY_FIELD);
                string collections_Request2Pay_MsgTemplate = RedisHelper.ReadData_HASH(AppConfig.COLLECTIONS_REQUEST2PAY_MSGTEMPLATE_KEY, AppConfig.COLLECTIONS_REQUEST2PAY_MSGTEMPLATE_KEY_FIELD);
                string xRefId = Guid.NewGuid().ToString();
                string targetEnvironment = AppConfig.SERVICE_ENVIRONMENT;

                Dictionary<string, string> mtnRequestParams = new Dictionary<string, string>();
                mtnRequestParams.Add("amount", amount);
                mtnRequestParams.Add("currency", currency);
                mtnRequestParams.Add("externalId", externalId);
                mtnRequestParams.Add("partyIdType", partyIdType);
                mtnRequestParams.Add("partyId", partyId);
                mtnRequestParams.Add("payerMessage", payerMessage);
                mtnRequestParams.Add("payeeNote", payeeNote);
                string collections_Request2Pay = CoreHelpers.PopulateStringTemplate(collections_Request2Pay_MsgTemplate, mtnRequestParams);
                #region ... <logging />
                logMessage = "apiUser_Collections: " + apiUser_Collections;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "subKey_Primary_Collections: " + subKey_Primary_Collections;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "accessToken_Collections: **** **** i am a secure secret.  **** ****";
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "collections_Request2Pay_URL: " + collections_Request2Pay_URL;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "xRefId: " + xRefId;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "targetEnvironment: " + targetEnvironment;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "collections_Request2Pay_MsgTemplate: " + collections_Request2Pay_MsgTemplate;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "collections_Request2Pay: " + collections_Request2Pay;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);
                #endregion

                #endregion

                #region ... 005: Making Request to MTN
                Dictionary<string, dynamic> respProcMessage = moah.InitiateRequestToPay(accessToken_Collections, xRefId, targetEnvironment, subKey_Primary_Collections, collections_Request2Pay_URL, collections_Request2Pay);
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
