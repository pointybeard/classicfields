<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\ClassicFields\Fields;

use pointybeard\Symphony\Extensions\ClassicFields;

class Date extends ClassicFields\AbstractField
{
    public function getCreateFieldSQL(): string
    {
        return "CREATE TABLE IF NOT EXISTS `tbl_fields_date` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `field_id` int(11) unsigned NOT NULL,
          `pre_populate` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
          `calendar` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
          `time` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
          PRIMARY KEY (`id`),
          KEY `field_id` (`field_id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
    }
}
