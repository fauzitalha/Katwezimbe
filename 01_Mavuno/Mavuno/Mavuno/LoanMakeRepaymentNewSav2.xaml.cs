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
	public partial class LoanMakeRepaymentNewSav2 : ContentPage
	{
        #region ... Class Variables
        CoreFunctions cf = new CoreFunctions();
        AES256.AES256 aes = new AES256.AES256();
        private DateTime LAST_ACTIVITY_TIME;

        private Wallet WALLET = new Wallet();
        private List<string> SESS = new List<string>();
        private dynamic CORE_CLIENT_DETAILS;
        private LoanAcctBasic LAB = new LoanAcctBasic();
        private SavingsAcctBasic SAB_ACCT = new SavingsAcctBasic();
        #endregion

        #region ... 01: Class Constructor
        public LoanMakeRepaymentNewSav2()
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
        public LoanMakeRepaymentNewSav2(ArrayList datatransfered)
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
                SAB_ACCT = (SavingsAcctBasic)datatransfered[4];

                // ... Display Data
                lblTitleView.Text = "Savings Loan Rpymt: " + LAB.account_no;
                lblSvgAccountNum.Text = SAB_ACCT.account_no + " - " + SAB_ACCT.product_name;
                lblLoanAcctNum.Text = LAB.account_no + " - " + LAB.product_name;
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

        #region ... 06: Submit Withdraw
        private void BtnSubmit_Clicked(object sender, EventArgs e)
        {
            Device.BeginInvokeOnMainThread(async () =>
            {
                try
                {
                    using (UserDialogs.Instance.Loading("Submitting Loan Repayment"))
                    {
                        await Task.Delay(300);
                        await SubmitLoanRpymtAppln();          // ... the actual task
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
        private async Task SubmitLoanRpymtAppln()
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
                    string AmountToRepay = txtAmountRepay.Text;
                    string RpymtChannel = Constants.CHANNEL;
                    string RpymtMethod = "DIRECT-SVNGS-TRANSFER";
                    string CrLoanAcctId = LAB.loan_acct_id;
                    string Narration = txtNarration.Text;
                    string MemberPhone = aes.DecryptCipheredText(WALLET.CUST_PHONE);


                    IDevice device = DependencyService.Get<IDevice>();
                    string DeviceSerial = device.GetIdentifier();

                    string data_to_sign = WalletId + "^" + OrgCode + "^" + CustId + "^" + DrSavingsAcctId + "^" + AmountToRepay + "^" + RpymtChannel + "^" + RpymtMethod + "^" + CrLoanAcctId + "^" + Narration + "^" + MemberPhone + "^" + DeviceSerial;
                    string DigitalSignature = aes.EncryptRawText(data_to_sign);

                    // ... assemble inner request payload
                    Dictionary<string, string> RequestPayload = new Dictionary<string, string>
                    {
                        { "WalletId", WalletId },
                        { "OrgCode", OrgCode },
                        { "CustId", CustId },
                        { "DrSavingsAcctId", DrSavingsAcctId },
                        { "AmountToRepay", AmountToRepay },
                        { "RpymtChannel", RpymtChannel },
                        { "RpymtMethod", RpymtMethod },
                        { "CrLoanAcctId", CrLoanAcctId },
                        { "Narration", Narration },
                        { "MemberPhone", MemberPhone },
                        { "DeviceSerial", DeviceSerial },
                        { "DigitalSignature", DigitalSignature }
                    };
                    #endregion

                    #region ... Prepare and assemble OUTER request payload
                    string RequestRef = Guid.NewGuid().ToString();
                    string ProcCode = "300004";
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
                            await DisplayAlert("Success", "Loan repayment request has been received successfully.\nTRAN_REF: " + REF, "OK");

                            // ... prepare items to transmit
                            ArrayList datatransfered = new ArrayList();
                            datatransfered.Add(WALLET);
                            datatransfered.Add(SESS);
                            datatransfered.Add(CORE_CLIENT_DETAILS);
                            datatransfered.Add(LAB);

                            await Navigation.PushAsync(new LoanAcct(datatransfered));
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
                bool cnt_AmountRepay = (string.IsNullOrWhiteSpace(txtAmountRepay.Text)) ? false : true;
                bool cnt_Narration = (string.IsNullOrWhiteSpace(txtNarration.Text)) ? false : true;

                if (cnt_AmountRepay && cnt_Narration)
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
                            double RpymtAmt = double.Parse(txtAmountRepay.Text);

                            if (TransactableBal > RpymtAmt)
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


    }
}