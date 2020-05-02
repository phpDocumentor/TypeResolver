
%start DEFINITION

%right T_NULLABLE
%left T_OPEN_PARENTHESIS T_COMMA T_COMPOUND_OPERATOR T_INTERSECTION_OPERATOR
%left T_OPEN_SQUARE_BRACKET
%right T_LESS_THAN
%left T_CLOSE_SQUARE_BRACKET T_CLOSE_PARENTHESIS T_GREATER_THAN

%token T_TYPE
%token T_FULLY_QUALIFIED_NAME
%token T_QUALIFIED_NAME
%token T_VOID
%token T_COLLECTION_TYPE

%%

DEFINITION:
      VOID
    | TYPE
;

TYPE:
        T_TYPE { $$ = $this->resolveKeyword($1); }
    |   NULLABLE
    |   COMPOUND
    |   INTERSECTION
    |   EXPRESSION
    |   ARRAY
    |   GENERIC
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
      TYPE T_INTERSECTION_OPERATOR TYPE { $$ = new Types\Intersection([$1, $3]); }
;

GENERIC:
      T_COLLECTION_TYPE T_LESS_THAN TYPE T_GREATER_THAN { $$ = $this->resolveCollection($1, $3); }
    | T_COLLECTION_TYPE T_LESS_THAN TYPE T_COMMA TYPE T_GREATER_THAN { $$ = $this->resolveCollection($1, $5, $3); }
    | T_COLLECTION_TYPE { $$ = $this->resolveCollection($1); }
    | FQSEN T_LESS_THAN TYPE T_GREATER_THAN { $$ = new Types\Collection($1, $3); }
    | FQSEN T_LESS_THAN T_LESS_THAN TYPE T_COMMA TYPE T_GREATER_THAN { $$ = new Types\Collection($1, $5, $3); }
    | FQSEN { $$ = new Types\Object_($1); }
;

EXPRESSION:
      T_OPEN_PARENTHESIS TYPE T_CLOSE_PARENTHESIS { $$ = new Types\Expression($2); }
;

FQSEN:
      T_FULLY_QUALIFIED_NAME { $$ = new Fqsen($1); }
    | T_QUALIFIED_NAME { $$ = $this->fqsenResolver->resolve($1, $this->context); }
;

VOID:
    T_VOID { $$ = new Types\Void_(); }
;

%%
