<?php

namespace App\Taxonomies;

interface TaxonomyInterface
{

    public static function register();

    public static function getTermType(): string;

    public static function getAssociatedPostTypes(): array;

    public static function getTaxonomyConfig(): array;

}
