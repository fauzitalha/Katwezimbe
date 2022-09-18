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
	public partial class SetWalletPin : ContentPage
	{

        #region ... Class Variables
        CoreFunctions cf = new CoreFunctions();
        AES256.AES256 aes = new AES256.AES256();
        private Wallet WALLET = new Wallet();
        private WalletAdditionProgress WALLET_ADDTN_PROG = new WalletAdditionProgress();
        #endregion


        #region ... 01: Class Constructor
        public SetWalletPin()
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
        public SetWalletPin(ArrayList datatransfered)
        {
            try
            {
                InitializeComponent();

                // ... receive data
                WALLET = (Wallet)datatransfered[0];
                WALLET_ADDTN_PROG = (WalletAdditionProgress)datatransfered[1];

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
            string WALLET_ID = aes.DecryptCipheredText(WALLET.WALLET_ID);
            string WALLET_ORGCODE = aes.DecryptCipheredText(WALLET.WALLET_ORGCODE);
            string WALLET_ORGNAME = aes.DecryptCipheredText(WALLET.WALLET_ORGNAME);
            string CUST_ID = aes.DecryptCipheredText(WALLET.CUST_ID);
            string CUST_CORE_ID = aes.DecryptCipheredText(WALLET.CUST_CORE_ID);
            string APPLN_REF_MOB = aes.DecryptCipheredText(WALLET.APPLN_REF_MOB);
            string CUST_PHONE = aes.DecryptCipheredText(WALLET.CUST_PHONE);

            lblOrgName.Text = WALLET_ORGNAME;
            //lblWalletId.Text = WALLET_ID;
            lblPhoneNumber.Text = CUST_PHONE;
        }
        #endregion


        #region ... 04: Save Pin
        private void BtnSavePin_Clicked(object sender, EventArgs e)
        {
            Device.BeginInvokeOnMainThread(async () =>
            {
                try
                {
                    using (UserDialogs.Instance.Loading("Working. Please wait ..."))
                    {
                        await Task.Delay(300);
                        await SavePin();   // ... the actual task
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


        #region ... 05: Inquire Appln Details
        private async Task SavePin()
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
                    string ApplnRefMob = aes.DecryptCipheredText(WALLET.APPLN_REF_MOB);
                    string MemberPin = aes.EncryptRawText(txtPin1.Text);

                    IDevice device = DependencyService.Get<IDevice>();
                    string DeviceSerial = device.GetIdentifier();

                    string data_to_sign = WalletId + "^" + OrgCode + "^" + CustId + "^" + ApplnRefMob + "^" + MemberPin + "^" + DeviceSerial;
                    string DigitalSignature = aes.EncryptRawText(data_to_sign);

                    // ... assemble inner request payload
                    Dictionary<string, string> RequestPayload = new Dictionary<string, string>
                    {
                        { "WalletId", WalletId },
                        { "OrgCode", OrgCode },
                        { "CustId", CustId },
                        { "ApplnRefMob", ApplnRefMob },
                        { "MemberPin", MemberPin },
                        { "DeviceSerial", DeviceSerial },
                        { "DigitalSignature", DigitalSignature }
                    };
                    #endregion

                    #region ... Prepare and assemble OUTER request payload
                    string RequestRef = Guid.NewGuid().ToString();
                    string ProcCode = "100002";
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
                            // ... update wallet addition progress
                            bool isUpdated = UpdateWalletAdditionProgress(WALLET_ADDTN_PROG);
                            if (isUpdated)
                            {
                                await DisplayAlert("Success", RespPayload.ToString(), "OK");
                                await Navigation.PushAsync(new MainPage());
                            }
                        }
                        else
                        {
                            await DisplayAlert("Alert", RespMessage, "OK");
                        }//..end..iffelse03
                    }//..end..iffelse02
                }//..end..iffelse01
            }//..end..try
            catch (Exception mm)
            {
                await DisplayAlert("Error 05", mm.Message, "OK");
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
                bool cnt_Pin1 = (string.IsNullOrWhiteSpace(txtPin1.Text)) ? false : true;
                bool cnt_Pin2 = (string.IsNullOrWhiteSpace(txtPin2.Text)) ? false : true;

                if (cnt_Pin1 && cnt_Pin2)
                {
                    int Pin1 = int.Parse(txtPin1.Text);
                    int Pin2 = int.Parse(txtPin2.Text);
                    if (Pin1==Pin2)
                    {
                        is_valid = true;
                        val_mssg = "data is good";
                    }
                    else
                    {
                        is_valid = false;
                        val_mssg = "Supplied pins are not matching";
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


        #region ... 07: UpdateWalletAdditionProgress
        private bool UpdateWalletAdditionProgress(WalletAdditionProgress WALLET_ADDTN_PROG)
        {
            bool isUpdated = false;
            try
            {
                WALLET_ADDTN_PROG.ADDITION_PROGRESS = aes.EncryptRawText("COMPLETED");
                using (SQLiteConnection conn = new SQLiteConnection(App.FilePath))
                {
                    conn.CreateTable<Wallet>();
                    int update_flg = conn.Update(WALLET_ADDTN_PROG);
                    if (update_flg == 1)
                    {
                        isUpdated = true;
                    }//..end..iff
                }//..end..conn
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 07", mm.Message, "OK");
            }
            return isUpdated;
        }
        #endregion

    }
}