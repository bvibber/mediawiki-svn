-- phpMyAdmin SQL Dump
-- version 2.11.9.4
-- http://www.phpmyadmin.net
--
-- Host: mysql.vrijemedia.org
-- Generation Time: Nov 08, 2009 at 01:35 AM
-- Server version: 5.0.67
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `uploadwizard`
--

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE IF NOT EXISTS `images` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `source` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `license` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `filename` text NOT NULL,
  `timestamp` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=760 ;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL auto_increment,
  `language` varchar(3) NOT NULL,
  `constant` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `explanation` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=95 ;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `language`, `constant`, `message`, `explanation`) VALUES
(1, 'nl', 'WELCOME_MESSAGE', 'Welkom bij de uploadwizard. Deze wizard zal u helpen bij het maken van een keuze voor een licentie.', ''),
(2, 'nl', 'CLICK_TO_BEGIN', 'Klik hier om te beginnen', ''),
(3, 'nl', 'YES', 'Ja', ''),
(4, 'nl', 'NO', 'Nee', ''),
(5, 'nl', 'EMAIL_PERMISSION', 'info-nl@wikimedia.org', ''),
(6, 'nl', 'DO_NOT_UPLOAD', 'Niet uploaden', ''),
(7, 'nl', 'UPLOAD_PD', 'Uploaden als publiek domein', ''),
(8, 'nl', 'UPLOAD_LICENSE', 'Uploaden onder een van de <a href="?question=licenses">aangegeven licenties</a>.', ''),
(9, 'nl', 'UPLOAD_LICENSE_EMAIL', '1. Uploaden onder een van de <a href="javascript:popUp(GE_WIZARD + ''?question=licenses'')">aangegeven licenties</a> <br />2. Toestemming wordt via gemaild naar de vrijwilligers van Wikipedia.', 'Om er zeker van te zijn dat er later geen problemen ontstaan met betrekking tot de auteursrechten, is het belangrijk om de toestemming goed vast te leggen. Via deze website wordt uw toestemming doorgestuurd naar de vrijwilligers van Wikipedia. De toestemming wordt dan opgeslagen in de (digitale) "kluis", en is dan voor een beperkt aantal Wikipedia-medewerkers zichtbaar. '),
(76, 'nl', 'EMPTY_VALUE', 'Deze waardes zijn niet ingevuld: ', ''),
(77, 'nl', 'INVALID_EMAIL', 'U heeft een ongeldig e-mail adres ingevuld', ''),
(10, 'nl', 'SUBJECT_PROTECTED', 'Is het hoofdonderwerp van de foto mogelijk auteursrechtelijk beschermd? \r\n						(Bijvoorbeeld: hoes van CD of DVD, logo, reclamebord)', 'Als u een foto maakt, heeft u zelf de auteursrechten van die foto. Als u echter een foto maakt van iets anders, waarop ook auteursrechten rusten, dan mag u uw foto toch niet vrij verspreiden. Dat kan bijvoorbeeld zo zijn bij foto''s van een CD-hoes, DVD-hoes, logo, reclamebord of (film)poster. Het uploaden van uw foto betekent dan dat u de auteursrechten schendt op die CD-hoes enz.'),
(15, 'nl', 'CREATOR_PERMISSION', 'Geeft de maker toestemming voor onbeperkte verspreiding, bewerking en commercieel gebruik van de foto?', 'De auteursrechten van de foto liggen normaal gesproken bij degene die de foto gemaakt heeft. De maker moet daarom toestemming geven om de foto onbeperkt te gebruiken. \r\n\r\n''Onbeperkt'' betekent dat anderen zonder toestemming van de maker de foto mogen gebruiken, zonder dat zij aan de maker hoeven te melden dat ze de foto gebruiken. Wel moet de ander de maker als auteur noemen (tenzij de maker aangegeven heeft dat dat niet nodig is). ''Onbeperkt'' betekent dus ook dat de foto voor een commercieel doel gebruikt kan worden, bijvoorbeeld in een tijdschrift, website of reclamefolder.'),
(16, 'nl', 'WORK_IN_EMPLOYMENT', 'Heeft u de foto gemaakt in opdracht van een ander?', 'Als een ander u opdracht heeft gegeven om foto(''s) te maken, dan heeft die ander (ook) auteursrechten op de foto. Dat kan bijvoorbeeld uw werkgever zijn, of de organisatie of persoon waar u (freelance) een opdracht doet. Als u een foto maakt in opdracht van uw werkgever, dan heeft uw werkgever (bijna) altijd auteursrechten op de foto. Als u de foto heeft gemaakt voor een andere opdrachtgever, dan ligt het eraan wat u afgesproken heeft over de auteursrechten. Als de auteursrechten (deels) bij uw werkgever/opdrachtgever liggen, mag u de foto niet zomaar uploaden, ook al heeft u de foto zelf gemaakt. '),
(13, 'nl', 'OWN_WORK', 'Heeft u de foto zelf gemaakt?', ''),
(14, 'nl', 'CREATOR_PD', 'Is de maker meer dan 70 jaar geleden overleden?', 'De auteursrechten van de foto liggen normaal gesproken bij degene die de foto gemaakt heeft. Deze auteursrechten verlopen na verloop van tijd. De foto komt dan in het "publieke domein" (public domain) terecht. Hoe lang dat duurt verschilt van land tot land. Omdat Wikipedia een internationaal project is gaan we uit van de strengste wetgeving: de maker van de foto moet meer dan 70 jaar geleden overleden zijn voordat de foto vrij te gebruiken is.'),
(17, 'nl', 'OWN_PERMISSION', 'Geeft u toestemming voor onbeperkte verspreiding, bewerking en commercieel gebruik van de foto?', '''Onbeperkt'' betekent dat anderen zonder uw toestemming uw foto mogen gebruiken, zonder dat zij aan u hoeven te melden dat ze de foto gebruiken. Wel moet de ander u als auteur noemen (tenzij u zelf aangegeven hebt dat dat niet nodig is). ''Onbeperkt'' betekent dus ook dat uw foto voor een commercieel doel gebruikt kan worden, bijvoorbeeld in een tijdschrift, website of reclamefolder.'),
(18, 'nl', 'EMPLOYER_PERMISSION', 'Geeft de opdrachtgever toestemming voor onbeperkte verspreiding, bewerking en commercieel gebruik van de foto?', 'Als u de foto in opdracht van een ander hebt gemaakt, heeft u toestemming van die ander nodig om de foto te uploaden. Dit kunt u van tevoren hebben afgesproken (u heeft dan met de opdrachtgever afgesproken dat de auteursrechten volledig bij u liggen, en niet bij de opdrachtgever), maar u kunt ook achteraf toestemming vragen.\r\n\r\n''Onbeperkt'' betekent dat anderen zonder toestemming van de opdrachtgever uw foto mogen gebruiken, zonder dat zij aan u of de opdrachtgever hoeven te melden dat ze de foto gebruiken. Wel moet de ander u en/of de opdrachtgever als auteur noemen (tenzij u en de opdrachtgever aangegeven hebben dat dat niet nodig is). ''Onbeperkt'' betekent dus ook dat de foto voor een commercieel doel gebruikt kan worden, bijvoorbeeld in een tijdschrift, website of reclamefolder.'),
(78, 'nl', 'INVALID_EMAIL_PROVIDER', 'U maakt gebruik van een e-mail provider die niet is toegestaan: ', ''),
(19, 'nl', 'ADVICE_WIZARD', 'Het advies van de uploadwizard is:', ''),
(20, 'nl', 'START_AGAIN', 'Terug naar het begin', ''),
(21, 'nl', 'TITLE', 'Wikiportret - Stel uw foto''s ter beschikking', ''),
(22, 'nl', 'EXPLANATION', 'Uitleg\r\n', ''),
(23, 'nl', 'UPLOAD_LINK', 'Upload uw foto', ''),
(25, 'en', 'WELCOME_MESSAGE', 'Welcome to the uploadwizard. This wizard will help you making a choice for a license.', ''),
(26, 'en', 'CLICK_TO_BEGIN', 'Click here to start', ''),
(27, 'en', 'YES', 'Yes', ''),
(28, 'en', 'NO', 'No', ''),
(29, 'en', 'EMAIL_PERMISSION', 'permissions@wikimedia.org', ''),
(30, 'en', 'DO_NOT_UPLOAD', 'Don''t upload', ''),
(31, 'en', 'UPLOAD_PD', 'Upload as public domain', ''),
(32, 'en', 'UPLOAD_LICENSE', 'Upload onder one of the <a href="?question=licenses">recommended licenses</a>.', ''),
(33, 'en', 'UPLOAD_LICENSE_EMAIL', '1. Upload onder one of the <a href="?question=licenses">recommended licenses</a>.<br />\r\n2. Permission (including the name of the image and your name) to <a href="mailto:permissions@wikimedia.org">info-nl@wikimedia.org</a>', 'Om er zeker van te zijn dat er later geen problemen ontstaan met betrekking tot de auteursrechten, is het belangrijk om de toestemming goed vast te leggen. Dit doe je door de toestemming (een e-mail van degene die toestemming moet geven of een ingescande overeenkomst waarin de toestemming verleend wordt) te mailen naar info-nl@wikimedia.org. De toestemming wordt dan opgeslagen in de (digitale) "kluis", en is dan voor een beperkt aantal Wikimedia-medewerkers zichtbaar. '),
(34, 'en', 'SUBJECT_PROTECTED', 'Is the main subject of the photograph possibly protected by copyright? (For example: cover of a cd or dvd, logo, advertisement)', 'Als je een foto maakt, heb je zelf de auteursrechten van die foto. Als je echter een foto maakt van iets anders, waarop ook auteursrechten rusten, dan mag je jouw foto toch niet vrij verspreiden. Dat kan bijvoorbeeld zo zijn bij foto''s van een CD-hoes, DVD-hoes, logo, reclamebord of (film)poster. Het uploaden van jouw foto betekent dan dat je de auteursrechten schendt op die CD-hoes enz.'),
(35, 'en', 'CREATOR_PERMISSION', 'Does the creator give permission for unlimited distribution, modification and commercial use of the image?', 'De auteursrechten van de afbeelding liggen normaalgesproken bij degene die de afbeelding gemaakt heeft. De maker moet daarom toestemming geven om de afbeelding onbeperkt te gebruiken. \r\n\r\n''Onbeperkt'' betekent dat anderen zonder toestemming van de maker de afbeelding mogen gebruiken, zonder dat zij aan de maker hoeven te melden dat ze de afbeelding gebruiken. Wel moet de ander de maker als auteur noemen (tenzij de maker aangegeven heeft dat dat niet nodig is). ''Onbeperkt'' betekent dus ook dat de afbeelding voor een commercieel doel gebruikt kan worden, bijvoorbeeld in een tijdschrift, website of reclamefolder.\r\n'),
(36, 'en', 'WORK_IN_EMPLOYMENT', 'Did you make the image by the order of somebody else?', 'Als een ander jou opdracht heeft gegeven om afbeelding(en) te maken, dan heeft die ander (ook) auteursrechten op de afbeelding. Dat kan bijvoorbeeld jouw werkgever zijn, of de organisatie of persoon waar je (freelance) een opdracht doet. Als je een afbeelding maakt in opdracht van jouw werkgever, dan heeft jouw werkgever (bijna) altijd auteursrechten op de afbeelding. Als je de afbeelding hebt gemaakt voor een andere opdrachtgever, dan ligt het eraan wat je afgesproken hebt over de auteursrechten. Als de auteursrechten (deels) bij jouw werkgever/opdrachtgever liggen, mag je de afbeelding niet zomaar uploaden, ook al heb je de afbeelding zelf gemaakt. '),
(37, 'en', 'OWN_WORK', 'Did you make the image yourself?', ''),
(38, 'en', 'CREATOR_PD', 'Has the creator died more than 70 years ago?', 'De auteursrechten van de afbeelding liggen normaalgesproken bij degene die de afbeelding gemaakt heeft. Deze auteursrechten verlopen na verloop van tijd. De afbeelding komt dan in het "publieke domein" (public domain) terecht. Hoe lang dat duurt verschilt van land tot land. Omdat Wikipedia een internationaal project is gaan we uit van de strengste wetgeving: de maker van de afbeelding moet meer dan 70 jaar geleden overleden zijn voordat de afbeelding vrij te gebruiken is.'),
(39, 'en', 'OWN_PERMISSION', 'Do you give permission for unlimited distribution, modification and commercial use of the image?', '''Onbeperkt'' betekent dat anderen zonder jouw toestemming jouw afbeelding mogen gebruiken, zonder dat zij aan jou hoeven te melden dat ze de afbeelding gebruiken. Wel moet de ander jou als auteur noemen (tenzij je zelf aangegeven hebt dat dat niet nodig is). ''Onbeperkt'' betekent dus ook dat jouw afbeelding voor een commercieel doel gebruikt kan worden, bijvoorbeeld in een tijdschrift, website of reclamefolder.'),
(40, 'en', 'EMPLOYER_PERMISSION', 'Does the creator give permission for unlimited distribution, modification and commercial use of the image?', 'Als je de afbeelding in opdracht van een ander hebt gemaakt, heb je toestemming van die ander nodig om de afbeelding te uploaden. Dit kun je vantevoren hebben afgesproken (je hebt dan met de opdrachtgever afgesproken dat de auteursrechten volledig bij jou liggen, en niet bij de opdrachtgever), maar je kunt ook achteraf toestemming vragen.\r\n\r\n''Onbeperkt'' betekent dat anderen zonder toestemming van de opdrachtgever jouw afbeelding mogen gebruiken, zonder dat zij aan jou of de opdrachtgever hoeven te melden dat ze de afbeelding gebruiken. Wel moet de ander jou en/of de opdrachtgever als auteur noemen (tenzij jij en de opdrachtgever aangegeven hebben dat dat niet nodig is). ''Onbeperkt'' betekent dus ook dat de afbeelding voor een commercieel doel gebruikt kan worden, bijvoorbeeld in een tijdschrift, website of reclamefolder.\r\n'),
(41, 'en', 'ADVICE_WIZARD', 'The advice of the uploadwizard is:', ''),
(42, 'en', 'START_AGAIN', 'Back to the start', ''),
(43, 'en', 'TITLE', 'Wikiportrait - Make your photographs available', ''),
(44, 'en', 'EXPLANATION', 'Explanation\r\n', ''),
(45, 'en', 'UPLOAD_LINK', 'Upload your image', ''),
(46, 'nl', 'PHOTO_UPLOADED', 'Deze foto is succesvol geupload:', ''),
(47, 'nl', 'MAIL_SENT', 'De volgende e-mail is verzonden:', ''),
(48, 'nl', 'THANKS_UPLOAD', 'Bedankt voor uw upload!', ''),
(49, 'nl', 'UPLOAD_ANOTHER_IMAGE', 'Upload nog een bestand.', ''),
(50, 'nl', 'LICENSE_EMAIL', 'Ik, (uw naam)....., wonende ....... te ........ stel deze afbeelding(en) van ........., gemaakt op ........ ter beschikking onder een multi-licentie, dat wil zeggen onder zowel de GFDL als Creative Commons CC-BY-SA (http://creativecommons.org/licenses/by-sa/2.5/) licentie. Ik ben de maker van deze foto''s en/of beschik over de auteursrechten. De eventuele geportretteerden hebben geen bezwaar tegen publicatie onder genoemde licenties. Ook mijn eventuele opdrachtgever geeft toestemming. Mijn naam dient altijd als rechthebbende bij de foto''s te worden vermeld, zoals de GFDL en de Creative Commons licenties voorschrijven".', ''),
(51, 'en', 'UNKNOWN_ERROR', 'An unknown error has occured, try it again.', ''),
(52, 'nl', 'UNKNOWN_ERROR', 'Er is een onbekende fout opgetreden. Probeer het nogmaals.', ''),
(53, 'nl', 'UPLOAD_FAILED', 'Fout bij het uploaden', ''),
(54, 'en', 'UPLOAD_FAILED', 'Upload failed', ''),
(55, 'en', 'UPLOAD_SUCCESSFUL', 'Upload successful', ''),
(56, 'nl', 'UPLOAD_SUCCESSFUL', 'Uploaden geslaagd', ''),
(75, 'en', 'WAIT_FOR_UPLOAD', 'One moment please, your image is being uploaded. This can take several minutes, depending on the size of your image.', ''),
(79, 'nl', 'DISCLAIMER_NOT_AGREED', 'U bent niet akkoord gegaan met de voorwaarden (het vinkje boven de ''Upload mijn foto'' knop)', ''),
(72, 'nl', 'TRY_AGAIN_LATER', 'Probeer het later nog eens.', ''),
(73, 'en', 'TRY_AGAIN_LATER', 'Try again later.', ''),
(74, 'nl', 'WAIT_FOR_UPLOAD', 'Een moment geduld, uw afbeelding wordt geupload. Dit kan enkele minuten duren, afhankelijk van de grootte van uw afbeelding.', ''),
(57, 'nl', 'CHOOSE_LANGUAGE', 'Kies een taal', ''),
(58, 'en', 'CHOOSE_LANGUAGE', 'Choose language', ''),
(59, 'nl', 'GO_BACK_CHANGE_VALUES', 'Ga terug en verander de waardes', ''),
(60, 'en', 'GO_BACK_CHANGE_VALUES', 'Go back and change the values', ''),
(61, 'nl', 'FORM_TITLE', 'Titel', ''),
(62, 'nl', 'FORM_SOURCE', 'Bron / auteur', ''),
(63, 'nl', 'FORM_DISCLAIMER_AGREE', 'Akkoord met voorwaarden', ''),
(64, 'nl', 'UPLOAD_ANOTHER_IMAGE_WARNING', 'Nota bene: deze optie alleen gebruiken als het volgende bestand wat u wilt uploaden dezelfde licentievoorwaarden heeft als uw vorige afbeelding! Als u dit niet zeker weet, ga dan terug naar het begin en doe de uploadwizard overnieuw!', ''),
(65, 'en', 'UPLOAD_ANOTHER_IMAGE_WARNING', 'Attention: only this option when the next image you want to upload has the same licensing conditions as your previous image! If you are not sure, start the uploadwizard again.', ''),
(66, 'nl', 'FORM_NAME', 'Uw naam', ''),
(67, 'nl', 'FORM_EMAIL', 'E-mail', ''),
(68, 'en', 'FORM_NAME', 'Your name', ''),
(69, 'en', 'FORM_EMAIL', 'E-mail', ''),
(70, 'nl', 'UPLOAD_SUCCESSFUL_MESSAGE', 'Uw afbeelding is succesvol geupload. Uw e-mail met foto is gestuurd naar de vrijwilligers van Wikimedia. Een van hen zal u een e-mail bericht sturen op het opgegeven adres, om te controleren of u werkelijk degene bent die rechthebbende is van de foto. Na ontvangst van uw bevestiging zal de foto op de Wikimedia projecten worden neergezet. Ook daarvan zal u bericht ontvangen.', ''),
(71, 'en', 'UPLOAD_SUCCESSFUL_MESSAGE', 'Your upload was successful. Your e-mail with photograph has been sent to the volunteers of Wikimedia. One of them will send an e-mail to the adress you provided, to make sure that you are truely the right owner of the photograph. After you have given an ''ok'' the photo will appear on the Wikimedia projects. You will recieve an e-mail about that as well.', ''),
(80, 'nl', 'INVALID_FILETYPE', 'Het bestand wat u probeert up te loaden is geen geldige afbeelding: wij accepteren alleen .jpeg, .jpg, .png en .gif bestanden', ''),
(81, 'nl', 'ACTION_GOOD', 'Cool! Genereer een bevestingstekst om te mailen vanaf OTRS', ''),
(82, 'nl', 'ACTION_GOOGLE', 'Komt van Google, automatische afwijzing naar de uploader', ''),
(83, 'nl', 'ACTION_BAD_QUALITY', 'Slechte kwaliteit, automatische afwijzing naar uploader', ''),
(84, 'nl', 'ACTION_INVALID_EMAIL', 'E-mail adres onwaarschijnlijk, automatische afwijzing naar uploader', ''),
(85, 'nl', 'RATE_THIS_IMAGE', 'Beoordeel deze afbeelding', ''),
(86, 'nl', 'ACTION_FOR_IMAGE', 'Wat doen we daar mee?', ''),
(87, 'nl', 'IMAGE_ACTION_DONE', 'Deze afbeelding is al behandeld!', ''),
(88, 'nl', 'RESPONSE_ACTION_GOOGLE', 'Bedankt voor de moeite een afbeelding toe te voegen via wikiportret.nl. De afbeelding die u heeft toegevoegd is volgens mij niet vrij (het is niet uw werk of de afbeelding is niet vrijgegeven door de auteur). Ik zal de afbeelding dus ook niet aan Wikipedia toevoegen. Mocht u toch toestemming hebben de afbeelding toe te voegen, reageer dan op deze mail, eventueel met meer informatie over de afbeelding. Ik zal de afbeelding dan opnieuw beoordelen.', ''),
(92, 'nl', 'RESPONSE_HEADER', 'Geachte %s,\r\n\r\n', ''),
(89, 'nl', 'RESPONSE_DISCLAIMER', 'VOORBEHOUD: Wikipedia is het werk van vrijwilligers die werken zonder formeel kader. Dit bericht is verzonden door een ervaren vrijwilliger van de Nederlandstalige Wikipedia uit eigen naam en naar best vermogen. Er is geen enkele garantie over juistheid van deze informatie. De schrijver van dit bericht kan officieel niet namens de Wikimedia Foundation en/of haar projecten spreken.', ''),
(90, 'nl', 'RESPONSE_ACTION_INVALID_EMAIL', 'Hartelijk dank voor uw upload van uw foto. Het is ons echter niet duidelijk of u de betreffende foto door u zelf is gemaakt, of dat u er de rechten van bezit. Wij kunnen alleen foto''s accepteren waar u ook de rechten op bezit. Daarnaast accepteren wij geen e-mails van ''gratis'' adressen (zoals onder andere Yahoo, Hotmail en Gmail), omdat deze fraudegevoelig zijn. Mocht u wel de rechthebbende zijn van deze foto, stuurt u dan nogmaals de foto vanaf een officieel adres.', ''),
(91, 'nl', 'RESPONSE_FOOTER', '\r\n\r\nMet vriendelijke groet,\r\nhet Wikiportret contactpunt\r\n\r\n', ''),
(93, 'nl', 'RESPONSE_ACTION_BAD_QUALITY', 'Hartelijk dank voor uw upload van uw foto. Helaas is de foto die u geupload heeft van een erg slechte kwaliteit en daarom niet bruikbaar: mocht u echter nog andere afbeeldingen in uw bezit hebben die wel geschikt zijn voor Wikiportret, wordt u van harte uitgenodigd om deze als vervanging op te sturen.', ''),
(94, 'nl', 'RESPONSE_ACTION_CONFIRMATION', 'Iemand heeft een foto van %s upgeload op http://www.wikiportret.nl, een project van Wikimedia Nederland. Wij willen graag van u weten of u de rechthebbende van deze foto bent en of u ook akkoord gaat met de voorwaarden. Deze voorwaarden houden in dat u de foto heeft vrijgegeven onder de licentie CC-BY/GFDL; de licentievoorwaarden zijn ondermeer dat u akkoord gaat dat deze foto bewerkt, verspreid en eventueel commercieel gebruikt mag worden.\r\n\r\nAls u hiermee akkoord gaat reageer dan op deze e-mail met ''Ja, ik ga akkoord met de voorwaarden''. Mocht u *niet* degene zijn die de foto heeft geupload, laat ons dat dan ook weten. Zodra wij uw bevestiging hebben ontvangen zullen wij de foto plaatsen bij het relevante artikel en u daarvan op de hoogte stellen.', '');
