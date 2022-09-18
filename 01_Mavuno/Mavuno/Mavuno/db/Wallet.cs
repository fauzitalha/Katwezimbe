using SQLite;
using System;
using System.Collections.Generic;
using System.Text;

namespace Mavuno.db
{
    public class Wallet
    {
        [PrimaryKey, AutoIncrement]
        public int ID { get; set; }
        public string WALLET_ID { get; set; }
        public string WALLET_ORGCODE { get; set; }
        public string WALLET_ORGNAME { get; set; }
        public string CUST_ID { get; set; }
        public string CUST_CORE_ID { get; set; }
        public string APPLN_REF_MOB { get; set; }
        public string CUST_PHONE { get; set; }

        #region ... commented model sample
        /*
        "WALLET_ID": "B000004-M000032-W000005",
        "WALLET_ORGCODE": "B000004",
        "WALLET_ORGNAME": "Muntuyera SACCO",
        "CUST_ID": "M000032",
        "CUST_CORE_ID": "25",
        "APPLN_REF_MOB": "MB00000059",
        "CUST_PHONE": "256702445779"
        */
        #endregion

    }
}
