<?php

namespace App\Http\Controllers\SMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Helpers extends Controller
{
    //
    //get admission fee by degree type
    public static function getDegreeTypeFees($dbHandler, $degree_id)
    {
        $amount = 0;
        $query = $dbHandler->query("SELECT * FROM degrees WHERE id='" . $degree_id . "'");
        while ($results = $query->fetch_array()) {
            $amount = $results['amount'];
        }
        return $amount;
    }

    //send sms to a phone number
    public static function sendSMS($message, $phoneNumber)
    {
        //check if message length is greater than 160 characters
        if (strlen($message) > 160) {
            return "00";
        }
        //if recipient is an array, then we convert to an comma separated string
        if (is_array($phoneNumber)) {
            $phoneNumber = implode(",", $phoneNumber);
        }
        $sendSMSMessage = new SMSGateway($message, $phoneNumber, new SMSConfig());
        $smsUrl = $sendSMSMessage->prepareSMSMessage();
        $smsResponse = $sendSMSMessage->sendMessage($smsUrl);
        //check for success message;
        if ($smsResponse->status == 200) {
            //            return $smsResponse->body;
            return "01";
        }
        return "02";
    }

    //format array of numbers to include country code
    public static function formatPhoneNumbers($contacts)
    {
        $contactWithCountry = array();

        foreach ($contacts as $contactNumber) {
            $formattedNumber = "237" . $contactNumber;
            array_push($contactWithCountry, $formattedNumber);
        }
        return $contactWithCountry;
    }

    //validate payment input
    public static function validatePaymentInputs($momo_account_number, $degree_type)
    {
        $response = false;
        if (isset($momo_account_number) && isset($degree_type)) {
            if (!empty($momo_account_number) && !empty($degree_type)) {
                if (is_numeric($momo_account_number) && strlen($momo_account_number) == 9) {
                    $response = true;
                }
            }
        }
        return $response;
    }

    //redirect user to a define url
    public static function redirectTo($url)
    {
        echo '<meta http-equiv="Refresh" content="0; url=' . $url . '">';
    }

    //handle post curl request to a url
    public static function makeHttpCurlRequest($data, $header, $url)
    {
        //initiate curl connection
        $connection = curl_init($url);
        curl_setopt($connection, CURLOPT_POST, true);
        curl_setopt($connection, CURLOPT_POSTFIELDS, $data);
        curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($connection, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');

        $curlResponse = curl_exec($connection);
        $curlResponseCode = curl_getinfo($connection, CURLINFO_HTTP_CODE);
        curl_close($connection);
        return (object)array('body' => $curlResponse, 'status' => $curlResponseCode);
    }

    //handle curl get request to a url
    public static function makeGetHttpCurlRequest($header, $url)
    {
        //initiate curl connection
        $connection = curl_init($url);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($connection, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
        $curlResponse = curl_exec($connection);
        $curlResponseCode = curl_getinfo($connection, CURLINFO_HTTP_CODE);
        curl_close($connection);
        return (object)array('body' => $curlResponse, 'status' => $curlResponseCode);
    }
}


//Handle DB Connection Instance
class DBConnectionHandler
{
    private static $connection;
    private static $DBUser = "nishang";
    private static $DBName = "app_sys";
    private static $DBPassword = "google1234";
    private static $DBHost = "localhost";

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$connection == null) {
            self::$connection = mysqli_connect(self::$DBHost, self::$DBUser, self::$DBPassword, self::$DBName);
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
                return null;
            } else {
                return self::$connection;
            }
        }
        return self::$connection;
    }
}
