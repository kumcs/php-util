<?php
/** @noinspection AutoloadingIssuesInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
declare(strict_types=1);

use Xtuple\Util\RegEx\AbstractRegEx;
use Xtuple\Util\RegEx\RegExPattern;

// Defining a class for a specific pattern.
final class CloudDomainRegEx
  extends AbstractRegEx {
  public function __construct() {
    // Pattern to parse cloud EC2-like IPv4 domains
    parent::__construct(new RegExPattern('/
       (?:(\w+)\-)?                                        # prefix
       (?P<ip>
         (?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9])?\-){3} # first 3 parts of IP
         (?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9])           # last part of IP
       )
       \.(.*)                                              # base domain
     /x'));
  }
}

$regex = new CloudDomainRegEx();

// RegEx implements __toString()
(string) $regex === $regex->pattern();

$domain = 'ec2-255-249-199-99.compute-1.amazonaws.com';

// RegEx::group() returns group value by name or number
$regex->group($domain, 'ip') === '255-249-199-99';
$regex->group($domain, '3') === 'compute-1.amazonaws.com';

// RegEx::matches() wraps up preg_match(), but instead of bitmask flags, uses boolean flag parameter
// $capture === true sets PREG_OFFSET_CAPTURE
$regex->matches($domain, $capture = true) === [
  ['ec2-255-249-199-99.compute-1.amazonaws.com', 0],
  ['ec2', 0],
  ['255-249-199-99', 4],
  ['compute-1.amazonaws.com', 19],
  'ip' => ['255-249-199-99', 4],
];

// RegEx::all() wraps up preg_match_all(), but instead of bitmask flags, uses boolean flag parameters
// $set === true sets PREG_SET_ORDER
// $capture === true sets PREG_OFFSET_CAPTURE
$regex->all($domain, $set = true, $capture = true) === [
  [
    ['ec2-255-249-199-99.compute-1.amazonaws.com', 0],
    ['ec2', 0],
    ['255-249-199-99', 4],
    ['compute-1.amazonaws.com', 19],
    'ip' => ['255-249-199-99', 4],
  ],
];

// RegEx::replace() wraps up preg_replace()
$regex->replace('$2.example.com', $domain) === '255-249-199-99.example.com';
