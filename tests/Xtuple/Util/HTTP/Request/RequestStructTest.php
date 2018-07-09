<?php declare(strict_types=1);

namespace Xtuple\Util\HTTP\Request;

use PHPUnit\Framework\TestCase;
use Xtuple\Util\HTTP\Message\Body\String\QueryBody;
use Xtuple\Util\HTTP\Message\Header\Collection\Set\ArraySetHeader;
use Xtuple\Util\HTTP\Message\Header\HeaderStruct;
use Xtuple\Util\HTTP\Request\Method\Method\GET;
use Xtuple\Util\HTTP\Request\Method\Method\POST;
use Xtuple\Util\HTTP\Request\URI\URL\URLString;
use Xtuple\Util\Type\Stream\String\StringStreamFromResource;
use Xtuple\Util\Type\UUID\UUIDv4;

final class RequestStructTest
  extends TestCase {
  /**
   * @throws \Throwable
   */
  public function testConstructor() {
    $request = new RequestStruct(new GET(), new URLString('http://example.com'));
    self::assertEquals('GET', (string) $request->method());
    self::assertEquals('http://example.com', (string) $request->uri());
    self::assertTrue($request->headers()->isEmpty());
    self::assertEquals('', (string) new StringStreamFromResource($request->body()->resource()));
    $etag = new UUIDv4();
    $request = new RequestStruct(new POST(), new URLString('http://example.com/post'), new ArraySetHeader([
      new HeaderStruct('ETag', (string) $etag),
    ]), new QueryBody([
      'title' => 'TestExample',
      'content' => [
        'header' => 'Lorem Ipsum',
        'footer' => 'O tempora o mores!',
      ],
    ]));
    self::assertEquals('POST', (string) $request->method());
    self::assertEquals('http://example.com/post', (string) $request->uri());
    self::assertEquals("ETag: {$etag}", (string) $request->headers()->get('ETag'));
    self::assertEquals(1, $request->headers()->count());
    self::assertEquals(
      'title=TestExample&content%5Bheader%5D=Lorem+Ipsum&content%5Bfooter%5D=O+tempora+o+mores%21',
      (string) new StringStreamFromResource($request->body()->resource())
    );
  }
}
