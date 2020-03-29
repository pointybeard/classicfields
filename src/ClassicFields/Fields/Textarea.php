<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\ClassicFields\Fields;

use pointybeard\Symphony\Extensions\ClassicFields;

class Textarea extends ClassicFields\AbstractField
{
    public function getCreateFieldSQL(): string
    {
        return 'CREATE TABLE IF NOT EXISTS `tbl_fields_textarea` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `field_id` int(11) unsigned NOT NULL,
          `formatter` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
          `size` int(3) unsigned NOT NULL,
          PRIMARY KEY (`id`),
          KEY `field_id` (`field_id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';
    }
}
