using SQLite;
using System;
using System.Collections.Generic;
using System.Text;

namespace Mavuno.db
{
    public class WalletAdditionProgress
    {
        [PrimaryKey, AutoIncrement]
        public int ID { get; set; }
        public string WALLET_ID { get; set; }
        public string ADDITION_PROGRESS { get; set; }
    }
}
