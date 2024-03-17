
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

/* Field repeater */
DROP TABLE IF EXISTS `bono_module_structure_repeater_fields`;
CREATE TABLE `bono_module_structure_repeater_fields` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `collection_id` INT NOT NULL COMMENT 'Attached collection ID',
    `field_id` INT NOT NULL COMMENT 'Attached field ID',
    `order` INT NOT NULL COMMENT 'Sorting order',
    `value` TEXT NOT NULL,
    `hidden` BOOLEAN NOT NULL COMMENT 'Whether this field is hidden',

    /* Remove self on removing attached relations */
    FOREIGN KEY (`collection_id`) REFERENCES bono_module_structure_collections(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`field_id`) REFERENCES bono_module_structure_collections_fields(`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = UTF8;
