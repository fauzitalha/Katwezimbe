using Mavuno.core;
using Mavuno.db;
using System;
using System.Collections;
using System.Collections.Generic;
using System.Linq;

using Xamarin.Forms;
using Xamarin.Forms.Xaml;

namespace Mavuno
{
	[XamlCompilation(XamlCompilationOptions.Compile)]
	public partial class SvgsAcctDepositPrevInfo : ContentPage
	{
        #region ... Class Variables
        CoreFunctions cf = new CoreFunctions();
        AES256.AES256 aes = new AES256.AES256();
        private DateTime LAST_ACTIVITY_TIME;

        private Wallet WALLET = new Wallet();
        private List<string> SESS = new List<string>();
        private dynamic CORE_CLIENT_DETAILS;
        private SavingsAcctBasic SAB = new SavingsAcctBasic();
        private DepositRqst DEPOSIT_RQST = new DepositRqst();
        #endregion

        #region ... 01: Class Constructor
        public SvgsAcctDepositPrevInfo()
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
        public SvgsAcctDepositPrevInfo(ArrayList datatransfered)
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
                SAB = (SavingsAcctBasic)datatransfered[3];
                DEPOSIT_RQST = (DepositRqst)datatransfered[4];

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
            lblRqstRef.Text = DEPOSIT_RQST.DEPOSIT_REF;
            lblRqstDate.Text = DEPOSIT_RQST.RQST_DATE;

            lblChannelMethod.Text = DEPOSIT_RQST.CHANNEL + "/" + DEPOSIT_RQST.METHOD;
            string DPST_INSTRUMENT = (DEPOSIT_RQST.METHOD.Equals("MOMO")) ? DEPOSIT_RQST.MSISDN : DEPOSIT_RQST.BANK_INST_ACCT_NO + " (" + DEPOSIT_RQST.BANK_INST_ACCT_NAME + ")";
            lblDpstMadeVia.Text = DEPOSIT_RQST.METHOD + "/" + DPST_INSTRUMENT;

            lblAmtDeposited.Text = DEPOSIT_RQST.AMOUNT_BANKED;
            lblReason.Text = DEPOSIT_RQST.REASON;
            lblRqstStatus.Text = DEPOSIT_RQST.RQST_STATUS;
            lblAddtRmks.Text = DEPOSIT_RQST.APPRVL_RMKS;

        }
        #endregion
    }
}