using System;
using System.Collections.Generic;
using System.Text;

namespace Mavuno.db
{
    public class WalletListView
    {
        public int ID { get; set; }
        public string WALLET_ID { get; set; }
        public string WALLET_ORGCODE { get; set; }
        public string WALLET_ORGNAME { get; set; }
        public string CUST_ID { get; set; }
        public string CUST_CORE_ID { get; set; }
        public string APPLN_REF_MOB { get; set; }
        public string CUST_PHONE { get; set; }
        public string IMAGE_URL { get; set; }
    }
}
