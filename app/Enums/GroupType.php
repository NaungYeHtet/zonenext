<?php

namespace App\Enums;

enum GroupType: string
{
    case FeaturedProjects = 'FeaturedProjects';
    case TopTenProjects = 'TopTenProjects';
    case MostPopularProperties = 'MostPopularProperties';
    case UnderConstruction = 'UnderConstruction';
    case TopRatedListing = 'TopRatedListing';

    public function getGroupableRelationship(): string
    {
        return match ($this) {
            self::FeaturedProjects => 'projects',
            self::TopTenProjects => 'projects',
            self::MostPopularProperties => 'properties',
            self::UnderConstruction => 'projects',
            self::TopRatedListing => 'properties',
        };
    }
}
