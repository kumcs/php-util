<?php declare(strict_types=1);

namespace Xtuple\Util\OAuth2\Client\Token\Access;

use PHPUnit\Framework\TestCase;
use Xtuple\Util\Type\DateTime\Timestamp\TimestampStruct;
use Xtuple\Util\Type\UUID\UUIDv4;

class AccessTokenStructTest
  extends TestCase {
  /**
   * @throws \Throwable
   */
  public function testConstructor() {
    $value = (string) new UUIDv4();
    $now = time();
    $token = new class (new AccessTokenStruct($value, 'bearer', new TimestampStruct($now), null))
      extends AbstractAccessToken {
    };
    self::assertEquals($value, $token->value());
    self::assertEquals('bearer', $token->type());
    self::assertEquals($now, $token->expiresAt()->seconds());
    self::assertNull($token->refresh());
    $refresh = (string) new UUIDv4();
    $token = new class (new AccessTokenStruct($value, 'bearer', new TimestampStruct($now), $refresh))
      extends AbstractAccessToken {
    };
    self::assertEquals($value, $token->value());
    self::assertEquals('bearer', $token->type());
    self::assertEquals($now, $token->expiresAt()->seconds());
    self::assertEquals($refresh, $token->refresh());
  }
}
