<?php

/**
 * Evp_Gsms_QueryResult
 *
 * @author Vytautas Gimbutas <v.gimbutas@evp.lt>
 */
class Evp_Gsms_QueryResult
{
    /**
     * Statuses
     */
    const STATUS_SUCCESS = 'Submitted';
    const STATUS_ERROR = 'Err';

    /**
     * @var string
     */
    protected $statusCode;

    /**
     * @var string
     */
    protected $coverage;

    /**
     * @var int
     */
    protected $price;

    /**
     * @var int
     */
    protected $balance;

    /**
     * @var string
     */
    protected $smsId;

    /**
     * @var bool
     */
    protected $test = false;

    /**
     * @var array
     */
    protected $smsIdList = array();


    /**
     * Status code
     *
     * @param string $statusCode
     */
    public function __construct($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->statusCode == self::STATUS_SUCCESS;
    }

    /**
     * Gets balance
     *
     * @param int $balance
     *
     * @return Evp_Gsms_QueryResult
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return int
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Gets coverage
     *
     * @param string $coverage
     *
     * @return Evp_Gsms_QueryResult
     */
    public function setCoverage($coverage)
    {
        $this->coverage = $coverage;

        return $this;
    }

    /**
     * @return string
     */
    public function getCoverage()
    {
        return $this->coverage;
    }

    /**
     * Gets price
     *
     * @param int $price
     *
     * @return Evp_Gsms_QueryResult
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Gets smsId
     *
     * @param string $smsId
     *
     * @return Evp_Gsms_QueryResult
     */
    public function setSmsId($smsId)
    {
        $this->smsId = $smsId;

        return $this;
    }

    /**
     * @return string
     */
    public function getSmsId()
    {
        return $this->smsId;
    }

    /**
     * Gets statusCode
     *
     * @param string $statusCode
     *
     * @return Evp_Gsms_QueryResult
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Gets test
     *
     * @param boolean $test
     *
     * @return Evp_Gsms_QueryResult
     */
    public function setTest($test)
    {
        $this->test = $test;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isTest()
    {
        return $this->test;
    }

    public function addSmsIdToList($smsId)
    {
        $this->smsIdList[] = $smsId;

        return $this;
    }

    public function setSmsIdList($smsIdList)
    {
        $this->smsIdList = $smsIdList;

        return $this;
    }

    public function getSmsIdList()
    {
        return $this->smsIdList;
    }
}