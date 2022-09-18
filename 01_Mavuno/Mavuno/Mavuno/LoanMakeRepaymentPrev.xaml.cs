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
	public partial class LoanMakeRepaymentPrev : ContentPage
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
        #endregion

        #region ... 01: Class Constructor
        public LoanMakeRepaymentPrev()
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
        public LoanMakeRepaymentPrev(ArrayList datatransfered)
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

                // ... Loan Repayment Data
                DisplayLoanRpymtRqstData();
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

        #region ... 06: DisplayLoanRpymtRqstData
        protected void DisplayLoanRpymtRqstData()
        {
            Device.BeginInvokeOnMainThread(async () =>
            {
                try
                {
                    base.OnAppearing();

                    using (UserDialogs.Instance.Loading("Processing Loan Repayment Requests"))
                    {
                        await Task.Delay(300);
                        await FetchLoanRpymtRequests();          // ... the actual task
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

        #region ... 07: FetchLoanRpymtRequests
        private async Task FetchLoanRpymtRequests()
        {
            try
            {
                #region ... Prepare and assemble INNER request payload
                string WalletId = aes.DecryptCipheredText(WALLET.WALLET_ID);
                string OrgCode = aes.DecryptCipheredText(WALLET.WALLET_ORGCODE);
                string CustId = aes.DecryptCipheredText(WALLET.CUST_ID);
                string TranRqstType = "RQST_LOAN_RR";
                string MemberPhone = aes.DecryptCipheredText(WALLET.CUST_PHONE);

                IDevice device = DependencyService.Get<IDevice>();
                string DeviceSerial = device.GetIdentifier();

                string data_to_sign = WalletId + "^" + OrgCode + "^" + CustId + "^" + TranRqstType + "^" + MemberPhone + "^" + DeviceSerial;
                string DigitalSignature = aes.EncryptRawText(data_to_sign);

                // ... assemble inner request payload
                Dictionary<string, string> RequestPayload = new Dictionary<string, string>
                    {
                        { "WalletId", WalletId },
                        { "OrgCode", OrgCode },
                        { "CustId", CustId },
                        { "TranRqstType", TranRqstType },
                        { "MemberPhone", MemberPhone },
                        { "DeviceSerial", DeviceSerial },
                        { "DigitalSignature", DigitalSignature }
                    };
                #endregion

                #region ... Prepare and assemble OUTER request payload
                string RequestRef = Guid.NewGuid().ToString();
                string ProcCode = "300005";
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
                        if (RespPayload.Count == 0)
                        {
                            await DisplayAlert("Alert", "There is nothing to display", "OK");
                        }
                        else
                        {
                            List<LoanRpymtRqst> lrr_rqst_list = new List<LoanRpymtRqst>();

                            for (int i = 0; i < RespPayload.Count; i++)
                            {
                                dynamic req = RespPayload[i];

                                LoanRpymtRqst lrr = new LoanRpymtRqst();
                                lrr.RECORD_ID = req.RECORD_ID;
                                lrr.LOAN_RR_REF = req.LOAN_RR_REF;
                                lrr.CHANNEL = req.CHANNEL;
                                lrr.METHOD = req.METHOD;
                                lrr.CUST_ID = req.CUST_ID;
                                lrr.MSISDN = req.MSISDN;
                                lrr.SVGS_ACCT_ID_TO_DEBIT = req.SVGS_ACCT_ID_TO_DEBIT;
                                lrr.RPYMT_AMT = req.RPYMT_AMT;
                                lrr.LOAN_ACCT_ID_TO_CREDIT = req.LOAN_ACCT_ID_TO_CREDIT;
                                lrr.REASON = req.REASON;
                                lrr.APPLN_SUBMISSION_DATE = req.APPLN_SUBMISSION_DATE;
                                lrr.HANDLER_USER_ID = req.HANDLER_USER_ID;
                                lrr.HANDLED_ON = req.HANDLED_ON;
                                lrr.HANDLE_RMKS = req.HANDLE_RMKS;
                                lrr.APPROVED_AMT = req.APPROVED_AMT;
                                lrr.APPROVED_BY = req.APPROVED_BY;
                                lrr.APPROVAL_DATE = req.APPROVAL_DATE;
                                lrr.APPROVAL_RMKS = req.APPROVAL_RMKS;
                                lrr.PROC_MODE = req.PROC_MODE;
                                lrr.PROC_BATCH_NO = req.PROC_BATCH_NO;
                                lrr.CORE_TXN_ID = req.CORE_TXN_ID;
                                lrr.MOMO_PROC_STATUS = req.MOMO_PROC_STATUS;
                                lrr.MOMO_TRAN_REF = req.MOMO_TRAN_REF;
                                lrr.MOMO_TELCO_TRAN_REF = req.MOMO_TELCO_TRAN_REF;
                                lrr.RQST_STATUS = req.RQST_STATUS;


                                lrr_rqst_list.Add(lrr);
                            }//..end..loop

                            prevLrrRqstListView.ItemsSource = lrr_rqst_list;
                        }//..end..iff

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

        #region ... 08: Item selected
        private void OnListViewItemSelected(object sender, SelectedItemChangedEventArgs e)
        {
            try
            {
                LoanRpymtRqst lrr = e.SelectedItem as LoanRpymtRqst;

                // ... prepare items to transmit
                ArrayList datatransfered = new ArrayList();
                datatransfered.Add(WALLET);
                datatransfered.Add(SESS);
                datatransfered.Add(CORE_CLIENT_DETAILS);
                datatransfered.Add(LAB);
                datatransfered.Add(LAB_LIST);
                datatransfered.Add(lrr);

                Navigation.PushAsync(new LoanMakeRepaymentPrevInfo(datatransfered));
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 01", mm.Message, "OK");
            }
        }
        #endregion

    }
}