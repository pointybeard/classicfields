<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\ClassicFields\Fields;

use pointybeard\Symphony\Extensions\ClassicFields;

class Select extends ClassicFields\AbstractField
{
    public function getCreateFieldSQL(): string
    {
        return "CREATE TABLE IF NOT EXISTS `tbl_fields_select` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `field_id` int(11) unsigned NOT NULL,
          `allow_multiple_selection` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
          `sort_options` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
          `static_options` text COLLATE utf8_unicode_ci,
          `dynamic_options` int(11) unsigned DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `field_id` (`field_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
    }
}
