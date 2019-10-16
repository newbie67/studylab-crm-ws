<?php

namespace app\Component;

class ParsedRequest
{
    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $token;

    /**
     * @var int
     */
    private $managerId;

    /**
     * @var \stdClass
     */
    private $data;

    /**
     * Request constructor.
     *
     * @param string         $action
     * @param string         $token
     * @param int            $managerId
     * @param \stdClass|null $data
     */
    public function __construct(string $action, string $token, int $managerId, $data = null)
    {
        $this->action = $action;
        $this->token = $token;
        $this->managerId = $managerId;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return \stdClass
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getManagerId()
    {
        return $this->managerId;
    }
}