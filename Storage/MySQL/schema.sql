
/* Collections */
DROP TABLE IF EXISTS `bono_module_structure_collections`;
CREATE TABLE `bono_module_structure_collections` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `name` varchar(254) NOT NULL COMMENT 'Collection name',
    `order` INT NOT NULL COMMENT 'Sorting order'
) ENGINE = InnoDB DEFAULT CHARSET = UTF8;

/* Collection fields */
DROP TABLE IF EXISTS `bono_module_structure_collections_fields`;
CREATE TABLE `bono_module_structure_collections_fields` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `collection_id` INT NOT NULL COMMENT 'Attached collection ID',
    `name` varchar(255) NOT NULL COMMENT 'Field name',
    `type` INT NOT NULL COMMENT 'Constant of field type',
    `alias` varchar(255) COMMENT 'Alias name for class property',
    `index` BOOLEAN COMMENT 'Whether to display in a grid',
    `order` INT NOT NULL COMMENT 'Sorting order',
    `translatable` BOOLEAN NOT NULL COMMENT 'Whether this field is translatable',
    `hint` TEXT NOT NULL COMMENT 'Hint for UI',

    /* Remove attached fields on removing a collection */
    FOREIGN KEY (`collection_id`) REFERENCES bono_module_structure_collections(`id`) ON DELETE CASCADE

) ENGINE = InnoDB DEFAULT CHARSET = UTF8;

/* Field repeater (Records) */
DROP TABLE IF EXISTS `bono_module_structure_repeater_fields`;
CREATE TABLE `bono_module_structure_repeater_fields` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `collection_id` INT NOT NULL COMMENT 'Attached collection ID',
    `order` INT NOT NULL COMMENT 'Sorting order',
    `published` BOOLEAN NOT NULL COMMENT 'Whether this field is published',

    /* Remove self on removing attached relations */
    FOREIGN KEY (`collection_id`) REFERENCES bono_module_structure_collections(`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = UTF8;

/* Field repeater (Record values) */
DROP TABLE IF EXISTS `bono_module_structure_repeater_fields_values`;
CREATE TABLE `bono_module_structure_repeater_fields_values` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `repeater_id` INT NOT NULL,
    `field_id` INT NOT NULL COMMENT 'Attached field ID',
    `value` TEXT NOT NULL,

    /* Remove self on removing attached relations */
    FOREIGN KEY (`field_id`) REFERENCES bono_module_structure_collections_fields(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`repeater_id`) REFERENCES bono_module_structure_repeater_fields(`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = UTF8;

/* Field repeater (Record values translations) */
DROP TABLE IF EXISTS `bono_module_structure_fields_values_translations`;
CREATE TABLE `bono_module_structure_fields_values_translations` (
    `id` INT NOT NULL COMMENT 'Value ID',
    `lang_id` INT NOT NULL COMMENT 'Attached language ID',
    `value` TEXT NOT NULL COMMENT 'Translated value',

    FOREIGN KEY (id) REFERENCES bono_module_structure_repeater_fields_values(id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = UTF8;
