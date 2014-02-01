<?php

/**
* @author     Dac Chartrand <dac.chartrand@gmail.com>
* @license    http://www.gnu.org/licenses/lgpl-2.1.txt
*/

namespace Sifu;

/**
* Pseudo example:
*
*   <code>
*   $pager = new SifuPager();
*
*   // Simple continue link
*   $start = 500
*   $q = "SELECT * FROM table WHERE condition = 1 ORDER BY foo LIMIT {$pager->getLimit()} OFFSET {$start} ";
*   // Do something with your query
*   $start += $pager->getLimit();
*   $pager->setStart($start);
*   echo $pager->continueHtml('http://some.url/');
*
*   // Complex pagination
*   $q = 'SELECT COUNT(*) FROM table ';
*   $db = SifuDb::get();
*   $st = $db->pdo->query($q);
*   $count = $st->fetchColumn();
*   $pager->setPages($count);
*   $q = "SELECT * FROM table WHERE condition = 1 ORDER BY foo LIMIT {$pager->getLimit()} OFFSET {$pager->getStart()} ";
*   // Do something with your query
*   echo $pager->pagesHtml('http://some.url/');
*   </code>
*/
class Pager {

    protected $range = 10;
    protected $limit = 50;
    protected $start = 0;
    protected $pages = 0;

    /**
    * Constructor
    */
    function __construct() {

        $this->setStart();
    }


    // -----------------------------------------------------------------------
    // Getters and Setters
    // -----------------------------------------------------------------------

    /**
    * @param int $range
    */
    function setRange($range) {

        if (filter_var($range, FILTER_VALIDATE_INT) === false) return;

        $this->range = $range;
    }


    /**
    * @param int $limit
    */
    function setLimit($limit) {

        if (filter_var($limit, FILTER_VALIDATE_INT) === false) return;

        $this->limit = $limit;
    }


    /**
    * @global string $_GET['page']
    * @param int $start [optional]
    */
    function setStart($start = 0) {

        if (filter_var($start, FILTER_VALIDATE_INT) === false) return;

        if ($start > 0) {
            $this->start = $start;
        }
        elseif (isset($_GET['page'])) {
            if (filter_var($_GET['page'], FILTER_VALIDATE_INT) && $_GET['page'] > 0) {
                $this->start = ($_GET['page'] - 1) * $this->limit;
            }
        }
        else {
            $this->start = 0;
            $_GET['page'] = 1;
        }
    }


    /**
    * @param int $count
    */
    function setPages($count) {

        if (filter_var($count, FILTER_VALIDATE_INT) === false) return;

        $pages = (($count % $this->limit) == 0) ? $count / $this->limit : floor($count / $this->limit) + 1;
        $this->pages = $pages;
    }


    /**
    * @return int
    */
    function getRange() {

        return $this->range;
    }


    /**
    * @return int
    */
    function getLimit() {

        return $this->limit;
    }


    /**
    * @return int
    */
    function getStart() {

        return $this->start;
    }


    /**
    * @return int
    */
    function getPages() {

        return $this->pages;
    }


    // -----------------------------------------------------------------------
    // Html
    // -----------------------------------------------------------------------

    /**
    * @global string $_GET['page']
    * @param string $url
    * @return string returns a list of pages in html
    */
    function pagesHtml($url) {

        if ($this->pages <= 1) return null; // No pages

        // Sanitize
        if (trim($url) == '') return null;
        if (!isset($_GET['page']) || filter_var($_GET['page'], FILTER_VALIDATE_INT) === false || $_GET['page'] < 1) {
            $_GET['page'] = 1;
        }
        if ($_GET['page'] > $this->pages) $_GET['page'] = $this->pages;

        // W3C valid url
        $q = mb_strpos($url, '?') ? '&' : '?';
        $url = $url . $q;
        $url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8', false);

        $html = '';
        // Print the first and previous page links if necessary
        if ($_GET['page'] != 1 && $_GET['page']) {
            $html .= "<a href='{$url}page=1' class='firstPage'>[1]</a> ";
        }
        if (($_GET['page'] - 1) > 0) {
            $html .= "<a href='{$url}page=" . ($_GET['page'] - 1) . "' class='prevPage'>&laquo;</a> ";
        }

        // Print the numeric page list; make the current page unlinked and bold

        $rc = $this->range - $_GET['page']; // right count
        $lc = $this->pages - $_GET['page']; // left count

        if ($rc >= ($this->range / 2)) {
            $lc = $this->range - $rc;
        }
        elseif ($lc <= ($this->range / 2)) {
            $lc = min($this->range - $lc, $this->range);
            $rc = $this->range - $lc;
        }
        else {
            $rc = round($this->range / 2);
            $lc = $rc;
        }

        // Html mess
        $tmp = "<span class='currentPage'>{$_GET['page']}</span> ";
        $tmp2 = '';
        while($lc) {
            $p = $_GET['page'] - $lc;
            if ($p >= 1) $tmp2 .= "<a href='{$url}page={$p}' class='page'>{$p}</a> ";
            --$lc;
        }
        $tmp = $tmp2 . $tmp;
        $tmp2 = '';
        while($rc) {
            $p = $_GET['page'] + $rc;
            if ($p <= $this->pages) $tmp2 = "<a href='{$url}page={$p}' class='page'>{$p}</a> " . $tmp2;
            --$rc;
        }
        $tmp .= $tmp2;
        $html .= $tmp;

        // Print the Next and Last page links if necessary
        if (($_GET['page'] + 1) <= $this->pages) {
            $html .= "<a href='{$url}page=" . ($_GET['page'] + 1) . "' class='nextPage'>&raquo;</a> ";
        }
        if ($_GET['page'] != $this->pages && $this->pages != 0) {
            $html .= "<a href='{$url}page={$this->pages}' class='lastPage'>[{$this->pages}]</a> ";
        }

        return "<div class='pager'>{$html}</div> ";
    }


    /**
    * @param string $url
    * @return string returns a contine link
    */
    function continueHtml($url) {

        if (trim($url) == '') return null;

        $start = $this->start;
        $gtext = SifuFunct::getGtext();

        // W3C valid url
        $q = mb_strpos($url, '?') ? '&' : '?';
        $url = $url . $q;
        $url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8', false);

        $html = "<a href='{$url}start={$start}' class='nextPage'>{$gtext['continue']} &raquo;</a> ";
        return "<div class='pager'>{$html}</div> ";
    }

}

?>