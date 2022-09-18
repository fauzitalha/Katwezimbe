using Acr.UserDialogs;
using Mavuno.core;
using Mavuno.db;
using Newtonsoft.Json;
using System;
using System.Collections;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

using Xamarin.Forms;
using Xamarin.Forms.Xaml;

namespace Mavuno
{
    [XamlCompilation(XamlCompilationOptions.Compile)]
    public partial class HomePage : ContentPage
    {

        #region ... Class Variables
        CoreFunctions cf = new CoreFunctions();
        AES256.AES256 aes = new AES256.AES256();
        private DateTime LAST_ACTIVITY_TIME;
        private Wallet WALLET = new Wallet();
        private List<string> SESS = new List<string>();
        private dynamic CORE_CLIENT_DETAILS;
        #endregion

        #region ... 01: Class Constructor
        public HomePage()
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
        public HomePage(ArrayList datatransfered)
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

                // ... DisplayClientData
                DisplayClientData();
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 02", mm.Message, "OK");
            }
        }
        #endregion

        #region ... 03: DisplayClientData
        private void DisplayClientData()
        {
            string cust_name = CORE_CLIENT_DETAILS.displayName;
            lblCustName.Text = cust_name;
            lblOrgName.Text = aes.DecryptCipheredText(WALLET.WALLET_ORGNAME);
        }
        #endregion

        #region ... 04: SignOut
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
                        if (x==existingPages.Count())
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

        #region ... 05: Frame Handler
        private void OnHomeFrameTapped(object sender, EventArgs e)
        {
            try
            {
                // ... execute timeout procedure
                ExecuteTimeout();

                var xamlframe = (Frame)sender;
                string framename = xamlframe.ClassId.ToString();

                // ... prepare items to transmit
                ArrayList datatransfered = new ArrayList();
                datatransfered.Add(WALLET);
                datatransfered.Add(SESS);
                datatransfered.Add(CORE_CLIENT_DETAILS);

                if (framename.Equals("MySavingsFrame"))
                {
                    Navigation.PushAsync(new MySavings(datatransfered));
                }
                else if (framename.Equals("MyLoansFrame"))
                {
                    Navigation.PushAsync(new MyLoans(datatransfered));
                }
                else if (framename.Equals("MySharesFrame"))
                {
                    Navigation.PushAsync(new MyShares(datatransfered));
                }
                else if (framename.Equals("MyDetailsFrame"))
                {
                    Navigation.PushAsync(new MyDetails(datatransfered));
                }
                else if (framename.Equals("GetHelpFrame"))
                {
                    Navigation.PushAsync(new GetHelp(datatransfered));
                }
                else if (framename.Equals("MoreFrame"))
                {
                    Navigation.PushAsync(new More(datatransfered));
                }
                else
                {
                    DisplayAlert("Alert", "Unknown Option Selected", "OK");
                }
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 01", mm.Message, "OK");
            }
        }
        #endregion

        #region ... 06: UpdateLastActivityTime
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

        #region ... 07: ExecuteTimeout
        private void ExecuteTimeout()
        {
            try
            {
                DateTime CUR_TIME = DateTime.Now;
                int minutes = (CUR_TIME.Subtract(LAST_ACTIVITY_TIME)).Minutes;
                if (minutes >= Constants.MAX_IDLE_TIME)
                {
                    DisplayAlert("Timeout Notification", "You have been timed out due to inactivity", "OK");

                    // ... clearing the Navigation stack
                    var existingPages = Navigation.NavigationStack.ToList();
                    existingPages.Reverse();
                    int x = 1;
                    foreach (var page in existingPages)
                    {
                        if (x == existingPages.Count())
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


    }
}