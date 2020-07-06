<?php

namespace App\Tests\Service;

use App\Service\LCNormalizer;
use PHPUnit\Framework\TestCase;

class LCNormalizerTest extends TestCase
{
    /** @var LCNormalizer */
    private $normalizer;

    public function setUp(): void
    {
        $this->normalizer = new LCNormalizer();
    }

    public function testNormalize(): void
    {
        $call_numbers = [
            new TestLCCallNumber('PN1009.A1 L724', 'PN 1009.0000A 1000L 7240'),
            new TestLCCallNumber('RJ45.Q', 'RJ 0045.0000Q 0000  0000'),
            new TestLCCallNumber('Z664.', 'Z  0664.0000  0000  0000'),
            new TestLCCallNumber('Q1.', 'Q  0001.0000  0000  0000'),
            new TestLCCallNumber('E', 'E  0000.0000  0000  0000'),
            new TestLCCallNumber('E184.A2 G78 1970', 'E  0184.0000A 2000G 7800 1970'),
            new TestLCCallNumber('B765.T54 B', 'B  0765.0000T 5400B 0000')
        ];
        foreach ($call_numbers as $call_number) {
            $this->assertEquals($call_number->normalized, $this->normalizer->normalize($call_number->input));
        }
    }

    public function testNormalizeFinalCallNumbers(): void
    {
        $call_numbers = [
            new TestLCCallNumber('PN1009.A1 L724', 'PN 1009.0000A 1000L 7240'),
            new TestLCCallNumber('QH506.M652', 'QH 0506.0000M 6520Z 9999'),
            new TestLCCallNumber('Z664.', 'Z  0664.0000Z 9999Z 9999'),
            new TestLCCallNumber('QA76.58.I58', 'QA 0076.5800I 5800Z 9999'),
            new TestLCCallNumber('AP2 .P85285', 'AP 0002.0000P 8528Z 9999')

        ];
        foreach ($call_numbers as $call_number) {
            $expected = $call_number->normalized;
            $actual = $this->normalizer->normalize($call_number->input, true);
            $this->assertEquals($expected, $actual);
        }
    }
}

/**
 * Holder for test call number data
 *
 * @package App\Tests\Service
 */
class TestLCCallNumber
{
    /** @var string */
    public $input;

    /** @var string */
    public $normalized;

    public function __construct(string $input, string $normalized)
    {
        $this->input = $input;
        $this->normalized = $normalized;
    }
}
