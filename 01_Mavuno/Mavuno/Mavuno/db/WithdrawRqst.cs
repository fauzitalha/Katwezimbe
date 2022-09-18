using System;
using System.Collections.Generic;
using System.Text;

namespace Mavuno.db
{
    public class WithdrawRqst
    {

        public string RECORD_ID { get; set; }
        public string WITHDRAW_REF { get; set; }
        public string CHANNEL { get; set; }
        public string METHOD { get; set; }
        public string CUST_ID { get; set; }
        public string SVGS_ACCT_ID_TO_DEBIT { get; set; }
        public string RQSTD_AMT { get; set; }
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
        public string CUST_FIN_INST_ID { get; set; }
        public string PROC_MODE { get; set; }
        public string PROC_BATCH_NO { get; set; }
        public string CORE_TXN_ID { get; set; }
        public string SVGS_APPLN_STATUS { get; set; }

        #region ... comment
        /*
        "RECORD_ID": "4",
        "WITHDRAW_REF": "SWA00000000000000004",
        "CHANNEL": "MOBAPP",
        "METHOD": "MOMO",
        "CUST_ID": "M000032",
        "SVGS_ACCT_ID_TO_DEBIT": "7",
        "RQSTD_AMT": "25400",
        "REASON": "This is MoMo withdraw via MoBApp",
        "APPLN_SUBMISSION_DATE": "2020-06-26 23:27:52",
        "SVGS_HANDLER_USER_ID": null,
        "FIRST_HANDLED_ON": null,
        "FIRST_HANDLE_RMKS": null,
        "COMMITTEE_FLG": null,
        "COMMITTEE_HANDLER_USER_ID": null,
        "COMMITTEE_STATUS": null,
        "COMMITTEE_STATUS_DATE": null,
        "COMMITTEE_RMKS": null,
        "APPROVED_AMT": null,
        "APPROVED_BY": null,
        "APPROVAL_DATE": null,
        "APPROVAL_RMKS": null,
        "CUST_FIN_INST_ID": "256702445779",
        "PROC_MODE": null,
        "PROC_BATCH_NO": null,
        "CORE_TXN_ID": null,
        "SVGS_APPLN_STATUS": "PENDING"
                    */

        #endregion

    }
}
