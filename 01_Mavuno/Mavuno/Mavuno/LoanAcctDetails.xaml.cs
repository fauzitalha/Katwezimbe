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
	public partial class LoanAcctDetails : ContentPage
	{
        #region ... Class Variables
        CoreFunctions cf = new CoreFunctions();
        AES256.AES256 aes = new AES256.AES256();
        private DateTime LAST_ACTIVITY_TIME;

        private Wallet WALLET = new Wallet();
        private List<string> SESS = new List<string>();
        private dynamic CORE_CLIENT_DETAILS;
        LoanAcctBasic LAB = new LoanAcctBasic();
        #endregion

        #region ... 01: Class Constructor
        public LoanAcctDetails()
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
        public LoanAcctDetails(ArrayList datatransfered)
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

                // ... DisplayScreenData
                lblTitleView.Text = "Acct Details: " + LAB.account_no;

                DisplayLoanAcctDetails();
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

        #region ... 06: DisplayLoanAcctDetails
        protected void DisplayLoanAcctDetails()
        {
            Device.BeginInvokeOnMainThread(async () =>
            {
                try
                {
                    base.OnAppearing();

                    using (UserDialogs.Instance.Loading("Processing Loan Account Details"))
                    {
                        await Task.Delay(300);
                        await FetchLoanAcctDetails();          // ... the actual task
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

        #region ... 07: FetchLoanAcctDetails
        private async Task FetchLoanAcctDetails()
        {
            try
            {
                #region ... Prepare and assemble INNER request payload
                string WalletId = aes.DecryptCipheredText(WALLET.WALLET_ID);
                string OrgCode = aes.DecryptCipheredText(WALLET.WALLET_ORGCODE);
                string CustId = aes.DecryptCipheredText(WALLET.CUST_ID);
                string LoanAcctId = LAB.loan_acct_id;
                string MemberPhone = aes.DecryptCipheredText(WALLET.CUST_PHONE);

                IDevice device = DependencyService.Get<IDevice>();
                string DeviceSerial = device.GetIdentifier();

                string data_to_sign = WalletId + "^" + OrgCode + "^" + CustId + "^" + LoanAcctId + "^" + MemberPhone + "^" + DeviceSerial;
                string DigitalSignature = aes.EncryptRawText(data_to_sign);

                // ... assemble inner request payload
                Dictionary<string, string> RequestPayload = new Dictionary<string, string>
                {
                    { "WalletId", WalletId },
                    { "OrgCode", OrgCode },
                    { "CustId", CustId },
                    { "MemberPhone", MemberPhone },
                    { "LoanAcctId", LoanAcctId },
                    { "DeviceSerial", DeviceSerial },
                    { "DigitalSignature", DigitalSignature }
                };
                #endregion

                #region ... Prepare and assemble OUTER request payload
                string RequestRef = Guid.NewGuid().ToString();
                string ProcCode = "300002";
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
                            string AccountNum = MifosRespDetails.accountNo;

                            string Currency = MifosRespDetails.currency.code + " (" + MifosRespDetails.currency.name + ")";
                            string Product = MifosRespDetails.loanProductName;

                            string PrincipalDisbursed = (MifosRespDetails.summary.principalDisbursed == null) ? "" : MifosRespDetails.summary.principalDisbursed.ToString("#,##0.00");

                            // ... interest rate details
                            string InterestRatePerPeriod = (MifosRespDetails.interestRatePerPeriod == null) ? "" : MifosRespDetails.interestRatePerPeriod;
                            string InterestRateFrequencyType = (MifosRespDetails.interestRateFrequencyType.value == null) ? "" : MifosRespDetails.interestRateFrequencyType.value;
                            string LoanInterestRate = InterestRatePerPeriod + " " + InterestRateFrequencyType;

                            // ... loan period
                            string NumberOfRepayments = (MifosRespDetails.numberOfRepayments == null) ? "" : MifosRespDetails.numberOfRepayments;
                            string RepaymentEvery = (MifosRespDetails.repaymentEvery == null) ? "" : MifosRespDetails.repaymentEvery;
                            string RepaymentFrequencyType = (MifosRespDetails.repaymentFrequencyType.value == null) ? "" : MifosRespDetails.repaymentFrequencyType.value;
                            string LoanPeriod = NumberOfRepayments + " " + RepaymentFrequencyType;
                            string LoanRpymtFreq = "Every " + RepaymentEvery + " " + RepaymentFrequencyType;


                            string AmortizationType = (MifosRespDetails.amortizationType.value == null) ? "" : MifosRespDetails.amortizationType.value;
                            string InterestType = (MifosRespDetails.interestType.value == null) ? "" : MifosRespDetails.interestType.value;

                            // ... assign to the display screen
                            lblLoanAcctNum.Text = AccountNum;
                            lblLoanCrncy.Text = Currency;
                            lblLoanPdt.Text = Product;
                            lblAmountDisbursed.Text = PrincipalDisbursed;
                            lblLoanIntRate.Text = LoanInterestRate;
                            lblLoanPeriod.Text = LoanPeriod;
                            lblRpymtFreq.Text = LoanRpymtFreq;
                            lblAmortType.Text = AmortizationType;
                            lblLoanIntType.Text = InterestType;

                        }
                        else
                        {
                            await DisplayAlert("App Error", "An error occurred when processing saving account details", "OK");
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
    }
}