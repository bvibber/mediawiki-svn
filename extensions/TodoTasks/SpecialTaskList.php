<?php
 
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation, version 2
of the License.
 
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

$wgUseProjects = true;

global $wgHooks;
global $wgSpecialPages;

$wgSpecialPages['TaskList']            = 'TaskList';
if ($wgUseProjects)
    $wgSpecialPages['TaskListByProject']   = 'TaskListByProject';
$wgHooks['PersonalUrls'][]             = 'addPersonalUrl';
$wgHooks['AlternateEdit'][]            = 'todoPreviewAction';
$wgHooks['EditPage::attemptSave'][]    = 'todoSavePreparser';
$wgExtensionFunctions[]                = 'wfTodoParserFunction_Setup';
$wgHooks['LanguageGetMagic'][]         = 'wfTodoParserFunction_Magic';
$wgTodoTasksExtensionVersion = '0.8.1';
$wgExtensionCredits['parserhook'][]    = array(
        'version'     => $wgTodoTasksExtensionVersion,
        'name'        => 'Todo Tasks',
        'author'      => 'Paul Grinberg',
        'email'       => 'gri6507 at yahoo dot com',
        'url'         => 'http://www.mediawiki.org/wiki/Extension:Todo_Tasks',
        'description' => 'adds <nowiki>{{#todo:}}</nowiki> parser function for assigning tasks');
$wgExtensionCredits['specialpage'][] = array(
        'name'        => 'Todo Tasks',
        'version'     => $wgTodoTasksExtensionVersion,
        'author'      => 'Paul Grinberg',
        'email'       => 'gri6507 at yahoo dot com',
        'url'         => 'http://www.mediawiki.org/wiki/Extension:Todo_Tasks',
        'description' => 'Adds a special page for reviewing tasks assignments'
);


function wfTodoParserFunction_Setup() {
        global $wgParser;
        # Set a function hook associating the "example" magic word with our function
        $wgParser->setFunctionHook( 'todo', 'wfTodoParserFunction_Render' );
}

function wfTodoParserFunction_Magic( &$magicWords, $langCode ) {
        # Add the magic word
        # The first array element is case sensitive, in this case it is not case sensitive
        # All remaining elements are synonyms for our parser function
        $magicWords['todo'] = array( 0, 'todo' );
        # unless we return true, other parser functions extensions won't get loaded.
        return true;
}

function getUserIDFromUserText($user) {
    $dbr = wfGetDB( DB_SLAVE );
    $userid = 0;

    if (preg_match('/^\s*(.*?)\s*$/', $user, $matches))
        $user = $matches[1];
    
    $u = User::newFromName($user);
    if ($u) {
        $userid = $u->idForName();               // valid userName
    }
    if (!$userid) {                              // if not a valid userName, try as a userRealName
        $userid = $dbr->selectField( 'user', 'user_id', array( 'user_real_name' => $user ), 'renderTodo' );
        if (!$userid) {                          // if not valid userRealName, try case insensitive userRealName
            $sql = "SELECT user_id FROM ". $dbr->tableName('user') ." WHERE UPPER(user_real_name) LIKE '%" . strtoupper($user) 
. "%'";
            $res = $dbr->query( $sql, __METHOD__ );
            if ($dbr->numRows($res)) {
                $row = $dbr->fetchRow($res);
                $userid = $row[0];
            }
            $dbr->freeResult($res);
            if (!$userid) {                      // if not case insensitive userRealName, try case insensitive lastname
                list ($first, $last) = preg_split('/\s+/', $user);
                if ($last != '') {
                    $sql = "SELECT user_id FROM ". $dbr->tableName('user') ." WHERE UPPER(user_real_name) LIKE '%" . 
strtoupper($last) . "%'";
                    $res = $dbr->query( $sql, __METHOD__ );
                    if ($dbr->numRows($res)) {
                        $row = $dbr->fetchRow($res);
                        $userid = $row[0];
                    }
                    $dbr->freeResult($res);
                }
            }
        }
    }
    return $userid;
}

function getValidProjects() {
    $ProjPageTitle = Title::newFromText ('TodoTasksValidProjects', NS_MEDIAWIKI) ;
    return Revision::newFromTitle($ProjPageTitle)->getText();
}

function validateProject($projlist, $proj) {
    $validprojects = preg_split('/\s*\*\s*/', $projlist, -1, PREG_SPLIT_NO_EMPTY);
    foreach ($validprojects as $vp) {
        if (preg_match("/^$proj$/i", $vp))
            return $vp;
    }

    return 'Unknown Project';
}

