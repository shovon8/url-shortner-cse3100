<?php
final class Link {
    const CREATE_SUCCESS        = 2;
    const CREATE_FAILED         = 4;
    const USER_INVALID          = 8;
    const ITERATION_SUCCESS     = 16;


    private function __construct() {
    }


    private static function hash($link, $length = 5) {
        list($usec, $sec) = explode(' ', microtime());
        $salt = $sec + ($usec * 1000000);
        $hash = md5($link . $salt);

        return substr($hash, 0, $length);
    }


    public static function addLink($link, $userObj = null) {
        global $mysqli;

        // check if $userObj is null, if not check whether a valid user is logged in
        // if not, then return error flag right away
        if(!is_null($userObj) && !($userObj instanceof User && $userObj->getId() > 1)) {
            return Link::USER_INVALID;
        }

        if(is_null($userObj)) {
            $sql = 'INSERT INTO links(link_hash, link_src) VALUES(?, ?);';
        } else {
            $sql = 'INSERT INTO links(link_hash, link_src, user_id) VALUES(?, ?, ?);';
        }

        $hash = Link::hash($link);


        if($smtp = $mysqli->prepare($sql)) {
            if(is_null($userObj)) {
                $smtp->bind_param('ss', $hash, $link);
                $smtp->execute();
            } else {
                $id = $userObj->getId();
                $smtp->bind_param('ssi', $hash, $link, $id);
                $smtp->execute();
            }

            if($smtp->affected_rows === 1) {
                $smtp->close();
                return Link::CREATE_SUCCESS;
            }

            $smtp->close();
            return Link::CREATE_FAILED;

        }
    }








    public static function getLinks($userObj = null, &$data, $page = 0, $limit = 10) {
        global $mysqli;

        $exclude = $page * $limit;

        if(!is_null($userObj) && !($userObj instanceof User && $userObj->getId() > 1)) {
            return Link::USER_INVALID;
        }

        if($smtp = $mysqli->prepare('SELECT link_id, link_hash, link_src, date_added, visits FROM links WHERE user_id=? ORDER BY date_added DESC LIMIT ?,?')) {
            $id = $userObj->getId();
            $smtp->bind_param('iii', $id, $exclude, $limit);
            $smtp->execute();

            $data = [];

            $smtp->bind_result($linkId, $linkHash, $linkSrc, $dateAdded, $numVisited);

            while($smtp->fetch()) {
                array_push($data, ['id' => $linkId, 'hash' => $linkHash, 'link' => $linkSrc, 'date' => $dateAdded, 'visits' => $numVisited]);
            }

            $smtp->close();

            return Link::ITERATION_SUCCESS;
        }
    }




    public static function getLinkByHash($hash) {
        global $mysqli;

        if($smtp = $mysqli->prepare('SELECT link_id, link_src FROM links WHERE link_hash=? LIMIT 1')) {
            $smtp->bind_param('s', $hash);
            $smtp->execute();

            $smtp->bind_result($id, $link);

            if($smtp->fetch()) {
                $smtp->close();
                Link::updateLinkView($id);

                return $link;
            }

            $smtp->close();
        }

        return false;
    }


    private static function updateLinkView($id) {
        global $mysqli;

        if($smtp = $mysqli->prepare('UPDATE links SET visits = visits + 1 WHERE link_id=?')) {
            $smtp->bind_param('i', $id);
            $smtp->execute();
            $smtp->close();
            return true;
        }

        return false;
    }
}


