<?php
/**
 *
 * Date: 09.11.2021
 * Time: 16:16
 *
 */
namespace Pimcorecasts\Bundle\QrCode\Services;

use Pimcore\Model\DataObject\Data\UrlSlug;

class UrlSlugResolver
{

    // just a wrapper for the pimcore url slug feature, as it is internal and might change
    public static function resolveSlug($slug) {
        return UrlSlug::resolveSlug($slug);
    }

}
