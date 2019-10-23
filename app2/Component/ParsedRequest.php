<?php

namespace app2\Component;

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
    private $userId;

    /**
     * @var \stdClass
     */
    private $data;

    /**
     * Request constructor.
     *
     * @param string         $action
     * @param string         $token
     * @param int            $userId
     * @param \stdClass|null $data
     */
    public function __construct(string $action, string $token, int $userId, $data = null)
    {
        $this->action = $action;
        $this->token = $token;
        $this->userId = $userId;
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
    public function userId()
    {
        return $this->userId;
    }
}