

namespace MTNOpenApi_TokenService
{
    internal class Program
    {


        #region ... M01: Main Method
        static void Main(string[] args)
        {

            CoreProcessor cp = new CoreProcessor();


            // ... collections token
            cp.RefreshAccessToken_COLLECTIONS();

            
            // ... disbursements token
            cp.RefreshAccessToken_DISBURSEMENTS();

            

        }
        #endregion



    }
}
