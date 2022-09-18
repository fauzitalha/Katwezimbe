using Acr.UserDialogs;
using Mavuno.core;
using Mavuno.db;
using Newtonsoft.Json;
using System;
using System.Collections;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

using Xamarin.Forms;
using Xamarin.Forms.Xaml;

namespace Mavuno
{
	[XamlCompilation(XamlCompilationOptions.Compile)]
	public partial class LoanRpymtScheduleDetails : ContentPage
	{
        #region ... Class Variables
        CoreFunctions cf = new CoreFunctions();
        AES256.AES256 aes = new AES256.AES256();
        private DateTime LAST_ACTIVITY_TIME;

        private Wallet WALLET = new Wallet();
        private List<string> SESS = new List<string>();
        private dynamic CORE_CLIENT_DETAILS;
        private LoanAcctBasic LAB = new LoanAcctBasic();
        private LoanRpymtSchedule RPS = new LoanRpymtSchedule();
        #endregion

        #region ... 01: Class Constructor
        public LoanRpymtScheduleDetails()
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
        public LoanRpymtScheduleDetails(ArrayList datatransfered)
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
                RPS = (LoanRpymtSchedule)datatransfered[4];

                // ... DisplayLoanAcctTranDetails
                lblTitleView.Text = "Rpymt Schedule: " + LAB.account_no;

                DisplayLoanAcctRpymtSchedule();
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

        #region ... 06: DisplayLoanAcctRpymtSchedule
        protected void DisplayLoanAcctRpymtSchedule()
        {
            lblInstNum.Text = RPS.XX_INSTALL_NUM;
            lblInstDays.Text = RPS.DAYS;
            lblInstDate.Text = cf.HumanDate(RPS.DATE);
            lblInstPymtDate.Text = cf.HumanDate(RPS.PAID_DATE);
            lblInstDue.Text = double.Parse(RPS.DUE).ToString("#,##0.00");
            lblInstPrincPort.Text = double.Parse(RPS.PRINCIPAL_DUE).ToString("#,##0.00");
            lblInstIntPort.Text = double.Parse(RPS.INTEREST).ToString("#,##0.00");
            lblInstFeesPort.Text = double.Parse(RPS.FEES).ToString("#,##0.00");
            lblInstPenPort.Text = double.Parse(RPS.PENALTIES).ToString("#,##0.00");
            lblInstPaid.Text = double.Parse(RPS.PAID).ToString("#,##0.00");
            lblInstPercentPaid.Text = RPS.XX_PERCENT_PAID;
            lblInstPaidInAdvance.Text = double.Parse(RPS.IN_ADVANCE).ToString("#,##0.00");
            lblInstPaidLate.Text = double.Parse(RPS.LATE).ToString("#,##0.00");
            lblInstOut.Text = double.Parse(RPS.OUTSTANDING).ToString("#,##0.00");
            lblInstStatus.Text = RPS.INSTLMT_STATUS;
        }
        #endregion
    }
}