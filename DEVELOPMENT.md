Notes
=====

Collections should have fields. 

1. Database structure

structure_collections:
    id
    name
    order

structure_collections_fields
    id
    collection_id
    alias
    index `` Display in grid ``
    order
    hint
    translatable
    placeholder
    type

structure_collections_repeater
    id
    field_id
    order
    value
    hidden

2. Display in HTML

<?php foreach ($structure->getCollection('phones') as $phone): ?>

<?= $phone->name; ?>

<?php endforeach; ?>