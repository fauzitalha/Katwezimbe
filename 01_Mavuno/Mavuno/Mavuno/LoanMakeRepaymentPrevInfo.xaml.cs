using Acr.UserDialogs;
using Mavuno.core;
using Mavuno.db;
using Newtonsoft.Json;
using System;
using System.Collections;
using System.Collections.Generic;
using System.Linq;
using System.Threading;
using System.Threading.Tasks;

using Xamarin.Forms;
using Xamarin.Forms.Xaml;

namespace Mavuno
{
	[XamlCompilation(XamlCompilationOptions.Compile)]
	public partial class LoanMakeRepaymentPrevInfo : ContentPage
	{
        #region ... Class Variables
        CoreFunctions cf = new CoreFunctions();
        AES256.AES256 aes = new AES256.AES256();
        private DateTime LAST_ACTIVITY_TIME;

        private Wallet WALLET = new Wallet();
        private List<string> SESS = new List<string>();
        private dynamic CORE_CLIENT_DETAILS;
        private LoanAcctBasic LAB = new LoanAcctBasic();
        private List<LoanAcctBasic> LAB_LIST = new List<LoanAcctBasic>();
        private LoanRpymtRqst LRR = new LoanRpymtRqst();
        #endregion

        #region ... 01: Class Constructor
        public LoanMakeRepaymentPrevInfo()
        {
            try
            {
                InitializeComponent();

                // ... Set Timeout Value
                UpdateLastActivityTime();
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 01", mm.Message, "OK");
            }
        }
        #endregion

        #region ... 02: Class Constructor Overload
        public LoanMakeRepaymentPrevInfo(ArrayList datatransfered)
        {
            try
            {
                InitializeComponent();

                // ... Set Timeout Value
                UpdateLastActivityTime();

                // ... receive data
                WALLET = (Wallet)datatransfered[0];
                SESS = (List<string>)datatransfered[1];
                CORE_CLIENT_DETAILS = (dynamic)datatransfered[2];
                LAB = (LoanAcctBasic)datatransfered[3];
                LAB_LIST = (List<LoanAcctBasic>)datatransfered[4];
                LRR = (LoanRpymtRqst)datatransfered[5];

                // ... DisplayRqstInfoData
                DisplayRqstInfoData();
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 02", mm.Message, "OK");
            }
        }
        #endregion

        #region ... 03: SignOut
        private async void SignOutToolBarItem_Clicked(object sender, EventArgs e)
        {
            try
            {
                var ans = await DisplayAlert("Alert", "Do you want to sign out?", "Yes", "No");
                if (ans == true) //Success condition
                {
                    // ... clearing the Navigation stack
                    var existingPages = Navigation.NavigationStack.ToList();
                    existingPages.Reverse();
                    int x = 1;
                    foreach (var page in existingPages)
                    {
                        if (x == existingPages.Count)
                        {
                            break;
                        }
                        else
                        {
                            Navigation.RemovePage(page);
                        }
                        x++;
                    }

                    // ... navigate to the main page
                    await Navigation.PushAsync(new MainPage());
                }
                else
                {
                    //false conditon
                }
            }
            catch (Exception mm)
            {
                await DisplayAlert("Error 01", mm.Message, "OK");
            }
        }
        #endregion

        #region ... 04: UpdateLastActivityTime
        private void UpdateLastActivityTime()
        {
            try
            {
                LAST_ACTIVITY_TIME = DateTime.Now;
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 02", mm.Message, "OK");
            }
        }
        #endregion

        #region ... 05: ExecuteTimeout
        private void ExecuteTimeout()
        {
            try
            {
                DateTime CUR_TIME = DateTime.Now;
                int minutes = (CUR_TIME.Subtract(LAST_ACTIVITY_TIME)).Minutes;
                if (minutes >= Constants.MAX_IDLE_TIME)
                {
                    DisplayAlert("Timeout Alert", "You have been timed out due to inactivity for sometime", "OK");

                    // ... clearing the Navigation stack
                    var existingPages = Navigation.NavigationStack.ToList();
                    existingPages.Reverse();
                    int x = 1;
                    foreach (var page in existingPages)
                    {
                        if (x == existingPages.Count)
                        {
                            break;
                        }
                        else
                        {
                            Navigation.RemovePage(page);
                        }
                        x++;
                    }

                    // ... navigate to the main page
                    Navigation.PushAsync(new MainPage());
                }
                else
                {
                    // ... update last activity time
                    LAST_ACTIVITY_TIME = DateTime.Now;
                }
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 02", mm.Message, "OK");
            }
        }
        #endregion

        #region ... 06: DisplayRqstInfoData
        private void DisplayRqstInfoData()
        {
            lblRqstRef.Text = LRR.LOAN_RR_REF;
            lblRqstDate.Text = LRR.APPLN_SUBMISSION_DATE;
            lblChannelMethod.Text = LRR.CHANNEL + "/" + LRR.METHOD;

            LoanAcctBasic LLL = SearchArray(LAB_LIST, LRR.LOAN_ACCT_ID_TO_CREDIT);
            lblLoanAcctNum.Text = LLL.account_no + " - " + LLL.product_name;

            lblRpymtAmt.Text = LRR.RPYMT_AMT;
            lblReason.Text = LRR.REASON;
            lblRqstStatus.Text = LRR.RQST_STATUS;
            lblAddtRmks.Text = LRR.APPROVAL_RMKS;
        }
        #endregion

        #region ... 07: Search Array
        private LoanAcctBasic SearchArray(List<LoanAcctBasic> LAB_LIST, string LOAN_ACCT_ID)
        {
            LoanAcctBasic LLL = new LoanAcctBasic();
            for (int i = 0; i < LAB_LIST.Count; i++)
            {
                LoanAcctBasic LBBB = LAB_LIST[i];
                if (LBBB.loan_acct_id.Equals(LOAN_ACCT_ID))
                {
                    LLL = LBBB;
                    break;
                }
            }

            return LLL;
        }
        #endregion

    }
}