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
     * Sends SMS message to parner
     *
     * @param string $from
     * @param string $to
     * @param string $message
     * @param string $callbackUri
     * @param string $type
     * @param bool   $test
     *
     * @return mixed
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

        return new Evp_Gsms_QueryResult($sxe->status);
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
     * @param array $params
     *
     * @return array
     */
    protected function call(array $params)
    {
        $params = array_merge($params, array(
            'username' => $this->username,
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