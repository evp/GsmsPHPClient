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
    private $apiUri;

    /**
     * Stores gsms.lt username
     *
     * @var string
     */
    private $username;

    /**
     * Stores gsms.lt password
     *
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $lastResponse;

    /**
     * Class constructor
     *
     * @param string $username
     * @param string $password
     * @param string $apiUri
     */
    public function __construct($username, $password, $apiUri)
    {
        $this->username = $username;
        $this->password = $password;
        $this->apiUri = $apiUri;
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
     * @return Evp_Gsms_Client
     *
     * @static
     */
    public static function newInstance($username, $password)
    {
        return new self(
            $username,
            $password,
            'https://www.gsms.lt/remote.php?ru=bS9tX3Ntcy9hZG1pbi9yX2dhdGV3YXkucGhw'
        );
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
