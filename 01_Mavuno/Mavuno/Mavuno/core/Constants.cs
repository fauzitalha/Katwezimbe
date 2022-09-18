using System;
using System.Collections.Generic;
using System.Text;

namespace Mavuno.core
{
    public class Constants
    {
        private static string dev_year = "2020";
        private static string current_year = DateTime.Now.ToString("y");

        // ... App details
        public static string APP_NAME = "Mavuno";
        public static string APP_VERSION = "Version: 1.0.0";
        public static string APP_BUILD = "Build: 00001";
        public static string APP_OWNER = "Powered by The-SLANK-Initiative";
        public static string APP_COPY = dev_year + " - " + current_year + " " + APP_NAME;

        // ... Channel
        public static string CHANNEL = "MOBAPP";

        // ... External Mobilr Endpoints
        //public static string TRAN_URI = "http://10.0.3.2/mobilr/api/v1/tran-interface.php";
        //public static string DOCC_URI = "http://10.0.3.2/mobilr/api/v1/file-interface.php";
        public static string TRAN_URI = "https://mobilr.slankinit.com/api/v1/tran-interface.php";
        public static string DOCC_URI = "https://mobilr.slankinit.com/api/v1/file-interface.php";

        // ... Max idle time (Minutes)
        public static int MAX_IDLE_TIME = 5;

        // ... Withdrawal Tran Type
        public static List<string> WITHDRAW_TRAN_TYPE_LIST = new List<string>() {
            "Withdraw to Mobile Money",
            "Withdraw to Bank Account"
        };

        // ... Deposit Tran Type
        public static List<string> DEPOSIT_TRAN_TYPE_LIST = new List<string>() {
            "Deposit with Mobile Money",
            "Deposit done in Bank/Other Partner"
        };

        // ... File upload rules
        public static List<string> ACCEPTABLE_FILE_TYPES = new List<string>() {
            ".png",
            ".jpeg",
            ".jpg",
            ".pdf"
        };

        // ... Acceptable file size
        public static double MAX_FILE_SIZE = 5000000;


        // ... Loan Repayment Method
        public static string[] LOAN_RPYMT_METHOD_LIST = {
            "Repay using my savings account",
            "Repay using my Mobile Money"
        };


        // ... Buy Shares Method
        public static string[] BUY_SHARES_METHOD_LIST = {
            "Buy using my savings account",
            "Buy using my Mobile Money"
        };


        // ... More Option
        public static string[] MORE_MENU_LIST = {
            "Change Wallet Access Pin",
            "About App"
        };


    }
}
