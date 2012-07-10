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
    private $statusCode;

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
}