using Microsoft.AspNetCore.Mvc;
using MTNOpenApi_Collections_RequestToPay_Service.Core;
using MTNOpenApi_Collections_RequestToPay_Service.Models;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;

// For more information on enabling Web API for empty projects, visit https://go.microsoft.com/fwlink/?LinkID=397860

namespace MTNOpenApi_Collections_RequestToPay_Service.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class RequesttopayController : ControllerBase
    {

        #region ... VARIABLES
        CoreProcessor cops = new CoreProcessor();
        LogMessage logmsg = new LogMessage();
        AppLogger applogger = new AppLogger();
        #endregion

        #region ... 01: Process JSON request
        [HttpPost("json")]
        [Consumes("application/json")]
        [Produces("application/json")]
        public dynamic PostJson([FromBody] Object request)
        {
            #region ... variables
            Dictionary<string, dynamic> respMsg = new Dictionary<string, dynamic>();
            string logMessage = "";
            logmsg.CLASS = this.GetType().Name;
            logmsg.LOG_LEVEL = LogInfo.INFO;
            logmsg.FUNCTION = System.Reflection.MethodBase.GetCurrentMethod().Name;
            #endregion

            try
            {
                applogger.LogFileSeparatorInternal();

                // ... process requests
                respMsg = cops.ProcessWebRequest(request);

                #region ... <logging />
                string responseJSON = JsonConvert.SerializeObject(respMsg);
                JObject responseJSONObject = JObject.Parse(responseJSON);
                string responseJSONFormatted = CoreHelpers.FormatJson(responseJSONObject);
                logMessage = "Response Message\r\n" + responseJSONFormatted;
                applogger.LogToFile(logmsg, logmsg.FUNCTION, "FINAL_RESPONSE", logMessage);
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

                // ... build response
                respMsg.Add("AuthCode", "ERROR");
                respMsg.Add("AuthMessage", msg);
                respMsg.Add("AuthDetailedMessage", stack_trace);

                #region ... <logging />
                string responseJSON = JsonConvert.SerializeObject(respMsg);
                JObject responseJSONObject = JObject.Parse(responseJSON);
                string responseJSONFormatted = CoreHelpers.FormatJson(responseJSONObject);
                logMessage = "Error Response Message\r\n" + responseJSONFormatted;
                applogger.LogToFile(logmsg, logmsg.FUNCTION, "FINAL_ERROR_RESPONSE", logMessage);
                #endregion

            }

            return respMsg;
        }
        #endregion







    }
}