$todoPreview;
function wfTodoParserFunction_Render( &$parser, $input, $users, $project='') {
    global $wgOut, $wgSitename, $wgEmergencyContact, $todoPreview;
    global $wgUseProjects;
    
    $username = '';
    $fullname = '';
    $u = 0;
    $userIdList = array();
    $userIdList2 = array();
    $dbr = wfGetDB( DB_SLAVE );

    $task_text = '';
    $task_project = '';
    
    if ($wgUseProjects) {
        if (isset($project) && ($project != '')) {
            $task_project .= "''";
            $first = true;
            $validProjects = getValidProjects();
            $projlist = preg_split('/\s*,\s*/', $project, -1, PREG_SPLIT_NO_EMPTY);
            foreach ( $projlist as $proj ) {
                if (!$first) {
                    $task_project .= ', ';
                } else {
                    $first = false;
                }
                $task_project .= validateProject($validProjects, $proj);
            }
            $task_project .= "'' - ";
        }
    }

    $task_text .= "$input (''' ";
    
    if ($users == '') {                                  // if no user specified
        $task_text .= "unspecified user";
    } else {
        $first = true;
        $userlist = preg_split('/\s*,\s*/', $users, -1, PREG_SPLIT_NO_EMPTY);
        foreach ( $userlist as $user ) {
            if (!$first) {
                $task_text .= ', ';
            } else {
                $first = false;
            }
            $userid = getUserIDFromUserText($user);
            if ($userid != 0) {                         // successfully found the user
                $u = User::newFromId($userid);
                $username = $u->getName();
                $fullname = $u->getRealName();
                $task_text .= "${fullname}";
                array_push($userIdList, $userid);
                array_push($userIdList2, $userid);
            } else {                                    // fall through to worst case scenario
                $task_text .= "incorrect username";
            }
        }
    }
    $task_text .= "''' )";
    $text = $task_project . $task_text;

    /* The following assumes the existance of an extra wiki table called $prefix_todo.
       To create this table issue
          CREATE TABLE wiki_todo (
               id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
               hash TINYBLOB
          );
     */

    $hash = md5($task_text);
    if (is_object($todoPreview) && !$todoPreview->preview) {
        /* Only when the action is a Submit instead of a Preview, send out an email
           reminder and store in database (if needed).
         */
        $adminAddress = new MailAddress( $wgEmergencyContact, 'WikiAdmin' );
        $tasklist = Title::newFromText("Special:TaskList");
        $body = ",\n\nSomeone has assigned a new Task for you on " . $parser->getTitle()->getFullURL() .
                ".\n\nTo see your complete Task List go to " . $tasklist->getFullURL() .
                ".\n\n\tYour friendly ${wgSitename} notification system\n";
        $row = $dbr->selectRow( 'todo', 'id', array( 'hash' => $hash ), __METHOD__ );
        if (!$row) {                                    // this is a new todo item
            while ($userid = array_pop($userIdList)) {
                $u = User::newFromId($userid);
                $fullname = $u->getRealName();
                $email = "Dear ${fullname}$body";
                $u->sendMail("[${wgSitename}] Task List Change", $email, $adminAddress->toString());
                $dbr->insert('todo', array( 'hash' => $hash ), __METHOD__ );
            }
        }
    }

    
    return $text;
}

function addPersonalUrl(&$personal_urls, $wgTitle)
{
    global $wgOut;
    
    $personal_urls['mytasks'] = array(
            'text' => "My tasks",
            'href' => Skin::makeSpecialUrl( 'TaskList')
    );
    return true;
}

function todoPreviewAction(&$q) {
    global $todoPreview;

    $todoPreview = $q;
    return true;
}

