<?php
/**
 * Class for sending Push-Notifications to Push-Notification-Service
 *
 * The method render() is triggered through eZPublish event mechanism,
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MyVideoRenderMyVideoPushNotification extends MyVideoRendererBase implements MyVideoRendererInterface
{
    /**
     * Gathers data from content object, creates the request payload, send the request and stores a report.
     */
    public function render()
    {
        // Only send the message if we do not have a previous version yet.
        // If the editor wants to send a new message he/she has to create a new object
        if (!$this->ContentObject->previousVersion()) {

            $payload = $this->createRequestPayload();

            $endpoint = $this->getEndpoint();
            list($responseCode, $responseContent) = $this->sendPayload($endpoint, $payload);

            $info = $this->buildInfo($payload, $endpoint, $responseCode, $responseContent);
            $this->setServerResponseField($info);
        }
    }

    /**
     * Overriden and does nothing, because we dont have a primary redis key for this content class
     *
     * @param $json
     * @override
     */
    public function storePrimaryRedisData($json)
    {
        // nothing to do here, just overriding parent method
    }

    /**
     * Overriden and does nothing, because we dont have a primary redis key for this content class
     *
     * @override
     */
    public function deletePrimaryRedisData()
    {
        // nothing to do here, just overriding parent method
    }


    /**
     * @return string json string
     */
    private function createRequestPayload()
    {
        $payload = new stdClass();
        $payload->body = $this->getAttributeValue('message');
        $payload->link_id = $this->getAttributeValue('deeplink');
        $payload->timestamp = time();
        return json_encode($payload);
    }

    /**
     * @return string
     */
    private function getEndpoint()
    {
        $envSelection = $this->getAttributeValue('environment');

        // use live endpoint?
        if (isset($envSelection['selected_values'][0]) && $envSelection['selected_values'][0] == 'Live') {
            // Todo: what is the live endpoint?
            $endpoint = '';

            $this->setServerResponseField('LIVE endpoint not know yet! No push notification was sent!');

            echo "LIVE endpoint not know yet!";
            die('died at' . __FILE__ . ':' . __LINE__ . "\n");
        } else {
            $endpoint = 'http://myvideo-api.stage.cellular.de/push/send-notification';
        }

        return $endpoint;
    }

    /**
     * Send the message to the service
     *
     * @param string $endpoint
     * @param string $payload
     * @return array    with result information of form [HTTP_STATUS_CODE, RESPONSE]
     */
    private function sendPayload($endpoint, $payload)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload)
            )
        );

        $result = curl_exec($ch);

        $info = array(curl_getinfo($ch, CURLINFO_HTTP_CODE), $result);

        curl_close($ch);
        return $info;
    }

    /**
     * Assembles an information messages
     *
     * @param string $payload
     * @param string $endpoint
     * @param int $responseCode
     * @param string $responseContent
     * @return string   The message
     */
    private function buildInfo($payload, $endpoint, $responseCode, $responseContent)
    {
        $info =
            'Erfolgreich versendet: ' . ($responseCode == 200 ? 'JA' : 'NEIN') . PHP_EOL .
            'Zeitpunkt: ' . date('d.m.Y H:i:s') . PHP_EOL .
            '----------------------------------------------------------------------------------------------------' .
            '------------------------------------------------------------------------------------------' . PHP_EOL .
            'Endpoint: ' . $endpoint . PHP_EOL .
            'Gesendete Daten : ' . $payload . PHP_EOL .
            '----------------------------------------------------------------------------------------------------' .
            '------------------------------------------------------------------------------------------' . PHP_EOL .
            'Reponse-Code: ' . $responseCode . PHP_EOL .
            '----------------------------------------------------------------------------------------------------' .
            '------------------------------------------------------------------------------------------' . PHP_EOL .
            $responseContent . PHP_EOL;

        return $info;
    }

    /**
     * Sets the 'server_response' field in the current ContentObject
     *
     * @param $info     string
     */
    private function setServerResponseField($info)
    {
        // write the info to the 'server_response' attribute;
        $dataMap = $this->ContentObject->dataMap();
        /** @var eZContentObjectAttribute $serverResponseAttr */
        $serverResponseAttr = $dataMap['server_response'];
        $serverResponseAttr->setAttribute('data_text', $info);
        $serverResponseAttr->setHasDirtyData(true);
        $serverResponseAttr->store();
    }
}
