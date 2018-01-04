<?php declare(strict_types=1);

namespace Xtuple\Util\Collection\Map\ArrayMap\StrictType;

use PHPUnit\Framework\TestCase;

class StrictlyTypedArrayMapTest
  extends TestCase {
  /**
   * @expectedException \InvalidArgumentException
   * @expectedExceptionMessage Method key() is not defined in type \stdClass
   */
  public function testConstructor() {
    $map = new StrictlyTypedArrayMap(TestEntry::class, [
      'key' => new TestEntry('key', 'value'),
    ]);
    self::assertEquals('value', $map->get('key')->value());
    $map = new StrictlyTypedArrayMap(TestEntry::class, [
      new TestEntry('key', 'value'),
    ], 'key');
    self::assertEquals('value', $map->get('key')->value());
    new StrictlyTypedArrayMap(TestEntry::class, [], 'key');
    new StrictlyTypedArrayMap(\stdClass::class, [], 'key');
  }

  public function testKey() {
    $map = new StrictlyTypedArrayMap(TestEntry::class, [
      new TestEntry('key', 'value'),
    ], 'key');
    self::assertEquals('value', $map->get('key')->value());
    self::assertEquals(null, $map->get('value'));
    self::assertEquals(1, $map->count());
    self::assertFalse($map->isEmpty());
    foreach ($map as $k => $v) {
      self::assertEquals('key', $k);
      self::assertEquals('value', $v->value());
    }
  }
}

final class TestEntry {
  /** @var string */
  private $key;
  /** @var string */
  private $value;

  public function __construct(string $key, string $value) {
    $this->key = $key;
    $this->value = $value;
  }

  public function key(): string {
    return $this->key;
  }

  public function value(): string {
    return $this->value;
  }
}
