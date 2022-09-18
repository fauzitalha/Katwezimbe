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
	public partial class SvgsAcctTransferNew : ContentPage
	{
        #region ... Class Variables
        CoreFunctions cf = new CoreFunctions();
        AES256.AES256 aes = new AES256.AES256();
        private DateTime LAST_ACTIVITY_TIME;

        private Wallet WALLET = new Wallet();
        private List<string> SESS = new List<string>();
        private dynamic CORE_CLIENT_DETAILS;
        private SavingsAcctBasic SAB = new SavingsAcctBasic();
        #endregion

        #region ... 01: Class Constructor
        public SvgsAcctTransferNew()
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
        public SvgsAcctTransferNew(ArrayList datatransfered)
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

                // ... DisplayWalletData
                lblTitleView.Text = "New Transfer: " + SAB.account_no;
                lblSrcAcct.Text = SAB.account_no + " - " + SAB.product_name;
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

        #region ... 06: Submit Transfer
        private void BtnSubmit_Clicked(object sender, EventArgs e)
        {
            Device.BeginInvokeOnMainThread(async () =>
            {
                try
                {
                    using (UserDialogs.Instance.Loading("Submitting Transfer Application"))
                    {
                        await Task.Delay(300);
                        await SubmitTransferAppln();          // ... the actual task
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

        #region ... 07: Submit Withdraw Appln
        private async Task SubmitTransferAppln()
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
                    string acc_svg_id = (string)validation_results[2];
                    string acc_acct_num = (string)validation_results[3];
                    string acc_client_name = (string)validation_results[4];

                    string conf_mssg = "Do you want to transfer amount: " + txtTransferAmount.Text + " to account: " + acc_acct_num + " (" + acc_client_name + ") ?";
                    var ans = await DisplayAlert("Confirmation", conf_mssg, "Yes", "No");
                    if (ans == true) //Success condition
                    {

                        #region ... Prepare and assemble INNER request payload
                        string WalletId = aes.DecryptCipheredText(WALLET.WALLET_ID);
                        string OrgCode = aes.DecryptCipheredText(WALLET.WALLET_ORGCODE);
                        string CustId = aes.DecryptCipheredText(WALLET.CUST_ID);
                        string SavingsAcctId = SAB.svgs_acct_id;
                        string TransferAmount = txtTransferAmount.Text;
                        string TransferMethod = "INTERNAL-TRANSFER";
                        string CrAcctId = acc_svg_id;
                        string Narration = txtReason.Text;
                        string MemberPhone = aes.DecryptCipheredText(WALLET.CUST_PHONE);

                        IDevice device = DependencyService.Get<IDevice>();
                        string DeviceSerial = device.GetIdentifier();

                        string data_to_sign = WalletId + "^" + OrgCode + "^" + CustId + "^" + SavingsAcctId + "^" + TransferAmount + "^" + TransferMethod + "^" + CrAcctId + "^" + Narration + "^" + MemberPhone + "^" + DeviceSerial;
                        string DigitalSignature = aes.EncryptRawText(data_to_sign);

                        // ... assemble inner request payload
                        Dictionary<string, string> RequestPayload = new Dictionary<string, string>
                        {
                            { "WalletId", WalletId },
                            { "OrgCode", OrgCode },
                            { "CustId", CustId },
                            { "SavingsAcctId", SavingsAcctId },
                            { "TransferAmount", TransferAmount },
                            { "TransferMethod", TransferMethod },
                            { "CrAcctId", CrAcctId },
                            { "Narration", Narration },
                            { "MemberPhone", MemberPhone },
                            { "DeviceSerial", DeviceSerial },
                            { "DigitalSignature", DigitalSignature }
                        };
                        #endregion

                        #region ... Prepare and assemble OUTER request payload
                        string RequestRef = Guid.NewGuid().ToString();
                        string ProcCode = "200006";
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
                                await DisplayAlert("Success", "Transfer request has been received successfully.\nTRAN_REF: " + REF, "OK");

                                // ... prepare items to transmit
                                ArrayList datatransfered = new ArrayList();
                                datatransfered.Add(WALLET);
                                datatransfered.Add(SESS);
                                datatransfered.Add(CORE_CLIENT_DETAILS);
                                datatransfered.Add(SAB);

                                await Navigation.PushAsync(new SvgsAcct(datatransfered));
                            }
                            else
                            {
                                await DisplayAlert("Alert", RespMessage, "OK");
                            }//..end..iff..else03
                        }//..end..iff..else02
                    }
                    else
                    {
                        // false cond
                    }
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
            string acc_svg_id = "";
            string acc_acct_num = "";
            string acc_client_name = "";
            ArrayList val_results = new ArrayList();
            try
            {
                bool cnt_DestAcct = (string.IsNullOrWhiteSpace(txtDestAcct.Text)) ? false : true;
                bool cnt_TransferAmount = (string.IsNullOrWhiteSpace(txtTransferAmount.Text)) ? false : true;
                bool cnt_Reason = (string.IsNullOrWhiteSpace(txtReason.Text)) ? false : true;

                if (cnt_DestAcct && cnt_TransferAmount && cnt_Reason)
                {
                    bool hasEnoughBalance = await VerifyAcctBal();
                    if (hasEnoughBalance)
                    {
                        // ... get destination account details
                        ArrayList acct_details = await FetchAccountNumDetails();
                        bool RESP_CODE = (bool)acct_details[0];
                        string RESP_MSSG = (string)acct_details[1];
                        if (RESP_CODE)
                        {
                            acc_svg_id = (string)acct_details[2];
                            acc_acct_num = (string)acct_details[3];
                            acc_client_name = (string)acct_details[6];


                            is_valid = true;
                            val_mssg = "data is good";
                        }
                        else
                        {
                            is_valid = false;
                            val_mssg = RESP_MSSG;
                        }
                    }
                    else
                    {
                        is_valid = false;
                        val_mssg = "Withdraw amount is greater than transactable balance";
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
                val_results.Add(acc_svg_id);
                val_results.Add(acc_acct_num);
                val_results.Add(acc_client_name);
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
                string SavingsAcctId = SAB.svgs_acct_id;
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
                            double TransferAmt = double.Parse(txtTransferAmount.Text);

                            if (TransactableBal > TransferAmt)
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

        #region ... 10: FetchAccountNumDetails
        private async Task<ArrayList> FetchAccountNumDetails()
        {
            ArrayList acct_details = new ArrayList();
            bool RESP_CODE = false;
            string RESP_MSSG = "";
            
            try
            {
                #region ... Prepare and assemble INNER request payload
                string WalletId = aes.DecryptCipheredText(WALLET.WALLET_ID);
                string OrgCode = aes.DecryptCipheredText(WALLET.WALLET_ORGCODE);
                string CustId = aes.DecryptCipheredText(WALLET.CUST_ID);
                string SvgAcctNum = txtDestAcct.Text;
                string MemberPhone = aes.DecryptCipheredText(WALLET.CUST_PHONE);

                IDevice device = DependencyService.Get<IDevice>();
                string DeviceSerial = device.GetIdentifier();

                string data_to_sign = WalletId + "^" + OrgCode + "^" + CustId + "^" + SvgAcctNum + "^" + MemberPhone + "^" + DeviceSerial;
                string DigitalSignature = aes.EncryptRawText(data_to_sign);

                // ... assemble inner request payload
                Dictionary<string, string> RequestPayload = new Dictionary<string, string>
                {
                    { "WalletId", WalletId },
                    { "OrgCode", OrgCode },
                    { "CustId", CustId },
                    { "MemberPhone", MemberPhone },
                    { "SvgAcctNum", SvgAcctNum },
                    { "DeviceSerial", DeviceSerial },
                    { "DigitalSignature", DigitalSignature }
                };
                #endregion

                #region ... Prepare and assemble OUTER request payload
                string RequestRef = Guid.NewGuid().ToString();
                string ProcCode = "200009";
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
                    RESP_CODE = false;
                    RESP_MSSG = resp_mssg;

                    acct_details.Add(RESP_CODE);
                    acct_details.Add(RESP_MSSG);
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
                            dynamic acct_data_list = MifosRespDetails.data[0].row;
                            string acc_svg_id = acct_data_list[0];
                            string acc_acct_num = acct_data_list[1];
                            string acc_crncy = acct_data_list[2];
                            string acc_client_id = acct_data_list[3];
                            string acc_client_name = acct_data_list[4];
                            string acc_product_id = acct_data_list[5];
                            string acc_product_name = acct_data_list[6];
                            string acc_product_short_name = acct_data_list[7];
                            string acc_group_id = acct_data_list[8];
                            string acc_status_enum = acct_data_list[9];

                            RESP_CODE = true;
                            RESP_MSSG = "success";
                            acct_details.Add(RESP_CODE);
                            acct_details.Add(RESP_MSSG);
                            acct_details.Add(acc_svg_id);
                            acct_details.Add(acc_acct_num);
                            acct_details.Add(acc_crncy);
                            acct_details.Add(acc_client_id);
                            acct_details.Add(acc_client_name);
                            acct_details.Add(acc_product_id);
                            acct_details.Add(acc_product_name);
                            acct_details.Add(acc_product_short_name);
                            acct_details.Add(acc_group_id);
                            acct_details.Add(acc_status_enum);
                        }
                        else
                        {
                            RESP_CODE = false;
                            RESP_MSSG = "Error at app core. Please contact management";

                            acct_details.Add(RESP_CODE);
                            acct_details.Add(RESP_MSSG);
                        }//...end..iff03
                    }
                    else
                    {
                        RESP_CODE = false;
                        RESP_MSSG = RespMessage;
                        acct_details.Add(RESP_CODE);
                        acct_details.Add(RESP_MSSG);
                    }//..end..iff..else03
                }//..end..iff..else02
            }//..end..try
            catch (Exception mm)
            {
                RESP_CODE = false;
                RESP_MSSG = mm.Message;
                acct_details.Add(RESP_CODE);
                acct_details.Add(RESP_MSSG);
            }

            return acct_details;
        }

        #endregion


    }
}