using Newtonsoft.Json;
using System;
using System.Collections.Generic;
using System.Collections.Specialized;
using System.IO;
using System.Net;
using System.Net.Http;
using System.Net.Http.Headers;
using System.Text;
using System.Threading.Tasks;

namespace Mavuno.core
{
    class CoreFunctions
    {

        #region ... Class Variables
        string TRAN_URI = Constants.TRAN_URI;
        string DOCC_URI = Constants.DOCC_URI;
        #endregion

        #region ... 01: Send Request Message
        public string[] PostToMoBilr(string message)
        {
            string[] respdetails = new string[2];
            string resp_code = "ERR";
            string resp_mssg = "";
            try
            {
                HttpWebRequest request = (HttpWebRequest)WebRequest.Create(TRAN_URI);
                request.KeepAlive = false;
                byte[] bytes;
                bytes = Encoding.ASCII.GetBytes(message);

                // ... preparing and encoding the request
                request.ContentType = "application/json; encoding='utf-8'";
                request.ContentLength = bytes.Length;
                //request.Credentials = new NetworkCredential(username, password);
                request.ServerCertificateValidationCallback = new System.Net.Security.RemoteCertificateValidationCallback(delegate { return true; });
                request.Method = "POST";
                Stream requestStream = request.GetRequestStream();
                requestStream.Write(bytes, 0, bytes.Length);
                requestStream.Close();

                // ... preparing for the response
                HttpWebResponse response;
                response = (HttpWebResponse)request.GetResponse();
                if (response.StatusCode == HttpStatusCode.OK)
                {
                    Stream responseStream = response.GetResponseStream();
                    resp_mssg = new StreamReader(responseStream).ReadToEnd();
                    resp_code = "OKK";

                    // ... Loading the resp details
                    respdetails[0] = resp_code;
                    respdetails[1] = resp_mssg;
                }
            }
            catch (Exception mm)
            {
                string err = mm.Message;
                resp_mssg = "ERR 0001: " + err;
                resp_code = "ERR";

                // ... Loading the resp details
                respdetails[0] = resp_code;
                respdetails[1] = resp_mssg;
            }

            return respdetails;
        }
        #endregion

        #region ... 01.11: Send Request Message (Async)
        public async Task<string[]> PostToMoBilrAsync(string message)
        {
            string[] respdetails = new string[2];
            string resp_code = "ERR";
            string resp_mssg = "";
            try
            {
                HttpWebRequest request = (HttpWebRequest)WebRequest.Create(TRAN_URI);
                request.KeepAlive = false;
                byte[] bytes;
                bytes = Encoding.ASCII.GetBytes(message);

                // ... preparing and encoding the request
                request.ContentType = "application/json; encoding='utf-8'";
                request.ContentLength = bytes.Length;
                //request.Credentials = new NetworkCredential(username, password);
                request.ServerCertificateValidationCallback = new System.Net.Security.RemoteCertificateValidationCallback(delegate { return true; });
                request.Method = "POST";
                Stream requestStream = request.GetRequestStream();
                requestStream.Write(bytes, 0, bytes.Length);
                requestStream.Close();

                // ... preparing for the response
                HttpWebResponse response;
                response = (HttpWebResponse) await request.GetResponseAsync();
                if (response.StatusCode == HttpStatusCode.OK)
                {
                    Stream responseStream = response.GetResponseStream();
                    resp_mssg = new StreamReader(responseStream).ReadToEnd();
                    resp_code = "OKK";

                    // ... Loading the resp details
                    respdetails[0] = resp_code;
                    respdetails[1] = resp_mssg;
                }
            }
            catch (Exception mm)
            {
                string err = mm.Message;
                resp_mssg = "ERR 0001: " + err;
                resp_code = "ERR";

                // ... Loading the resp details
                respdetails[0] = resp_code;
                respdetails[1] = resp_mssg;
            }

            return respdetails;
        }
        #endregion


        #region ... 02: FillUp Date
        public string FillUpDate(string nn)
        {
            string datev = "";
            if (nn.Length==1)
            {
                datev = "0" + nn;
            }
            else
            {
                datev = nn;
            }
            return datev;
        }
        #endregion

        #region ... 03: Human Date
        public string HumanDate(string TDate)
        {
            DateTime TDate2;
            DateTime.TryParse(TDate, out TDate2);
            string TranDate = TDate2.ToString("dd-MMM-yyyy");
            return TranDate;
        }
        #endregion

        #region ... 04: IsValidEmail
        public bool IsValidEmail(string email)
        {
            try
            {
                var addr = new System.Net.Mail.MailAddress(email);
                return addr.Address == email;
            }
            catch
            {
                return false;
            }
        }
        #endregion

