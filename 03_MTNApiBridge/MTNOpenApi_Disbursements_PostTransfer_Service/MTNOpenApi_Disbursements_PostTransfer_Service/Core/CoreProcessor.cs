using MTNOpenApi_Disbursements_PostTransfer_Service.Models;
using Newtonsoft.Json.Linq;

namespace MTNOpenApi_Disbursements_PostTransfer_Service.Core
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

                JObject payee = (JObject)requestObject["payee"];
                string partyIdType = payee["partyIdType"].ToString().Trim();
                string partyId = payee["partyId"].ToString().Trim();

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
                string apiUser_Disbursements = RedisHelper.ReadData_HASH(AppConfig.API_USER_DISBURSEMENTS_KEY, AppConfig.API_USER_DISBURSEMENTS_KEY_FIELD);
                string subKey_Primary_Disbursements = RedisHelper.ReadData_HASH(AppConfig.SUB_KEY_PRIMARY_DISBURSEMENTS_KEY, AppConfig.SUB_KEY_PRIMARY_DISBURSEMENTS_KEY_FIELD);
                string accessToken_Disbursements = RedisHelper.ReadData_HASH(AppConfig.ACCESS_TOKEN_DISBURSEMENTS_KEY, AppConfig.ACCESS_TOKEN_DISBURSEMENTS_KEY_FIELD);
                string disbursements_PostTransfer_URL = RedisHelper.ReadData_HASH(AppConfig.DISBURSEMENTS_POSTTRANSFER_URL_KEY, AppConfig.DISBURSEMENTS_POSTTRANSFER_URL_KEY_FIELD);
                string disbursements_PostTransfer_MsgTemplate = RedisHelper.ReadData_HASH(AppConfig.DISBURSEMENTS_POSTTRANSFER_MSGTEMPLATE_KEY, AppConfig.DISBURSEMENTS_POSTTRANSFER_MSGTEMPLATE_KEY_FIELD);
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
                string disbursements_PostTransfer = CoreHelpers.PopulateStringTemplate(disbursements_PostTransfer_MsgTemplate, mtnRequestParams);
                #region ... <logging />
                logMessage = "apiUser_Collections: " + apiUser_Disbursements;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "subKey_Primary_Collections: " + subKey_Primary_Disbursements;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "accessToken_Collections: **** **** i am a secure secret.  **** ****";
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "collections_Request2Pay_URL: " + disbursements_PostTransfer_URL;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "xRefId: " + xRefId;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "targetEnvironment: " + targetEnvironment;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "collections_Request2Pay_MsgTemplate: " + disbursements_PostTransfer_MsgTemplate;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "collections_Request2Pay: " + disbursements_PostTransfer;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);
                #endregion

                #endregion

                #region ... 005: Making Request to MTN
                Dictionary<string, dynamic> respProcMessage = moah.PostDisbursementTransfer(accessToken_Disbursements, xRefId, targetEnvironment, subKey_Primary_Disbursements, disbursements_PostTransfer_URL, disbursements_PostTransfer);
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
