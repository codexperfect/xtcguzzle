<?php
/**
 * Created by PhpStorm.
 * User: aisrael
 * Date: 2019-04-18
 * Time: 19:23
 */

namespace Drupal\xtcguzzle\Plugin\XtcHandler;


use Drupal\Component\Serialization\Json;
use Drupal\xtc\PluginManager\XtcHandler\XtcHandlerPluginBase;
use Drupal\xtc\XtendedContent\API\XtcServer;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Client;

/**
 * Plugin implementation of the xtc_handler.
 *
 * @XtcHandler(
 *   id = "guzzle_get",
 *   label = @Translation("Guzzle Get for XTC"),
 *   description = @Translation("Guzzle Get for XTC description.")
 * )
 */
class GuzzleGet extends GuzzleBase
{

  /**
   * @var string
   */
  protected $url;

  /**
   * @var Uri
   */
  protected $uri;

  /**
   * @var Client
   */
  protected $client;

  public function process() {
    $this->buildClient();
    $this->getStream();
    return $this;
  }

  public function values() {
    return Json::decode($this->content) ?? null;
  }

  protected function getStream(){
    $qs = \Drupal::request()->getQueryString();
    $request = (!empty($qs)) ? $this->url . '?' .$qs : $this->url;
    try{
      $stream = $this->client->get($request);
      $this->content = $stream->getBody()->getContents();
    }
    catch (\Exception $exception){
      $this->content = Json::encode(['Exception' => $exception->getMessage()]);
    }
  }

  /**
   * @param array $options
   *
   * @return \Drupal\xtc\PluginManager\XtcHandler\XtcHandlerPluginBase
   */
  public function setOptions($options = []) : XtcHandlerPluginBase {
    parent::setOptions($options);
    $this->setUrl();
    $this->options['base_uri'] = $this->getUrl();
    $this->options['headers']['auth_token'] = $this->getToken();
    return $this;
  }

  /**
   * @return string
   */
  public function getToken() : string
  {
//    return (isset($this->clientProfile['token'])) ? $this->clientProfile['token'] : '';
    return '';
  }

  protected function buildClient() : GuzzleBase {
    $this->client = New Client($this->options);
    return $this;
  }

  public function setUrl() : GuzzleBase {
    $server = XtcServer::load($this->profile['server']);
    $this->method = $this->profile['method'];
    $env = $server['path'][$server['env']];
    $this->endpoint = $env['endpoint'];
    $protocole = ($env['tls']) ? 'https' : 'http';
    $port = (!empty($env['port'])) ? ':' . $env['port'] : '';
    $this->uri =
      (!empty($this->endpoint)) ? $this->endpoint . '/' . $this->method
        : $this->method;
    $this->url = $protocole . '://' . $env['server'] . $port . '/' . $this->uri;
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


}