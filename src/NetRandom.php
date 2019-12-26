<?php

namespace NetRandom;

/**
 * .Net framework Random class port to php.
 * ported from:
 * https://referencesource.microsoft.com/#mscorlib/system/random.cs
 *
 * Comments are from the original file,
 * except the ones starting with 'port:'.
 */
class NetRandom
{
    // private
    private const MBIG = 2147483647;
    private const MSEED = 161803398;
    private const MZ = 0;

    private const MMIN = -2147483648;

    /** @var int */
    private $inext;
    /** @var int */
    private $inextp;

    /** @var array<int, int> */
    private $seedArray;

    public function __construct(int $seed = null)
    {
        if (ini_get('precision') != 15)
            ini_set('precision', 15);

        if ($seed === null)
            $seed = (int)microtime(true);

        //port: .Net seed is Int32.
        if ($seed > self::MBIG || $seed < self::MMIN)
            throw new \Exception('Value was either too large or too small for an Int32.');

        $this->seedArray = array_fill(0, 56, 0);

        //Initialize our Seed array.
        //This algorithm comes from Numerical Recipes in C (2nd Ed.)
        /** @var int */
        $subtraction = ($seed === self::MMIN) ? self::MBIG : abs($seed);
        /** @var int */
        $mj = self::MSEED - $subtraction;
        $this->seedArray[55] = $mj;

        /** @var int */
        $mk = 1;
        /** @var int */
        $ii = 0;
        for ($i = 1; $i < 55; $i++)
        {
            //Apparently the range [1..55] is special (Knuth)
            //and so we're wasting the 0'th position.
            $ii = (21 * $i) % 55;
            $this->seedArray[$ii] = $mk;
            $mk = $mj - $mk;
            if ($mk < 0)
                $mk += self::MBIG;
            $mj = $this->seedArray[$ii];
        }

        for ($k = 1; $k < 5; $k++)
        {
            for ($i = 1; $i < 56; $i++)
            {
                $this->seedArray[$i] -= $this->seedArray[1 + ($i + 30) % 55];

                //port: binary complement must be the sign, not a 32 plus bit number.
                $this->seedArray[$i] &= 0xFFFFFFFF;
                if (0x80000000 & $this->seedArray[$i])
                    $this->seedArray[$i] = - (0xFFFFFFFF - $this->seedArray[$i] + 1);

                if ($this->seedArray[$i] < 0)
                    $this->seedArray[$i] += self::MBIG;
            }
        }

        $this->inext = 0;
        $this->inextp = 21;
        $seed = 1;
    }

    /**
     * Return a new random number [0..1) and reSeed the Seed array.
     *
     * @return float (double) [0..1)
     */
    protected function Sample() : float
    {
        //Including this division at the end gives us
        //significantly improved random number distribution.
        return ($this->InternalSample() * (1.0 / self::MBIG));
    }

    private function InternalSample() : int
    {
        /** @var int */
        $locINext = $this->inext;
        /** @var int */
        $locINextp = $this->inextp;

        if (++$locINext >= 56)
            $locINext = 1;
        if (++$locINextp >= 56)
            $locINextp = 1;

        /** @var int */
        $retVal = $this->seedArray[$locINext] - $this->seedArray[$locINextp];

        if ($retVal == self::MBIG)
            $retVal--;
        if ($retVal < 0)
            $retVal += self::MBIG;

        $this->seedArray[$locINext] = $retVal;

        $this->inext = $locINext;
        $this->inextp = $locINextp;

        return $retVal;
    }

    private function GetSampleForLargeRange() : float
    {
        // The distribution of double value returned by Sample
        // is not distributed well enough for a large range.
        // If we use Sample for a range [-2147483648..2147483647)
        // We will end up getting even numbers only.

        /** @var int */
        $result = $this->InternalSample();

        // Note we can't use addition here.
        // The distribution will be bad if we do that.
        /** @var bool */
        $negative = ($this->InternalSample() % 2 == 0) ? true : false;  // decide the sign based on second sample

        if ($negative)
            $result = -$result;

        /** @var float */
        $d = $result;
        // get a number in range [0 .. 2 * self::MBIG - 1)
        $d += (self::MBIG - 1);
        $d /= 2 * self::MBIG - 1;
        return $d;
    }

    /**
     * @param int $minValue the least legal value for the Random number.
     * @param int $maxValue One greater than the greatest legal return value.
     *
     * @return int [minvalue..maxvalue)
     */
    public function Next(int $minValue = null, int $maxValue = null) : int
    {
        if ($minValue === null && $maxValue === null)
            return $this->InternalSample();

        if ($minValue !== null && $maxValue === null)
        {
            $maxValue = $minValue;
            $minValue = null;
            if ($maxValue < 0)
                throw new \Exception('maxValue out of range');

            return (int)($this->Sample() * $maxValue);
        }

        if ($minValue === null)
            throw new \Exception('minValue null');

        if ($minValue > $maxValue)
            throw new \Exception('minValue out of range');

        /** @var int */
        $range = $maxValue - $minValue;
        if ($range <= self::MBIG)
            return (int)($this->Sample() * $range) + $minValue;
        else
            return (int)($this->GetSampleForLargeRange() * $range) + $minValue;
    }

    /**
     * @return float (double) [0..1)
     */
    public function NextDouble() : float
    {
        return $this->Sample();
    }


    /**
     * Fills the byte array with random bytes [0..0x7f].
     * The entire array is filled.
     *
     * @param array $buffer the array to be filled.
     *
     */
    public function NextBytes(array &$buffer) : void
    {
        for ($i = 0; $i < count($buffer); $i++)
            $buffer[$i] = (int)($this->InternalSample() % (255 + 1));
    }
}

