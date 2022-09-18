using System;
using System.Collections.Generic;
using System.Text;

namespace Mavuno.db
{
    public class LoanRpymtRqst
    {
        public string RECORD_ID { get; set; }
        public string LOAN_RR_REF { get; set; }
        public string CHANNEL { get; set; }
        public string METHOD { get; set; }
        public string CUST_ID { get; set; }
        public string MSISDN { get; set; }
        public string SVGS_ACCT_ID_TO_DEBIT { get; set; }
        public string RPYMT_AMT { get; set; }
        public string LOAN_ACCT_ID_TO_CREDIT { get; set; }
        public string REASON { get; set; }
        public string APPLN_SUBMISSION_DATE { get; set; }
        public string HANDLER_USER_ID {get; set;}
        public string HANDLED_ON {get; set;}
        public string HANDLE_RMKS {get; set;}
        public string APPROVED_AMT {get; set;}
        public string APPROVED_BY {get; set;}
        public string APPROVAL_DATE {get; set;}
        public string APPROVAL_RMKS {get; set;}
        public string PROC_MODE {get; set;}
        public string PROC_BATCH_NO {get; set;}
        public string CORE_TXN_ID {get; set;}
        public string MOMO_PROC_STATUS {get; set;}
        public string MOMO_TRAN_REF {get; set;}
        public string MOMO_TELCO_TRAN_REF {get; set;}
        public string RQST_STATUS { get; set; }

    }
}
