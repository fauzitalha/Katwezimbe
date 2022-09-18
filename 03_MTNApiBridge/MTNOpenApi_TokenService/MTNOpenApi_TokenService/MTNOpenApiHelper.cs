using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http.Headers;
using System.Text;
using System.Threading.Tasks;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;

namespace MTNOpenApi_TokenService
{
    internal class MTNOpenApiHelper
    {

        #region ... VARIABLES
        AppLogger applogger = new AppLogger();
        #endregion


        #region ... 001: FetchAuthApiKey
        public string FetchAuthApiKey(LogMessage logmsg, string UUID, string SUB_KEY)
        {
            string api_key = "";

            try
            {
                var client = new HttpClient();

                // Request headers
                client.DefaultRequestHeaders.Add("Ocp-Apim-Subscription-Key", SUB_KEY);

                // endpoint url
                string uri_template = RedisHelper.ReadData_HASH(AppConfig.AUTH_API_KEY_URL_KEY, AppConfig.AUTH_API_KEY_URL_KEY_FIELD);
                Dictionary<string, string> paramss = new Dictionary<string, string>();
                paramss.Add("UUID", UUID);
                string uri = CoreHelpers.PopulateStringTemplate(uri_template, paramss);


                HttpResponseMessage response;


                // Request body
                byte[] byteData = Encoding.UTF8.GetBytes("");

                using (var content = new ByteArrayContent(byteData))
                {
                    content.Headers.ContentType = new MediaTypeHeaderValue("application/json");
                    response = client.PostAsync(uri, content).Result;

                    var http_IsSuccessStatusCode = response.IsSuccessStatusCode;
                    var http_Version = response.Version;
                    var http_status_code = response.StatusCode;
                    var resp_content = response.Content.ReadAsStringAsync().Result;

                    api_key = JObject.Parse(resp_content)["apiKey"].ToString();
                }

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
            
            return api_key;
        }
        #endregion


        #region ... 002: CreateAuthHeaderValue
        public string CreateAuthHeaderValue(string api_user, string api_key)
        {
            var encodedData = Convert.ToBase64String(Encoding.GetEncoding("ISO-8859-1").GetBytes(api_user + ":" + api_key));
            var auth_header = "Basic " + encodedData;
            return auth_header;
        }
        #endregion


        #region ... 003: MakeTokenRequest
        public string GetTranToken(LogMessage logmsg, string auth, string sub_key)
        {
            string access_token = "";
            try
            {
                var client = new HttpClient();

                // Request headers
                client.DefaultRequestHeaders.Add("Authorization", auth);
                client.DefaultRequestHeaders.Add("Ocp-Apim-Subscription-Key", sub_key);

                // endpoint url
                string uri = RedisHelper.ReadData_HASH(AppConfig.COLLECTIONS_TOKEN_URL_KEY, AppConfig.COLLECTIONS_TOKEN_URL_FIELD);
                HttpResponseMessage response;
                

                // Request body
                byte[] byteData = Encoding.UTF8.GetBytes("");

                using (var content = new ByteArrayContent(byteData))
                {
                    content.Headers.ContentType = new MediaTypeHeaderValue("application/json");
                    response = client.PostAsync(uri, content).Result;

                    var http_status_code = response.StatusCode;
                    var resp_content = response.Content.ReadAsStringAsync().Result;
                    access_token = JObject.Parse(resp_content)["access_token"].ToString();

                }
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
            


            return access_token;
        }
        #endregion


        #region ... 004: Token Disbursement
        public string GetTranTokenDisbursement(LogMessage logmsg, string auth, string sub_key)
        {
            string access_token = "";
            try
            {
                var client = new HttpClient();
                var queryString = string.Empty;

                // Request headers
                client.DefaultRequestHeaders.Add("Authorization", auth);
                client.DefaultRequestHeaders.Add("Ocp-Apim-Subscription-Key", sub_key);

                // endpoint url
                string uri = RedisHelper.ReadData_HASH(AppConfig.DISBURSEMENTS_TOKEN_URL_KEY, AppConfig.DISBURSEMENTS_TOKEN_URL_KEY_FIELD);
                HttpResponseMessage response;
                
                // Request body
                byte[] byteData = Encoding.UTF8.GetBytes("");

                using (var content = new ByteArrayContent(byteData))
                {
                    content.Headers.ContentType = new MediaTypeHeaderValue("application/json");
                    response = client.PostAsync(uri, content).Result;

                    var http_status_code = response.StatusCode;
                    var resp_content = response.Content.ReadAsStringAsync().Result;
                    access_token = JObject.Parse(resp_content)["access_token"].ToString();

                }

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
            

            return access_token;
        }

        #endregion


    }
}
