<h2>Upload een afbeelding</h2>

<div class="info">
	<script type="text/javascript">
		document.write('<img src="' + GE_URL + 'images/question_mark.png" alt="" />');
	</script>
	<h3>Aan welke voorwaarden moet mijn foto voldoen?</h3>
	<p>
		Lees eerst deze voorwaarden door. Als uw foto voldoet aan alle eisen klik dan op 'Upload mijn foto'
	</p>
	<ul>
		<li>U moet uw werkelijke naam en e-mail adres opgeven.</li>
		<li>Deze moeten controleerbaar een relatie hebben met de foto, of met de manager van de persoon op de foto.</li>
		<li>Er wordt gecontroleerd of u rechthebbende kan zijn van de foto.</li>
		<li>Uw foto moet minimaal een resolutie hebben van 640x480 pixels</li>
	</ul>
	<p>
		<strong>Let op:</strong> Inzendingen zonder vermelding van de fotograaf en van de rechthebbende, of inzendingen die
		van zoekmachines als Google e.d. zijn afgehaald worden <strong>niet</strong> in behandeling genomen!
	</p>

	<button id="toggleUpload">Upload mijn foto</button>
</div>

<div id="uploadbox" class="jshide"> <!-- might be something IE-specific ? -->
	<form name="upload" id="upload" method="post" enctype="multipart/form-data" action="">
		<div class="item">
			<label for="file">Bestand dat u wilt uploaden</label>
			<input type="file" name="file" id="file" size="40" />
		</div>

		<div class="item">
			<label for="title">Wie staat er op de foto</label>
			<input type="text" name="title" id="title" size="40" value="" />
		</div>

		<div class="item">
			<label for="source">Rechthebbende en/of auteur van de foto</label>
			<input type="text" name="source" id="source" size="40" value="" />
		</div>

		<div class="item">
			<label for="name">Uw naam</label>
			<input type="text" name="name" id="name" size="40" value="" />
		</div>

		<div class="item">
			<label for="email">E-mail</label>
			<input type="text" name="email" id="email" size="40" value="" /><br />
			<p><em>Dit moet een officieel adres zijn van u of uw agentschap, geen Hotmail, Gmail, Yahoo, e.d. adressen aub</em></p>
		</div>

		<div class="item">
			<label for="data">Datum van foto (optioneel)</label>
			<input type="text" name="date" id="date" size="40" value="" />
		</div>

		<div class="item">
			<label for="description">Beschrijving van de foto (optioneel)</label>
			<textarea name="description" id="description" rows="6" cols="80"></textarea>
		</div>

		<div class="item">
			<label for="license">Licentie</label>
			<select name="license" id="license">
				<option value="ccby-gfdl" style="font-weight:bold;">Multi-licentie Creative Commons Naamsvermelding / GFDL (aanbevolen)</option> <!-- Multi-license CC-BY / GFDL -->
				<!--
				<option value="ccbysa-gfdl">Multi-licentie Creative Commons Naamsvermelding-GelijkDelen / GFDL</option>
					<option value="gfdl">GNU Licentie voor Vrije Documentatie (GFDL)</option>
					<option value="ccby">Creative Commons Naamsvermelding</option>
					<option value="ccbysa">Creative Commons Naamsvermelding-Gelijkdelen</option>
					<option value="pd">Publiek Domein</option>
				-->

			</select>
			<em><a href="javascript:popUp(GE_WIZARD + '?question=licenses')">Wat is een licentie?</a></em>
		</div>

		<div class="item">
			<label for="uploadDisclaimer">Voorwaarden</label>
			<textarea name="uploadDisclaimer" id="uploadDescription" rows="6" cols="80">Door het uploaden van dit materiaal en het klikken op de knop 'Upload foto' verklaart u dat u de rechthebbende eigenaar bent van het materiaal. Door dit materiaal te uploaden geeft u toestemming voor het gebruik van het materiaal onder de condities van de door u geselecteerde licentie(s), deze condities variëren per licentie maar houden in ieder geval in dat het materiaal verspreid, bewerkt en commercieel gebruikt mag worden door eenieder. Voor de specifieke extra condities per licentie verwijzen u naar de bijbehorende licentieteksten. U kunt op het vrijgeven van deze rechten na het akkoord gaan met deze voorwaarden niet meer terugkomen. De Wikimedia Foundation en haar chapters (waaronder de Vereniging Wikimedia Nederland) zijn op geen enkele wijze aansprakelijk voor misbruik van het materiaal of aanspraak op het materiaal door derden. De eventuele geportretteerden hebben geen bezwaar tegen publicatie onder genoemde licenties. Ook mijn eventuele opdrachtgever geeft toestemming.</textarea>
		</div>

		<div class="item">
			<label for="disclaimerAgree">Ik ga akkoord met de bovengenoemde voorwaarden. <br />Mijn toestemming wordt automatisch gemaild naar info-nl at wikimedia dot org.</label>
			<input type="checkbox" name="disclaimerAgree" id="disclaimerAgree" />
		</div>

		<br style="clear:both;" />

		<button type="submit" id="btnUpload" name="btnUpload">Upload mijn foto</button>

		<div id="loading" class="imgbox jshide">
			<script type="text/javascript">
				document.write('<img src="' + GE_URL + 'images/loading.gif" alt="Loading..." />');
				document.write('<p>' + messages.WAIT_FOR_UPLOAD + '</p>');
			</script>
		</div>

	</form> <!-- we upload using javascript, so disable this -->

</div> <!-- #upload -->

<br style="clear:both;" />
