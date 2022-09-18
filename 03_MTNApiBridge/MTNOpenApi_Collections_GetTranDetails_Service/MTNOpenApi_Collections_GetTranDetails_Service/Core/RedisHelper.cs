using MTNOpenApi_Collections_GetTranDetails_Service.Models;
using StackExchange.Redis;

namespace MTNOpenApi_Collections_GetTranDetails_Service.Core
{
    public class RedisHelper
    {
        #region ... VARIABLES
        static AppLogger applogger = new AppLogger();
        static LogMessage lmsg = new LogMessage();
        private static Lazy<ConnectionMultiplexer> lazyConnection;
        #endregion


        #region ... M01: RedisHelper [CONSTRUCTOR]
        static RedisHelper()
        {
            try
            {
                string redisHost = AppConfig.REDIS_HOST;
                string redisPort = AppConfig.REDIS_PORT;
                string redisHostPort = redisHost + ":" + redisPort;

                RedisHelper.lazyConnection = new Lazy<ConnectionMultiplexer>(() =>
                {
                    return ConnectionMultiplexer.Connect(redisHostPort);
                });
            }
            catch (Exception ex)
            {
                lmsg.LOG_LEVEL = LogInfo.ERROR;
                string msg = ex.Message;
                string stack_trace = ex.StackTrace;
                applogger.LogToFile(lmsg, lmsg.LOG_LEVEL, "RedisHelper.RedisHelper", msg);
                applogger.LogToFile(lmsg, lmsg.LOG_LEVEL, "RedisHelper.RedisHelper", stack_trace);
                applogger.LogFileSeparator();
            }
        }
        #endregion


        #region ... VARIABLE: Connection
        public static ConnectionMultiplexer Connection
        {
            get
            {
                return lazyConnection.Value;
            }
        }
        #endregion


        #region ... M02: ReadData_STRING
        public static string ReadData_STRING(string key)
        {
            string value = "";
            try
            {
                IDatabase cache = Connection.GetDatabase();
                value = cache.StringGet(key);
            }
            catch (Exception ex)
            {
                lmsg.LOG_LEVEL = LogInfo.ERROR;
                string msg = ex.Message;
                string stack_trace = ex.StackTrace;
                applogger.LogToFile(lmsg, lmsg.LOG_LEVEL, "RedisHelper.ReadData", msg);
                applogger.LogToFile(lmsg, lmsg.LOG_LEVEL, "RedisHelper.ReadData", stack_trace);
                applogger.LogFileSeparator();
            }


            return value;
        }
        #endregion


        #region ... M03: SaveData_HASH
        public static void SaveData_HASH(string hash_key, List<HashEntry> hash_value)
        {
            try
            {
                IDatabase cache = Connection.GetDatabase();
                cache.HashSet(hash_key, hash_value.ToArray(), CommandFlags.None);
            }
            catch (Exception ex)
            {
                lmsg.LOG_LEVEL = LogInfo.ERROR;
                string msg = ex.Message;
                string stack_trace = ex.StackTrace;
                applogger.LogToFile(lmsg, lmsg.LOG_LEVEL, "RedisHelper.SaveData_HASH", msg);
                applogger.LogToFile(lmsg, lmsg.LOG_LEVEL, "RedisHelper.SaveData_HASH", stack_trace);
                applogger.LogFileSeparator();
            }
        }
        #endregion


        #region ... M04: ReadData_HASH
        public static string ReadData_HASH(string key, string hashField)
        {
            string value = "";
            try
            {
                IDatabase cache = Connection.GetDatabase();
                value = cache.HashGet(key, hashField, CommandFlags.None);
            }
            catch (Exception ex)
            {
                lmsg.LOG_LEVEL = LogInfo.ERROR;
                string msg = ex.Message;
                string stack_trace = ex.StackTrace;
                applogger.LogToFile(lmsg, lmsg.LOG_LEVEL, "RedisHelper.ReadData_HASH", msg);
                applogger.LogToFile(lmsg, lmsg.LOG_LEVEL, "RedisHelper.ReadData_HASH", stack_trace);
                applogger.LogFileSeparator();
            }


            return value;
        }
        #endregion


        #region ... M05: SaveData_STR
        public static void SaveData_STR(string key, string value)
        {
            try
            {
                IDatabase cache = Connection.GetDatabase();
                cache.StringSet(key, value);
            }
            catch (Exception ex)
            {
                lmsg.LOG_LEVEL = LogInfo.ERROR;
                string msg = ex.Message;
                string stack_trace = ex.StackTrace;
                applogger.LogToFile(lmsg, lmsg.LOG_LEVEL, "RedisHelper.SaveData_STR", msg);
                applogger.LogToFile(lmsg, lmsg.LOG_LEVEL, "RedisHelper.SaveData_STR", stack_trace);
                applogger.LogFileSeparator();
            }
        }
        #endregion


        #region ... M06: ReadData_STR
        public static string ReadData_STR(string key)
        {
            string value = "";
            try
            {
                IDatabase cache = Connection.GetDatabase();
                value = cache.StringGet(key);
            }
            catch (Exception ex)
            {
                lmsg.LOG_LEVEL = LogInfo.ERROR;
                string msg = ex.Message;
                string stack_trace = ex.StackTrace;
                applogger.LogToFile(lmsg, lmsg.LOG_LEVEL, "RedisHelper.ReadData_STR", msg);
                applogger.LogToFile(lmsg, lmsg.LOG_LEVEL, "RedisHelper.ReadData_STR", stack_trace);
                applogger.LogFileSeparator();
            }


            return value;
        }
        #endregion


        #region ... M07: ReadAllData_HASH
        public static HashEntry[] ReadAllData_HASH(string key)
        {
            HashEntry[] value = { };
            try
            {
                IDatabase cache = Connection.GetDatabase();
                value = cache.HashGetAll(key, CommandFlags.None);
            }
            catch (Exception ex)
            {
                lmsg.LOG_LEVEL = LogInfo.ERROR;
                string msg = ex.Message;
                string stack_trace = ex.StackTrace;
                applogger.LogToFile(lmsg, lmsg.LOG_LEVEL, "RedisHelper.ReadAllData_HASH", msg);
                applogger.LogToFile(lmsg, lmsg.LOG_LEVEL, "RedisHelper.ReadAllData_HASH", stack_trace);
                applogger.LogFileSeparator();
            }


            return value;
        }
        #endregion


    }
}
