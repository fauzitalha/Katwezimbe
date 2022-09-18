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
	public partial class SvgsAcctTransferPrev : ContentPage
	{
        #region ... Class Variables
        CoreFunctions cf = new CoreFunctions();
        AES256.AES256 aes = new AES256.AES256();
        private DateTime LAST_ACTIVITY_TIME;

        private Wallet WALLET = new Wallet();
        private List<string> SESS = new List<string>();
        private dynamic CORE_CLIENT_DETAILS;
        SavingsAcctBasic SAB = new SavingsAcctBasic();
        #endregion

        #region ... 01: Class Constructor
        public SvgsAcctTransferPrev()
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
        public SvgsAcctTransferPrev(ArrayList datatransfered)
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

                // ... DisplayTransferRqstData
                DisplayTransferRqstData();
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

        #region ... 06: DisplayTransferRqstData
        protected void DisplayTransferRqstData()
        {
            Device.BeginInvokeOnMainThread(async () =>
            {
                try
                {
                    base.OnAppearing();

                    using (UserDialogs.Instance.Loading("Processing Withdrawal Requests"))
                    {
                        await Task.Delay(300);
                        await FetchTransferRequests();          // ... the actual task
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

        #region ... 07: FetchTransferRequests
        private async Task FetchTransferRequests()
        {
            try
            {
                #region ... Prepare and assemble INNER request payload
                string WalletId = aes.DecryptCipheredText(WALLET.WALLET_ID);
                string OrgCode = aes.DecryptCipheredText(WALLET.WALLET_ORGCODE);
                string CustId = aes.DecryptCipheredText(WALLET.CUST_ID);
                string TranRqstType = "RQST_TRANSFER";
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
                string ProcCode = "200008";
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
                            List<TransferRqst> transfer_rqst_list = new List<TransferRqst>();

                            for (int i = 0; i < RespPayload.Count; i++)
                            {
                                dynamic req = RespPayload[i];

                                TransferRqst transfer_rqst = new TransferRqst();
                                transfer_rqst.RECORD_ID = req.RECORD_ID;
                                transfer_rqst.TRANSFER_REF = req.TRANSFER_REF;
                                transfer_rqst.CHANNEL = req.CHANNEL;
                                transfer_rqst.METHOD = req.METHOD;
                                transfer_rqst.CUST_ID = req.CUST_ID;
                                transfer_rqst.SVGS_ACCT_ID_TO_DEBIT = req.SVGS_ACCT_ID_TO_DEBIT;
                                transfer_rqst.TRANSFER_AMT = req.TRANSFER_AMT;
                                transfer_rqst.SVGS_ACCT_ID_TO_CREDIT = req.SVGS_ACCT_ID_TO_CREDIT;
                                transfer_rqst.REASON = req.REASON;
                                transfer_rqst.APPLN_SUBMISSION_DATE = req.APPLN_SUBMISSION_DATE;
                                transfer_rqst.SVGS_HANDLER_USER_ID = req.SVGS_HANDLER_USER_ID;
                                transfer_rqst.FIRST_HANDLED_ON = req.FIRST_HANDLED_ON;
                                transfer_rqst.FIRST_HANDLE_RMKS = req.FIRST_HANDLE_RMKS;
                                transfer_rqst.COMMITTEE_FLG = req.COMMITTEE_FLG;
                                transfer_rqst.COMMITTEE_HANDLER_USER_ID = req.COMMITTEE_HANDLER_USER_ID;
                                transfer_rqst.COMMITTEE_STATUS = req.COMMITTEE_STATUS;
                                transfer_rqst.COMMITTEE_STATUS_DATE = req.COMMITTEE_STATUS_DATE;
                                transfer_rqst.COMMITTEE_RMKS = req.COMMITTEE_RMKS;
                                transfer_rqst.APPROVED_AMT = req.APPROVED_AMT;
                                transfer_rqst.APPROVED_BY = req.APPROVED_BY;
                                transfer_rqst.APPROVAL_DATE = req.APPROVAL_DATE;
                                transfer_rqst.APPROVAL_RMKS = req.APPROVAL_RMKS;
                                transfer_rqst.PROC_MODE = req.PROC_MODE;
                                transfer_rqst.PROC_BATCH_NO = req.PROC_BATCH_NO;
                                transfer_rqst.CORE_TXN_ID = req.CORE_TXN_ID;
                                transfer_rqst.TRANSFER_APPLN_STATUS = req.TRANSFER_APPLN_STATUS;


                                transfer_rqst_list.Add(transfer_rqst);
                            }//..end..loop

                            prevTransferRqstListView.ItemsSource = transfer_rqst_list;
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
                TransferRqst transfer_rqst = e.SelectedItem as TransferRqst;

                // ... prepare items to transmit
                ArrayList datatransfered = new ArrayList();
                datatransfered.Add(WALLET);
                datatransfered.Add(SESS);
                datatransfered.Add(CORE_CLIENT_DETAILS);
                datatransfered.Add(SAB);
                datatransfered.Add(transfer_rqst);

                Navigation.PushAsync(new SvgsAcctTransferPrevInfo(datatransfered));
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 01", mm.Message, "OK");
            }
        }
        #endregion


    }
}