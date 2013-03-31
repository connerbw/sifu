<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

require_once(dirname(__FILE__) . '/SifuDb/interface.SifuDb.php');

abstract class SifuDb implements ISifuDb {

    /**
     * @var PDO
     */
    public $pdo;

    /**
     * PDO::ATTR_DRIVER_NAME
     * @var string
     */
    public $driver;

    /**
     * Transaction id
     * @var string
     */
    private $transaction;


    /**
    * @param PDO $pdo
    */
    function __construct($pdo) {

        $this->pdo = $pdo;
        $this->driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    }


    // -------------------------------------------------------------------------
    // PDO Transactions
    // -------------------------------------------------------------------------


    /**
    * Request transaction
    *
    * @return string unique id
    */
    function requestTransaction() {

        $tid = uniqid();
        if (empty($this->transaction)) {
            $this->transaction = $tid;
            $this->pdo->beginTransaction();
        }
        return $tid;
    }


    /**
    * Commit transaction
    *
    * @param string $tid unique id
    */
    function commitTransaction($tid) {

        if (empty($this->transaction)) throw new Exception('Transaction was never initiated');

        if ($tid == $this->transaction) {
            $this->pdo->commit();
            unset($this->transaction);
        }
    }


    /**
    * Abort and rollback transaction
    */
    function abortTransaction() {

        if (isset($this->pdo) && !empty($this->transaction)) {

            $this->pdo->rollback();
            unset($this->transaction);
        }
    }

    // -------------------------------------------------------------------------
    // Link tables
    // -------------------------------------------------------------------------


    /**
    * Link tables are special tables that join two other tables using a 1 to 1
    * relationship with primary keys. The table name is "link__" followed by two
    * other table names. The order of the two other tables should be in
    * alphabetical order. An example of a link table for "foo" and "bar" :
    *
    * CREATE TABLE IF NOT EXISTS `link__bar__foo` (
    *  `foo_id` int(11) NOT NULL,
    *  `bar_id` int(11) NOT NULL,
    *  PRIMARY KEY (`foo_id`,`bar_id`),
    *  KEY `bar_id` (`bar_id`)
    * ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    *
    *
    * @param string $table name of a 1st table
    * @param string $table name of a 2nd table
    * @return string
    **/
    function buildLinkTableName($table1, $table2) {

        $tmp = array($table1, $table2);
        natsort($tmp);
        return 'link__' . implode('__', $tmp);
    }


    /**
    * Get list of link tables
    *
    * @param string $match [optional]
    * @return array
    */
    function getLinkTables($match = null) {

        $return = array();
        $tables = $this->showTables();
        foreach ($tables as $val) {
            if (preg_match('/^link__/', $val) && (!$match || mb_strpos($val, "__{$match}")))
                $return[] = $val;
        }
        return $return;
    }


