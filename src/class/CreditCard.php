<?php

class CreditCard
{

    private $min_size = 11;
    private $brands = array(
        'amex' => '/^3[47][0-9]{5,}$/',
        'visa' => '/^4\d{12}(\d{3})?$/',
        'mastercard' => '/^(5[1-5]\d{4}|677189)\d{10}$/',
        'diners' => '/^3(0[0-5]|[68]\d)\d{11}$/',
        'discover' => '/^6(?:011|5[0-9]{2})[0-9]{12}$/',
        'elo' => '/^(40117[8-9]|431274|438935|451416|457393|45763[1-2]|506(699|7[0-6][0-9]|77[0-8])|509\d{3}|504175|627780|636297|636368|65003[1-3]|6500(3[5-9]|4[0-9]|5[0-1])|6504(0[5-9]|[1-3][0-9])|650(4[8-9][0-9]|5[0-2][0-9]|53[0-8])|6505(4[1-9]|[5-8][0-9]|9[0-8])|6507(0[0-9]|1[0-8])|65072[0-7]|6509(0[1-9]|1[0-9]|20)|6516(5[2-9]|[6-7][0-9])|6550([0-1][0-9]|2[1-9]|[3-4][0-9]|5[0-8]))/',
        'jcb' => '/^(?:2131|1800|35\d{3})\d{11}$/',
        'aura' => '/^(5078\d{2})(\d{2})(\d{11})$/',
        'hipercard' => '/^(606282\d{10}(\d{3})?)|(3841\d{15})$/',
        'maestro' => '/^(?:5[0678]\d\d|6304|6390|67\d\d)\d{8,15}$/',
    );
    private $validate_array = array(
        array('name' => 'amex',
            'length' => '15',
            'prefixes' => '34,37',
            'checkdigit' => true
        ),
        array('name' => 'diners carte',
            'length' => '14',
            'prefixes' => '300,301,302,303,304,305',
            'checkdigit' => true
        ),
        array('name' => 'diners',
            'length' => '14,16',
            'prefixes' => '36,38,54,55',
            'checkdigit' => true
        ),
        array('name' => 'discover',
            'length' => '16',
            'prefixes' => '6011,622,64,65',
            'checkdigit' => true
        ),
        array('name' => 'diners enroute',
            'length' => '15',
            'prefixes' => '2014,2149',
            'checkdigit' => true
        ),
        array('name' => 'jcb',
            'length' => '16',
            'prefixes' => '35',
            'checkdigit' => true
        ),
        array('name' => 'maestro',
            'length' => '12,13,14,15,16,18,19',
            'prefixes' => '5018,5020,5038,6304,6759,6761,6762,6763',
            'checkdigit' => true
        ),
        array('name' => 'mastercard',
            'length' => '16',
            'prefixes' => '51,52,53,54,55',
            'checkdigit' => true
        ),
        array('name' => 'solo',
            'length' => '16,18,19',
            'prefixes' => '6334,6767',
            'checkdigit' => true
        ),
        array('name' => 'switch',
            'length' => '16,18,19',
            'prefixes' => '4903,4905,4911,4936,564182,633110,6333,6759',
            'checkdigit' => true
        ),
        array('name' => 'visa',
            'length' => '16',
            'prefixes' => '4',
            'checkdigit' => true
        ),
        array('name' => 'visaelectron',
            'length' => '16',
            'prefixes' => '417500,4917,4913,4508,4844',
            'checkdigit' => true
        ),
        array('name' => 'lasercard',
            'length' => '16,17,18,19',
            'prefixes' => '6304,6706,6771,6709',
            'checkdigit' => true
        )
    );

    public function getBrand($number)
    {
        global $text;
        $brands = $this->brands;
        $number = $text->removeSpace($number, "");
        if (not_empty($number) && strlen($number) > $this->min_size) {
            foreach ($brands as $_brand => $regex) {
                if (preg_match($regex, $number)) {
                    return $_brand;
                }
            }
        }
        return null;
    }

