<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 19/04/2018
 * Time: 17:21
 */

namespace Drupal\xtcguzzle\XtendedContent\Serve\XtcRequest;


use Drupal\Core\Site\Settings;
use Drupal\xtc\XtendedContent\API\Config;
use Drupal\xtc\XtendedContent\Serve\XtcRequest\AbstractXtcRequestOFF;
use Drupal\xtcguzzle\XtendedContent\Serve\Client\GuzzleClient;

class AbstractGuzzleXtcRequestOFF extends AbstractXtcRequestOFF
{
  protected function buildClient(){
    $this->client = $this->getGuzzleClient();
    $this->client->setXtcConfig($this->config);
    return $this;
  }

  protected function getGuzzleClient(){
    return New GuzzleClient($this->profile);
  }

  public function setConfigfromPlugins(array $config = [])
  {
    $name = $this->profile;
    $profile = Config::loadXtcProfile($name);
    $settings = Settings::get('xtc.serve_client')['xtc']['serve_client']['server'];
    $server = Config::loadXtcServer($profile['server']);
    if(!empty($settings[$profile['server']]['env'])){
      $server['env'] = $settings[$profile['server']]['env'];
    }

    $this->webservice = [
      'type' => $profile['type'],
      'env' => $server['env'],
      'connection' => $server['connection'] ?? '',
      'method' => $profile['method'],
    ];

    $xtctoken = Config::getConfigs('serve', 'xtctoken');
    $this->config['xtc']['serve_client'][$name] = $profile;
    $this->config['xtc']['serve_client'][$name]['token'] = $xtctoken['xtc']['serve_client'][$name]['token'] ?? '';
    $this->config['xtc']['serve_client']['server'][$profile['server']] = $server;

    $this->buildClient();
    return $this;
  }

  public function getConfigFromYaml()
  {
    $client = Config::getConfigs('serve', 'client');
    $xtctoken = Config::getConfigs('serve', 'xtctoken');
    $params = array_merge_recursive($client, $xtctoken);

    // Enable config override from settings.local.php
    $settings = Settings::get('xtc.serve_client');
    if(!empty($settings)){
      return array_replace_recursive($params, $settings);
    }
    return $params;
  }

}
