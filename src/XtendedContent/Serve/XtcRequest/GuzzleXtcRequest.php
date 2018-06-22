<?php
/**
 * Created by PhpStorm.
 * User: aisrael
 * Date: 22/06/2018
 * Time: 11:46
 */

namespace Drupal\xtcguzzle\XtendedContent\Serve\XtcRequest;


use Drupal\xtcguzzle\XtendedContent\Serve\Client\GuzzleClient;

class GuzzleXtcRequest extends AbstractGuzzleXtcRequest
{
  protected function getGuzzleClient(){
    return New GuzzleClient($this->profile);
  }

}