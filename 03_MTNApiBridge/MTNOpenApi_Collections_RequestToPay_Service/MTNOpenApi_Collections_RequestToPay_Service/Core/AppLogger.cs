using MTNOpenApi_Collections_RequestToPay_Service.Models;

namespace MTNOpenApi_Collections_RequestToPay_Service.Core
{
    public class AppLogger
    {

        #region ... F1: Log to File
        public void LogToFile(LogMessage lmsg, string log_level, string message_type, string message)
        {

            // ... processing time
            DateTime date = DateTime.Now;
            var shortDate2 = date.ToString("yyyyMMdd");
            char[] delim = { ' ' };
            string[] words = TimeZone.CurrentTimeZone.StandardName.Split(delim, StringSplitOptions.RemoveEmptyEntries);
            string abbrev = string.Empty;
            foreach (string chaStr in words)
            {
                abbrev += chaStr[0];
            }
            // ... end of time processing
            string LOG_FILE_EXXT = AppConfig.LOG_FILE_EXXT;
            string LOG_FILE_PRFX = AppConfig.LOG_FILE_PRFX;
            string base_path = AppConfig.LOG_BASE_PATH;

            string filename = LOG_FILE_PRFX + "_" + shortDate2 + "." + LOG_FILE_EXXT;
            string filepath = Path.Combine(base_path, filename);
            string timeIn = date.ToString("yyyy-MMM-dd HH:mm:ss.fff").ToString();
            string message_to_be_logged = "[" + timeIn + "] [" + lmsg.SERVICE_UNIT + "] [" + log_level + "] [" + message_type + "]: " + message;

            using (StreamWriter file = new StreamWriter(filepath, true))
            {
                file.WriteLine(message_to_be_logged);
            }


        }
        #endregion



        #region ... F2: Log File Seperator
        public void LogFileSeparator()
        {
            DateTime date = DateTime.Now;
            var shortDate2 = date.ToString("yyyyMMdd");

            string LOG_FILE_EXXT = AppConfig.LOG_FILE_EXXT;
            string LOG_FILE_PRFX = AppConfig.LOG_FILE_PRFX;
            string base_path = AppConfig.LOG_BASE_PATH;
            string filename = LOG_FILE_PRFX + "_" + shortDate2 + "." + LOG_FILE_EXXT;
            string filepath = Path.Combine(base_path, filename);
            string message_to_be_logged = "============================================================================================================================\r\n";

            using (StreamWriter file = new StreamWriter(filepath, true))
            {
                file.WriteLine(message_to_be_logged);
            }

        }
        #endregion



        #region ... F3: Log File Seperator
        public void LogFileSeparatorInternal()
        {
            DateTime date = DateTime.Now;
            var shortDate2 = date.ToString("yyyyMMdd");

            string LOG_FILE_EXXT = AppConfig.LOG_FILE_EXXT;
            string LOG_FILE_PRFX = AppConfig.LOG_FILE_PRFX;
            string base_path = AppConfig.LOG_BASE_PATH;
            string filename = LOG_FILE_PRFX + "_" + shortDate2 + "." + LOG_FILE_EXXT;
            string filepath = Path.Combine(base_path, filename);
            string message_to_be_logged = "----------------------------------------------------------------------------------------------------------------------------\r\n";

            using (StreamWriter file = new StreamWriter(filepath, true))
            {
                file.WriteLine(message_to_be_logged);
            }

        }
        #endregion


    }
}
