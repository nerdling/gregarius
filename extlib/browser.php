<?php
/*****************************************************************
 * File name: browser.php
 * Author: Gary White & Damien Raude-Morvan
 * Last modified: December 10, 2006
 *
 **************************************************************
 * Copyright (C) 2003  Gary White
 * Copyright (C) 2006  Damien Raude-Morvan <drazzib@drazzib.com>
 *
 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.
 *
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details at:
 http://www.gnu.org/copyleft/gpl.html
 */

/**
 **************************************************************
 *
 Browser class
 *
 Identifies the user's Operating system, browser and version
 by parsing the HTTP_USER_AGENT string sent to the server
 *
 Typical Usage:
 *
 require_once($_SERVER['DOCUMENT_ROOT'].'/include/browser.php');
 $br = new Browser;
 echo "$br->Platform, $br->Name version $br->Version";
 *
 For operating systems, it will correctly identify:
 Microsoft Windows
 MacIntosh
 Linux
 *
 Anything not determined to be one of the above is considered to by Unix
 because most Unix based browsers seem to not report the operating system.
 The only known problem here is that, if a HTTP_USER_AGENT string does not
 contain the operating system, it will be identified as Unix. For unknown
 browsers, this may not be correct.
 *
 For browsers, it should correctly identify all versions of:
 Amaya
 Galeon
 iCab
 Internet Explorer
 For AOL versions it will identify as Internet Explorer (AOL) and the version
 will be the AOL version instead of the IE version.
 Konqueror
 Lynx
 Mozilla
 Netscape Navigator/Communicator
 OmniWeb
 Opera
 Pocket Internet Explorer for handhelds
 Safari
 WebTV
 *****************************************************************/
class browser {

    var $Name = "Unknown";
    var $Version = "Unknown";
    var $Platform = "Unknown";
    var $UserAgent = "Not reported";
    var $AOL = false;
    var $isMoz = false;
    var $isOpera = false;
    var $isSafari = false;
    var $isKonqueror = false;
    var $isIE = false;

    function browser() {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $agent = $_SERVER['HTTP_USER_AGENT'];
        } else {
            $agent = null;
        }
        // initialize properties
        $bd['platform'] = "Unknown";
        $bd['browser'] = "Unknown";
        $bd['version'] = "Unknown";
        $this->UserAgent = $agent;

        // find operating system
        if (stripos($agent, 'win') !== false)
            $bd['platform'] = "Windows";
        elseif (stripos($agent, 'mac') !== false) $bd['platform'] = "MacIntosh";
        elseif (stripos($agent, 'linux') !== false) $bd['platform'] = "Linux";
        elseif (stripos($agent, 'OS/2') !== false) $bd['platform'] = "OS/2";
        elseif (stripos($agent, 'BeOS') !== false) $bd['platform'] = "BeOS";

