
%start DEFINITION

%left T_NULLABLE
%left T_COMPOUND_OPERATOR
%token T_INTERSECTION_OPERATOR
%left T_OPEN_SQUARE_BRACKET
%token T_CLOSE_SQUARE_BRACKET
%token T_OPEN_PARENTHESIS
%token T_CLOSE_PARENTHESIS

%token T_TYPE
%token T_FULLY_QUALIFIED_NAME
%token T_QUALIFIED_NAME
%token T_VOID

%%

DEFINITION:
      VOID
    | TYPE
;

TYPE:
        TYPE_IDENTIFIER
    |   NULLABLE
    |   COMPOUND
    |   INTERSECTION
    |   ARRAY
    |   EXPRESSION
;

TYPE_IDENTIFIER:
    T_TYPE { $$ = $this->resolveKeyword($1); }
    | FQSEN { $$ = new Types\Object_($1); }
;

FQSEN:
      T_FULLY_QUALIFIED_NAME { $$ = new Fqsen($1); }
    | T_QUALIFIED_NAME { $$ = $this->fqsenResolver->resolve($1, $this->context); }
;

NULLABLE:
    T_NULLABLE TYPE { $$ = new Types\Nullable($2); }
;

ARRAY:
    TYPE T_OPEN_SQUARE_BRACKET T_CLOSE_SQUARE_BRACKET { $$ = new Types\Array_($1); }
;

COMPOUND:
      TYPE T_COMPOUND_OPERATOR TYPE { $$ = new Types\Compound([$1, $3]); }
;

INTERSECTION:
      TYPE_IDENTIFIER T_INTERSECTION_OPERATOR TYPE_IDENTIFIER { $$ = new Types\Intersection([$1, $3]); }
    | INTERSECTION T_INTERSECTION_OPERATOR TYPE_IDENTIFIER { $$ = new Types\Intersection(array_merge($1->getAll(), [$3])); }
;

EXPRESSION:
      T_OPEN_PARENTHESIS TYPE T_CLOSE_PARENTHESIS { $$ = new Types\Expression($2); }
;

VOID:
    T_VOID { $$ = new Types\Void_(); }
;

%%
