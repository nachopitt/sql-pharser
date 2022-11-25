<?php

namespace Nachopitt\SqlPharser;

use Doctrine\Common\Lexer\AbstractLexer;

class SqlLexer extends AbstractLexer
{
    const T_NONE = 0;
    const T_INTEGER = 1;
    const T_STRING = 2;
    const T_FLOAT = 3;
    const T_OPEN_PARENTHESIS  = 4;
    const T_CLOSE_PARENTHESIS  = 5;
    const T_DOT = 6;
    const T_BACKTICK = 7;
    const T_COMMA = 8;
    const T_SEMICOLON = 9;
    const T_EQUALS = 10;

    const T_FULLY_QUALIFIED_NAME = 100;
    const T_IDENTIFIER = 101;

    const T_CREATE = 200;
    const T_TABLE = 201;
    const T_IF = 202;
    const T_EXISTS = 203;
    const T_NOT = 204;
    const T_UNSIGNED = 205;
    const T_NULL = 206;
    const T_AUTO_INCREMENT = 207;
    const T_COLLATE = 208;
    const T_DEFAULT = 209;
    const T_PRIMARY = 210;
    const T_KEY = 211;
    const T_CONSTRAINT = 212;
    const T_FOREIGN = 213;
    const T_REFERENCES = 214;
    const T_ENGINE = 215;
    const T_CHARSET = 216;
    const T_ON = 217;
    const T_DELETE = 218;
    const T_UPDATE = 219;
    const T_NO = 220;
    const T_ACTION = 221;
    const T_SET = 222;
    const T_FOREIGN_KEY_CHECKS = 223;
    const T_SQL_MODE = 224;

    /**
     * Creates a new query scanner object.
     *
     * @param string $input A query string.
     */
    public function __construct($input)
    {
        $this->setInput($input);
    }

    protected function getCatchablePatterns()
    {
        return [
            '[a-z_\\\][a-z0-9_]*(?:\\\[a-z_][a-z0-9_]*)*', // identifier or qualified name
            '(?:[0-9]+(?:[\.][0-9]+)*)(?:e[+-]?[0-9]+)?', // numbers
            "'(?:[^']|'')*'", // quoted strings
        ];
    }

    protected function getNonCatchablePatterns()
    {
        return ['\s+', '(.)'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getType(&$value)
    {
        $type = self::T_NONE;

        switch (true) {
            // Recognize numeric values
            case (is_numeric($value)):
                if (strpos($value, '.') !== false || stripos($value, 'e') !== false) {
                    return self::T_FLOAT;
                }

                return self::T_INTEGER;

            // Recognize quoted strings
            case ($value[0] === "'"):
                $value = str_replace("''", "'", substr($value, 1, strlen($value) - 2));

                return self::T_STRING;

            // Recognize identifiers or qualified names
            case (ctype_alpha($value[0]) || $value[0] === '_' || $value[0] === '\\'):
                $name = 'Nachopitt\SqlPharser\SqlLexer::T_' . strtoupper($value);

                if (defined($name)) {
                    $type = constant($name);

                    if ($type >= 200) {
                        return $type;
                    }
                }

                if (strpos($value, '\\') !== false) {
                    return self::T_FULLY_QUALIFIED_NAME;
                }

                return self::T_IDENTIFIER;

            // Recognize symbols
            case ($value === '('):
                return self::T_OPEN_PARENTHESIS;
            case ($value === ')'):
                return self::T_CLOSE_PARENTHESIS;
            case ($value === '.'):
                return self::T_DOT;
            case ($value === '`'):
                return self::T_BACKTICK;
            case ($value === ','):
                return self::T_COMMA;
            case ($value === ';'):
                return self::T_SEMICOLON;
            case ($value === '='):
                return self::T_EQUALS;

            // Default
            default:
                // Do nothing
        }

        return $type;
    }
}