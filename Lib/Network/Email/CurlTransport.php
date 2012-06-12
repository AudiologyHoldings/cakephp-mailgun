<?php
/**
 * Mailgun curl class
 *
 * Enables sending of email over mailgun via curl
 *
 * Licensed under The MIT License
 * 
 * @author Brad Koch <bradkoch2007@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class CurlTransport extends AbstractTransport {

/**
 * Configurations
 *
 * @var array
 */
	protected $_config = array();

/**
 * Send mail
 *
 * @params CakeEmail $email
 * @return array
 */
	public function send(CakeEmail $email) {
        $post = array();
        $post_preprocess = array_merge(
            $email->getHeaders(array('from', 'sender', 'replyTo', 'readReceipt', 'returnPath', 'to', 'cc', 'bcc', 'subjects')),
            array(
                'text' => $email->message(CakeEmail::MESSAGE_TEXT),
                'html' => $email->message(CakeEmail::MESSAGE_HTML)
            )
        );
        foreach ($post_preprocess as $k => $v) {
            if (! empty($v)) {
                $post[strtolower($k)] = $v;
            }
        }

        $ch = curl_init('https://api.mailgun.net/v2/' . $this->_config['domain'] . '/messages');

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERPWD, 'api:' . $this->_config['api_key']);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $response = curl_exec($ch);

        CakeLog::write(LOG_ERROR, $response);
        curl_close($ch);
        return array(
            'headers' => $this->_headersToString($email->getHeaders(), PHP_EOL),
            'message' => implode(PHP_EOL, $email->message())
        );
    }

}
