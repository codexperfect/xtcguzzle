<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 19/04/2018
 * Time: 17:21
 */

namespace Drupal\xtcguzzle\XtendedContent\Serve\XtcRequest;


use Drupal\xtc\XtendedContent\API\Config;
use Drupal\xtc\XtendedContent\Serve\XtcRequest\AbstractXtcRequest;
use Drupal\xtcguzzle\XtendedContent\Serve\Client\GuzzleClient;

class AbstractGuzzleXtcRequest extends AbstractXtcRequest
{
  protected function buildClient(){
    $this->client = $this->getGuzzleClient();
    $this->client->setXtcConfig($this->config);
    return $this;
  }

  protected function getGuzzleClient(){
    return New GuzzleClient($this->profile);
  }

  public function getConfigFromYaml()
  {
    $client = Config::getConfigs('serve', 'client');
    $xtctoken = Config::getConfigs('serve', 'xtctoken');
    return array_merge_recursive($client, $xtctoken);
  }

}
