<html>
	<head></head>
	<body>
	

	<p><img id="captcha" src="captcha.php" width="160" height="45" border="1" alt="CAPTCHA">
<small><a href="#" onclick="
  document.getElementById('captcha').src = 'captcha.php?' + Math.random();
  document.getElementById('captcha_code_input').value = '';
  return false;
">refresh</a></small></p>
<p><input id="captcha_code_input" type="text" name="captcha" size="10" maxlength="5" onkeyup="this.value = this.value.replace(/[^\d]+/g, '');"> <small>copy the digits from the image into this box</small></p>


	
	</body>
</html>



