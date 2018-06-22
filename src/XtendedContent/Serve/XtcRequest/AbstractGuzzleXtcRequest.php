<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 19/04/2018
 * Time: 17:21
 */

namespace Drupal\xtcguzzle\XtendedContent\Serve\XtcRequest;


use Drupal\xtc\XtendedContent\Serve\XtcRequest\AbstractXtcRequest;
use Drupal\xtcguzzle\XtendedContent\Serve\Client\GuzzleClient;

class AbstractGuzzleXtcRequest extends AbstractXtcRequest
{
  protected function buildClient(){
    $this->getGuzzleClient();
    $this->client->setXtcConfig($this->config);
    return $this;
  }

  protected function getGuzzleClient(){
    return New GuzzleClient($this->profile);
  }
}