        #region ... 05: Send File
        public string[] PostToMoBilr_File(string file, string paramName, string contentType, NameValueCollection nvc)
        {
            string[] respdetails = new string[2];
            string resp_code = "ERR";
            string resp_mssg = "";

            try
            {
                string boundary = "---------------------------" + DateTime.Now.Ticks.ToString("x");
                byte[] boundarybytes = Encoding.ASCII.GetBytes("\r\n--" + boundary + "\r\n");

                HttpWebRequest wr = (HttpWebRequest)WebRequest.Create(DOCC_URI);
                wr.ContentType = "multipart/form-data; boundary=" + boundary;
                wr.Method = "POST";
                Stream rs = wr.GetRequestStream();

                string formdataTemplate = "Content-Disposition: form-data; name=\"{0}\"\r\n\r\n{1}";
                foreach (string key in nvc.Keys)
                {
                    rs.Write(boundarybytes, 0, boundarybytes.Length);
                    string formitem = string.Format(formdataTemplate, key, nvc[key]);
                    byte[] formitembytes = Encoding.UTF8.GetBytes(formitem);
                    rs.Write(formitembytes, 0, formitembytes.Length);
                }
                rs.Write(boundarybytes, 0, boundarybytes.Length);

                string headerTemplate = "Content-Disposition: form-data; name=\"{0}\"; filename=\"{1}\"\r\nContent-Type: {2}\r\n\r\n";
                string header = string.Format(headerTemplate, paramName, file, contentType);
                byte[] headerbytes = Encoding.UTF8.GetBytes(header);
                rs.Write(headerbytes, 0, headerbytes.Length);

                FileStream fileStream = new FileStream(file, FileMode.Open, FileAccess.Read);
                byte[] buffer = new byte[4096];
                int bytesRead = 0;
                while ((bytesRead = fileStream.Read(buffer, 0, buffer.Length)) != 0)
                {
                    rs.Write(buffer, 0, bytesRead);
                }
                fileStream.Close();

                byte[] trailer = Encoding.ASCII.GetBytes("\r\n--" + boundary + "--\r\n");
                rs.Write(trailer, 0, trailer.Length);
                rs.Close();

                // ... preparing for the response
                HttpWebResponse response;
                response = (HttpWebResponse)wr.GetResponse();
                if (response.StatusCode == HttpStatusCode.OK)
                {
                    Stream responseStream = response.GetResponseStream();
                    resp_mssg = new StreamReader(responseStream).ReadToEnd();
                    resp_code = "OKK";

                    // ... Loading the resp details
                    respdetails[0] = resp_code;
                    respdetails[1] = resp_mssg;
                } 
            }
            catch (Exception mm)
            {
                string err = mm.Message;
                resp_mssg = "ERR 0001: " + err;
                resp_code = "ERR";

                // ... Loading the resp details
                respdetails[0] = resp_code;
                respdetails[1] = resp_mssg;
            }

            
            return respdetails;
        }



        /*public static void HttpUploadFile(string url, string file, string paramName, string contentType, NameValueCollection nvc)
        {
            log.Debug(string.Format("Uploading {0} to {1}", file, url));
            string boundary = "---------------------------" + DateTime.Now.Ticks.ToString("x");
            byte[] boundarybytes = System.Text.Encoding.ASCII.GetBytes("\r\n--" + boundary + "\r\n");

            HttpWebRequest wr = (HttpWebRequest)WebRequest.Create(url);
            wr.ContentType = "multipart/form-data; boundary=" + boundary;
            wr.Method = "POST";
            wr.KeepAlive = true;
            wr.Credentials = System.Net.CredentialCache.DefaultCredentials;

            Stream rs = wr.GetRequestStream();

            string formdataTemplate = "Content-Disposition: form-data; name=\"{0}\"\r\n\r\n{1}";
            foreach (string key in nvc.Keys)
            {
                rs.Write(boundarybytes, 0, boundarybytes.Length);
                string formitem = string.Format(formdataTemplate, key, nvc[key]);
                byte[] formitembytes = System.Text.Encoding.UTF8.GetBytes(formitem);
                rs.Write(formitembytes, 0, formitembytes.Length);
            }
            rs.Write(boundarybytes, 0, boundarybytes.Length);

            string headerTemplate = "Content-Disposition: form-data; name=\"{0}\"; filename=\"{1}\"\r\nContent-Type: {2}\r\n\r\n";
            string header = string.Format(headerTemplate, paramName, file, contentType);
            byte[] headerbytes = System.Text.Encoding.UTF8.GetBytes(header);
            rs.Write(headerbytes, 0, headerbytes.Length);

            FileStream fileStream = new FileStream(file, FileMode.Open, FileAccess.Read);
            byte[] buffer = new byte[4096];
            int bytesRead = 0;
            while ((bytesRead = fileStream.Read(buffer, 0, buffer.Length)) != 0)
            {
                rs.Write(buffer, 0, bytesRead);
            }
            fileStream.Close();

            byte[] trailer = System.Text.Encoding.ASCII.GetBytes("\r\n--" + boundary + "--\r\n");
            rs.Write(trailer, 0, trailer.Length);
            rs.Close();

            WebResponse wresp = null;
            try
            {
                wresp = wr.GetResponse();
                Stream stream2 = wresp.GetResponseStream();
                StreamReader reader2 = new StreamReader(stream2);
                log.Debug(string.Format("File uploaded, server response is: {0}", reader2.ReadToEnd()));
            }
            catch (Exception ex)
            {
                log.Error("Error uploading file", ex);
                if (wresp != null)
                {
                    wresp.Close();
                    wresp = null;
                }
            }
            finally
            {
                wr = null;
            }
        }*/
        #endregion

    }
}