        // test for Opera
        if (stripos($agent, 'opera') !== false) {
            $val = stristr($agent, "opera");
            if (stripos($val, '/') !== false) {
                $val = explode("/", $val);
                $bd['browser'] = $val[0];
                $val = explode(" ", $val[1]);
                $bd['version'] = $val[0];
            } else {
                $val = explode(" ", stristr($val, "opera"));
                $bd['browser'] = $val[0];
                $bd['version'] = $val[1];
            }

            $this->isOpera = true;

            // test for WebTV
        }
        elseif (stripos($agent, 'webtv') !== false) {
            $val = explode("/", stristr($agent, "webtv"));
            $bd['browser'] = $val[0];
            $bd['version'] = $val[1];

            // test for MS Internet Explorer version 1
        }
        elseif (stripos($agent, 'microsoft internet explorer') !== false) {
            $bd['browser'] = "MSIE";
            $bd['version'] = "1.0";
            $var = stristr($agent, "/");
            if (preg_match("/308|425|426|474|0b1/", $var)) {
                $bd['version'] = "1.5";
            }
            $this -> isIE = true;

            // test for NetPositive
        }
        elseif (stripos($agent, 'NetPositive') !== false) {
            $val = explode("/", stristr($agent, "NetPositive"));
            $bd['platform'] = "BeOS";
            $bd['browser'] = $val[0];
            $bd['version'] = $val[1];

            // test for MS Internet Explorer
        }
        elseif ((stripos($agent, 'msie') !== false) && (stripos($agent, 'opera') === false)) {
            $val = explode(" ", stristr($agent, "msie"));
            $bd['browser'] = $val[0];
            $bd['version'] = $val[1];

            $this -> isIE = true;

            // test for MS Pocket Internet Explorer
        }
        elseif ((stripos($agent, 'mspie') !== false) || (stripos($agent, 'pocket') !== false)) {
            $val = explode(" ", stristr($agent, "mspie"));
            $bd['browser'] = "MSPIE";
            $bd['platform'] = "WindowsCE";
            if (stripos($agent, 'mspie') !== false)
                $bd['version'] = $val[1];
            else {
                $val = explode("/", $agent);
                $bd['version'] = $val[1];
            }

            // test for Galeon
        }
        elseif (stripos($agent, "galeon") !== false) {
            $val = explode(" ", stristr($agent, "galeon"));
            $val = explode("/", $val[0]);
            $bd['browser'] = $val[0];
            $bd['version'] = $val[1];

            $this->isMoz = true;

            // test for Konqueror
        }
        elseif (stripos($agent, "Konqueror") !== false) {
            $val = explode(" ", stristr($agent, "Konqueror"));
            $val = explode("/", $val[0]);
            $bd['browser'] = $val[0];
            $bd['version'] = $val[1];

            $this->isKonqueror = true;

            // test for iCab
        }
        elseif (stripos($agent, "icab") !== false) {
            $val = explode(" ", stristr($agent, "icab"));
            $bd['browser'] = $val[0];
            $bd['version'] = $val[1];

            // test for OmniWeb
        }
        elseif (stripos($agent, "omniweb") !== false) {
            $val = explode("/", stristr($agent, "omniweb"));
            $bd['browser'] = $val[0];
            $bd['version'] = $val[1];

            // test for Phoenix
        }
        elseif (stripos($agent, "Phoenix") !== false) {
            $bd['browser'] = "Phoenix";
            $val = explode("/", stristr($agent, "Phoenix/"));
            $bd['version'] = $val[1];

            $this->isMoz = true;

            // test for Firebird
        }
        elseif (stripos($agent, "firebird") !== false) {
            $bd['browser'] = "Firebird";
            $val = stristr($agent, "Firebird");
            $val = explode("/", $val);
            $bd['version'] = $val[1];
            $this->isMoz = true;

            // test for Firefox
        }
        elseif (stripos($agent, "Firefox") !== false) {
            $bd['browser'] = "Firefox";
            $val = stristr($agent, "Firefox");
            $val = explode("/", $val);
            $bd['version'] = $val[1];
            $this->isMoz = true;

            // test for Mozilla Alpha/Beta Versions
        }
        elseif ((stripos($agent, "mozilla") !== false) && (stripos($agent, "rv:[0-9].[0-9][a-b]") !== false) && (stripos($agent, "netscape") === false)) {
            $bd['browser'] = "Mozilla";
            $val = explode(" ", stristr($agent, "rv:"));
            preg_match("/rv:[0-9].[0-9][a-b]/", $agent, $val);
            $bd['version'] = str_replace("rv:", "", $val[0]);
            $this->isMoz = true;

            // test for Mozilla Stable Versions
        }
        elseif ((stripos($agent, "mozilla") !== false) && preg_match("/rv:[0-9]\.[0-9]/", $agent) && (stripos($agent, "netscape") === false)) {
            $bd['browser'] = "Mozilla";
            $val = explode(" ", stristr($agent, "rv:"));
            preg_match("/rv:[0-9]\.[0-9]\.[0-9]/", $agent, $val);
            $bd['version'] = str_replace("rv:", "", $val[0]);
            $this->isMoz = true;

            // test for Lynx & Amaya
        }
        elseif (stripos($agent, "libwww") !== false) {
            if (stripos($agent, "amaya") !== false) {
                $val = explode("/", stristr($agent, "amaya"));
                $bd['browser'] = "Amaya";
                $val = explode(" ", $val[1]);
                $bd['version'] = $val[0];
            } else {
                $val = explode("/", $agent);
                $bd['browser'] = "Lynx";
                $bd['version'] = $val[1];
            }

            // test for Safari
        }
        elseif (stripos($agent, "AppleWebKit") !== false) {
            $bd['browser'] = "Safari";
            $val = explode("/", $agent);
            $bd['version'] = $val[3];
            $this -> isSafari = true;

            // remaining two tests are for Netscape
        }
        elseif (stripos($agent, "netscape") !== false) {
            $val = explode(" ", stristr($agent, "netscape"));
            $val = explode("/", $val[0]);
            $bd['browser'] = $val[0];
            $bd['version'] = $val[1];

            if ($bd['version'] > 6) {
                $this->isMoz = true;
            }
        }
        elseif ((stripos($agent, "mozilla") !== false) && preg_match("/rv:[0-9]\.[0-9]\.[0-9]", $agent)) {
            $val = explode(" ", stristr($agent, "mozilla"));
            $val = explode("/", $val[0]);
            $bd['browser'] = "Netscape";
            $bd['version'] = $val[1];

            if ($bd['version'] > 6) {
                $this->isMoz = true;
            }
        }

