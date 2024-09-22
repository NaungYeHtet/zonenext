<?php

namespace App\Enums\Filters;

use App\Traits\HasOptions;
use Illuminate\Support\Collection;

enum FilterPrice: int
{
    use HasOptions;
    case OneLakh = 100000;
    case TwoLakh = 200000;
    case ThreeLakh = 300000;
    case FourLakh = 400000;
    case FiveLakh = 500000;
    case SixLakh = 600000;
    case SevenLakh = 700000;
    case EightLakh = 800000;
    case NineLakh = 900000;
    case HundredLakh = 10000000;
    case TwoHundredLakh = 20000000;
    case ThreeHundredLakh = 30000000;
    case FourHundredLakh = 40000000;
    case FiveHundredLakh = 50000000;
    case SixHundredLakh = 60000000;
    case SevenHundredLakh = 70000000;
    case EightHundredLakh = 80000000;
    case NineHundredLakh = 90000000;
    case OneCrore = 100000000;
    case OneCroreFiftyThousand = 150000000;
    case TwoCrore = 200000000;
    case TwoCroreFiftyThousand = 250000000;
    case ThreeCrore = 300000000;
    case ThreeCroreFiftyThousand = 350000000;
    case FourCrore = 400000000;
    case FourCroreFiftyThousand = 450000000;
    case FiveCrore = 500000000;
    case SixCrore = 600000000;
    case SevenCrore = 700000000;
    case EightCrore = 800000000;
    case NineCrore = 900000000;
    case TenCrore = 1000000000;
    case HundredCrore = 1000000000;
    case TwoHundredCrore = 2000000000;
    case ThreeHundredCrore = 3000000000;

    public function getLabel(): string
    {
        return number_format_price($this->value);
    }

    public static function getRangeOptions(): Collection
    {
        $ranges = [];
        $filterPrices = collect(self::cases());

        foreach (FilterListType::cases() as $filterListType) {
            $ranges[$filterListType->value] = collect($filterPrices)->filter(function ($price) use ($filterListType) {
                if ($price->value < $filterListType->getFilterPriceMinimum()) {
                    return false;
                }

                $max = $filterListType->getFilterPriceMaximum();

                if (! $max) {
                    return true;
                }

                return $price->value <= $max;
            })->values()->map(fn ($price) => $price->getOption());
        }

        return collect($ranges);
    }
}
