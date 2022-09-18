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
	public partial class MoreChangePassword : ContentPage
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
        public MoreChangePassword()
        {
            try
            {
                InitializeComponent();
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 01", mm.Message, "OK");
            }
        }
        #endregion

        #region ... 02: Class Constructor Overload
        public MoreChangePassword(ArrayList datatransfered)
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

                // ... DisplayWalletData
                DisplayWalletData();
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

        #region ... 06: Initialize Screen Data
        private void DisplayWalletData()
        {
            lblChangeReason.Text = "Voluntary Pin Change";
            lblOrgName.Text = aes.DecryptCipheredText(WALLET.WALLET_ORGNAME);
            //lblWalletId.Text = aes.DecryptCipheredText(WALLET.WALLET_ID);
            lblPhoneNumber.Text = aes.DecryptCipheredText(WALLET.CUST_PHONE);
        }
        #endregion

        #region ... 07: Save Access Pin
        private void SaveAccessPin_Clicked(object sender, EventArgs e)
        {
            Device.BeginInvokeOnMainThread(async () =>
            {
                try
                {
                    using (UserDialogs.Instance.Loading("Working. Please wait ..."))
                    {
                        await Task.Delay(300);
                        await SaveAccessPinTask();          // ... the actual task
                    }
                }
                catch (Exception ex)
                {
                    var val = ex.Message;
                    await DisplayAlert("Alert", ex.Message, "OK");
                }
            });
        }
        #endregion

        #region ... 08: SaveAccessPinTask
        private async Task SaveAccessPinTask()
        {
            try
            {
                ArrayList validation_results = ValidateDataReceived();
                bool is_valid = (bool)validation_results[0];
                string val_mssg = (string)validation_results[1];
                if (!is_valid)
                {
                    await DisplayAlert("Alert", val_mssg, "OK");
                }
                else
                {
                    #region ... Prepare and assemble INNER request payload
                    string WalletId = aes.DecryptCipheredText(WALLET.WALLET_ID);
                    string OrgCode = aes.DecryptCipheredText(WALLET.WALLET_ORGCODE);
                    string CustId = aes.DecryptCipheredText(WALLET.CUST_ID);
                    string MemberPhone = aes.DecryptCipheredText(WALLET.CUST_PHONE);
                    string NewMemberPin = aes.EncryptRawText(txtAccessPin1.Text);

                    IDevice device = DependencyService.Get<IDevice>();
                    string DeviceSerial = device.GetIdentifier();

                    string data_to_sign = WalletId + "^" + OrgCode + "^" + CustId + "^" + MemberPhone + "^" + NewMemberPin + "^" + DeviceSerial;
                    string DigitalSignature = aes.EncryptRawText(data_to_sign);

                    // ... assemble inner request payload
                    Dictionary<string, string> RequestPayload = new Dictionary<string, string>
                    {
                        { "WalletId", WalletId },
                        { "OrgCode", OrgCode },
                        { "CustId", CustId },
                        { "MemberPhone", MemberPhone },
                        { "NewMemberPin", NewMemberPin },
                        { "DeviceSerial", DeviceSerial },
                        { "DigitalSignature", DigitalSignature }
                    };
                    #endregion

                    #region ... Prepare and assemble OUTER request payload
                    string RequestRef = Guid.NewGuid().ToString();
                    string ProcCode = "100004";
                    RequestMsg reqmsg = new RequestMsg();
                    reqmsg.RequestRef = RequestRef;
                    reqmsg.ProcCode = ProcCode;
                    reqmsg.RequestPayLoad = RequestPayload;
                    #endregion

                    // ... send request
                    string json_request_msg = JsonConvert.SerializeObject(reqmsg, Formatting.Indented);
                    //string[] respdetails = cf.PostToMoBilr(json_request_msg);
                    string[] respdetails = await cf.PostToMoBilrAsync(json_request_msg);
                    string resp_code = respdetails[0];
                    string resp_mssg = respdetails[1];
                    if (resp_code.Equals("ERR"))
                    {
                        await DisplayAlert("Alert", resp_mssg, "OK");
                    }
                    else
                    {
                        var json_resp = (dynamic)JsonConvert.DeserializeObject(resp_mssg);
                        string ClientReqRef = json_resp.ClientReqRef;
                        string RespCode = json_resp.RespCode;
                        string RespMessage = json_resp.RespMessage;
                        string RespProcCode = json_resp.RespProcCode;
                        dynamic RespPayload = json_resp.RespPayload;
                        if (RespCode.Equals("00"))
                        {
                            // ... step 01: verify if login was successful
                            string ACCT_STATUS_CODE = RespPayload.ACCT_STATUS_CODE;
                            string ACCT_STATUS_NAME = RespPayload.ACCT_STATUS_NAME;
                            string ACCT_STATUS_DETAILS = RespPayload.ACCT_STATUS_DETAILS;
                            if (ACCT_STATUS_CODE.Equals("OK"))
                            {
                                // ... Pin has been modified successfully
                                await DisplayAlert("Success", ACCT_STATUS_NAME + "\n" + ACCT_STATUS_DETAILS, "OK");
                                LogOutCurrentUser();
                            }
                            else
                            {
                                // ... Pin is declined
                                await DisplayAlert("Alert", ACCT_STATUS_NAME + "\n" + ACCT_STATUS_DETAILS, "OK");
                            }
                        }
                        else
                        {
                            await DisplayAlert("Alert", RespMessage, "OK");
                        }//..end..iff..else03
                    }//..end..iff..else02
                }//..end..iff..else01
            }//..end..try
            catch (Exception mm)
            {
                await DisplayAlert("Error 05", mm.Message, "OK");
            }
        }
        #endregion

        #region ... 09: ValidateDataReceived
        private ArrayList ValidateDataReceived()
        {
            bool is_valid = false;
            string val_mssg = "";
            ArrayList val_results = new ArrayList();
            try
            {
                bool cnt_PrevPin = (string.IsNullOrWhiteSpace(txtPrevAccessPin.Text)) ? false : true;
                bool cnt_Pin1 = (string.IsNullOrWhiteSpace(txtAccessPin1.Text)) ? false : true;
                bool cnt_Pin2 = (string.IsNullOrWhiteSpace(txtAccessPin2.Text)) ? false : true;

                if (cnt_PrevPin && cnt_Pin1 && cnt_Pin2)
                {
                    string op = SESS[1];
                    int OldPin1 = int.Parse(aes.DecryptCipheredText(op));
                    int OldPin2 = int.Parse(txtPrevAccessPin.Text);
                    if (OldPin1 == OldPin2)
                    {
                        int Pin1 = int.Parse(txtAccessPin1.Text);
                        int Pin2 = int.Parse(txtAccessPin2.Text);
                        if (Pin1 == Pin2)
                        {
                            is_valid = true;
                            val_mssg = "data is good";
                        }
                        else
                        {
                            is_valid = false;
                            val_mssg = "Supplied new access pins are not matching";
                        }
                    }
                    else
                    {
                        is_valid = false;
                        val_mssg = "Previous pin supplied is invalid";
                    }
                }
                else
                {
                    is_valid = false;
                    val_mssg = "Enter Missing Data. Cannot Proceed";
                }

                // ... add the data
                val_results.Add(is_valid);
                val_results.Add(val_mssg);
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 06", mm.Message, "OK");
            }

            return val_results;
        }
        #endregion

        #region ... 10: LogOutCurrentUser
        private void LogOutCurrentUser()
        {
            try
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
                Navigation.PushAsync(new MainPage());
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 02", mm.Message, "OK");
            }
        }
        #endregion

    }
}