<?php

namespace App\Controller;

use App\Domain\Service\CrmInterface;
use Psr\Log\LoggerInterface;
use Workerman\Connection\TcpConnection;
use stdClass;

class NewMessageController extends AbstractController
{
    private $actionMap = [
        'changeManagerStatus' => 'changeCurrentStatusAction',
    ];



//    'changeManagerStatus' => ChangeManagerStatus::class,
//    'getManagersStatuses' => GetManagersStatuses::class,
//    'startEdit' => StartEditForm::class,
//    'endEdit' => EndEditForm::class,
//    'focusFields' => LockField::class,
//    'blurFields' => UnLockField::class,

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CrmInterface
     */
    private $crm;

    /**
     * NewMessageController constructor.
     *
     * @param LoggerInterface $logger
     * @param CrmInterface    $crm
     */
    public function __construct(
        LoggerInterface $logger,
        CrmInterface $crm
    ) {
        $this->crm = $crm;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     *
     * @param string $data
     */
    public function run(TcpConnection $currentConnection, string $data = null)
    {
        $this->connection = $currentConnection;
    }

    public function changeCurrentStatusAction(stdClass $data)
    {

    }
}