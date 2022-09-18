using System;
using System.Collections.Generic;
using System.Text;

namespace Mavuno.db
{
    public class GetHelpRequest
    {
        public string RECORD_ID {get; set;}
        public string ISSUE_DATE {get; set;}
        public string INIT_CHANNEL {get; set;}
        public string CUST_ID {get; set;}
        public string CUST_PHONE {get; set;}
        public string ENTERRED_PHONE {get; set;}
        public string ENTERRED_EMAIL {get; set;}
        public string ENTERRED_SUBJECT {get; set;}
        public string ENTERRED_ISSUE_DESC {get; set;}
        public string INIT_DEVICE_ID {get; set;}
        public string HANDLER_USER_ID {get; set;}
        public string HANDLER_DATE {get; set;}
        public string HANDLER_RMKS {get; set;}
        public string ISSUE_STATUS {get; set;}

        public string XX_RECORD_ID { get; set; }

    }
}
