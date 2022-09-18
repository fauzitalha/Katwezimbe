using System;
using System.Collections.Generic;
using System.Text;

namespace Mavuno.db
{
    public class TransferRqst
    {
        public string RECORD_ID { get; set; }
        public string TRANSFER_REF { get; set; }
        public string CHANNEL { get; set; }
        public string METHOD { get; set; }
        public string CUST_ID { get; set; }
        public string SVGS_ACCT_ID_TO_DEBIT { get; set; }
        public string TRANSFER_AMT { get; set; }
        public string SVGS_ACCT_ID_TO_CREDIT { get; set; }
        public string REASON { get; set; }
        public string APPLN_SUBMISSION_DATE { get; set; }
        public string SVGS_HANDLER_USER_ID { get; set; }
        public string FIRST_HANDLED_ON { get; set; }
        public string FIRST_HANDLE_RMKS { get; set; }
        public string COMMITTEE_FLG { get; set; }
        public string COMMITTEE_HANDLER_USER_ID { get; set; }
        public string COMMITTEE_STATUS { get; set; }
        public string COMMITTEE_STATUS_DATE { get; set; }
        public string COMMITTEE_RMKS { get; set; }
        public string APPROVED_AMT { get; set; }
        public string APPROVED_BY { get; set; }
        public string APPROVAL_DATE { get; set; }
        public string APPROVAL_RMKS { get; set; }
        public string PROC_MODE { get; set; }
        public string PROC_BATCH_NO { get; set; }
        public string CORE_TXN_ID { get; set; }
        public string TRANSFER_APPLN_STATUS { get; set; }
    }
}
