using Newtonsoft.Json;
using StackExchange.Redis;
using System.Text;
using System.Text.RegularExpressions;
using System.Xml;
using System.Xml.Linq;
using System.Xml.Serialization;

namespace MTNOpenApi_Collections_GetTranDetails_Service.Core
{
    public class CoreHelpers
    {
        #region ... UTIL 01: ToDictionary
        public static Dictionary<string, string> ToDictionary(object obj)
        {
            var json = JsonConvert.SerializeObject(obj);
            var dictionary = JsonConvert.DeserializeObject<Dictionary<string, string>>(json);
            return dictionary;
        }
        #endregion


        #region ... UTIL 02: PopulateStringTemplate
        public static string PopulateStringTemplate(string template, Dictionary<string, string> dictnry)
        {
            string final_string = Regex.Replace(template, @"\[(.+?)\]", m => dictnry[m.Groups[1].Value]);
            return final_string;
        }
        #endregion


        #region ... UTIL 03: FormatJson
        public static string FormatJson(object obj)
        {
            string final_string = JsonConvert.SerializeObject(obj, Newtonsoft.Json.Formatting.Indented);
            return final_string;
        }
        #endregion


        #region ... UTIL 04: FormatXml
        public static string FormatXml(object obj)
        {

            XmlSerializer xsSubmit = new XmlSerializer(obj.GetType());
            //var subReq = new RequestMessage();
            var subReq = "";
            var xml = "";

            using (var sww = new StringWriter())
            {
                using (XmlWriter writer = XmlWriter.Create(sww))
                {
                    xsSubmit.Serialize(writer, subReq);
                    xml = sww.ToString(); // Your XML
                }
            }

            return xml;
        }
        #endregion


        #region ... UTIL 05: GenerateRequestProcRef
        public static string GenerateRequestProcRef()
        {
            string proc_ref = Guid.NewGuid().ToString();
            return proc_ref;
        }
        #endregion


        #region ... UTIL 06: SerializeToXml
        public static string SerializeToXml(object dataToSerialize)
        {
            if (dataToSerialize == null) return null;

            using (StringWriter stringwriter = new System.IO.StringWriter())
            {
                var serializer = new XmlSerializer(dataToSerialize.GetType());
                serializer.Serialize(stringwriter, dataToSerialize);
                return stringwriter.ToString();
            }
        }
        #endregion


        #region ... UTIL 07: PrettyXml
        public static string PrettyXml(string xml)
        {
            var stringBuilder = new StringBuilder();

            var element = XElement.Parse(xml);

            var settings = new XmlWriterSettings();
            settings.OmitXmlDeclaration = true;
            settings.Indent = true;
            settings.NewLineOnAttributes = true;

            using (var xmlWriter = XmlWriter.Create(stringBuilder, settings))
            {
                element.Save(xmlWriter);
            }

            return stringBuilder.ToString();
        }

        #endregion


        #region ... UTIL 08: ToDictionaryXml
        public static Dictionary<string, string> ToDictionaryXml(XmlNode[] xmlNodeList)
        {
            Dictionary<string, string> result = new Dictionary<string, string>();
            foreach (XmlNode xn in xmlNodeList)
            {
                string key = xn.Name;
                string value = xn.InnerText;

                result.Add(key, value);
            }
            return result;
        }
        #endregion


        #region ... UTIL 09: ToDictionaryHashEntry
        public static Dictionary<string, string> ToDictionaryHashEntry(HashEntry[] hashEntryList)
        {
            Dictionary<string, string> result = new Dictionary<string, string>();
            foreach (HashEntry he in hashEntryList)
            {
                string key = he.Name;
                string value = he.Value;

                result.Add(key, value);
            }
            return result;
        }
        #endregion


        #region ... UTIL 10: FromDictionaryToObject
        public static dynamic FromDictionaryToObject(Dictionary<string, string> dict)
        {
            string json_str = JsonConvert.SerializeObject(dict);
            dynamic result = JsonConvert.DeserializeObject(json_str);
            return result;
        }
        #endregion

    }
}
