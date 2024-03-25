# Structure

A powerful, yet super-simple module that lets you create dynamic data structures and use them anywhere on your website.


# Display in HTML

Just create a collection 

   <?php foreach ($structure->getCollection('phones') as $phone): ?>

   <?= $phone->name; ?>

   <?php endforeach; ?>