    public function validate($cardnumber, &$errornumber, &$errortext)
    {

        $cardname = $this->getBrand($cardnumber);
        $cards = $this->validate_array;
        $ccErrorNo = 0;
        $ccErrors [0] = "Não foi possível identificar a bandeira do cartão fornecido. Verifique se você digitou o número corretamente.";
        $ccErrors [1] = "Não encontramos o número do cartão, digite-o e tente novamente.";
        $ccErrors [2] = "O formato do número do cartão é inválido";
        $ccErrors [3] = "O número do cartão é inválido.";
        $ccErrors [4] = "A quantidade de dígitos do cartão fornecido não corresponde com a quantidade de digitos esperada. Se esse erro persistir, entre em contato com nosso suporte para seguirmos com o atendimento manual..";
        // Establish card type
        $cardType = -1;
        for ($i = 0; $i < sizeof($cards); $i++) {

            // See if it is this card (ignoring the case of the string)
            if (strtolower($cardname) == strtolower($cards[$i]['name'])) {
                $cardType = $i;
                break;
            }
        }
        // If card type not found, report an error
        if ($cardType == -1) {
            $errornumber = 0;
            $errortext = $ccErrors [$errornumber];
            return false;
        }
        // Ensure that the user has provided a credit card number
        if (strlen($cardnumber) == 0) {
            $errornumber = 1;
            $errortext = $ccErrors [$errornumber];
            return false;
        }
        // Remove any spaces from the credit card number
        $cardNo = str_replace(' ', '', $cardnumber);

        // Check that the number is numeric and of the right sort of length.
        if (!preg_match("/^[0-9]{13,19}$/", $cardNo)) {
            $errornumber = 2;
            $errortext = $ccErrors [$errornumber];
            return false;
        }

        // Now check the modulus 10 check digit - if required
        if ($cards[$cardType]['checkdigit']) {
            $checksum = 0;                                  // running checksum total
            $mychar = "";                                   // next char to process
            $j = 1;                                         // takes value of 1 or 2

            // Process each digit one by one starting at the right
            for ($i = strlen($cardNo) - 1; $i >= 0; $i--) {

                // Extract the next digit and multiply by 1 or 2 on alternative digits.
                $calc = $cardNo{$i} * $j;

                // If the result is in two digits add 1 to the checksum total
                if ($calc > 9) {
                    $checksum = $checksum + 1;
                    $calc = $calc - 10;
                }

                // Add the units element to the checksum total
                $checksum = $checksum + $calc;

                // Switch the value of j
                if ($j == 1) {
                    $j = 2;
                } else {
                    $j = 1;
                };
            }

            // All done - if checksum is divisible by 10, it is a valid modulus 10.
            // If not, report an error.
            if ($checksum % 10 != 0) {
                $errornumber = 3;
                $errortext = $ccErrors [$errornumber];
                return false;
            }
        }

        // The following are the card-specific checks we undertake.

        // Load an array with the valid prefixes for this card
        $prefix = explode(',', $cards[$cardType]['prefixes']);

        // Now see if any of them match what we have in the card number
        $PrefixValid = false;
        for ($i = 0; $i < sizeof($prefix); $i++) {
            $exp = '/^' . $prefix[$i] . '/';
            if (preg_match($exp, $cardNo)) {
                $PrefixValid = true;
                break;
            }
        }

        // If it isn't a valid prefix there's no point at looking at the length
        if (!$PrefixValid) {
            $errornumber = 3;
            $errortext = $ccErrors [$errornumber];
            return false;
        }

        // See if the length is valid for this card
        $LengthValid = false;
        $lengths = explode(',', $cards[$cardType]['length']);
        for ($j = 0; $j < sizeof($lengths); $j++) {
            if (strlen($cardNo) == $lengths[$j]) {
                $LengthValid = true;
                break;
            }
        }

        // See if all is OK by seeing if the length was valid.
        if (!$LengthValid) {
            $errornumber = 4;
            $errortext = $ccErrors [$errornumber];
            return false;
        };

        // The credit card is in the required format.
        return true;
    }


}