<?php

/*
 +------------------------------------------------------------------------+
 | Copyright (c) 2025 Azirax Team                                         |
 +------------------------------------------------------------------------+
 | This source file is subject to the MIT that is bundled     			  |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | <https://opensource.org/license/mit> MIT License                       |
 +------------------------------------------------------------------------+
 | Authors: Rene Dziuba <php.tux@web.de>                                  |
 |			Fabien Potencier <fabien@symfony.com>						  |
 +------------------------------------------------------------------------+
*/
declare(strict_types = 1);

namespace Azirax\Documentation\Tests\Source\Db;

use Exception;
use PDO;

/**
 * Test class for parse.
 *
 * @package      Azirax\Documentation\Tests\Source\Db
 * @author       Rene Dziuba <php.tux@web.de>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
enum Enum
{
    /**
     * Werte-Typ: null
     */
    case BIND_PARAM_NULL;

    /**
     * Werte-Typ: integer
     */
    case BIND_PARAM_INT;

    /**
     * Werte-Typ: string
     */
    case BIND_PARAM_STR;

    /**
     * Werte-Typ: blob
     */
    case BIND_PARAM_BLOB;

    /**
     * Werte-Typ: bool
     */
    case BIND_PARAM_BOOL;

    /**
     * Werte-Typ: decimal
     */
    case BIND_PARAM_DECIMAL;

    /**
     * Werte-Typ: wird übersprungen
     */
    case BIND_SKIP;

    /**
     * Abfrage-Type:
     *
     * Legt fest, dass die Methode zum Abrufen von Daten jede Zeile in einem assoziativen Array zurückgibt,
     * das mit den Spaltennamen aus der Ergebnismenge indiziert wird.
     */
    case FETCH_ASSOC;

    /**
     * Abfrage-Type:
     *
     * Legt fest, dass die Methode zum Abrufen von Daten jede Zeile als Array zurückgeben soll,
     * das durch den Spaltennamen und die Spaltennummer indiziert ist, wie sie in der entsprechenden
     * Ergebnismenge zurückgegeben werden, beginnend bei Spalte 0.
     */
    case FETCH_BOTH;

    /**
     * Abfrage-Type:
     *
     * Legt fest, dass die Methode zum Abrufen von Daten TRUE zurückgeben soll und die Werte der Spalten
     * in der Ergebnismenge den PHP-Variablen zuweist, an die sie mit der Methode PDOStatement::bindParam()
     * oder der Methode PDOStatement::bindColumn() gebunden wurden.
     */
    case FETCH_BOUND;

    /**
     * Abfrage-Type:
     *
     * Legt fest, dass die Methode zum Abrufen von Daten eine neue Instanz der angeforderten Klasse zurückgeben soll,
     * die die Spalten den benannten Eigenschaften der Klasse zuordnet.
     */
    case FETCH_CLASS;

    /**
     * Abfrage-Type:
     *
     * Ermittelt den Klassennamen aus dem Wert der ersten Spalte.
     */
    case FETCH_CLASSTYPE;

    /**
     * Abfrage-Type:
     *
     * Legt fest, dass die Methode zum Abrufen von Daten nur eine einzige angeforderte Spalte
     * aus der nächsten Zeile in der Ergebnismenge zurückgeben soll.
     */
    case FETCH_COLUMN;

    /**
     * Abfrage-Type:
     *
     * Ermöglicht die ad-hoc Anpassung der Daten (nur gültig innerhalb von PDOStatement::fetchAll()).
     */
    case FETCH_FUNC;

    /**
     * Abfrage-Type:
     *
     * Gruppiert die Rückgabe nach den Werten.
     * Üblicherweise in Verbindung mit PDO::FETCH_COLUMN oder PDO::FETCH_KEY_PAIR.
     */
    case FETCH_GROUP;

    /**
     * Abfrage-Type:
     *
     * Legt fest, dass die Methode zum Abrufen von Daten eine vorhandene Instanz der angeforderten Klasse
     * aktualisieren soll, indem die Spalten den benannten Eigenschaften der Klasse zugeordnet werden.
     */
    case FETCH_INTO;

    /**
     * Abfrage-Type:
     *
     * Liefert das Ergebnis von zwei Spalten als Array. Der Inhalt der ersten Spalte ist der Schlüssel
     * und der Inhalt der zweiten Spalte ist der Wert.
     */
    case FETCH_KEY_PAIR;

    /**
     * Abfrage-Type:
     *
     * Legt fest, dass die Methode zum Abrufen von Daten jede Zeile als eigenständiges Objekt mit
     * variablem Namen zurückgeben soll, welcher gleichlautend mit den Spaltennamen in der Ergebnismenge ist.
     */
    case FETCH_LAZY;

    /**
     * Abfrage-Type:
     *
     * Legt fest, dass die Methode zum Abrufen von Daten jede Zeile als ein Array mit dem Index des
     * Spaltennamens der entsprechenden Ergebnismenge zurückgeben soll.
     */
    case FETCH_NAMED;

    /**
     * Abfrage-Type:
     *
     * Legt fest, dass die Methode zum Abrufen von Daten jede Zeile als Array zurückgeben soll, indiziert durch
     * die Spaltennummer, wie sie in der entsprechenden Ergebnismenge zurückgegeben wird, beginnend bei Spalte 0.
     */
    case FETCH_NUM;

    /**
     * Abfrage-Type:
     *
     * Legt fest, dass die Methode zum Abrufen von Daten jede Zeile als ein Objekt mit Eigenschaftsnamen
     * zurückgeben soll, die den in der Ergebnismenge zurückgegebenen Spaltennamen entsprechen.
     */
    case FETCH_OBJ;

    /**
     * Abfrage-Type:
     *
     * Ruft den Konstruktor auf, bevor die Eigenschaften gesetzt werden.
     */
    case FETCH_PROPS_LATE;

    /**
     * Abfrage-Type:
     *
     * Ruft nur eindeutige Werte abrufen.
     */
    case FETCH_UNIQUE;

    /**
     * Spaltentyp: BIGINT
     */
    case TYPE_BIG_INTEGER;

    /**
     * Spaltentyp: BIT
     */
    case TYPE_BIT;

    /**
     * Spaltentyp: BLOB
     */
    case TYPE_BLOB;

    /**
     * Spaltentyp: BINARY
     */
    case TYPE_BINARY;

    /**
     * Spaltentyp: BOOL (1 oder 0)
     */
    case TYPE_BOOLEAN;

    /**
     * Spaltentyp: CHAR
     */
    case TYPE_CHAR;

    /**
     * Spaltentyp: DATE
     */
    case TYPE_DATE;

    /**
     * Spaltentyp: DATETIME
     */
    case TYPE_DATETIME;

    /**
     * Spaltentyp: DECIMAL
     */
    case TYPE_DECIMAL;

    /**
     * Spaltentyp: DOUBLE
     */
    case TYPE_DOUBLE;

    /**
     * Spaltentyp: ENUM
     */
    case TYPE_ENUM;

    /**
     * Spaltentyp: FLOAT
     */
    case TYPE_FLOAT;

    /**
     * Spaltentyp: INTEGER
     */
    case TYPE_INTEGER;

    /**
     * Spaltentyp: JSON
     */
    case TYPE_JSON;

    /**
     * Spaltentyp: JSONB
     */
    case TYPE_JSONB;

    /**
     * Spaltentyp: LONGBLOB
     */
    case TYPE_LONG_BLOB;

    /**
     * Spaltentyp: LONGTEXT
     */
    case TYPE_LONG_TEXT;

    /**
     * Spaltentyp: MEDIUMBLOB
     */
    case TYPE_MEDIUM_BLOB;

    /**
     * Spaltentyp: MEDIUMINTEGER
     */
    case TYPE_MEDIUM_INTEGER;

    /**
     * Spaltentyp: MEDIUMTEXT
     */
    case TYPE_MEDIUM_TEXT;

    /**
     * Spaltentyp: SMALLINTEGER
     */
    case TYPE_SMALL_INTEGER;

    /**
     * Spaltentyp: TEXT
     */
    case TYPE_TEXT;

    /**
     * Spaltentyp: TIME
     */
    case TYPE_TIME;

    /**
     * Spaltentyp: TIMESTAMP
     */
    case TYPE_TIMESTAMP;

    /**
     * Spaltentyp: TINYBLOB
     */
    case TYPE_TINY_BLOB;

    /**
     * Spaltentyp: TINYINT
     */
    case TYPE_TINY_INTEGER;

    /**
     * Spaltentyp: TINYTEXT
     */
    case TYPE_TINY_TEXT;

    /**
     * Spaltentyp: VARCHAR
     */
    case TYPE_VARCHAR;

    /**
     * Event: Transaktion gestartet
     */
    case EVENT_TRANSACTION_BEGIN;

    /**
     * Event: Transaktion ausführen
     */
    case EVENT_TRANSACTION_COMMIT;

    /**
     * Event: Transaktion zurücksetzen
     */
    case EVENT_TRANSACTION_ROLLBACK;

    /**
     * Event: Sicherungspunkt erstellen
     */
    case EVENT_SAVEPOINT_CREATE;

    /**
     * Event: Sicherungspunkt freigeben
     */
    case EVENT_SAVEPOINT_RELEASE;

    /**
     * Event: Sicherungspunkt zurücksetzen
     */
    case EVENT_SAVEPOINT_ROLLBACK;

    /**
     * Event: nach der Ausführung eines SQL-Statements
     */
    case EVENT_QUERY_AFTER;

    /**
     * Event: vor der Ausführung eines SQL-Statements
     */
    case EVENT_QUERY_BEFORE;

    /**
     * WHERE Ausdruck: `AND`
     */
    case CONDITION_AND;

    /**
     * WHERE Ausdruck: `NOT`
     */
    case CONDITION_NOT;

    /**
     * WHERE Ausdruck: `OR`
     */
    case CONDITION_OR;

    /**
     * ORDER BY Ausdruck: ASC
     */
    case ORDER_ASC;

    /**
     * ORDER BY Ausdruck: DESC
     */
    case ORDER_DESC;

    /**
     * Gibt den Wert des ORDER BY-Ausdrucks zurück.
     *
     * @return string
     * @throws Exception
     */
    public function orderBy(): string
    {
        return match ($this) {
            self::ORDER_ASC  => 'ASC',
            self::ORDER_DESC => 'DESC',
            default          => throw new Exception('Unknown ORDER BY name ' . $this->name)
        };
    }

    /**
     * Gibt den Wert des WHERE-Ausdrucks zurück.
     *
     * @return string
     * @throws Exception
     */
    public function where(): string
    {
        return match ($this) {
            self::CONDITION_AND => 'AND',
            self::CONDITION_NOT => 'NOT',
            self::CONDITION_OR  => 'OR',
            default             => throw new Exception('Unknown WHERE condition ' . $this->name)
        };
    }

    /**
     * Gibt den Wert des Events zurück.
     *
     * @return string
     * @throws Exception
     */
    public function event(): string
    {
        return match ($this) {
            self::EVENT_QUERY_AFTER          => 'db:afterQuery',
            self::EVENT_QUERY_BEFORE         => 'db:beforeQuery',
            self::EVENT_SAVEPOINT_CREATE     => 'db:createSavepoint',
            self::EVENT_SAVEPOINT_RELEASE    => 'db:releaseSavepoint',
            self::EVENT_SAVEPOINT_ROLLBACK   => 'db:rollbackSavepoint',
            self::EVENT_TRANSACTION_BEGIN    => 'db:beginTransaction',
            self::EVENT_TRANSACTION_COMMIT   => 'db:commitTransaction',
            self::EVENT_TRANSACTION_ROLLBACK => 'db:rollbackTransaction',
            default                          => throw new Exception('Unknown event ' . $this->name)
        };
    }

    /**
     * Gibt den Wert des Bind-Typs zurück.
     *
     * @return int
     * @throws Exception
     */
    public function bind(): int
    {
        return match ($this) {
            self::BIND_PARAM_NULL    => PDO::PARAM_NULL,
            self::BIND_PARAM_INT     => PDO::PARAM_INT,
            self::BIND_PARAM_STR     => PDO::PARAM_STR,
            self::BIND_PARAM_BLOB    => PDO::PARAM_LOB,
            self::BIND_PARAM_BOOL    => PDO::PARAM_BOOL,
            self::BIND_PARAM_DECIMAL => 32,
            self::BIND_SKIP          => 1024,
            default                  => throw new Exception('Unknown bind type ' . $this->name)
        };
    }

    /**
     * Gibt den Wert des Abfrage-Typs zurück.
     *
     * @return int
     * @throws Exception
     */
    public function fetch(): int
    {
        return match ($this) {
            self::FETCH_ASSOC      => PDO::FETCH_ASSOC,
            self::FETCH_BOTH       => PDO::FETCH_BOTH,
            self::FETCH_BOUND      => PDO::FETCH_BOUND,
            self::FETCH_CLASS      => PDO::FETCH_CLASS,
            self::FETCH_CLASSTYPE  => PDO::FETCH_CLASSTYPE,
            self::FETCH_COLUMN     => PDO::FETCH_COLUMN,
            self::FETCH_FUNC       => PDO::FETCH_FUNC,
            self::FETCH_GROUP      => PDO::FETCH_GROUP,
            self::FETCH_INTO       => PDO::FETCH_INTO,
            self::FETCH_KEY_PAIR   => PDO::FETCH_KEY_PAIR,
            self::FETCH_LAZY       => PDO::FETCH_LAZY,
            self::FETCH_NAMED      => PDO::FETCH_NAMED,
            self::FETCH_NUM        => PDO::FETCH_NUM,
            self::FETCH_OBJ        => PDO::FETCH_OBJ,
            self::FETCH_PROPS_LATE => PDO::FETCH_PROPS_LATE,
            self::FETCH_UNIQUE     => PDO::FETCH_UNIQUE,
            default                => throw new Exception('Unknown fetch type ' . $this->name)
        };
    }

    /**
     * Gibt den Wert des Spalten-Typs zurück.
     *
     * @return int
     * @throws Exception
     */
    public function column(): int
    {
        return match ($this) {
            self::TYPE_BIG_INTEGER    => 0,
            self::TYPE_BINARY         => 1,
            self::TYPE_BIT            => 2,
            self::TYPE_BLOB           => 3,
            self::TYPE_BOOLEAN        => 4,
            self::TYPE_CHAR           => 5,
            self::TYPE_DATE           => 6,
            self::TYPE_DATETIME       => 7,
            self::TYPE_DECIMAL        => 8,
            self::TYPE_DOUBLE         => 9,
            self::TYPE_ENUM           => 10,
            self::TYPE_FLOAT          => 11,
            self::TYPE_INTEGER        => 12,
            self::TYPE_JSON           => 13,
            self::TYPE_JSONB          => 14,
            self::TYPE_LONG_BLOB      => 15,
            self::TYPE_LONG_TEXT      => 16,
            self::TYPE_MEDIUM_BLOB    => 17,
            self::TYPE_MEDIUM_INTEGER => 18,
            self::TYPE_MEDIUM_TEXT    => 19,
            self::TYPE_SMALL_INTEGER  => 20,
            self::TYPE_TEXT           => 21,
            self::TYPE_TIME           => 22,
            self::TYPE_TIMESTAMP      => 23,
            self::TYPE_TINY_BLOB      => 24,
            self::TYPE_TINY_INTEGER   => 25,
            self::TYPE_TINY_TEXT      => 26,
            self::TYPE_VARCHAR        => 27,
            default                   => throw new Exception('Unknown column type ' . $this->name)
        };
    }
}
