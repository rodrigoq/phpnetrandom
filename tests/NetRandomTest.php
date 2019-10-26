<?php

use NetRandom\NetRandom;
use PHPUnit\Framework\TestCase;

final class NetRandomTest extends TestCase
{
    public function testMax() : void
    {
        $random = new NetRandom(1);
        $this->assertEquals(2147483646, $random->Next(2147483646, 2147483647));
        $this->assertEquals(2147483647, $random->Next(2147483647, 2147483647));
    }

    public function testMin() : void
    {
        $random = new NetRandom(2147483647);
        $this->assertEquals(0, $random->Next(0, 0));
        $this->assertEquals(0, $random->Next(0, 1));
        $this->assertEquals(1, $random->Next(0, 2));
    }

    public function testMultiple() : void
    {
        $random = new NetRandom(0);

        $this->assertEquals(1559595546, $random->Next());
        $this->assertEquals(0.817325359590969, $random->NextDouble());
        $this->assertEquals(7, $random->Next(1, 10));
        $this->assertEquals(0.558161191436537, $random->NextDouble());
        $this->assertEquals(0.206033154021033, $random->NextDouble());

        $bytes = array_fill(0, 4, 0);
        $random->NextBytes($bytes);
        $this->assertEquals([117, 228, 216, 173], $bytes);

        $this->assertEquals(587775847, $random->Next());
        $this->assertEquals(626863973, $random->Next());
        $this->assertEquals(0, $random->Next(0, 2));
        $this->assertEquals(0.632659072816679, $random->NextDouble());
    }

    public function testInt(): void
    {
        $expected = [
            1005837373, 1990280917,
            777395470, 514080283,
            1199797630, 698074035,
            1042177332, 1787422614,
            801760634, 202050422
        ];
        $random = new NetRandom(1939835508);

        foreach($expected as $exp)
            $this->assertEquals($exp, $random->Next());
    }

    public function testIntMax(): void
    {
        $expected = [3, 7, 0, 9, 9, 8, 2, 6, 9, 1];

        $random = new NetRandom(24220893);

        foreach($expected as $exp)
            $this->assertEquals($exp, $random->Next(0, 10));
    }

    public function testIntMinMax(): void
    {
        $expected = [649, 124, 742, 854, 327,
            390, 202, 398, 996, 632];

        $random = new NetRandom(361973920);

        foreach($expected as $exp)
            $this->assertEquals($exp, $random->Next(100, 1000));
    }

    public function testBytes(): void
    {
        $expected = [253, 76, 88, 30, 61,
            112, 181, 86, 173, 30];

        $random = new NetRandom(675715172);

        $bytes = array_fill(0, 10, 0);
        $random->NextBytes($bytes);
        $this->assertEquals($expected, $bytes);
    }

    public function testBytes2(): void
    {
        $expected = [167];

        $random = new NetRandom(1020396879);
        $bytes = [0];
        $random->NextBytes($bytes);

        $this->assertEquals($expected, $bytes);
    }

    public function testDouble(): void
    {
        $expected = [
            0.0432694028333153,
            0.291299793073581,
            0.686976668279142,
            0.6876487912087,
            0.248401598654874,
            0.32245239351059,
            0.423265538375483,
            0.662290578085133,
            0.218414166112623,
            0.564337236603879,
        ];
        $random = new NetRandom(71901281);

        foreach($expected as $exp)
            $this->assertEquals($exp, $random->NextDouble());
    }

    public function testDouble2(): void
    {
        $expected = [
            0.140547333816321,
            0.402765257005936,
            0.913445201662111,
            0.988114932546446,
            0.348282372275499,
            0.642510076818294,
            0.374620074115051,
            0.508085621757473,
            0.60641471976713,
            0.42751051877975,
        ];
        $random = new NetRandom(1054086677);

        foreach($expected as $exp)
            $this->assertEquals($exp, $random->NextDouble());
    }
}

