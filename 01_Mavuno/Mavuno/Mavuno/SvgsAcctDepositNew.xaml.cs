using Acr.UserDialogs;
using Mavuno.core;
using Mavuno.db;
using Newtonsoft.Json;
using Plugin.FilePicker;
using Plugin.FilePicker.Abstractions;
using System;
using System.Collections;
using System.Collections.Generic;
using System.Collections.Specialized;
using System.IO;
using System.Linq;
using System.Net.Http;
using System.Threading.Tasks;
using Xamarin.Forms;
using Xamarin.Forms.Xaml;

namespace Mavuno
{
	[XamlCompilation(XamlCompilationOptions.Compile)]
	public partial class SvgsAcctDepositNew : ContentPage
	{

        #region ... Class Variables
        CoreFunctions cf = new CoreFunctions();
        AES256.AES256 aes = new AES256.AES256();
        private DateTime LAST_ACTIVITY_TIME;

        private Wallet WALLET = new Wallet();
        private List<string> SESS = new List<string>();
        private dynamic CORE_CLIENT_DETAILS;
        private SavingsAcctBasic SAB = new SavingsAcctBasic();
        private FileData DEPOSIT_RECEIPT = new FileData();
        private string DEPOSIT_REF = "";
        #endregion

        #region ... 01: Class Constructor
        public SvgsAcctDepositNew()
        {
            try
            {
                InitializeComponent();

                // ... Set Timeout Value
                UpdateLastActivityTime();

                // ... populate dropdown
                ddlTrantype.ItemsSource = Constants.DEPOSIT_TRAN_TYPE_LIST;
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 01", mm.Message, "OK");
            }
        }
        #endregion

