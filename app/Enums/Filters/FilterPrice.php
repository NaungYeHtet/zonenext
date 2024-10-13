<?php

namespace App\Enums\Filters;

use App\Traits\HasOptions;
use Illuminate\Support\Collection;

enum FilterPrice: string
{
    use HasOptions;
    case OneLakh = '1-lakh';
    case TwoLakh = '2-lakh';
    case ThreeLakh = '3-lakh';
    case FourLakh = '4-lakh';
    case FiveLakh = '5-lakh';
    case SixLakh = '6-lakh';
    case SevenLakh = '7-lakh';
    case EightLakh = '8-lakh';
    case NineLakh = '9-lakh';
    case TenLakh = '10-lakh';
    case HundredLakh = '100-lakh';
    case TwoHundredLakh = '200-lakh';
    case ThreeHundredLakh = '300-lakh';
    case FourHundredLakh = '400-lakh';
    case FiveHundredLakh = '500-lakh';
    case SixHundredLakh = '600-lakh';
    case SevenHundredLakh = '700-lakh';
    case EightHundredLakh = '800-lakh';
    case NineHundredLakh = '900-lakh';
    case OneCrore = '1000-lakh';
    case OneCroreFiftyThousand = '1500-lakh';
    case TwoCrore = '2000-lakh';
    case TwoCroreFiftyThousand = '2500-lakh';
    case ThreeCrore = '3000-lakh';
    case ThreeCroreFiftyThousand = '3500-lakh';
    case FourCrore = '4000-lakh';
    case FourCroreFiftyThousand = '4500-lakh';
    case FiveCrore = '5000-lakh';
    case SixCrore = '6000-lakh';
    case SevenCrore = '7000-lakh';
    case EightCrore = '8000-lakh';
    case NineCrore = '9000-lakh';
    case HundredCrore = '10000-lakh';
    case TwoHundredCrore = '20000-lakh';
    case ThreeHundredCrore = '30000-lakh';

    public function getValue(): int
    {
        return match ($this) {
            self::OneLakh => 100000,
            self::TwoLakh => 200000,
            self::ThreeLakh => 300000,
            self::FourLakh => 400000,
            self::FiveLakh => 500000,
            self::SixLakh => 600000,
            self::SevenLakh => 700000,
            self::EightLakh => 800000,
            self::NineLakh => 900000,
            self::TenLakh => 1000000,
            self::HundredLakh => 10000000,
            self::TwoHundredLakh => 20000000,
            self::ThreeHundredLakh => 30000000,
            self::FourHundredLakh => 40000000,
            self::FiveHundredLakh => 50000000,
            self::SixHundredLakh => 60000000,
            self::SevenHundredLakh => 70000000,
            self::EightHundredLakh => 80000000,
            self::NineHundredLakh => 90000000,
            self::OneCrore => 100000000,
            self::OneCroreFiftyThousand => 150000000,
            self::TwoCrore => 200000000,
            self::TwoCroreFiftyThousand => 250000000,
            self::ThreeCrore => 300000000,
            self::ThreeCroreFiftyThousand => 350000000,
            self::FourCrore => 400000000,
            self::FourCroreFiftyThousand => 450000000,
            self::FiveCrore => 500000000,
            self::SixCrore => 600000000,
            self::SevenCrore => 700000000,
            self::EightCrore => 800000000,
            self::NineCrore => 900000000,
            self::HundredCrore => 1000000000,
            self::TwoHundredCrore => 2000000000,
            self::ThreeHundredCrore => 3000000000,
        };
    }

    public function getLabel(): string
    {
        return number_format_price($this->getValue());
    }

    public static function getRangeOptions(FilterListType $filterListType): Collection
    {
        $filterPrices = collect(self::cases());

        return collect($filterPrices)->filter(function ($price) use ($filterListType) {
            if ($price->getValue() < $filterListType->getFilterPriceMinimum()) {
                return false;
            }

            $max = $filterListType->getFilterPriceMaximum();

            if (! $max) {
                return true;
            }

            return $price->getValue() <= $max;
        })->values()->map(fn ($price) => $price->getOption());
    }
}
