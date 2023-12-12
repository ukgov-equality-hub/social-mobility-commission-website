<?php

return [
    /**
     * List all the sub-classes of Rareloop\Lumberjack\Post in your app that you wish to
     * automatically register with WordPress as part of the bootstrap process.
     */
    'register' => [
        // List your CUSTOM PostTypes as  follows
        // -------------------------------------------------
//         /App\PostTypes\Product::class,
            \App\PostTypes\PressReleases::class,
            \App\PostTypes\Speeches::class,
            \App\PostTypes\People::class,
            \App\PostTypes\Blogs::class,
            \App\PostTypes\CaseStudies::class,
            \App\PostTypes\Resources::class,
            \App\PostTypes\Events::class,
            \App\PostTypes\Reports::class,
            \App\PostTypes\PolicyPapers::class,
            \App\PostTypes\GuidanceAndRegulation::class,
            \App\PostTypes\CorporateReports::class,
            \App\PostTypes\ResearchAndStatsIndex::class,
            \App\PostTypes\OrganisationalDirectory::class,
            \App\PostTypes\Charity::class,
            \App\PostTypes\Funders::class,


        // CUSTOM Taxonomies Below Here in the same fashion
        // -------------------------------------------------
        // \App\Taxonomies\Division::class,
        \App\Taxonomies\PeoplePosition::class,
        \App\Taxonomies\EventLocations::class,
        \App\Taxonomies\EventType::class,
        \App\Taxonomies\EventOrganiser::class,
        \App\Taxonomies\ResourceType::class,
        \App\Taxonomies\CaseStudyType::class,
        \App\Taxonomies\CaseStudyLocation::class,
        \App\Taxonomies\CaseStudySector::class,
        \App\Taxonomies\CaseStudyIndustryArea::class,
        \App\Taxonomies\DocumentKeyArea::class,
        \App\Taxonomies\DocumentType::class,

    ],
];