        #region ... 02: Class Constructor Overload
        public SvgsAcctDepositNew(ArrayList datatransfered)
        {
            try
            {
                InitializeComponent();

                // ... Set Timeout Value
                UpdateLastActivityTime();

                // ... populate dropdown
                ddlTrantype.ItemsSource = Constants.DEPOSIT_TRAN_TYPE_LIST;

                // ... receive data
                WALLET = (Wallet)datatransfered[0];
                SESS = (List<string>)datatransfered[1];
                CORE_CLIENT_DETAILS = (dynamic)datatransfered[2];
                SAB = (SavingsAcctBasic)datatransfered[3];

                // ... DisplayWithdrawRqstData
                lblTitleView.Text = "New Deposit: " + SAB.account_no;
                lblSvgCrAcct.Text = SAB.account_no + " - " + SAB.product_name;
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

        #region ... 06: File Picker
        private async void BtnPymtReceipt_Clicked(object sender, EventArgs e)
        {
            try
            {
                FileData fileData = await CrossFilePicker.Current.PickFile();
                if (fileData == null)
                {
                    return; // user canceled file picking
                }
                else
                {
                    FileMimeType dd = new FileMimeType();
                    string fileName = fileData.FileName;
                    string filePath = fileData.FilePath;
                    string f_exxt = Path.GetExtension(filePath);
                    FileInfo fi = new FileInfo(filePath);
                    double fff_size = fi.Length;
                    string f_size = fff_size.ToString();
                    string file_name = fileData.FileName;
                    string file_type = dd.GetMimeType(f_exxt);
                    string contents = System.Text.Encoding.UTF8.GetString(fileData.DataArray);

                    // ... Verify file type
                    bool is_acceptable = Constants.ACCEPTABLE_FILE_TYPES.Contains(f_exxt) ? true : false;
                    if (is_acceptable)
                    {
                        // ... verify file size
                        if (fff_size>Constants.MAX_FILE_SIZE)
                        {
                            await DisplayAlert("File Size", "Uploaded file size exceeds acceptable size. Upload file of less size", "OK");
                        }
                        else
                        {
                            lblReceiptFile.Text = "Payment Receipt: " + fileName;
                            DEPOSIT_RECEIPT = fileData;
                        }
                    }
                    else
                    {
                        await DisplayAlert("File Format", "The receipt format submitted is invalid. Only JPG, JPEG, PNG and PDF are acceptable.", "OK");
                    }
                }
            }
            catch (Exception mm)
            {
                await DisplayAlert("Error 02", mm.Message, "OK");
            }
        }
        #endregion

        #region ... 07: Submit Deposit
        private void BtnSubmit_Clicked(object sender, EventArgs e)
        {
            Device.BeginInvokeOnMainThread(async () =>
            {
                try
                {
                    using (UserDialogs.Instance.Loading("Submitting Deposit Application"))
                    {
                        await Task.Delay(300);
                        await SubmitDepositAppln();          // ... the actual task
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

        #region ... 08: Submit Deposit Appln
        private async Task SubmitDepositAppln()
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
                    string SavingsAcctIdToCr = SAB.svgs_acct_id;
                    string DepositedAmount = txtDepositAmount.Text;
                    string channel = Constants.CHANNEL;

                    #region ... processing channel specific parameters
                    string p_method = "", p_partnerId = "", p_partnerAcctNo = "", p_partnerAcctName = "", p_partnerReceiptRef = "";
                    string tran_type = ddlTrantype.SelectedItem.ToString();

                    if (tran_type.Equals("Deposit with Mobile Money"))
                    {
                        p_method = "MOMO";
                        p_partnerId = "MOMO";
                        p_partnerAcctNo = aes.DecryptCipheredText(WALLET.CUST_PHONE);
                        p_partnerAcctName = "MOMO";
                        p_partnerReceiptRef = "MOMO_NO_RECEIPT_REF";
                    }
                    else if (tran_type.Equals("Deposit done in Bank/Other Partner"))
                    {
                        p_method = "BANK-DEPOSIT";
                        p_partnerId = "BANK";
                        p_partnerAcctNo = txtBankAcctNum.Text;
                        p_partnerAcctName = txtBankName.Text;
                        p_partnerReceiptRef = txtDepositReceiptRef.Text;
                    }
                    #endregion

                    string DepositMethod = p_method;
                    string PartnerId = p_partnerId;
                    string PartnerAcctNo = p_partnerAcctNo;
                    string PartnerAcctName = p_partnerAcctName;
                    string PartnerReceiptRef = p_partnerReceiptRef;
                    string Narration = txtReason.Text;
                    string MemberPhone = aes.DecryptCipheredText(WALLET.CUST_PHONE);

                    IDevice device = DependencyService.Get<IDevice>();
                    string DeviceSerial = device.GetIdentifier();

                    string data_to_sign = WalletId + "^" + OrgCode + "^" + CustId + "^" + SavingsAcctIdToCr + "^" + DepositedAmount + "^" + channel + "^" + 
                                          DepositMethod + "^" + PartnerId + "^" + PartnerAcctNo + "^" + PartnerAcctName + "^" + PartnerReceiptRef + "^" + 
                                          Narration + "^" + MemberPhone + "^" + DeviceSerial;
                    string DigitalSignature = aes.EncryptRawText(data_to_sign);

                    // ... assemble inner request payload
                    Dictionary<string, string> RequestPayload = new Dictionary<string, string>
                    {
                        { "WalletId", WalletId },
                        { "OrgCode", OrgCode },
                        { "CustId", CustId },
                        { "SavingsAcctIdToCr", SavingsAcctIdToCr },
                        { "DepositedAmount", DepositedAmount },
                        { "channel", channel },
                        { "DepositMethod", DepositMethod },
                        { "PartnerId", PartnerId },
                        { "PartnerAcctNo", PartnerAcctNo },
                        { "PartnerAcctName", PartnerAcctName },
                        { "PartnerReceiptRef", PartnerReceiptRef },
                        { "Narration", Narration },
                        { "MemberPhone", MemberPhone },
                        { "DeviceSerial", DeviceSerial },
                        { "DigitalSignature", DigitalSignature }
                    };
                    #endregion

                    #region ... Prepare and assemble OUTER request payload
                    string RequestRef = Guid.NewGuid().ToString();
                    string ProcCode = "200007";
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
                            DEPOSIT_REF = RespPayload.ToString();

                            if (DEPOSIT_RECEIPT.FileName == null|| DEPOSIT_RECEIPT.FileName == "")
                            {
                                await DisplayAlert("Success", "Deposit request has been received successfully.\nTRAN_REF: " + DEPOSIT_REF, "OK");
                            }
                            else
                            {
                                // ... upload receipt file
                                ArrayList file_upload_details = UploadReceiptFile();
                                bool is_uploaded = (bool)file_upload_details[0];
                                string rr_mssg = (string)file_upload_details[1];
                                if (is_uploaded)
                                {
                                    await DisplayAlert("Success", "Deposit request has been received successfully.\n" +
                                                                  "TRAN_REF: " + DEPOSIT_REF + "\n\n" +
                                                                  "Deposit receipt upload result\n" +
                                                                  "MESSAGE: " + rr_mssg, "OK");
                                }
                                else
                                {
                                    await DisplayAlert("Success", "Deposit request has been received successfully.\n" +
                                                                  "TRAN_REF: " + DEPOSIT_REF + "\n\n" +
                                                                  "Deposit receipt upload Failed\n" +
                                                                  "REASON: " + rr_mssg, "OK");
                                }
                            }
                            
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
                bool cnt_WithdrawAmount = (string.IsNullOrWhiteSpace(txtDepositAmount.Text)) ? false : true;
                bool cnt_Reason = (string.IsNullOrWhiteSpace(txtReason.Text)) ? false : true;
                bool cnt_BankAcctNum = (string.IsNullOrWhiteSpace(txtBankAcctNum.Text)) ? false : true;
                bool cnt_BankName = (string.IsNullOrWhiteSpace(txtBankName.Text)) ? false : true;

                string tran_type = ddlTrantype.SelectedItem.ToString();
                if (tran_type.Equals("Deposit with Mobile Money"))
                {
                    if (cnt_WithdrawAmount && cnt_Reason)
                    {
                        is_valid = true;
                        val_mssg = "data is good";
                    }
                    else
                    {
                        is_valid = false;
                        val_mssg = "Enter Missing Data. Cannot Proceed";
                    }
                }
                else if (tran_type.Equals("Deposit done in Bank/Other Partner"))
                {
                    if (cnt_WithdrawAmount && cnt_Reason && cnt_BankAcctNum && cnt_BankName)
                    {
                        is_valid = true;
                        val_mssg = "data is good";
                    }
                    else
                    {
                        is_valid = false;
                        val_mssg = "Enter Missing Data. Cannot Proceed";
                    }
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

        #region ... 10: On selecting trantype
        private void DdlTrantype_SelectedIndexChanged(object sender, EventArgs e)
        {
            string tran_type = ddlTrantype.SelectedItem.ToString();
            if (tran_type.Equals("Deposit with Mobile Money"))
            {
                lblAcctMsisdn.IsVisible = false;
                txtBankAcctNum.IsVisible = false;

                lblPartner.IsVisible = false;
                txtBankName.IsVisible = false;

                lblDepositReceiptRef.IsVisible = false;
                txtDepositReceiptRef.IsVisible = false;

                lblDepositReceiptFile.IsVisible = false;
                btnDepositReceiptFile.IsVisible = false;
                lblReceiptFile.IsVisible = false;
            }
            else
            {
                lblAcctMsisdn.IsVisible = true;
                txtBankAcctNum.IsVisible = true;

                lblPartner.IsVisible = true;
                txtBankName.IsVisible = true;

                lblDepositReceiptRef.IsVisible = true;
                txtDepositReceiptRef.IsVisible = true;

                lblDepositReceiptFile.IsVisible = true;
                btnDepositReceiptFile.IsVisible = true;
                lblReceiptFile.IsVisible = true;
            }

        }
        #endregion

        #region ... 11: Upload Receipt
        private ArrayList UploadReceiptFile()
        {
            ArrayList file_upload_details = new ArrayList();
            bool is_uploaded = false;
            string rr_mssg = "";
            try
            {
                if (DEPOSIT_RECEIPT==null)
                {
                    is_uploaded = false;
                    rr_mssg = "There is no receipt attached for upload";
                }
                else
                {
                    FileMimeType dd = new FileMimeType();
                   
                    #region ... assemble file parameters
                    string filePath = DEPOSIT_RECEIPT.FilePath;
                    string f_exxt = Path.GetExtension(filePath);
                    FileInfo fi = new FileInfo(filePath);
                    double fff_size = fi.Length;
                    string f_size = fff_size.ToString();
                    string file_name = DEPOSIT_RECEIPT.FileName;
                    string file_type = dd.GetMimeType(f_exxt);
                    string contents = System.Text.Encoding.UTF8.GetString(DEPOSIT_RECEIPT.DataArray);
                    #endregion

                    #region ... assemble request message
                    string WalletId = aes.DecryptCipheredText(WALLET.WALLET_ID);
                    string OrgCode = aes.DecryptCipheredText(WALLET.WALLET_ORGCODE);
                    string CustId = aes.DecryptCipheredText(WALLET.CUST_ID);
                    string CustCoreId = aes.DecryptCipheredText(WALLET.CUST_CORE_ID);
                    string FileUploadProc = "UPL_SVNGS_DEPOSIT_RECEIPT";
                    string FileUploadProcRef = DEPOSIT_REF;
                    string Filename = file_name;
                    string FileMimeType = file_type;
                    string FileExt = f_exxt.Replace(".", "");
                    string FileSize = f_size;
                    string MemberPhone = aes.DecryptCipheredText(WALLET.CUST_PHONE);

                    IDevice device = DependencyService.Get<IDevice>();
                    string DeviceSerial = device.GetIdentifier();

                    string data_to_sign = WalletId + "^" + OrgCode + "^" + CustId + "^" + CustCoreId + "^" + FileUploadProc + "^" + FileUploadProcRef + "^" + Filename + "^" +
                                          FileMimeType + "^" + FileExt + "^" + FileSize + "^" + MemberPhone + "^" + DeviceSerial;
                    string DigitalSignature = aes.EncryptRawText(data_to_sign);
                    #endregion

                    #region ... assemble posting parameters
                    NameValueCollection nvc = new NameValueCollection();
                    nvc.Add("WalletId", WalletId);
                    nvc.Add("OrgCode", OrgCode);
                    nvc.Add("CustId", CustId);
                    nvc.Add("CustCoreId", CustCoreId);
                    nvc.Add("FileUploadProc", FileUploadProc);
                    nvc.Add("FileUploadProcRef", FileUploadProcRef);
                    nvc.Add("Filename", Filename);
                    nvc.Add("FileMimeType", FileMimeType);
                    nvc.Add("FileExt", FileExt);
                    nvc.Add("FileSize", FileSize);
                    nvc.Add("MemberPhone", MemberPhone);
                    nvc.Add("DeviceSerial", DeviceSerial);
                    nvc.Add("DigitalSignature", DigitalSignature);


                    string FILE_PATH = filePath;
                    string FILE_PARAM_NAME = "FileAttachment";
                    string FILE_MIMETYPE = file_type;
                    #endregion

                    string[] respdetails = cf.PostToMoBilr_File(FILE_PATH, FILE_PARAM_NAME, FILE_MIMETYPE, nvc);
                    string resp_code = respdetails[0];
                    string resp_mssg = respdetails[1];
                    if (resp_code.Equals("ERR"))
                    {
                        is_uploaded = false;
                        rr_mssg = resp_mssg;
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
                            string good_message = RespPayload.ToString();
                            is_uploaded = true;
                            rr_mssg = good_message;
                        }
                        else
                        {
                            is_uploaded = false;
                            rr_mssg = RespMessage;
                        }
                    }

                }
            }
            catch (Exception mm)
            {
                is_uploaded = false;
                rr_mssg = mm.Message;
            }

            // ... add details
            file_upload_details.Add(is_uploaded);
            file_upload_details.Add(rr_mssg);

            return file_upload_details;
        }
        #endregion

    }
}