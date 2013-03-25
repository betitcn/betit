<?php
function setSession($wxkey) {

	ssetcookie('wxkey', "$wxkey", 31536000);



}
?>