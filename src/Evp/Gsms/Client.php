<?php

/**
 * gsms.lt service (API implementation)
 *
 * @author Vytautas Gimbutas <v.gimbutas@evp.lt>
 * @author Šarūnas Dubinskas <s.dubinskas@evp.lt>
 */
class Evp_Gsms_Client
{
    /**
     * Stores message types
     */
    const TYPE_SMS = 1;
    const TYPE_EMAIL = 3;
    const TYPE_WAPPUSH = 4;

    /**
     * API URI
     *
     * @var string
     */
    protected $apiUri;

    /**
     * Stores gsms.lt username
     *
     * @var string
     */
    protected $username;

    /**
     * Stores gsms.lt password
     *
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $lastResponse;

    /**
     * @var string
     */
    protected $defaultCallbackUri;

    /**
     * @var string
     */
    protected $defaultFrom;

    /**
     * Class constructor
     *
     * @param string $username
     * @param string $password
     * @param string $apiUri
     * @param string $defaultFrom        used in sendMessage method if from field is not set in message object
     * @param string $defaultCallbackUri used in sendMessage method
     */
    public function __construct($username, $password, $apiUri, $defaultFrom = null, $defaultCallbackUri = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->apiUri = $apiUri;
        $this->defaultFrom = $defaultFrom;
        $this->defaultCallbackUri = $defaultCallbackUri;
    }

    /**
     * Sends SMS message to partner
     *
     * @param string $from
     * @param string $to
     * @param string $message
     * @param string $callbackUri
     * @param string $type
     * @param bool   $test
     *
     * @return Evp_Gsms_QueryResult
     *
     * @throws Evp_Gsms_Exception
     * @throws Evp_Gsms_Exception_InvalidResponse
     */
    public function send($from, $to, $message, $callbackUri = null, $type = null, $test = false)
    {
        $params = array(
            'from'     => $from,
            'to'       => $to,
            'text'     => $message,
            'test'     => (int) $test,
        );

        if ($callbackUri !== null) {
            $params['callback'] = $callbackUri;
        }
        if ($type !== null) {
            $params['type'] = $type;
        }

        $response = $this->call($params);

        $sxe = simplexml_load_string($response);
        if ($sxe === false) {
            throw new Evp_Gsms_Exception_InvalidResponse(sprintf('Invalid response=%s.', $response));
        }

        if (!empty($sxe->error)) {
            throw new Evp_Gsms_Exception((string) $sxe->error);
        }

        if ($test) {
            $result = new Evp_Gsms_QueryResult(Evp_Gsms_QueryResult::STATUS_SUCCESS);
            $result->setTest(true);
        } else {
            $result = new Evp_Gsms_QueryResult((string) $sxe->status);
            $result->setSmsId((string) $sxe->smsid);
            if (!empty($sxe->smsidlist) && !empty($sxe->smsidlist->smsid)) {
                foreach ($sxe->smsidlist->smsid as $key => $value) {
                    $result->addSmsIdToList((int)$value);
                }
            }
        }

        return $result
            ->setCoverage((string) $sxe->coverage)
            ->setPrice((int)(string) $sxe->price)
            ->setBalance((int)(string) $sxe->balance)
        ;
    }

    /**
     * Gets information about SMS - price, coverage etc. Does not send the actual message.
     *
     * @param string $from
     * @param string $to
     * @param string $message
     * @param string $type
     *
     * @return Evp_Gsms_QueryResult
     *
     * @throws Evp_Gsms_Exception
     * @throws Evp_Gsms_Exception_InvalidResponse
     */
    public function info($from, $to, $message, $type = null)
    {
        return $this->send($from, $to, $message, null, $type, true);
    }

    /**
     * Send message. Similar to Evp_Gsms_Client::send, but takes message object as argument and uses default values
     *
     * @param Evp_Gsms_Message $message
     *
     * @return Evp_Gsms_QueryResult
     */
    public function sendMessage(Evp_Gsms_Message $message)
    {
        return $this->sendMessageObject($message, false);
    }

    /**
     * Gets message info. Similar to Evp_Gsms_Client::info, but takes message object as argument and uses default values
     *
     * @param Evp_Gsms_Message $message
     *
     * @return Evp_Gsms_QueryResult
     */
    public function getMessageInfo(Evp_Gsms_Message $message)
    {
        return $this->sendMessageObject($message, true);
    }

    /**
     * Get last response
     *
     * @return string
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Returns configured service instance
     *
     * @param string $username
     * @param string $password
     * @param string $defaultCallbackUri
     * @param string $defaultFrom
     *
     * @return Evp_Gsms_Client
     */
    public static function newInstance($username, $password, $defaultFrom = null, $defaultCallbackUri = null)
    {
        return new self(
            $username,
            $password,
            'https://www.gsms.lt/remote.php?ru=bS9tX3Ntcy9hZG1pbi9yX2dhdGV3YXkucGhw',
            $defaultFrom,
            $defaultCallbackUri
        );
    }

    /**
     * For internal use. Maps message object to arguments
     *
     * @param Evp_Gsms_Message $message
     * @param boolean          $test
     *
     * @return Evp_Gsms_QueryResult
     */
    protected function sendMessageObject(Evp_Gsms_Message $message, $test)
    {
        $to = $message->getTo();
        $text = $message->getText();
        $from = $message->getFrom() ? $message->getFrom() : $this->defaultFrom;
        $type = $message->getType();

        return $this->send($from, $to, $text, $this->defaultCallbackUri, $type, $test);
    }

    /**
     * Calls API action with specified params
     *
     * @param string $action
     * @param array  $params
     *
     * @return array
     */
    protected function call(array $params)
    {
        $params = array_merge($params, array(
            'user'     => $this->username,
            'password' => $this->password,
        ));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUri);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));

        $output = $this->lastResponse = curl_exec($ch);

        try {
            if ($output === false) {
                throw new Evp_Gsms_Exception_InvalidResponse(curl_error($ch), curl_errno($ch));
            } else if (($code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) != 200) {
                throw new Evp_Gsms_Exception_InvalidResponse(sprintf('Http code is not 200. Code=%d.', $code));
            } else if (empty($output)) {
                throw new Evp_Gsms_Exception_InvalidResponse('Empty response.');
            }
        } catch (Evp_Gsms_Exception_InvalidResponse $e) {
            curl_close($ch);
            throw $e;
        }

        curl_close($ch);

        return $output;
    }

}
