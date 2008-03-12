<?php
/**
 * Internationalisation file for YouTubeAuthSub extension.
 *
 * @addtogroup Extensions
*/

$wgYouTubeAuthSubMessages = array();

$wgYouTubeAuthSubMessages['en'] = array(
            'youtubeauthsub' => 'Upload YouTube Video',
            'youtubeauthsub_info' => "<img src='http://s.ytimg.com/yt/img/pixel-vfl73.gif'>
                To upload a video to YouTube to include on a wikiHow article, fill out the following information:",
            'youtubeauthsub_title' => 'Title',
            'youtubeauthsub_description' => 'Description',
            'youtubeauthsub_password' => "YouTube Passsword",
            'youtubeauthsub_username' => "YouTube Username",
            'youtubeauthsub_keywords' => 'Keywords',
            'youtubeauthsub_category'       => 'Category',
            'youtubeauthsub_submit' => 'Submit',
            'youtubeauthsub_clickhere' => 'Click here to log in to YouTube',
            'youtubeauthsub_tokenerror' => 'Error generating authorization token, try refreshing.',
            'youtubeauthsub_success' => "Congratulations! Your video is uploaded. To view your video click <a href='http://www.youtube.com/watch?v=$1'>here</a>. YouTube may require some time to process your video, so it might not be ready just yet. <br/><br/>
To include your video in an article on the wiki, insert the following code into an article:
<code>
{{&#35;ev:youtube|$1}}
</code>
<br/><br/>",
            'youtubeauthsub_authsubinstructions' => "To upload a video, you will be required to first log in to YouTube.<br/><br/>",
			'youtubeauthsub_uploadhere' => "Upload your video from here:",
            'youtubeauthsub_uploadbutton' => 'Upload',
			'youtubeauthsub_code' => '{{#ev:youtube|$1}}.\n\nThis video can be viewed [http://www.youtube.com/watch?v=$1 here]',
			'youtubeauthsub_summary' => 'Uploading YouTube video',
			'youtubeauthsub_uploading' => 'Your video is being uploaded. Please be patient.',
			'youtubeauthsub_viewpage' => 'Alternatively, you can view your video [[$1|here]].',
			'youtubeauthsub_jserror_nokeywords'	=> 'Please enter 1 or more keywords.',
			'youtubeauthsub_jserror_notitle'	=> 'Please enter a title for the video.',
			'youtubeauthsub_jserror_nodesc'	=> 'Please enter a description for the video.',

);
