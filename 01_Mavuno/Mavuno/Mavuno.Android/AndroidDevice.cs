using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

using Android.App;
using Android.Content;
using Android.OS;
using Android.Runtime;
using Android.Views;
using Android.Widget;
using Mavuno.core;
using Mavuno.Droid;
using Xamarin.Forms;
using static Android.Provider.Settings;

[assembly: Xamarin.Forms.Dependency(typeof(AndroidDevice))]
namespace Mavuno.Droid
{
    public class AndroidDevice : IDevice
    {
        public string GetIdentifier()
        {
            var context = Android.App.Application.Context;
            return Secure.GetString(context.ContentResolver, Secure.AndroidId);
        }
    }
}