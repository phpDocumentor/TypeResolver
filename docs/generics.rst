========
Generics
========

This project is capable of parsing generics notation as used by PHPStan. But it has some limitations, in regards to
PHPStan. The main difference is that PHPStan does scan your whole codebase to find out what types are used in generics,
while this library only parses the types as they are given to it.

This means that if you use a generic type like.

.. code:: php

    namespace MyApp;

    /**
     * @template T of Item
     */
    class Collection {

        /**
         * @return T[]
         */
        public function getItems() : array {
            // ...
        }
    }

The type resolver will not be able to determine what ``T`` is. In fact there is no difference between ``T`` and any other relative
used classname like ``Item``. The resolver will handle ``T`` as a normal class name. In this example it will resolve ``T`` to ``\MyApp\T``.
