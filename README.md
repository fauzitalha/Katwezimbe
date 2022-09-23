# STAGE 01: Katwezimbe (Demonstration)


Demonstration Links

<b>GOOGLE PLAY LINK</b><br>
https://play.google.com/store/apps/details?id=com.slankinit.Mavuno<br>
<b>TEST WALLET:</b>        MB00000078<br>
<b>TEST ACTIVATION:</b>    39296B000004<br>


<b>CUSTOMER PORTAL</b><br>
https://katwezimbe.slankinit.com/<br>
<b>TEST CUST USER:</b> fauzi<br>
<b>TEST CUST PASS:</b> Fauzi@7727<br> 

<br>
<b>STAFF PORTAL</b><br>
https://kstaff.slankinit.com/<br>
<b>TEST STAFF USER:</b> mathew.kasozi<br>
<b>TEST STAFF PASS:</b> mathew.kasozi<br>
<br>



# STAGE 02: Katwezimbe (Integration)
Katwezimbe is connected to the MTN-Open-API for collecting and disbursement of funds among groups and individuals.<br>
This is how we integrated to the API
1. We built a tokenizer service to simply update tokens every after 45 minutes. This service works for the Collections and Disbursment productions of the API. <br>
2. We also split the various methods from the APIs into Micro-Services to allow for scalability and easy of support and maintenance <br>
3. So for every Collection request originating from our System to API, we use an already existing valid access token to speed up request processing.<br>
4. For disbursement, the same applies as explained in #4.








