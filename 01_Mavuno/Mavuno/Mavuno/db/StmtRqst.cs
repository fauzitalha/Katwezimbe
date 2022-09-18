using System;
using System.Collections.Generic;
using System.Text;

namespace Mavuno.db
{
    public class StmtRqst
    {
        public string RECORD_ID { get; set; }
        public string INIT_CHANNEL { get; set; }
        public string CUST_ID { get; set; }
        public string SVGS_ACCT_ID { get; set; }
        public string START_DATE { get; set; }
        public string END_DATE { get; set; }
        public string DELIVERY_EMAIL { get; set; }
        public string DEVICE_SERIAL { get; set; }
        public string RQST_DATE { get; set; }
        public string HANDLED_BY { get; set; }
        public string HANDLED_ON { get; set; }
        public string HANDLER_RMKS { get; set; }
        public string APPRVD_BY { get; set; }
        public string APPRVL_DATE { get; set; }
        public string APPRVL_RMKS { get; set; }
        public string RQST_STATUS { get; set; }

    }
}
