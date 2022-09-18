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
    public partial class AddNewWallet : ContentPage
    {

        #region ... Class Variables
        CoreFunctions cf = new CoreFunctions();
        AES256.AES256 aes = new AES256.AES256();
        #endregion


        #region ... 01:  Class constructor
        public AddNewWallet()
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


        #region ... 02: Inquire Registration Details
        private void BtnInquireRegDetails_Clicked(object sender, EventArgs e)
        {
            Device.BeginInvokeOnMainThread(async () =>
            {
                try
                {
                    using (UserDialogs.Instance.Loading(("Please wait ...")))
                    {
                        await Task.Delay(300);
                        await InquireApplnRef();   // ... the actual task
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


        #region ... 03: Inquire Appln Details
        private async Task InquireApplnRef()
        {
            try
            {
                bool isValid = ValidateDataReceived();
                if (!isValid)
                {
                    await DisplayAlert("Alert", "Enter Missing Data. Cannot Proceed", "OK");
                }
                else
                {
                    #region ... Prepare and assemble INNER request payload
                    string ApplnRef = txtApplnRef.Text.Trim();
                    string ActivationCode = txtActivationCode.Text.Trim();

                    // ... sign the request data
                    string data_to_sign = ApplnRef + "^" + ActivationCode;
                    string DigitalSignature = aes.EncryptRawText(data_to_sign);

                    // ... assemble inner request payload
                    Dictionary<string, string> RequestPayload = new Dictionary<string, string>
                    {
                        { "ApplnRef", ApplnRef },
                        { "ActivationCode", ActivationCode },
                        { "DigitalSignature", DigitalSignature }
                    };
                    #endregion

                    #region ... Prepare and assemble OUTER request payload
                    string RequestRef = Guid.NewGuid().ToString();
                    string ProcCode = "100001";
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
                            #region ... save wallet details

                            // ... unpack the response payload
                            string WALLET_ID = RespPayload.WALLET_ID;
                            string WALLET_ORGCODE = RespPayload.WALLET_ORGCODE;
                            string WALLET_ORGNAME = RespPayload.WALLET_ORGNAME;
                            string CUST_ID = RespPayload.CUST_ID;
                            string CUST_CORE_ID = RespPayload.CUST_CORE_ID;
                            string APPLN_REF_MOB = RespPayload.APPLN_REF_MOB;
                            string CUST_PHONE = RespPayload.CUST_PHONE;

                            // ... prepare to save data to local database
                            Wallet w = new Wallet();
                            w.WALLET_ID = aes.EncryptRawText(WALLET_ID);
                            w.WALLET_ORGCODE = aes.EncryptRawText(WALLET_ORGCODE);
                            w.WALLET_ORGNAME = aes.EncryptRawText(WALLET_ORGNAME);
                            w.CUST_ID = aes.EncryptRawText(CUST_ID);
                            w.CUST_CORE_ID = aes.EncryptRawText(CUST_CORE_ID);
                            w.APPLN_REF_MOB = aes.EncryptRawText(APPLN_REF_MOB);
                            w.CUST_PHONE = aes.EncryptRawText(CUST_PHONE);

                            // ... prepare to save wallet addition progress
                            WalletAdditionProgress wap = new WalletAdditionProgress();
                            wap.WALLET_ID = aes.EncryptRawText(WALLET_ID);
                            wap.ADDITION_PROGRESS = aes.EncryptRawText("ADDED");

                            // ... check if the wallet already exists on the device
                            bool isAlreadyAdded = CheckIfWalletExistsOnDevice(WALLET_ID);
                            if (isAlreadyAdded)
                            {
                                await DisplayAlert("Info", "This wallet is already added", "OK");
                                await Navigation.PushAsync(new MainPage());
                            }
                            else
                            {
                                int rowsAdded = 0;
                                int rowsAddedWap = 0;
                                using (SQLiteConnection conn = new SQLiteConnection(App.FilePath))
                                {
                                    // ... create the wallet
                                    conn.CreateTable<Wallet>();
                                    rowsAdded = conn.Insert(w);

                                    // ... create the addition progress
                                    conn.CreateTable<WalletAdditionProgress>();
                                    rowsAddedWap = conn.Insert(wap);
                                }
                                if ((rowsAdded == 1) && (rowsAddedWap == 1))
                                {
                                    await DisplayAlert("Success", "Wallet has been added to this device", "OK");

                                    // ... get added wallet details
                                    Wallet addedwallet = GetWalletDetailsByID(WALLET_ID);
                                    WalletAdditionProgress addedwalletprog = GetWalletAddedWalletProgDetailsByID(WALLET_ID);

                                    ArrayList datatransfered = new ArrayList();
                                    datatransfered.Add(addedwallet);
                                    datatransfered.Add(addedwalletprog);
                                    await Navigation.PushAsync(new SetWalletPin(datatransfered));
                                }
                                else
                                {
                                    await DisplayAlert("App Error", "Failed to add the wallet to this device.", "OK");
                                }
                            }
                            #endregion
                        }
                        else
                        {
                            //string alert_msg = RespProcCode + "\n" + RespMessage;
                            await DisplayAlert("Alert", RespMessage, "OK");
                        }
                    }
                }
            }
            catch (Exception mm)
            {
                await DisplayAlert("Error 02", mm.Message, "OK");
            }
        }
     
        #endregion


        #region ... 04: ValidateDataReceived
        private bool ValidateDataReceived()
        {
            bool isValid = false;
            try
            {
                bool cnt_ApplnRef = (string.IsNullOrWhiteSpace(txtApplnRef.Text)) ? false : true;
                bool cnt_ActivationCode = (string.IsNullOrWhiteSpace(txtActivationCode.Text)) ? false : true;

                if (cnt_ApplnRef && cnt_ActivationCode)
                {
                    isValid = true;
                }
                else
                {
                    isValid = false;
                }
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 03", mm.Message, "OK");
            }

            return isValid;
        }
        #endregion


        #region ... 05: CheckIfWalletExistsOnDevice 
        private bool CheckIfWalletExistsOnDevice(string WALLET_ID)
        {
            bool isAlreadyAdded = false;
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
                            if (dec_wallet_id == WALLET_ID)
                            {
                                isAlreadyAdded = true;
                            }
                        }//..end..loop
                    }//..end..iff
                }//..end..conn
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 04", mm.Message, "OK");
            }
            return isAlreadyAdded;
        }
        #endregion


        #region ... 06: GetWalletDetailsByID
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


        #region ... 07: GetWalletAddedWalletProgDetailsByID
        private WalletAdditionProgress GetWalletAddedWalletProgDetailsByID(string WALLET_ID)
        {
            WalletAdditionProgress wap = new WalletAdditionProgress();
            try
            {
                using (SQLiteConnection conn = new SQLiteConnection(App.FilePath))
                {
                    conn.CreateTable<Wallet>();
                    List<WalletAdditionProgress> wallet_list = conn.Query<WalletAdditionProgress>("SELECT * FROM WALLETADDITIONPROGRESS").ToList();
                    if (wallet_list.Count > 0)
                    {
                        for (int i = 0; i < wallet_list.Count; i++)
                        {
                            WalletAdditionProgress wap_ww = new WalletAdditionProgress();
                            wap_ww = wallet_list[i];

                            string dec_wallet_id = aes.DecryptCipheredText(wap_ww.WALLET_ID);
                            if (dec_wallet_id.Equals(WALLET_ID))
                            {
                                wap = wap_ww;
                            }
                        }//..end..loop
                    }//..end..iff
                }//..end..conn
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 04", mm.Message, "OK");
            }
            return wap;
        }
        #endregion


    }
}