<?
#
# Simple crypt function. Returns an encrypted version of argument.
# Does not matter what type of info you encrypt, the function will return
# a string of ASCII chars representing the encrypted version of argument.
# Note: text_crypt returns string, which length is 2 time larger
#
function text_crypt_symbol($c) {
# $c is ASCII code of symbol. returns 2-letter text-encoded version of symbol

        global $START_CHAR_CODE;

        return chr($START_CHAR_CODE + ($c & 240) / 16).chr($START_CHAR_CODE + ($c & 15));
}

function text_crypt($s) {
    global $START_CHAR_CODE, $CRYPT_SALT;

    if ($s == "")
        return $s;
    $enc = rand(1,255); # generate random salt.
    $result = text_crypt_symbol($enc); # include salt in the result;
    $enc ^= $CRYPT_SALT;
    for ($i = 0; $i < strlen($s); $i++) {
        $r = ord(substr($s, $i, 1)) ^ $enc++;
        if ($enc > 255)
            $enc = 0;
        $result .= text_crypt_symbol($r);
    }
    return $result;
}

function text_decrypt_symbol($s, $i) {
# $s is a text-encoded string, $i is index of 2-char code. function returns number in range 0-255

        global $START_CHAR_CODE;

        return (ord(substr($s, $i, 1)) - $START_CHAR_CODE)*16 + ord(substr($s, $i+1, 1)) - $START_CHAR_CODE;
}

function text_decrypt($s) {
    global $START_CHAR_CODE, $CRYPT_SALT;

	$result = "";
    if ($s == "")
        return $s;
    $enc = $CRYPT_SALT ^ text_decrypt_symbol($s, 0);
    for ($i = 2; $i < strlen($s); $i+=2) { # $i=2 to skip salt
        $result .= chr(text_decrypt_symbol($s, $i) ^ $enc++);
        if ($enc > 255)
            $enc = 0;
    }
    return $result;
}
?>