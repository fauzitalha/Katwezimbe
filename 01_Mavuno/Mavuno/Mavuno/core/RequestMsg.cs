using System;
using System.Collections.Generic;
using System.Text;

namespace Mavuno.core
{
    class RequestMsg
    {
        public string RequestRef { get; set; }
        public string ProcCode { get; set; }
        public Dictionary<string, string> RequestPayLoad { get; set; }
    }
}
