<?php declare(strict_types=1);

namespace Xtuple\Util\SOAP\Element\XSD\Numeric;

use Xtuple\Util\SOAP\Element\XSD\AbstractXSDElement;
use Xtuple\Util\SOAP\Element\XSD\XSDElementStruct;
use Xtuple\Util\SOAP\Type\XSD\Numeric\XSDNonNegativeInteger;

final class XSDNonNegativeIntegerElementStruct
  extends AbstractXSDElement {
  public function __construct(string $name, ?string $namespace, int $data) {
    parent::__construct(new XSDElementStruct(
      new XSDNonNegativeInteger(),
      $name,
      $namespace,
      $data
    ));
  }
}
