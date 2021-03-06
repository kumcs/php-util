<?php declare(strict_types=1);

namespace Xtuple\Util\File\Directory;

use PHPUnit\Framework\TestCase;
use Xtuple\Util\File\Directory\Make\MakeDirectoryPath;

class DirectoryTest
  extends TestCase {
  public function tearDown() {
    parent::tearDown();
    if (file_exists('/tmp/phpunit/php-util/dir-test/test')) {
      unlink('/tmp/phpunit/php-util/dir-test/test');
    }
    if (file_exists('/tmp/phpunit/php-util/dir-test')) {
      rmdir('/tmp/phpunit/php-util/dir-test');
    }
  }

  /**
   * @throws \Throwable
   */
  public function testDirectory() {
    $directory = '/tmp/phpunit/php-util/dir-test';
    $make = new MakeDirectoryPath($directory);
    self::assertEquals('dir-test', $make->name());
    self::assertEquals($directory, $make->path()->absolute());
    self::assertEquals(filemtime($directory), $make->modified());
    $dir = new DirectoryPath('/tmp/phpunit/php-util');
    self::assertEquals('php-util', $dir->name());
    self::assertEquals('/tmp/phpunit/php-util', $dir->path()->absolute());
    self::assertEquals(filemtime($directory), $dir->modified());
    $relative = new RelativeDirectory($dir, 'dir-test');
    self::assertEquals('dir-test', $relative->name());
    self::assertEquals($directory, $relative->path()->absolute());
    self::assertEquals($make->modified(), $relative->modified());
    rmdir($directory);
    rmdir('/tmp/phpunit/php-util');
  }

  public function testMakeDirectoryError() {
    mkdir('/tmp/phpunit/php-util/dir-test', 0777, true);
    touch('/tmp/phpunit/php-util/dir-test/test');
    $previous = error_reporting(0);
    try {
      new MakeDirectoryPath('/tmp/phpunit/php-util/dir-test/test');
      self::fail('Exception "Failed to create a directory" is not thrown');
    }
    catch (\Throwable $e) {
      self::assertEquals(
        'Failed to create a directory /tmp/phpunit/php-util/dir-test/test: mkdir(): File exists',
        $e->getMessage()
      );
      error_reporting($previous);
    }
    try {
      new MakeDirectoryPath('/tmp/phpunit/php-util/dir-test/test');
      self::fail('Error "mkdir(): File exists" is not thrown');
    }
    catch (\Throwable $e) {
      self::assertEquals(
        'mkdir(): File exists',
        $e->getMessage()
      );
    }
  }

  /**
   * @expectedException \Throwable
   * @expectedExceptionMessage Path /tmp/phpunit/php-util/dir-test/test is not a directory
   * @throws \Throwable
   */
  public function testDirectoryPathError() {
    new MakeDirectoryPath('/tmp/phpunit/php-util/dir-test');
    touch('/tmp/phpunit/php-util/dir-test/test');
    new DirectoryPath('/tmp/phpunit/php-util/dir-test/test');
  }

  /**
   * @throws \Throwable
   */
  public function testPackage() {
    $package = new PackageDirectory(__NAMESPACE__, __DIR__);
    $directory = dirname($GLOBALS['__PHPUNIT_BOOTSTRAP'], 2);
    self::assertEquals($directory, $package->path()->absolute());
    self::assertEquals((new \SplFileInfo($directory))->getFilename(), $package->name());
    self::assertEquals(filemtime($directory), $package->modified());
  }
}