        // clean up extraneous garbage that may be in the name
        $bd['browser'] = preg_replace("/[^a-z,A-Z]/", "", $bd['browser']);
        // clean up extraneous garbage that may be in the version
        $bd['version'] = preg_replace("/[^0-9,.,a-z,A-Z]/", "", $bd['version']);

        // check for AOL
        if (stripos($agent, "AOL") !== false) {
            $var = stristr($agent, "AOL");
            $var = explode(" ", $var);
            $bd['aol'] = preg_replace("/[^0-9,.,a-z,A-Z]/", "", $var[1]);
        } else {
            $bd['aol'] = false;
        }

        // finally assign our properties
        $this->Name = $bd['browser'];
        $this->Version = $bd['version'];
        $this->Platform = $bd['platform'];
        $this->AOL = $bd['aol'];
    }

    function isGecko() {
        return $this->isMoz;
    }

    function isOpera() {
        return $this->isOpera;
    }

    /** Support of multipart/x-mixed-replace complete and stable :
      * o in Safari 2.0.2 (http://webkit.org/blog/?p=32)
      *     (http://developer.apple.com/internet/safari/uamatrix.html)
      * o in Konqueror at least 3.4.3
      *     (http://websvn.kde.org/trunk/KDE/kdelibs/khtml/kmultipart/kmultipart.cpp/)
      */
    function supportsServerPush() {
        return  ($this->isMoz
             ||  $this->isOpera
             || ($this->isSafari && $this->compareBrowserVersion("416", $this->Version))
             || ($this->isKonqueror && $this->compareBrowserVersion("3.4.3", $this->Version))
                );
    }

    /** Support of XMLHTTPRequest complete and stable :
      * o in Konqueror at least 3.4 (since 2004 -
      *     http://websvn.kde.org/trunk/KDE/kdelibs/khtml/ecma/xmlhttprequest.cpp/)
      */
    function supportsAJAX() {
        return  ($this->isMoz
             ||  $this->isOpera
             ||  $this->isSafari
             || ($this->isIE && $this->compareBrowserVersion("5", $this->Version))
             || ($this->isKonqueror && $this->compareBrowserVersion("3.4", $this->Version))
                );
    }

    /**
     * Compare two version strings of browser.
     * @param $requiredVersion minimal version number required for feature
     * @param $browserVersion current detected browser version
     * @return true if $browserVersion is greater than $requiredVersion
     */
    function compareBrowserVersion($requiredVersion, $browserVersion) {
        // Standardise versions
        $requiredVersion = preg_replace('/([^0-9\.]+)/', '.$1.', $requiredVersion);
        $requiredVersion = trim($requiredVersion);
        $v1 = explode('.', $requiredVersion);

        $browserVersion = preg_replace('/([^0-9\.]+)/', '.$1.', $browserVersion);
        $browserVersion = trim($browserVersion);
        $v2 = explode('.', $browserVersion);

        $compare = 0;
        for ($i = 0, $x = min(count($v1), count($v2)); $i < $x; $i++) {
            if ($v1[$i] == $v2[$i]) {
                continue;
            }

            $i1 = $v1[$i];
            $i2 = $v2[$i];

            if (is_numeric($i1) && is_numeric($i2)) {
                $compare = ($i1 < $i2) ? -1 : 1;
            }
        }

        return (bool) ($compare <= 0);
    }
}
?>