function todoSavePreparser(&$q) {
    // update the text of the todo so that it has a propper full name in it.
    // this way, the <dpl> search will work better
    $newpagetext = '';
    $sections = preg_split('/({{.*?}})/', $q->textbox1, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    foreach($sections as $section) {
        if (preg_match("/
                 ^{{                            # we found a template
                    \s*                         # that may begin with spaces
                    (                           # capture the template name in $1
                      (todo)                    # template may be a TODO
                      |                         # or
                      (action item)             # template may be an ACTION ITEM
                    ) 
                    \s*                         # the template name may have trailing spaces 
                    \|                          # the pipe to indicate template parameter #1 (task description)
                    (                           # capture the template parameter in $4; this string is comprized of
                      [^|]*?                    # a possible sequence of non pipe characters
                      (\[\[.*\|?.*\]\])?        # a possible wiki syntax link
                      [^|]*                     # a possible sequence on non pipe characters
                    )
                    \|                          # the pipe for the template parameter #3 (task assignees)
                    ([,.\s\w\d]*)               # any combination of 'chars' repeated any number of times, captured in $6
                    (                           # following by an optional grouping captured in $7
                      \|                        # which is the template parameter #4 (project associated with task)
                      \s*project\s*=\s*         # which begins with project= (with possible spaces)
                      [,.\s\w\d]*?              # any combination of 'chars' repeated any number of times
                    )?
                  }}$                           # which ends the template
                  /ix", $section, $matches)) {
            $newpagetext .= '{{' . "$matches[1]|$matches[4]|";
            $first = true;
            $userlist = preg_split('/\s*,\s*/', $matches[6], -1, PREG_SPLIT_NO_EMPTY);
            foreach ( $userlist as $user ) {
                if (!$first) {
                    $newpagetext .= ', ';
                } else {
                    $first = false;
                }
                $userid = getUserIDFromUserText($user);
                if ($userid != 0) {                         // successfully found the user
                    $u = User::newFromId($userid);
                    $username = $u->getName();
                    $fullname = $u->getRealName();
                    $newpagetext .= "${fullname}";
                } else {                                    // fall through to worst case scenario
                    $newpagetext .= $user;
                }
            }
            if (isset($matches[7]))
                $newpagetext .= "$matches[7]";
            $newpagetext .= '}}';
        } else {
            $newpagetext .= $section;
        }
    }
    $q->textbox1 = $newpagetext;
    return true;
}


require ( dirname( __FILE__ ) . "/../includes/SpecialPage.php" );

class TaskList extends SpecialPage
{
    function TaskList() {
        SpecialPage::SpecialPage("TaskList");
        self::loadMessages();
        return true;
    }

    function loadMessages() {
        static $messagesLoaded = false;
        global $wgMessageCache;
        if ($messagesLoaded) return;
            $messagesLoaded = true;

        $allMessages = array(
            'en' => array(
                'tasklist' => 'Task List',
            )
        );
        
        foreach ( $allMessages as $lang => $langMessages ) {
            $wgMessageCache->addMessages( $langMessages, $lang );
        }
        return true;
    }
    
    function execute($user) {
        global $wgRequest, $wgOut, $wgUser;

        $this->setHeaders();
        $wgOut->setPagetitle(wfMsg('tasklist'));
    
        $u = array();
        if (is_null($user)) {
            $u[] = $wgUser;
        } else {
            foreach (explode(',', $user) as $usr) {
                $u[] = User::newFromName($usr);
            }
        }
    
        foreach ($u as $user) {
            if (!$user) {
                $user = $wgUser;
            }
            $username = $user->getName();
            $fullname = $user->getRealName();
            list ($firstname, $lastname) = preg_split('/ /', $fullname);
            
            $wgOut->addWikiText("== Todo List for $fullname ==");
            $wgOut->addWikiText("<dpl> uses=Template:Todo\n notuses=Template:Status Legend\n include={Todo}.dpl\n 
includematch=/${fullname}/i\n </dpl>");
        }
    }
}

class TaskListByProject extends SpecialPage
{
    function TaskListByProject() {
        SpecialPage::SpecialPage("TaskListByProject");
        self::loadMessages();
        return true;
    }

    function loadMessages() {
        static $messagesLoaded = false;
        global $wgMessageCache;
        if ($messagesLoaded) return;
            $messagesLoaded = true;

        $allMessages = array(
            'en' => array(
                'tasklistbyproject' => 'Task List By Project',
            )
        );
        
        foreach ( $allMessages as $lang => $langMessages ) {
            $wgMessageCache->addMessages( $langMessages, $lang );
        }
        return true;
    }
    
    function execute($proj) {
        global $wgRequest, $wgOut;

        $this->setHeaders();
        $wgOut->setPagetitle(wfMsg('tasklistbyproject'));

        $project = '';
        
        if (isset($proj)) {
            $proj = str_replace('+', ' ', $proj);
            $validProjects = getValidProjects();
            $project = validateProject($validProjects, $proj);
            if ($project == 'Unknown Project') {
                $wgOut->addWikiText("Project '''$proj''' is not a valid project. For a list of valid projects, see 
[[MediaWiki:TodoTasksValidProjects]].");
                self::ValidProjectsForm();
                return;
            }
        }

        self::ValidProjectsForm();

        if ($project == '')
            $project = $wgRequest->getVal('project');
        if ($project) {
            $wgOut->addWikiText("----");
            $wgOut->addWikiText("Assigned Tasks for '''$project'''");
            $dpl =  "<dpl>\n uses=Template:Todo \n notuses=Template:Status Legend \n include={Todo}.dpl \n";
            $dpl .= 'includematch=/project\s*=\s*([^\x2c]*\x2c)*\s*' . $project . '\s*(\x2c[^\x2c]*\s*)*$/i';
            $dpl .= "\n </dpl>";
            $wgOut->addWikiText($dpl);
        }
    }

    function ValidProjectsForm() {
        global $wgOut;
        
        $titleObj = SpecialPage::getTitleFor( "TaskListByProject" );
        $kiaction = $titleObj->getLocalUrl();
        $wgOut->addHtml("<FORM ACTION=\"{$kiaction}\" METHOD=GET><LABEL FOR=project>Select Project: </LABEL>");
        $wgOut->addHtml("<select name=project>");
        
        $validprojects = preg_split('/\s*\*\s*/', getValidProjects(), -1, PREG_SPLIT_NO_EMPTY);
        foreach ($validprojects as $vp) 
            $wgOut->addHtml("<option value=\"$vp\">$vp</option>");
            
        $wgOut->addHtml("</select><INPUT TYPE=submit VALUE='Display'>");
    }
}

 
?>