    /**
    * Get links
    *
    * @param string $link name of the link table
    * @param string $table name of the table
    * @param int $id a key
    * @return array
    */
    function getLinks($link, $table, $id) {

        $st = $this->pdo->prepare("SELECT * FROM {$link} WHERE {$table}_id = ? ");
        $this->autoBind($st, array(1 => $id));
        $st->execute();
        $return = array();
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
            unset($row["{$table}_id"]);
            $return[] = array_pop($row);
        }
        return $return;
    }


    /**
    * Save links
    *
    * One to many mapping
    * $id1 = One, $id2 = Many
    *
    * @param string $link name of the link table
    * @param string $table1 name of the first table
    * @param int $id1 a primary key
    * @param string $table2 name of the second table
    * @param int|array $id2 either a primary key, or an array of primary keys
    * @param bool [optional] if true, use the key of $id2 as the data
    */
    function saveLink($link, $table1, $id1, $table2, $id2, $onkey = false) {

        if (!is_array($id2)) {
            $tmp = $id2;
            unset($id2);
            $id2[] = $tmp;
        }

        $tid = $this->requestTransaction();

        foreach ($id2 as $key => $val) {

            $foo = array();
            $foo["{$table1}_id"] = $id1;
            if ($onkey) $foo["{$table2}_id"] = $key;
            else $foo["{$table2}_id"] = $val;

            if ($foo["{$table2}_id"]) {

                // Make sure this doesn't already exist
                $query = $this->prepareCountQuery($link, $foo);
                $st = $this->pdo->prepare($query);
                $this->autoBind($st, $foo);
                $st->execute();

                if (!$st->fetchColumn()) {
                    // It's new, insert it
                    $query = $this->prepareInsertQuery($link, $foo);
                    $st = $this->pdo->prepare($query);
                    $this->autoBind($st, $foo);
                    $st->execute();
                }
            }
        }

        $this->commitTransaction($tid);
    }


    /**
    * Delete link
    *
    * @param string $link name of the link table
    * @param string $table name of the table
    * @param int|array $id either a primary key, or an array of primary keys
    * @param bool [optional] if true, use the key of $id as the data
    */
    function deleteLink($link, $table, $id, $onkey = false) {

        if (!is_array($id)) {
            $tmp = $id;
            unset($id);
            $id[] = $tmp;
        }

        $tid = $this->requestTransaction();

        foreach ($id as $key => $val) {
            $st = $this->pdo->prepare("DELETE FROM {$link} WHERE {$table}_id = ? ");
            if ($onkey) {
                $this->autoBind($st, array(1 => $key));
                $st->execute();
            }
            else {
                $this->autoBind($st, array(1 => $val));
                $st->execute();
            }
        }

        $this->commitTransaction($tid);
    }


    // -------------------------------------------------------------------------
    // Fuzzy duplicate finder
    // -------------------------------------------------------------------------

    /**
    * Fuzzy find duplicates using column names as constraint,
    * returns an array of ids that are probably duplicates.
    *
    * @param string $table db table
    * @param array $columns column names to compare in table
    * @param bool $use_stopwords
    * @param string $stopwords_lang ISO-639 two letter language code
    * @return array [ hash => [id, id, ...] ]
    */
    function fuzzyFindDuplicateRows($table, $columns, $use_stopwords = false, $stopwords_lang = 'en') {

    	$st = $this->pdo->query("SELECT * FROM $table ");
    	$results =  $st->fetchAll(PDO::FETCH_ASSOC);

    	$stopwords = array();
    	if ($use_stopwords) {
    		// TODO
    		$path_to_stopwords = '/TODO/stopwords/' . strtolower($stopwords_lang) . '.txt';
    		$stopwords = file($path_to_stopwords, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    		$stopwords = array_flip($stopwords);
    	}

    	$dupes = array();
    	foreach ($results as $row)
    	{
    		$hash = null;
    		foreach ($columns as $col)
    		{
    			if (isset($row[$col])) {
    				$value = $row[$col];
    			}
    			else {
    				continue; // Skip
    			}

    			if (is_bool($value)) {
    				// Stringify bool
    				$value = $value ? 'TRUE' : 'FALSE';
    			}
    			elseif (is_null($value) || $value == '') {
    				// Stringify null
    				$value = 'NULL';
    			}
    			elseif ($use_stopwords) {
    				//  Eliminate stopwords and punctuation, extra fuzz
    				$value = mb_strtolower($value);
    				$rawtokens = mb_split("\W", $value);
    				if (!count($rawtokens)) continue; // Skip

    				$tmp = '';
    				foreach ($rawtokens as $val) {
    					if (!(empty($val) || mb_strlen($val) < 3 || ctype_digit($val) || isset($stopwords[$val]))) {
    						$tmp .= $val;
    					}
    				}
    				$value = $tmp;
    			}
    			else {
    				// Remove whitespaces, simple fuzz
    				$value = preg_replace('/\s+/', '', $value); // Remove whitespaces, fuzzy compare
    			}

    			$hash .= $value;
    		}
    		$hash = md5($hash);
    		$dupes[$hash][] = $row['id'];
    	}

    	// Unset items that aren't duplicated
    	foreach ($dupes as $key => $val) {
    		if (count($val) < 2) {
    			unset($dupes[$key]);
    		}
    	}

    	return $dupes;
    }

    // -------------------------------------------------------------------------
    // Query building functions
    // -------------------------------------------------------------------------

    /**
    * Automatic bindParam() of an array to a PDOStatement
    *
    * @param PDOStatement $st
    * @param array $bind keys are parameter identifiers, values are sanitized data
    */
    function autoBind($st, $bind) {

        // Don't forget the ampersand! bindParam() needs &$val
        foreach ($bind as $key => &$val) {

            // Guess the constant
            if (is_int($val)) $const = PDO::PARAM_INT;
            elseif (is_bool($val)) $const = PDO::PARAM_BOOL;
            elseif (is_null($val) || $val == '') $const = PDO::PARAM_NULL;
            else $const = PDO::PARAM_STR;

            // Guess if question marks or named placeholders
            if ((int) $key == $key && (int) $key > 0) $st->bindParam((int) $key, $val, $const);
            else $st->bindParam(":$key", $val, $const);
        }
    }


    /**
    * Autogenerate SQL COUNT query with PDO named placeholders
    *
    * @param string $table the name of a table to insert into
    * @param array $form a list where the keys (optionally values) are database column names and placeholders
    * @param bool $useValues [optional] use the keys or the values as placeholders? Default is keys
    * @return string PDO formated prepared statement
    */
    function prepareCountQuery($table, array $form, $useValues = false) {

        $query = "SELECT COUNT(*) FROM {$table} WHERE ";

        foreach ($form as $key => $value ) {
            $query .= ($useValues ? "$value = :$value " : "$key = :$key ");
            $query .= 'AND ';
        }

        $query = rtrim($query, 'AND '); // Remove trailing AND

        return "$query "; // Add space, just incase
    }


    /**
    * Autogenerate ANSI SQL INSERT query with PDO named placeholders
    *
    * @param string $table the name of a table to insert into
    * @param array $form a list where the keys (optionally values) are database column names and placeholders
    * @param bool $useValues [optional] use the keys or the values as placeholders? Default is keys
    * @return string PDO formated prepared statement
    */
    function prepareInsertQuery($table, array $form, $useValues = false) {

        $query    = 'INSERT INTO ';
        $column = "$table (";
        $placeholders = 'VALUES (';

        foreach ($form as $key => $value ) {
            $column .= ($useValues ? "$value, " : "$key, ");
            $placeholders .= ($useValues ? ":$value, " : ":$key, ");
        }

        $column = rtrim($column, ', '); // Remove trailing Coma
        $placeholders = rtrim($placeholders, ', ');
        $query = $query . $column . ') ' . $placeholders . ') ';

        return "$query "; // Add space, just incase
    }


    /**
    * Autogenerate SQL UPDATE query with PDO named placeholders using a table
    * name, an associative array, an and id column name
    *
    * @param string $table the name of a table to insert into
    * @param array $form a list where the keys (optionally values) are database column names and placeholders
    * @param string $id_column [optional] the name of the column to use as id
    * @param bool $useValues [optional] use the keys or the values as placeholders? Default is keys
    * @return string PDO formated prepared statement
    */
    function prepareUpdateQuery($table, array $form, $id_column = 'id', $useValues = false) {

        $query    = 'UPDATE ';
        $column = "$table SET ";
        $placeholders   = '';
        $where = '';

        foreach($form as $key => $value ) {
            $placeholders .= ($useValues ? "$value = :$value, " : "$key = :$key, ");
        }

        $where = " WHERE $id_column = :$id_column";
        $placeholders = rtrim($placeholders, ', '); // Remove trailing Coma
        $query = $query . $column . $placeholders . $where;

        return "$query "; // Add space, just incase
    }

}

?>