using Acr.UserDialogs;
using Mavuno.core;
using Mavuno.db;
using Newtonsoft.Json;
using SQLite;
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
    public partial class LoginScreen : ContentPage
    {

        #region ... Class Variables
        CoreFunctions cf = new CoreFunctions();
        AES256.AES256 aes = new AES256.AES256();
        private WalletListView WLV = new WalletListView();
        #endregion


        #region ... 01: Init
        public LoginScreen()
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
        public LoginScreen(ArrayList datatransfered)
        {
            try
            {
                InitializeComponent();

                // ... receive data
                WLV = (WalletListView)datatransfered[0];

                // ... DisplayWalletData
                DisplayWalletData();
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 02", mm.Message, "OK");
            }
        }
        #endregion


        #region ... 03: Initialize Screen Data
        private void DisplayWalletData()
        {
            lblOrgName.Text = WLV.WALLET_ORGNAME;
            //lblWalletId.Text = WLV.WALLET_ID;
            lblPhoneNumber.Text = WLV.CUST_PHONE;
        }
        #endregion


        #region ... 04: Log In
        private void BtnLogin_Clicked(object sender, EventArgs e)
        {
            Device.BeginInvokeOnMainThread(async () =>
            {
                try
                {
                    using (UserDialogs.Instance.Loading("Signing In. Please wait"))
                    {
                        await Task.Delay(300);
                        await LoginTask();            // ... the actual task
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


        #region ... 05: Process Login
        private async Task LoginTask()
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
                    string WalletId = WLV.WALLET_ID;
                    string OrgCode = WLV.WALLET_ORGCODE;
                    string CustId = WLV.CUST_ID;
                    string MemberPhone = WLV.CUST_PHONE;
                    string MemberPin = aes.EncryptRawText(txtAccessPin.Text);

                    IDevice device = DependencyService.Get<IDevice>();
                    string DeviceSerial = device.GetIdentifier();

                    string data_to_sign = WalletId + "^" + OrgCode + "^" + CustId + "^" + MemberPhone + "^" + MemberPin + "^" + DeviceSerial;
                    string DigitalSignature = aes.EncryptRawText(data_to_sign);

                    // ... assemble inner request payload
                    Dictionary<string, string> RequestPayload = new Dictionary<string, string>
                    {
                        { "WalletId", WalletId },
                        { "OrgCode", OrgCode },
                        { "CustId", CustId },
                        { "MemberPhone", MemberPhone },
                        { "MemberPin", MemberPin },
                        { "DeviceSerial", DeviceSerial },
                        { "DigitalSignature", DigitalSignature }
                    };
                    #endregion

                    #region ... Prepare and assemble OUTER request payload
                    string RequestRef = Guid.NewGuid().ToString();
                    string ProcCode = "100003";
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
                            Wallet addedwallet = GetWalletDetailsByID(WLV.WALLET_ID);

                            // ... step 01: verify if login was successful
                            string ACCT_STATUS_CODE = RespPayload.ACCT_STATUS_CODE;
                            string ACCT_STATUS_NAME = RespPayload.ACCT_STATUS_NAME;
                            string ACCT_STATUS_DETAILS = RespPayload.ACCT_STATUS_DETAILS;
                            if (ACCT_STATUS_CODE.Equals("RR") || ACCT_STATUS_CODE.Equals("EE"))
                            {
                                // ... Pin has been reset or is expired
                                await DisplayAlert("Alert", ACCT_STATUS_NAME + "\n" + ACCT_STATUS_DETAILS, "OK");

                                // ... get added wallet details
                                string INP_ACCESSPIN = MemberPin;
                                string INP_ACCT_STATUS_NAME = ACCT_STATUS_NAME;
                                string INP_ACCT_STATUS_DETAILS = ACCT_STATUS_DETAILS;

                                ArrayList datatransfered = new ArrayList();
                                datatransfered.Add(addedwallet);
                                datatransfered.Add(INP_ACCESSPIN);
                                datatransfered.Add(INP_ACCT_STATUS_NAME);
                                datatransfered.Add(INP_ACCT_STATUS_DETAILS);
                                datatransfered.Add(WLV);
                                await Navigation.PushAsync(new ChangeAccessPin(datatransfered));

                            }
                            else if (ACCT_STATUS_CODE.Equals("OK"))
                            {
                                // ... Pin is successfully Authorized
                                string CUST_ID = RespPayload.CUST_ID;
                                string CUST_CORE_ID = RespPayload.CUST_CORE_ID;
                                string CUST_PHONE = RespPayload.CUST_PHONE;
                                string MOB_WALLET = RespPayload.MOB_WALLET;

                                // ... Verify Wallet Integrity
                                if (CUST_ID.Equals(WLV.CUST_ID) && CUST_PHONE.Equals(WLV.CUST_PHONE) && MOB_WALLET.Equals(WLV.WALLET_ID))
                                {
                                    // ... get customer core details
                                    //ArrayList resp_details = GetCustomerCoreDetailsTask(addedwallet);
                                    ArrayList resp_details = await GetCustomerCoreDetailsTask(addedwallet);
                                    string isgood = (string)resp_details[0];
                                    dynamic CORE_CLIENT_DETAILS = (dynamic)resp_details[1];
                                    if (isgood.Equals("false"))
                                    {
                                        await DisplayAlert("Alert", CORE_CLIENT_DETAILS.ToString(), "OK");
                                    }
                                    else
                                    {
                                        // ... navigate to home page
                                        List<string> SESS = new List<string>();
                                        string SESS_CUST_CORE_ID = CUST_CORE_ID;
                                        string SESS_ACCESSPIN = MemberPin;
                                        SESS.Add(SESS_CUST_CORE_ID);
                                        SESS.Add(SESS_ACCESSPIN);

                                        ArrayList datatransfered = new ArrayList();
                                        datatransfered.Add(addedwallet);
                                        datatransfered.Add(SESS);
                                        datatransfered.Add(CORE_CLIENT_DETAILS);
                                        await Navigation.PushAsync(new HomePage(datatransfered));
                                    }
                                }
                                else
                                {
                                    await DisplayAlert("Alert", "Wallet integrity is compromised. You cannot access this wallet from here. Contact Management Immediately", "OK");
                                }
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
                //await DisplayAlert("Error 05", mm.InnerException.Message, "OK");
            }
        }
        #endregion


        #region ... 06: ValidateDataReceived
        private ArrayList ValidateDataReceived()
        {
            bool is_valid = false;
            string val_mssg = "";
            ArrayList val_results = new ArrayList();
            try
            {
                bool cnt_AccessPin = (string.IsNullOrWhiteSpace(txtAccessPin.Text)) ? false : true;

                if (cnt_AccessPin)
                {
                    is_valid = true;
                    val_mssg = "data is good";
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


        #region ... 07: GetWalletDetailsByID
        private Wallet GetWalletDetailsByID(string WALLET_ID)
        {
            Wallet wallet = new Wallet();
            try
            {
                using (SQLiteConnection conn = new SQLiteConnection(App.FilePath))
                {
                    conn.CreateTable<Wallet>();
                    List<Wallet> wallet_list = conn.Query<Wallet>("SELECT * FROM WALLET").ToList();
                    if (wallet_list.Count > 0)
                    {
                        for (int i = 0; i < wallet_list.Count; i++)
                        {
                            Wallet ww = new Wallet();
                            ww = wallet_list[i];

                            string dec_wallet_id = aes.DecryptCipheredText(ww.WALLET_ID);
                            if (dec_wallet_id.Equals(WALLET_ID))
                            {
                                wallet = ww;
                                break;
                            }
                        }//..end..loop
                    }//..end..iff
                }//..end..conn
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 04", mm.Message, "OK");
            }
            return wallet;
        }
        #endregion


        #region ... 08: Report Issue
        private void ReportIssueToolBarItem_Clicked(object sender, EventArgs e)
        {
            try
            {
                // ... get added wallet details
                Wallet addedwallet = GetWalletDetailsByID(WLV.WALLET_ID);

                ArrayList datatransfered = new ArrayList();
                datatransfered.Add(addedwallet);
                datatransfered.Add(WLV);
                Navigation.PushAsync(new ReportIssue(datatransfered));
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 01", mm.Message, "OK");
            }
        }
        #endregion


        #region ... 09: Get Customer Details
        private async Task<ArrayList> GetCustomerCoreDetailsTask(Wallet WALLET)
        {
            ArrayList resp_details = new ArrayList();
            string isgood = "false";

            try
            {
                #region ... Prepare and assemble INNER request payload
                string WalletId = aes.DecryptCipheredText(WALLET.WALLET_ID);
                string OrgCode = aes.DecryptCipheredText(WALLET.WALLET_ORGCODE);
                string CustId = aes.DecryptCipheredText(WALLET.CUST_ID);
                string CustCoreId = aes.DecryptCipheredText(WALLET.CUST_CORE_ID);
                string MemberPhone = aes.DecryptCipheredText(WALLET.CUST_PHONE);

                IDevice device = DependencyService.Get<IDevice>();
                string DeviceSerial = device.GetIdentifier();

                string data_to_sign = WalletId + "^" + OrgCode + "^" + CustId + "^" + CustCoreId + "^" + MemberPhone + "^" + DeviceSerial;
                string DigitalSignature = aes.EncryptRawText(data_to_sign);

                // ... assemble inner request payload
                Dictionary<string, string> RequestPayload = new Dictionary<string, string>
                {
                    { "WalletId", WalletId },
                    { "OrgCode", OrgCode },
                    { "CustId", CustId },
                    { "CustCoreId", CustCoreId },
                    { "MemberPhone", MemberPhone },
                    { "DeviceSerial", DeviceSerial },
                    { "DigitalSignature", DigitalSignature }
                };
                #endregion

                #region ... Prepare and assemble OUTER request payload
                string RequestRef = Guid.NewGuid().ToString();
                string ProcCode = "990001";
                RequestMsg reqmsg = new RequestMsg();
                reqmsg.RequestRef = RequestRef;
                reqmsg.ProcCode = ProcCode;
                reqmsg.RequestPayLoad = RequestPayload;
                #endregion

                string json_request_msg = JsonConvert.SerializeObject(reqmsg, Formatting.Indented);
                //string[] respdetails = cf.PostToMoBilr(json_request_msg);
                string[] respdetails = await cf.PostToMoBilrAsync(json_request_msg);
                string resp_code = respdetails[0];
                string resp_mssg = respdetails[1];
                if (resp_code.Equals("ERR"))
                {
                    isgood = "false";
                    resp_details.Add(isgood);
                    resp_details.Add(resp_mssg);
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
                            isgood = "true";
                            resp_details.Add(isgood);
                            resp_details.Add(MifosRespDetails);
                        }
                        else
                        {
                            isgood = "false";
                            resp_details.Add(isgood);
                            resp_details.Add(RoutingProcMessage);
                        }//...end..iff03
                    }
                    else
                    {
                        isgood = "false";
                        resp_details.Add(isgood);
                        resp_details.Add(RespMessage);
                    }//...end..iff02
                }//...end..iff01
            }
            catch (Exception mm)
            {
                isgood = "false";
                resp_details.Add(isgood);
                resp_details.Add(mm.Message);
            }

            return resp_details;
        }
        #endregion


    }
}