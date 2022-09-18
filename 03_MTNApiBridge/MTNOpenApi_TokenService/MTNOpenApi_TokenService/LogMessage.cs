using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace MTNOpenApi_TokenService
{
    internal class LogMessage
    {

        public string PROCESSING_REF { set; get; }      // ... from session
        public string LOG_LEVEL { set; get; }
        public string BILLER { set; get; }
        public string SERVICE { set; get; }
        public string CHANNEL { set; get; }             // ... from session
        public string SERVICE_UNIT { set; get; }        // ... from config
        public string CLASS { set; get; }
        public string FUNCTION { set; get; }
        public string RAW_LOG_MSG { set; get; }
        public string EXCPTN_MSG_01 { set; get; }
        public string EXCPTN_MSG_02 { set; get; }
        public string EXCPTN_MSG_03 { set; get; }


        #region ... 01: contructor
        public LogMessage()
        {
            BILLER = AppConfig.BILLER;
            SERVICE = AppConfig.SERVICE;
            SERVICE_UNIT = AppConfig.SERVICE_UNIT;
        }
        #endregion



    }
}
