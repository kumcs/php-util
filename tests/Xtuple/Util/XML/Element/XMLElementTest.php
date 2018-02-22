<?php declare(strict_types=1);

namespace Xtuple\Util\XML\Element;

use PHPUnit\Framework\TestCase;
use Xtuple\Util\XML\Attribute\Collection\Map\ArrayMapXMLAttribute;
use Xtuple\Util\XML\Attribute\Optional\OptionalXMLAttributeStruct;
use Xtuple\Util\XML\Attribute\XMLAttributeStruct;
use Xtuple\Util\XML\Element\Collection\Sequence\ArrayListXMLElement;

class XMLElementTest
  extends TestCase {
  public function testEmpty() {
    $empty = new TestXMLElement(new XMLElementString('<Test/>'));
    self::assertEquals('Test', $empty->name());
    self::assertEquals('<Test/>', (string) $empty);
    self::assertEquals('', $empty->value());
    self::assertTrue($empty->attributes()->isEmpty());
    self::assertTrue($empty->children()->isEmpty());
    self::assertEquals('none', $empty->attributes()->getOptional(new OptionalXMLAttributeStruct('none'))->name());
  }

  public function testXML() {
    $element = new TestXMLElement(new XMLElementString(implode('', [
      '<Test name="test" debug="true" xmlns="https://xdruple.xtuple.com/schema/test">',
      '<Child name="not-empty">Test</Child>',
      '</Test>',
    ])));
    self::assertEquals(
      '<Test name="test" debug="true"><Child name="not-empty">Test</Child></Test>',
      (string) $element
    );
    $element = new TestXMLElement(new XMLElementString(implode('', [
      '<?xml version="1.0" encoding="UTF-8" ?>',
      '<Test name="test" debug="true" xmlns="https://xdruple.xtuple.com/schema/test">',
      '<Child name="not-empty">Test</Child>',
      '</Test>',
    ])));
    self::assertEquals(
      '<Test name="test" debug="true"><Child name="not-empty">Test</Child></Test>',
      (string) $element
    );
    self::assertEquals('<Child name="not-empty">Test</Child>', (string) $element->children('Child')->get(0));
  }

  /**
   * @expectedException \InvalidArgumentException
   * @expectedExceptionMessage Passed element is an attribute.
   */
  public function testElement() {
    $element = new TestXMLElement(new XMLElementString(implode('', [
      '<Test name="test" debug="true" xmlns="https://xdruple.xtuple.com/schema/test">',
      '<Child name="empty"></Child>',
      '<Child name="not-empty">Test</Child>',
      '<Element>Element</Element>',
      'Content',
      '</Test>',
    ])));
    self::assertEquals('Test', $element->name());
    self::assertEquals('Content', $element->value());
    self::assertEquals(3, $element->children()->count());
    self::assertEquals(2, $element->children('Child')->count());
    self::assertEquals(2, $element->children('/Test/Child')->count());
    self::assertEquals('Test', $element->children('Child[@name="not-empty"]')->get(0)->value());
    self::assertEquals('true', $element->attributes()->getOptional(new OptionalXMLAttributeStruct('debug'))->value());
    self::assertEquals(2, $element->attributes()->count());
    self::assertFalse($element->isEmpty());
    new XMLElementSimple((new \SimpleXMLElement('<Test name="test" />'))->attributes('name'));
  }

  public function testStruct() {
    self::assertEquals(
      '<Test/>',
      (string) new XMLElementStruct('Test')
    );
    self::assertEquals(
      '<Test>Value</Test>',
      (string) new XMLElementStruct('Test', 'Value')
    );
    self::assertEquals(
      '<Test><Child>Value</Child></Test>',
      (string) new XMLElementStruct('Test', '<Child>Value</Child>')
    );
    self::assertEquals(
      '<Test attribute1="value1" attribute2="value2">Value</Test>',
      (string) new XMLElementStruct('Test', 'Value', new ArrayMapXMLAttribute([
        new XMLAttributeStruct('attribute1', 'value1'),
        new XMLAttributeStruct('attribute2', 'value2'),
      ]))
    );
    self::assertEquals(
      '<Test><Child>value1</Child><Child>value2</Child>Value</Test>',
      (string) new XMLElementStruct('Test', 'Value', null, new ArrayListXMLElement([
        new XMLElementStruct('Child', 'value1'),
        new XMLElementStruct('Child', 'value2'),
      ]))
    );
    self::assertEquals(implode('', [
      '<Test attribute1="value1" attribute2="value2">',
      '<Element>value1</Element><Element>value2</Element>',
      '<Child attribute1="value1"><Grandchild>value1</Grandchild><Grandchild/>value3</Child>',
      'value1',
      '</Test>',
    ]), (string) new XMLElementStruct('Test', 'value1', new ArrayMapXMLAttribute([
      new XMLAttributeStruct('attribute1', 'value1'),
      new XMLAttributeStruct('attribute2', 'value2'),
    ]), new ArrayListXMLElement([
      new XMLElementStruct('Element', 'value1'),
      new XMLElementStruct('Element', 'value2'),
      new XMLElementStruct('Child', 'value3', new ArrayMapXMLAttribute([
        new XMLAttributeStruct('attribute1', 'value1'),
      ]), new ArrayListXMLElement([
        new XMLElementStruct('Grandchild', 'value1'),
        new XMLElementStruct('Grandchild'),
      ])),
    ])));
  }

  public function testSequence() {
    $element = new XMLElementSequence(new ArrayListXMLElement([
      new XMLElementString('<Name>phpunit</Name>'),
      new XMLElementString('<Debug>true</Debug>'),
      new XMLElementString('<Encoding>UTF-8</Encoding>'),
    ]));
    self::assertEquals('', $element->name());
    self::assertEquals('', $element->value());
    self::assertTrue($element->attributes()->isEmpty());
    self::assertNull($element->attributes()->getOptional(new OptionalXMLAttributeStruct('any'))->value());
    self::assertFalse($element->isEmpty());
    self::assertEquals('phpunit', $element->children()->get(0)->value());
    self::assertEquals('phpunit', $element->children('Name')->get(0)->value());
    $element = new XMLElementSequence(new ArrayListXMLElement());
    self::assertTrue($element->isEmpty());
    self::assertTrue($element->children()->isEmpty());
  }
}

final class TestXMLElement
  extends AbstractXMLElement {
}
