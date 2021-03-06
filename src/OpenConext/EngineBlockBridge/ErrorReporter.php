<?php

namespace OpenConext\EngineBlockBridge;

use EngineBlock_ApplicationSingleton;
use EngineBlock_Corto_Exception_PEPNoAccess;
use EngineBlock_Corto_Exception_HasFeedbackInfoInterface;
use EngineBlock_Exception;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ErrorReporter
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var EngineBlock_ApplicationSingleton
     */
    private $engineBlockApplicationSingleton;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(
        EngineBlock_ApplicationSingleton $engineBlockApplicationSingleton,
        LoggerInterface $logger,
        SessionInterface $session
    ) {
        $this->engineBlockApplicationSingleton = $engineBlockApplicationSingleton;
        $this->logger = $logger;
        $this->session = $session;
    }

    /**
     * @param Exception $exception
     * @param string    $messageSuffix
     */
    public function reportError(Exception $exception, $messageSuffix)
    {
        $logContext = ['exception' => $exception];

        if ($exception instanceof EngineBlock_Exception) {
            $severity = $exception->getSeverity();
        } else {
            $severity = EngineBlock_Exception::CODE_ERROR;
        }

        // unwrap the exception stack
        $prevException = $exception;
        while ($prevException = $prevException->getPrevious()) {
            if (!isset($logContext['previous_exceptions'])) {
                $logContext['previous_exceptions'] = [];
            }

            $logContext['previous_exceptions'][] = (string)$prevException;
        }

        // message building
        $message = $exception->getMessage();
        if (empty($message)) {
            $message = 'Exception without message "' . get_class($exception) . '"';
        }

        if ($messageSuffix) {
            $message .= ' | ' . $messageSuffix;
        }

        $this->logger->log($severity, $message, $logContext);

        // Store some valuable debug info in session so it can be displayed on feedback pages
        $feedback = $this->session->get('feedbackInfo');
        if (empty($feedback)) {
            $feedback = [];
        }

        if ($exception instanceof EngineBlock_Corto_Exception_HasFeedbackInfoInterface) {
            $feedback = array_merge($feedback, $exception->getFeedbackInfo());
        } elseif ($exception instanceof EngineBlock_Corto_Exception_PEPNoAccess) {
            $this->session->set('error_authorization_policy_decision', $exception->getPolicyDecision());
        }

        $this->session->set('feedbackInfo', array_merge(
            $feedback,
            $this->engineBlockApplicationSingleton->collectFeedbackInfo($exception)
        ));

        // flush all messages in queue, something went wrong!
        $this->engineBlockApplicationSingleton->flushLog('An error was caught');
    }
}
