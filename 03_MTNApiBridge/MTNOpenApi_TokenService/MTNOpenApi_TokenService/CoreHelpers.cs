using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Text.RegularExpressions;
using System.Threading.Tasks;

namespace MTNOpenApi_TokenService
{
    internal class CoreHelpers
    {

        #region ... UTIL 01: PopulateStringTemplate
        public static string PopulateStringTemplate(string template, Dictionary<string, string> dictnry)
        {
            string final_string = Regex.Replace(template, @"\[(.+?)\]", m => dictnry[m.Groups[1].Value]);
            return final_string;
        }
        #endregion


    }
}
