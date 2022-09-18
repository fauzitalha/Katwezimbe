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
	public partial class ShareAcctBuySharesNewSav2 : ContentPage
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
        private SavingsAcctBasic SAB_ACCT = new SavingsAcctBasic();
        private string SHARE_CURRENT_MARKET_PRICE;
        private string SHARE_CURRENT_MARKET_PRICE_DISPLAY;
        #endregion

        #region ... 01: Class Constructor
        public ShareAcctBuySharesNewSav2()
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
        public ShareAcctBuySharesNewSav2(ArrayList datatransfered)
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
                SAB_ACCT = (SavingsAcctBasic)datatransfered[5];

                // ... DisplayShareAcctData
                GetShareDetails();
                lblTitleView.Text = "Buy Shares: " + SHR.account_no;
                lblSvgAccountNum.Text = SAB_ACCT.account_no + " - " + SAB_ACCT.product_name;
                lblShareAcctNum.Text = SHR.account_no + " - " + SHR.product_name;
                
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

        #region ... 06: SubmitBuySharesAppln
        private void BtnSubmit_Clicked(object sender, EventArgs e)
        {
            Device.BeginInvokeOnMainThread(async () =>
            {
                try
                {
                    using (UserDialogs.Instance.Loading("Submitting Buy Shares Request"))
                    {
                        await Task.Delay(300);
                        await SubmitBuySharesAppln();          // ... the actual task
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

        #region ... 07: SubmitBuySharesAppln_000
        private async Task SubmitBuySharesAppln()
        {
            try
            {
                ArrayList validation_results = await ValidateDataReceived();
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
                    string DrSavingsAcctId = SAB_ACCT.svgs_acct_id;
                    string SharesToBuy = txtShareNum.Text;
                    string Channel = Constants.CHANNEL;
                    string Method = "DIRECT-SVNGS-TRANSFER";
                    string ShareAcctId = SHR.share_acct_id;
                    string Narration = txtNarration.Text;
                    string MemberPhone = aes.DecryptCipheredText(WALLET.CUST_PHONE);

                    IDevice device = DependencyService.Get<IDevice>();
                    string DeviceSerial = device.GetIdentifier();

                    string data_to_sign = WalletId + "^" + OrgCode + "^" + CustId + "^" + DrSavingsAcctId + "^" + SharesToBuy + "^" + Channel + "^" + Method + "^" + ShareAcctId + "^" + Narration + "^" + MemberPhone + "^" + DeviceSerial;
                    string DigitalSignature = aes.EncryptRawText(data_to_sign);

                    // ... assemble inner request payload
                    Dictionary<string, string> RequestPayload = new Dictionary<string, string>
                    {
                        { "WalletId", WalletId },
                        { "OrgCode", OrgCode },
                        { "CustId", CustId },
                        { "DrSavingsAcctId", DrSavingsAcctId },
                        { "SharesToBuy", SharesToBuy },
                        { "Channel", Channel },
                        { "Method", Method },
                        { "ShareAcctId", ShareAcctId },
                        { "Narration", Narration },
                        { "MemberPhone", MemberPhone },
                        { "DeviceSerial", DeviceSerial },
                        { "DigitalSignature", DigitalSignature }
                    };
                    #endregion

                    #region ... Prepare and assemble OUTER request payload
                    string RequestRef = Guid.NewGuid().ToString();
                    string ProcCode = "400004";
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
                            string REF = RespPayload.ToString();
                            await DisplayAlert("Success", "Buy shares request has been received successfully.\nTRAN_REF: " + REF, "OK");

                            // ... prepare items to transmit
                            ArrayList datatransfered = new ArrayList();
                            datatransfered.Add(WALLET);
                            datatransfered.Add(SESS);
                            datatransfered.Add(CORE_CLIENT_DETAILS);
                            datatransfered.Add(SHR);
                            datatransfered.Add(SHR_LIST);

                            await Navigation.PushAsync(new ShareAcct(datatransfered));
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

        #region ... 08: ValidateDataReceived
        private async Task<ArrayList> ValidateDataReceived()
        {
            bool is_valid = false;
            string val_mssg = "";
            ArrayList val_results = new ArrayList();
            try
            {
                bool cnt_ShareNum = (string.IsNullOrWhiteSpace(txtShareNum.Text)) ? false : true;
                bool cnt_Narration = (string.IsNullOrWhiteSpace(txtNarration.Text)) ? false : true;

                if (cnt_ShareNum && cnt_Narration)
                {
                    bool hasEnoughBalance = await VerifyAcctBal();
                    if (hasEnoughBalance)
                    {
                        is_valid = true;
                        val_mssg = "data is good";
                    }
                    else
                    {
                        is_valid = false;
                        val_mssg = "Amount to Repay is greater than transactable balance on savings account.\nACCT: " + lblSvgAccountNum.Text;
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
                await DisplayAlert("Error 06", mm.Message, "OK");
            }

            return val_results;
        }
        #endregion

        #region ... 09: VerifyAcctBal
        private async Task<bool> VerifyAcctBal()
        {
            bool hasEnoughBalance = false;
            try
            {
                #region ... Prepare and assemble INNER request payload
                string WalletId = aes.DecryptCipheredText(WALLET.WALLET_ID);
                string OrgCode = aes.DecryptCipheredText(WALLET.WALLET_ORGCODE);
                string CustId = aes.DecryptCipheredText(WALLET.CUST_ID);
                string SavingsAcctId = SAB_ACCT.svgs_acct_id;
                string MemberPhone = aes.DecryptCipheredText(WALLET.CUST_PHONE);

                IDevice device = DependencyService.Get<IDevice>();
                string DeviceSerial = device.GetIdentifier();

                string data_to_sign = WalletId + "^" + OrgCode + "^" + CustId + "^" + SavingsAcctId + "^" + MemberPhone + "^" + DeviceSerial;
                string DigitalSignature = aes.EncryptRawText(data_to_sign);

                // ... assemble inner request payload
                Dictionary<string, string> RequestPayload = new Dictionary<string, string>
                    {
                        { "WalletId", WalletId },
                        { "OrgCode", OrgCode },
                        { "CustId", CustId },
                        { "MemberPhone", MemberPhone },
                        { "SavingsAcctId", SavingsAcctId },
                        { "DeviceSerial", DeviceSerial },
                        { "DigitalSignature", DigitalSignature }
                    };
                #endregion

                #region ... Prepare and assemble OUTER request payload
                string RequestRef = Guid.NewGuid().ToString();
                string ProcCode = "200002";
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
                    hasEnoughBalance = false;
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
                        string ClientRequestExtID = RespPayload.ClientRequestExtID;
                        string RoutingProcCode = RespPayload.RoutingProcCode;
                        string RoutingProcMessage = RespPayload.RoutingProcMessage;
                        string RoutingProcRef = RespPayload.RoutingProcRef;
                        string MifosHttpRespcode = RespPayload.MifosHttpRespcode;
                        dynamic MifosRespDetails = RespPayload.MifosRespDetails;
                        if (MifosHttpRespcode.StartsWith("2"))
                        {
                            dynamic acct_data_list = MifosRespDetails.data;
                            string AccountNum = MifosRespDetails.accountNo;
                            string AcctBal = MifosRespDetails.summary.accountBalance;
                            string MinimumBalance = (MifosRespDetails.minRequiredBalance == null) ? "0" : MifosRespDetails.minRequiredBalance;
                            double TransactableBal = (double.Parse(AcctBal) - double.Parse(MinimumBalance));

                            double ShareNum = double.Parse(txtShareNum.Text);
                            double ShareMktPrice = double.Parse(SHARE_CURRENT_MARKET_PRICE);
                            double ShareValue = (ShareMktPrice * ShareNum);

                            if (TransactableBal > ShareValue)
                            {
                                hasEnoughBalance = true;
                            }
                        }
                        else
                        {
                            hasEnoughBalance = false;
                        }//...end..iff03
                    }
                    else
                    {
                        hasEnoughBalance = false;
                    }//..end..iff..else03
                }//..end..iff..else02
            }//..end..try
            catch (Exception)
            {
                hasEnoughBalance = false;
            }

            return hasEnoughBalance;
        }

        #endregion

        #region ... 10: GetShareDetails
        private void GetShareDetails()
        {
            Device.BeginInvokeOnMainThread(async () =>
            {
                try
                {
                    using (UserDialogs.Instance.Loading("Fetch Share Account Details"))
                    {
                        await Task.Delay(300);
                        await FetchShareAcctDetails();          // ... the actual task
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

        #region ... 11: FetchShareAcctDetails
        private async Task FetchShareAcctDetails()
        {
            try
            {
                #region ... Prepare and assemble INNER request payload
                string WalletId = aes.DecryptCipheredText(WALLET.WALLET_ID);
                string OrgCode = aes.DecryptCipheredText(WALLET.WALLET_ORGCODE);
                string CustId = aes.DecryptCipheredText(WALLET.CUST_ID);
                string ShareAcctId = SHR.share_acct_id;
                string MemberPhone = aes.DecryptCipheredText(WALLET.CUST_PHONE);

                IDevice device = DependencyService.Get<IDevice>();
                string DeviceSerial = device.GetIdentifier();

                string data_to_sign = WalletId + "^" + OrgCode + "^" + CustId + "^" + ShareAcctId + "^" + MemberPhone + "^" + DeviceSerial;
                string DigitalSignature = aes.EncryptRawText(data_to_sign);

                // ... assemble inner request payload
                Dictionary<string, string> RequestPayload = new Dictionary<string, string>
                {
                    { "WalletId", WalletId },
                    { "OrgCode", OrgCode },
                    { "CustId", CustId },
                    { "MemberPhone", MemberPhone },
                    { "ShareAcctId", ShareAcctId },
                    { "DeviceSerial", DeviceSerial },
                    { "DigitalSignature", DigitalSignature }
                };
                #endregion

                #region ... Prepare and assemble OUTER request payload
                string RequestRef = Guid.NewGuid().ToString();
                string ProcCode = "400002";
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
                        string ClientRequestExtID = RespPayload.ClientRequestExtID;
                        string RoutingProcCode = RespPayload.RoutingProcCode;
                        string RoutingProcMessage = RespPayload.RoutingProcMessage;
                        string RoutingProcRef = RespPayload.RoutingProcRef;
                        string MifosHttpRespcode = RespPayload.MifosHttpRespcode;
                        dynamic MifosRespDetails = RespPayload.MifosRespDetails;
                        if (MifosHttpRespcode.StartsWith("2"))
                        {
                            SHARE_CURRENT_MARKET_PRICE = (MifosRespDetails.currentMarketPrice == null) ? "" : MifosRespDetails.currentMarketPrice;
                            SHARE_CURRENT_MARKET_PRICE_DISPLAY = SHARE_CURRENT_MARKET_PRICE;
                            lblShareUnitPrice.Text = double.Parse(SHARE_CURRENT_MARKET_PRICE_DISPLAY).ToString("#,##0.00");
                        }
                        else
                        {
                            await DisplayAlert("App Error", "An error occurred when processing share account details", "OK");
                        }//...end..iff03
                    }
                    else
                    {
                        await DisplayAlert("Alert", RespMessage, "OK");
                    }//..end..iff..else03
                }//..end..iff..else02
            }//..end..try
            catch (Exception mm)
            {
                await DisplayAlert("Error 05", mm.Message, "OK");
            }
        }
        #endregion

        #region ... 12: TxtShareNum_TextChanged
        private void TxtShareNum_TextChanged(object sender, TextChangedEventArgs e)
        {
            try
            {
                string share_num = txtShareNum.Text;

                double shr_num, shr_unit_price;
                double.TryParse(share_num, out shr_num);
                double.TryParse(SHARE_CURRENT_MARKET_PRICE, out shr_unit_price);

                double shr_value = (shr_num * shr_unit_price);
                lblAmtToBeDebitted.Text = shr_value.ToString("#,##0.00");

            }
            catch (Exception mm)
            {
                DisplayAlert("Error 01", mm.Message, "OK");
            }
        }
        #endregion

    }
}