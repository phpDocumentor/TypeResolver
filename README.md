TypeResolver and FqsenResolver
==============================

The specification on types in DocBlocks (PSR-5) describes various keywords and special constructs
but also how to statically resolve the partial name of a Class into a Fully Qualified Class Name (FQCN).

PSR-5 also introduces an additional way to describe deeper elements than Classes, Interfaces and Traits 
called the Fully Qualified Structural Element Name (FQSEN). Using this it is possible to refer to methods,
properties and class constants but also functions and global constants.

This package provides two Resolvers that are capable of 

1. determining the Type of a given expression and/or resolve any class names, and 
2. resolve any partial Structural Element Names into Fully Qualified Structural Element names
