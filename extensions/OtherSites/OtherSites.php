<?php
/**
 * OtherSites.php -- Move interwiki links other than language links out to 
 * their own box in the right column
 * Copyright 2006 Mark Jaroski <mark@geekhive.net>
 * 
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @author Mark Jaroski <mark@geekhive.net>
 * @package MediaWiki
 * @subpackage Extensions
 */

if (defined('MEDIAWIKI')) {
    global $wgExtensionFunctions, $wgOtherSites;

    require_once('languages/Names.php');

    # to be redefined in LocalSettings.php
    $wgOtherSites = array(
            'wikitravel'     => 'Wikitravel',
            'wikipedia'      => 'Wikipedia',
            'lyriki'         => 'Lyriki',
            'lastfm-user'    => 'Last.fm',
            'lastfm-artist'  => 'Last.fm',
            'oglondon'       => 'OpenGuides',
        );

	$wgExtensionFunctions[] = 'setupOtherSites';

    function setupOtherSites() {
        global $wgOtherSites, $wgMessageCache, $wgLanguageNames;
        $wgMessageCache->addMessages( array(
                'othersites' => 'other sites'
            ));
        foreach ( array_keys( $wgOtherSites ) as $key ) {
            $wgLanguageNames[$key] = $wgOtherSites[$key];
        }
    }

    function wfOtherSitesMonoBook( &$skin ) {
        global $wgOtherSites;
        if( $skin->data['language_urls'] ) {
            $wgOtherSites = array_flip( $wgOtherSites );
            $others = array();
            $llinks = $skin->data['language_urls'];
            for ( $i = 0; $i < count( $llinks ); $i++ ) {
                $langlink = $llinks[$i];
                if ( $wgOtherSites[$langlink['text']] ) {
                    $others[] = $langlink;
                    unset( $skin->data['language_urls'][$i] );
                }
            }
            if ( count( $others ) > 0 ) {
                echo '<div id="p-others" class="portlet">';
                echo '<h5>';
                echo $skin->msg('othersites');
                echo '</h5>';
                echo '<div class="pBody">';
                echo '<ul>';
                foreach ( $others as $langlink ) {
                    if ( $langlink['text'] ) {
                        echo '<li class="' . $langlink['class'] . '">';
                        echo '<a href="' . $langlink['href'] . '">';
                        echo $langlink['text'];
                        echo '</a></li>';
                    }
                }
                echo '</ul>';
                echo '</div>'; # pBody
                echo '</div>'; # portlet
            }
        }
    }

}

?>

