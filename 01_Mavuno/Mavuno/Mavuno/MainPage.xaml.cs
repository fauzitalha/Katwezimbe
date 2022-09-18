using Mavuno.core;
using Mavuno.db;
using SQLite;
using System;
using System.Collections;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using Xamarin.Forms;

namespace Mavuno
{
    public partial class MainPage : ContentPage
    {

        #region ... Class Variables
        CoreFunctions cf = new CoreFunctions();
        AES256.AES256 aes = new AES256.AES256();
        #endregion


        #region ... 01: init page
        public MainPage()
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


        #region ... 02: On Appearing Class
        protected override void OnAppearing()
        {
            try
            {
                base.OnAppearing();

                List<Wallet> wallet_list = new List<Wallet>();
                List<WalletListView> wallet_list_view = new List<WalletListView>();

                // ... check for exisitng wallets
                using (SQLiteConnection conn = new SQLiteConnection(App.FilePath))
                {
                    conn.CreateTable<Wallet>();
                    wallet_list = conn.Table<Wallet>().ToList();
                }

                int wallet_count = wallet_list.Count;
                if (wallet_count <= 0)
                {
                    Navigation.PushAsync(new AddNewWallet());
                }
                else
                {
                    for (int i = 0; i < wallet_list.Count; i++)
                    {
                        Wallet WALLET = wallet_list[i];

                        WalletListView wallet_lv = new WalletListView();
                        wallet_lv.ID = WALLET.ID;
                        wallet_lv.WALLET_ID = aes.DecryptCipheredText(WALLET.WALLET_ID);
                        wallet_lv.WALLET_ORGCODE = aes.DecryptCipheredText(WALLET.WALLET_ORGCODE);
                        wallet_lv.WALLET_ORGNAME = aes.DecryptCipheredText(WALLET.WALLET_ORGNAME);
                        wallet_lv.CUST_ID = aes.DecryptCipheredText(WALLET.CUST_ID);
                        wallet_lv.CUST_CORE_ID = aes.DecryptCipheredText(WALLET.CUST_CORE_ID);
                        wallet_lv.APPLN_REF_MOB = aes.DecryptCipheredText(WALLET.APPLN_REF_MOB);
                        wallet_lv.CUST_PHONE = aes.DecryptCipheredText(WALLET.CUST_PHONE);
                        wallet_lv.IMAGE_URL = "http://10.0.3.2/mobilr/assets/img/payment.png";

                        wallet_list_view.Add(wallet_lv);
                    }

                    walletListView.ItemsSource = wallet_list_view;
                }
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 02", mm.Message, "OK");
            }
        }
        #endregion


        #region ... 03: Add Wallet
        private void AddWalletToolBarItem_Clicked(object sender, EventArgs e)
        {
            try
            {
                Navigation.PushAsync(new AddNewWallet());
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 01", mm.Message, "OK");
            }
        }
        #endregion


        #region ... 04: Item selected
        private void OnListViewItemSelected(object sender, SelectedItemChangedEventArgs e)
        {
            try
            {
                WalletListView selectedItem = e.SelectedItem as WalletListView;

                // ... Verify Wallet Addition Progress
                WalletAdditionProgress addedwalletprog = GetWalletAddedWalletProgDetailsByID(selectedItem.WALLET_ID);
                string STATUS = aes.DecryptCipheredText(addedwalletprog.ADDITION_PROGRESS);
                if (STATUS.Equals("COMPLETED"))
                {
                    ArrayList datatransfered = new ArrayList();
                    datatransfered.Add(selectedItem);
                    Navigation.PushAsync(new LoginScreen(datatransfered));
                }
                else
                {
                    DisplayAlert("Alert", "Cannot proceed to login. Wallet set up was not completed. Contact Management", "OK");
                }           
            }
            catch (Exception mm)
            {
                DisplayAlert("Error 01", mm.Message, "OK");
            }
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
