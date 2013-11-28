<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cqueiser
 * Date: 29.10.13
 * Time: 10:59
 */

class MyVideoSendMessageType extends MyVideoGenericEventType
{
    const WORKFLOW_TYPE_STRING = 'myvideosendmessage';
    const PUBLISH_REQUEST = 'PublishRequest';
    const PREVIEW_REQUEST = 'PreviewRequest';
    const DELETE_REQUEST = 'DeleteRequest';
    const MESSAGE_TYPE = 'REDIS_MESSAGE';
    const MAX_RETRIES = 5;
    const REQUEST_CONTENT_TYPE = 'application/json';

    /** @var resource $ch */
    private $ch;

    public function __construct()
    {
        parent::__construct(self::WORKFLOW_TYPE_STRING, 'MyVideo Send Message to HornetQ');
        $this->workflow = self::WORKFLOW_TYPE_STRING;
        $this->logFile = 'send_message.log';
        $this->ch = curl_init();
    }

    public function __destruct()
    {
        curl_close($this->ch);
    }

    /**
     * @param String $messageType
     * @param array $contentObjectInfo
     * @param String $hornetQUrl
     * @return bool True if send message was successful
     */
    private function trySendMessage($messageType, array $contentObjectInfo, $hornetQUrl)
    {
        $messageBody = array(
            'host' => gethostname(),
            'contentId' => $contentObjectInfo['id'],
            'version' => $contentObjectInfo['version'],
            'eventType' => $messageType
        );

        $data = array(
            'messageType' => self::MESSAGE_TYPE,
            'messageBody' => (string)json_encode($messageBody)
        );

        $data = json_encode($data);

        $curlConfig = array(
            CURLOPT_URL => $hornetQUrl,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: ' . self::REQUEST_CONTENT_TYPE
            ),
            CURLOPT_POSTFIELDS => $data,
        );

        curl_setopt_array($this->ch, $curlConfig);
        curl_exec($this->ch);

        if (curl_errno($this->ch)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param eZContentObjectTreeNode $contentObjectTreeNode
     * @return array
     */
    public function getExtendedContentObjectInfo(eZContentObjectTreeNode $contentObjectTreeNode)
    {
        $contentObjectInfo = array();
        $this->contentObject = $contentObjectTreeNode->object();
        $contentObjectInfo['id'] = $this->contentObject->ID;
        /** @var eZContentObjectVersion $contentObjectVersion */
        $contentObjectVersion = $this->contentObject->currentVersion();
        $contentObjectInfo['version'] = $contentObjectVersion->Version;

        return $contentObjectInfo;
    }

    /**
     * @param $messageType
     * @param array $contentObjectInfo
     */
    public function sendMessage($messageType, array $contentObjectInfo)
    {
        # url: POST http://hostname:8080/hornetq/queues/redis/create
        # POST_DATA:     "messageType": "REDIS_MESSAGE",
        #                "messageBody": (string){
        #                               "host": "hostname",
        #                               "contentID": ID,
        #                               "Version": Nr,
        #                               "EventType": "PublishRequest"
        #                               }

        if (!$this->getIsActive()) {
            eZLog::write('sending to HornetQ not activated.', 'send_message.log', 'var/log');
            return;
        }

        $retries = 0;
        $hornetQUrl = $this->getHornetQUrl();

        while ($retries++ <= self::MAX_RETRIES) {

            if ($this->trySendMessage($messageType, $contentObjectInfo, $hornetQUrl)) {
                eZLog::write('sent ' . $messageType . ' message (Object ID: ' . $contentObjectInfo['id'] . ' | Version: ' . $contentObjectInfo['version'] . ')', 'send_message.log', 'var/log');
                break;
            } else {
                sleep($retries);
            }
        }

        if ($retries >= self::MAX_RETRIES) {
            eZLog::write('ERROR ' . curl_error($this->ch) . ' while sent ' . $messageType . ' message (Object ID: ' . $contentObjectInfo['id'] . ' | Version: ' . $contentObjectInfo['version'] . ')', 'send_message.log', 'var/log');
        }
    }

    /**
     * @return boolean
     * @throws ezcConfigurationReadFailedException
     */
    private function getIsActive()
    {
        $ini = eZINI::instance('environment.ini');

        $active = $ini->variable('HornetQ', 'Active');
        if (false === $active) {
            throw new ezcConfigurationReadFailedException('Missing variable Active in environment.ini');
        }

        return $active == "true";
    }

    /**
     * @return String
     * @throws ezcConfigurationReadFailedException
     */
    private function getHornetQUrl()
    {
        $ini = eZINI::instance('environment.ini');

        $host = $ini->variable('HornetQ', 'Host');
        if (false === $host) {
            throw new ezcConfigurationReadFailedException('Missing variable Server/Host in environment.ini');
        }

        $port = $ini->variable('HornetQ', 'Port');
        if (false === $port) {
            throw new ezcConfigurationReadFailedException('Missing variable Port in environment.ini');
        }

        $path = $ini->variable('HornetQ', 'Path');
        if (false === $path) {
            throw new ezcConfigurationReadFailedException('Missing variable Path in environment.ini');
        }
        return $host . ':' . $port . $path;
    }
}

eZWorkflowEventType::registerEventType(MyVideoSendMessageType::WORKFLOW_TYPE_STRING, 'MyVideoSendMessageType');
