<?php
/*
 * @Author:Dieudonne Dengun
 * @Date: 14/07/2021
 * @Description: Handle sms message notification
 */

class SMSGateway
{
    private $smsConfig;
    private $message;
    private $recipients;

    public function __construct($message, $receivers, SMSConfig $config)
    {
        $this->message = $message;
        $this->recipients = $receivers;
        $this->smsConfig = $config;
    }

    public function prepareSMSMessage()
    {
        //convert recipient array to string of comma separated numbers;

        $requestParams = array(
            'username' => $this->smsConfig->getUsername(),
            'password' => $this->smsConfig->getPassword(),
            'type' => $this->smsConfig->getMessageType(),
            'dlr' => "1",
            'destination' => $this->recipients,
            'source' => $this->smsConfig->getSmsSenderName(),
            'message' => $this->message
        );
        $urlEncodedParams = http_build_query($requestParams);
        $baseUrl = $this->smsConfig->getSmsApiBaseUrl();
        return $baseUrl . $urlEncodedParams;
    }

    public function sendMessage($url)
    {
        $requestHeaders = array();
        array_push($requestHeaders, "Content-Type: application/x-www-form-urlencoded");
        return Helpers::makeGetHttpCurlRequest($requestHeaders, $url);
    }
}

class SMSConfig
{
    private $username = "nishang";
    private $password = "Nish@237";
    private $messageType = "0";
    private $smsSenderName = "St Louis"; //- Max Length of 11 if alphanumeric.
    private $smsApiBaseUrl = "https://api.rmlconnect.net/bulksms/bulksms?";

    /**
     * @return string
     */
    public function getSmsApiBaseUrl()
    {
        return $this->smsApiBaseUrl;
    }

    /**
     * @return string
     */
    public function getSmsSenderName()
    {
        return $this->smsSenderName;
    }

    /**
     * @return string
     */
    public function getMessageType()
    {
        return $this->messageType;
    }

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
}

?>