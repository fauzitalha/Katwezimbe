using System;
using System.Collections.Generic;
using System.Text;

namespace Mavuno.db
{
    public class LoanRpymtSchedule
    {
        public string INSTALLMENT { get; set; }
        public string DAYS { get; set; }
        public string DATE { get; set; }
        public string PAID_DATE { get; set; }
        public string PRINCIPAL_DUE { get; set; }
        public string BALANCE_OF_LOAN { get; set; }
        public string INTEREST { get; set; }
        public string FEES { get; set; }
        public string PENALTIES { get; set; }
        public string DUE { get; set; }
        public string PAID { get; set; }
        public string IN_ADVANCE { get; set; }
        public string LATE { get; set; }
        public string OUTSTANDING { get; set; }
        public string INSTLMT_STATUS { get; set; }

        public string XX_INSTALL_NUM { get; set; }
        public string XX_INSTALL_AMT { get; set; }
        public string XX_PERCENT_PAID { get; set; }

    }
}
