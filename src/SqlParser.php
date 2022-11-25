<?php

namespace nachopitt\SqlPharser;

class SqlParser
{
    private $lexer;

    public function __construct($dql)
    {
        $this->lexer = new SqlLexer($dql);
    }

    public function parse()
    {
        // Parse & build AST
        while($this->lexer->moveNext()) {
            echo $this->lexer->lookahead . ' ';
        }
    }
}