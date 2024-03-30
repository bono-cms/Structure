# Structure

A powerful, yet super-simple module that lets you create dynamic data structures and use them anywhere on your website.


# Getting started

Go ahead and create a new collection. Then add custom fields inside that collection. Then anywhere on your website call it like this:

    <div class="row">
        <?php foreach ($structure->getCollection(1) as $user): ?>
        <div class="col-lg-3">
           <img src="<?= $user['photo']; ?>" >
           <p><?= $user['name']; ?></p>
        </div>
        <?php endforeach; ?>
    </div>

The method `$structure->getCollection(1)` takes an ID of collection as an argument and returns an array of collection's data.

The key `$user` holds an array with data, where each key is an alias name of a field.

You're done! That's all you need to know.