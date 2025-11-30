====================
Upgrade to Version 2
====================

Version 2 of the Type Resolver introduces several breaking changes and new features. This guide will help you
upgrade your codebase to be compatible with the latest version. The usage of the TypeResolver remains the same. However,
some classes have been moved or replaced, and the minimum PHP version requirement has been raised.

PHP Version
-----------

Version 2 requires PHP 7.4 or higher. We have been supporting PHP 7.3 in version 1, but due to changing constraints
in our dependencies, we have had to raise the minimum PHP version. At the moment of writing this, PHP 7.3 is used by 2%
of all installations of this package according to Packagist. We believe this is a reasonable trade-off to ensure we
can continue to deliver new features and improvements.

Moved classes
-------------

- ``phpDocumentor\Reflection\Types\InterfaceString`` => :php:class:`phpDocumentor\Reflection\PseudoTypes\InterfaceString`
- ``phpDocumentor\Reflection\Types\ClassString`` => :php:class:`phpDocumentor\Reflection\PseudoTypes\ClassString`
- ``phpDocumentor\Reflection\Types\ArrayKey`` => :php:class:`phpDocumentor\Reflection\PseudoTypes\ArrayKey`
- ``phpDocumentor\Reflection\Types\True_`` => :php:class:`phpDocumentor\Reflection\PseudoTypes\True_`
- ``phpDocumentor\Reflection\Types\False_`` => :php:class:`phpDocumentor\Reflection\PseudoTypes\False_`

Replaced classes
-----------------

- ``phpDocumentor\Reflection\Types\Collection`` => :php:class:`phpDocumentor\Reflection\PseudoTypes\Generic`

Since the introduction of generics in PHP this library was not capable of parsing them correctly. The old Collection
was blocking the use of generics. The new Generic type is a representation of generics like supported in the eco system.

Changed implementations
-----------------------

:php:class:`phpDocumentor\Reflection\PseudoTypes\InterfaceString`, :php:class:`phpDocumentor\Reflection\PseudoTypes\ClassString` and
:php:class:`phpDocumentor\Reflection\PseudoTypes\TraitString` are no longer returning a :php:class:`phpDocumentor\Reflection\Fqsen` since
support for generics these classes can have type arguments like any other generic.

