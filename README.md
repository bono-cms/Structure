Structure
===========

A robust yet streamlined module designed to build dynamic data structures and seamlessly deploy them across your website.

## Getting started

Go ahead and create a new collection. Then add custom fields into it and populate with your data. Then anywhere on your website use it like this:

    <div class="row">
        <?php foreach ($structure->getCollection(1) as $item): ?>
        <div class="col-lg-3">
           <img src="<?= $item['photo']; ?>" >
           <p><?= $item['name']; ?></p>
        </div>
        <?php endforeach; ?>
    </div>

The method `$structure->getCollection(1)` takes an ID of collection and returns an array of collection's data.

The key `$item` holds an array with data, where each key is an alias name of a field.

You're done! That's all you need to know.

## Pagination

Sometimes, when having a large dataset, you might want to render it breaking it into pages. You can do it like this:

    <?php
    
    use Krystal\Widget\Pagination\PaginationWidget;
    
    $perPageCount = 10;
    
    ?>
    
    <div class="row">
     <?php foreach ($structure->getPaginatedCollection(7, $perPageCount) as $item): ?>
     <div class="col-lg-3">
       <p><?= $item['...alias...']; ?></p>
     </div>
     <?php endforeach; ?>
    </div>
    
    <?= $this->widget(new PaginationWidget($structure->getPaginator())); ?>

## Note

All output of `getPaginatedCollection()` and `getCollection()` are cached by default. However, the cache is reset whenever you update records.

For SQL dump exportation, verify that all Foreign Key definitions are exported; omission will result in compromised application functionality