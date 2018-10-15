<?php
declare(strict_types=1);

namespace Beeriously\Domain\Measurements\Temperature;

class DegreesCelsius implements Temperature
{
    use TemperatureStringFormat;

    private const SYMBOL = '°C';
    const FLOAT_PRECISION = 3;

    private $value;

    public function __construct(float $value)
    {
        $this->value = round($value, self::FLOAT_PRECISION);

        if ($this->value < AbsoluteZero::IN_CELSIUS) {
            throw new AbsoluteZeroException();
        }

        if ($this->value === -0.0) {
            $this->value = 0.0;
        }

    }

    public static function getSymbol(): string
    {
        return self::SYMBOL;
    }

    public static function fromFahrenheit(DegreesFahrenheit $fahrenheit): self
    {
        return new self(($fahrenheit->getValue() - 32.0) * 5 / 9);
    }

    public function getValue(): float
    {
        return $this->value;

    }

}
