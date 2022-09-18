using System;
using System.Collections.Generic;
using System.Text;

namespace Mavuno.db
{
    public class BuyShareRqst
    {
        public string RECORD_ID {set; get;}
        public string SHARES_APPLN_REF {set; get;}
        public string CHANNEL {set; get;}
        public string METHOD {set; get;}
        public string CUST_ID {set; get;}
        public string SVGS_ACCT_ID_TO_DEBIT {set; get;}
        public string MSISDN {set; get;}
        public string SHARES_REQUESTED {set; get;}
        public string SHARES_ACCT_ID_TO_CREDIT {set; get;}
        public string REASON {set; get;}
        public string APPLN_SUBMISSION_DATE {set; get;}
        public string SHARES_HANDLER_USER_ID {set; get;}
        public string FIRST_HANDLED_ON {set; get;}
        public string FIRST_HANDLE_RMKS {set; get;}
        public string APPROVED_AMT {set; get;}
        public string APPROVED_BY {set; get;}
        public string APPROVAL_DATE {set; get;}
        public string APPROVAL_RMKS {set; get;}
        public string CORE_TXN_ID {set; get;}
        public string MOMO_PROC_STATUS {set; get;}
        public string MOMO_PROC_REF {set; get;}
        public string MOMO_TELCO_REF {set; get;}
        public string SHARES_APPLN_STATUS {set; get;}
    }
}
