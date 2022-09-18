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
	public partial class ShareAcct : ContentPage
	{

        #region ... Class Variables
        CoreFunctions cf = new CoreFunctions();
        AES256.AES256 aes = new AES256.AES256();
        private DateTime LAST_ACTIVITY_TIME;

        private Wallet WALLET = new Wallet();
        private List<string> SESS = new List<string>();
        private dynamic CORE_CLIENT_DETAILS;
        private ShareAcctBasic SHR = new ShareAcctBasic();
        private List<ShareAcctBasic> SHR_LIST = new List<ShareAcctBasic>();
        #endregion

        #region ... 01: Class Constructor
        public ShareAcct()
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
        public ShareAcct(ArrayList datatransfered)
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
                SHR = (ShareAcctBasic)datatransfered[3];
                SHR_LIST = (List<ShareAcctBasic>)datatransfered[4];

                // ... DisplayShareAcctData
                DisplayShareAcctData();
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

        #region ... 06: DisplayShareAcctData
        private void DisplayShareAcctData()
        {
            lblTitleView.Text = "Share Acct: " + SHR.account_no;
            lblShareAcctNum.Text = SHR.account_no;
            lblCurrency.Text = SHR.currency_code;
            lblProduct.Text = SHR.product_name;
        }
        #endregion

        #region ... 07: Frame Handler
        private async void OnSavingsOpsFrameTapped(object sender, EventArgs e)
        {
            try
            {
                // ... execute timeout procedure
                ExecuteTimeout();

                // ... Prompt user to enter pin in order to make transaction
                var input = await UserDialogs.Instance.PromptAsync("Enter your access pin", "Authenticate", "Proceed", "Cancel", "Put Access Pin Here", InputType.NumericPassword);
                if (input.Ok)
                {
                    string SESS_ACCESSPIN = SESS[1];
                    string MemberPin = aes.DecryptCipheredText(SESS_ACCESSPIN);
                    string PromptPin = input.Text;
                    if (MemberPin.Equals(PromptPin))
                    {
                        var xamlframe = (Frame)sender;
                        string framename = xamlframe.ClassId.ToString();

                        // ... prepare items to transmit
                        ArrayList datatransfered = new ArrayList();
                        datatransfered.Add(WALLET);
                        datatransfered.Add(SESS);
                        datatransfered.Add(CORE_CLIENT_DETAILS);
                        datatransfered.Add(SHR);
                        datatransfered.Add(SHR_LIST);

                        #region ... commented route
                        if (framename.Equals("ShareAccountDetails"))
                        {
                            await Navigation.PushAsync(new ShareAcctDetails(datatransfered));
                        }
                        else if (framename.Equals("ShareBalance"))
                        {
                            await Navigation.PushAsync(new ShareAcctBalance(datatransfered));
                        }
                        else if (framename.Equals("ShareTransactions"))
                        {
                            await Navigation.PushAsync(new ShareAcctTransactions(datatransfered));
                        }
                        else if (framename.Equals("BuyShares"))
                        {
                            await Navigation.PushAsync(new ShareAcctBuyShares(datatransfered));
                        }
                        else
                        {
                            await DisplayAlert("Alert", "Unknown Option Selected", "OK");
                        }
                        #endregion
                    }
                    else
                    {
                        await DisplayAlert("Alert", "Invalid Pin Supplied", "OK");
                    }
                }
            }
            catch (Exception mm)
            {
                await DisplayAlert("Error 01", mm.Message, "OK");
            }
        }
        #endregion


    }
}