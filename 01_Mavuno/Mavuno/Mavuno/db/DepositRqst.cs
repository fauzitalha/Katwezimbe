using System;
using System.Collections.Generic;
using System.Text;

namespace Mavuno.db
{
    public class DepositRqst
    {
        public string RECORD_ID { get; set; }
        public string DEPOSIT_REF { get; set; }
        public string CHANNEL { get; set; }
        public string METHOD { get; set; }
        public string CUST_ID { get; set; }
        public string SVGS_ACCT_ID_TO_CREDIT { get; set; }
        public string MSISDN { get; set; }
        public string AMOUNT_BANKED { get; set; }
        public string REASON { get; set; }
        public string BANK_ID { get; set; }
        public string BANK_INST_ACCT_NO { get; set; }
        public string BANK_INST_ACCT_NAME { get; set; }
        public string BANK_RECEIPT_REF { get; set; }
        public string BANK_RECEIPT_ATTCHMT { get; set; }
        public string RQST_DATE { get; set; }
        public string HANDLED_BY { get; set; }
        public string HANDLED_ON { get; set; }
        public string HANDLER_RMKS { get; set; }
        public string APPRVD_BY { get; set; }
        public string APPRVL_DATE { get; set; }
        public string APPRVL_RMKS { get; set; }
        public string CORE_TXN_ID { get; set; }
        public string MOMO_PROC_STATUS { get; set; }
        public string MOMO_TRAN_REF { get; set; }
        public string MOMO_TELCO_TRAN_REF { get; set; }
        public string RQST_STATUS { get; set; }


        #region ... comment
        /*
        "RECORD_ID": "3",
        "DEPOSIT_REF": "SDA00000000000000003",
        "CHANNEL": "MOBAPP",
        "METHOD": "MOMO",
        "CUST_ID": "M000032",
        "SVGS_ACCT_ID_TO_CREDIT": "5",
        "MSISDN": "256702445779",
        "AMOUNT_BANKED": "54000",
        "REASON": "Transfer request via MoBApp",
        "BANK_ID": "KTL000003",
        "BANK_INST_ACCT_NO": "1211223342",
        "BANK_INST_ACCT_NAME": "WVI Union",
        "BANK_RECEIPT_REF": "S334RRT677",
        "BANK_RECEIPT_ATTCHMT": "SDA00000000000000003.jpg",
        "RQST_DATE": "2020-06-27 14:53:02",
        "HANDLED_BY": null,
        "HANDLED_ON": null,
        "HANDLER_RMKS": null,
        "APPRVD_BY": null,
        "APPRVL_DATE": null,
        "APPRVL_RMKS": null,
        "CORE_TXN_ID": null,
        "MOMO_PROC_STATUS": null,
        "MOMO_TRAN_REF": null,
        "MOMO_TELCO_TRAN_REF": null,
        "RQST_STATUS": "PENDING"
        */
        #endregion
    }
}
