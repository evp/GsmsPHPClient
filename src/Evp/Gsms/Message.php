<?php

/**
 * Evp_Gsms_Message
 */
class Evp_Gsms_Message
{
    const TYPE_SMS = 1;
    const TYPE_EMAIL = 3;
    const TYPE_WAPPUSH = 4;

    /**
     * @var string
     */
    protected $to;

    /**
     * Optional
     *
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var integer
     */
    protected $type;

    /**
     * @param string $to
     * @param string $text
     */
    public function __construct($to, $text)
    {
        $this->to = $to;
        $this->text = $text;
    }

    /**
     * Gets from
     *
     * @param string $from
     *
     * @return Evp_Gsms_Message
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Gets text
     *
     * @param string $text
     *
     * @return Evp_Gsms_Message
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Gets to
     *
     * @param string $to
     *
     * @return Evp_Gsms_Message
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Gets type
     *
     * @param integer $type
     *
     * @return Evp_Gsms_Message
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }


}