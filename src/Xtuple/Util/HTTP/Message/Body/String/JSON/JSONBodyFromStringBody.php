<?php declare(strict_types=1);

namespace Xtuple\Util\HTTP\Message\Body\String\JSON;

use Xtuple\Util\Collection\Tree\ArrayTree\ArrayTree;
use Xtuple\Util\Collection\Tree\Tree;
use Xtuple\Util\HTTP\Message\Body\String\AbstractStringBody;
use Xtuple\Util\HTTP\Message\Body\String\JSON\Exception\JSONException;

final class JSONBodyFromStringBody
  extends AbstractStringBody
  implements JSONBody {
  public function jsonSerialize() {
    return $this->data()->data();
  }

  public function data(): Tree {
    $data = [];
    if ($content = (string) $this) {
      $data = json_decode($content, true);
      if ($data === null) {
        throw new JSONException();
      }
    }
    return new ArrayTree((array) $data);
  }
}
