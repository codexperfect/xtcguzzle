<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 27/04/2018
 * Time: 11:01
 */

namespace Drupal\xtcguzzle\XtendedContent\Serve\Client;

use Drupal\xtc\XtendedContent\API\Config;
use Drupal\xtc\XtendedContent\Serve\Client\AbstractClient;
use Drupal\xtc\XtendedContent\Serve\Client\ClientInterface;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Client;

class GuzzleClient extends AbstractClient
{

  /**
   * @var string
   */
  private $url;

  /**
   * @var Uri
   */
  private $uri;

  /**
   * @var Client
   */
  protected $client;

  /**
   * @param string $method
   * @param string $param
   *
   * @return ClientInterface
   */
  public function init($method, $param = '') : ClientInterface {
    $this->setUri($method, $param);
    return $this;
  }

  /**
   * @return string
   */
  public function get() : string {
    $res = $this->client->get($this->uri);
    return $res->getBody()->getContents();
  }

  /**
   * @return ClientInterface
   */
  public function setOptions() : ClientInterface
  {
    $this->setClientProfile();
    $this->setUrl();

    $options = $this->clientProfile['options'];
    $options['base_uri'] = $this->getUrl();
    $options['headers']['auth_token'] = $this->getToken();
    $this->options = $options;
    return $this;
  }

  /**
   * @return ClientInterface
   */
  protected function buildClient() : ClientInterface {
    $this->setOptions();
    $this->client = New Client($this->options);
    return $this;
  }

  /**
   * @return Client
   */
  public function getClient() {
    return $this->client;
  }

  /**
   * @return ClientInterface
   */
  public function setXtcConfig(array $config = []) : ClientInterface {
    $this->xtcConfig = (!empty($config)) ? $config : $this->getXtcConfigFromYaml();
    $this->buildClient();
    return $this;
  }

  public function getXtcConfigFromYaml() : ClientInterface {
    $client = Config::getConfigs('serve', 'client');
    $xtctoken = Config::getConfigs('serve', 'xtctoken');
    return array_merge_recursive($client, $xtctoken);
  }

  /**
   * @return string
   */
  public function getToken() : string
  {
    return (isset($this->clientProfile['token'])) ? $this->clientProfile['token'] : '';
  }

  /**
   * @param $method
   * @param string $param
   *
   * @return ClientInterface
   */
  public function setUri($method, $param = '') : ClientInterface {
    $this->uri = New Uri($method . '/' . $param);
    return $this;
  }

  /**
   * @return Uri
   */
  public function getUri() {
    return $this->uri;
  }

  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }

  /**
   * @return ClientInterface
   */
  public function setUrl() : ClientInterface
  {
    $this->url = $this->buildWSPath($this->clientProfile['env']);
    return $this;
  }

  /**
   * @param string $environment
   *
   * @return string
   */
  private function buildWSPath($environment){
    $env =  $this->getEnvironment($environment);
    $protocole = ($env['tls']) ? 'https' : 'http' ;
    $port = (isset($env['port'])) ? ':'.$env['port'] : '';
    return $protocole.'://'.$env['server'].$port.'/'.$env['endpoint'].'/';
  }
}
