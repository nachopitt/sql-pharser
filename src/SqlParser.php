<?php

namespace Nachopitt\SqlPharser;

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
            echo str_pad($this->lexer->lookahead['type'], 3, " ", STR_PAD_LEFT) . ' - ' . $this->lexer->lookahead['value'] . "\r\n";
        }
    }
}
