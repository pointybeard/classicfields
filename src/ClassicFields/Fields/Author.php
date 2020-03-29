<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\ClassicFields\Fields;

use pointybeard\Symphony\Extensions\ClassicFields;

class Author extends ClassicFields\AbstractField
{
    public function getCreateFieldSQL(): string
    {
        return "CREATE TABLE IF NOT EXISTS `tbl_fields_author` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `field_id` int(11) unsigned NOT NULL,
          `allow_multiple_selection` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
          `default_to_current_user` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL,
          `author_types` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `field_id` (`field_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
    }
}
