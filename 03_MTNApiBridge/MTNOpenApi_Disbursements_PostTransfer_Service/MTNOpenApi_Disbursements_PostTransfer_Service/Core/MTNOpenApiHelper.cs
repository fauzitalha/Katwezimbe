using MTNOpenApi_Disbursements_PostTransfer_Service.Models;
using System.Net.Http.Headers;
using System.Text;

namespace MTNOpenApi_Disbursements_PostTransfer_Service.Core
{
    public class MTNOpenApiHelper
    {
        #region ... VARIABLES
        AppLogger applogger = new AppLogger();
        LogMessage logmsg = new LogMessage();
        #endregion

        #region ... 01: InitiateRequestToPay
        public Dictionary<string, dynamic> PostDisbursementTransfer(string authtoken, string xRefId, string targetEnvironment, string collectionsSubKey, string url, string requestMessage)
        {
            Dictionary<string, dynamic> respProcMessage = new Dictionary<string, dynamic>();
            string logMessage = "";
            try
            {
                #region ... 001: Determining Session Context
                logmsg.LOG_LEVEL = LogInfo.INFO;
                logmsg.FUNCTION = System.Reflection.MethodBase.GetCurrentMethod().Name;
                #endregion

                var client = new HttpClient();

                // Request headers
                client.DefaultRequestHeaders.Authorization = new AuthenticationHeaderValue("Bearer", authtoken);
                client.DefaultRequestHeaders.Add("X-Reference-Id", xRefId);
                client.DefaultRequestHeaders.Add("X-Target-Environment", targetEnvironment);
                client.DefaultRequestHeaders.Add("Ocp-Apim-Subscription-Key", collectionsSubKey);

                var uri = url;
                HttpResponseMessage response;


                byte[] byteData = Encoding.UTF8.GetBytes(requestMessage);

                using (var content = new ByteArrayContent(byteData))
                {
                    content.Headers.ContentType = new MediaTypeHeaderValue("application/json");
                    response = client.PostAsync(uri, content).Result;

                    bool http_IsSuccessStatusCode = response.IsSuccessStatusCode;
                    string http_Version = response.Version.ToString();
                    string http_status_code = response.StatusCode.ToString();
                    int http_status_code_num = (int)response.StatusCode;
                    string resp_content = response.Content.ReadAsStringAsync().Result;

                    // ... interpeting the response
                    string AuthCode = (http_IsSuccessStatusCode) ? "SUCCESS" : "DECLINE";
                    string AuthMessage = http_status_code;
                    string AuthDetailedMessage = resp_content;


                    respProcMessage.Add("AuthCode", AuthCode);
                    respProcMessage.Add("AuthMessage", AuthMessage);
                    respProcMessage.Add("AuthProcRef", xRefId);
                    respProcMessage.Add("AuthDetailedMessage", AuthDetailedMessage);
                    respProcMessage.Add("requestProcStatus", http_IsSuccessStatusCode);
                    respProcMessage.Add("http_Version", http_Version);
                    respProcMessage.Add("http_status_code", http_status_code);
                    respProcMessage.Add("http_status_code_num", http_status_code_num);

                    #region ... <logging />
                    logMessage = "AuthCode: " + AuthCode;
                    applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                    logMessage = "AuthMessage: " + AuthMessage;
                    applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                    logMessage = "AuthDetailedMessage: " + AuthDetailedMessage;
                    applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                    logMessage = "requestProcStatus: " + http_IsSuccessStatusCode;
                    applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                    logMessage = "http_Version: " + http_Version;
                    applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                    logMessage = "http_status_code: " + http_status_code;
                    applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                    logMessage = "http_status_code_num: " + http_status_code_num;
                    applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);



                    #endregion

                }


            }
            catch (Exception ex)
            {
                logmsg.LOG_LEVEL = LogInfo.ERROR;
                string msg = ex.Message;
                string stack_trace = ex.StackTrace;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, msg);
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, stack_trace);
                applogger.LogFileSeparator();

                // ... interpeting the response
                string AuthCode = "ERROR";
                string AuthMessage = msg;
                string AuthDetailedMessage = stack_trace;

                // ... error
                respProcMessage.Add("AuthCode", "ERROR");
                respProcMessage.Add("AuthMessage", msg);
                respProcMessage.Add("AuthDetailedMessage", stack_trace);

                #region ... <logging />
                logMessage = "AuthCode: " + AuthCode;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "AuthMessage: " + AuthMessage;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);

                logMessage = "AuthDetailedMessage: " + AuthDetailedMessage;
                applogger.LogToFile(logmsg, logmsg.LOG_LEVEL, logmsg.FUNCTION, logMessage);
                #endregion
            }

            return respProcMessage;
        }
        #endregion

    }
}
