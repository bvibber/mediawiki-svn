package commonist.ui

import java.awt.Dimension
import java.awt.GridBagLayout
import java.awt.GridBagConstraints
import java.awt.event.ActionEvent
import java.awt.event.ActionListener

import javax.swing.JComboBox
import javax.swing.JLabel
import javax.swing.JPanel
import javax.swing.JPasswordField
import javax.swing.JScrollPane
import javax.swing.JTextArea
import javax.swing.JTextField
import javax.swing.SwingConstants
import javax.swing.ScrollPaneConstants

import scutil.ext.AnyRefExt._
import scutil.ext.GridBagConstraintsExt._

import commonist.Constants
import commonist.data.WikiData
import commonist.data.LicenseData
import commonist.data.CommonData
import commonist.util.UIUtil2
import commonist.util.Messages
import commonist.util.Settings

/** an editor for Data common to all images */
final class CommonUI(wikiList:List[WikiData], licenseList:List[LicenseData]) extends NotWidePanel(300) {
	//## components
	
	// ui sugar
	private val commonLabel			= new JLabel(Messages.text("common.header"))

	// labels
	private val wikiLabel			= new JLabel(Messages.text("common.wiki"),			SwingConstants.RIGHT)
	private val userLabel			= new JLabel(Messages.text("common.user"),			SwingConstants.RIGHT)
	private val passwordLabel		= new JLabel(Messages.text("common.password"),		SwingConstants.RIGHT)
	private val descriptionLabel	= new JLabel(Messages.text("common.description"),	SwingConstants.RIGHT)
	private val sourceLabel			= new JLabel(Messages.text("common.source"),		SwingConstants.RIGHT)
	private val dateLabel			= new JLabel(Messages.text("common.date"),			SwingConstants.RIGHT)
	private val authorLabel			= new JLabel(Messages.text("common.author"),		SwingConstants.RIGHT)
	private val categoriesLabel		= new JLabel(Messages.text("common.categories"),	SwingConstants.RIGHT)
	private val licenseLabel		= new JLabel(Messages.text("common.license"),		SwingConstants.RIGHT)
	
	// editors
	private val wikiEditor			= new JComboBox(wikiList.toArray[Object])
	private val userEditor			= new JTextField(Constants.INPUT_FIELD_WIDTH)
	private val passwordEditor		= new JPasswordField(Constants.INPUT_FIELD_WIDTH)
	private val descriptionEditor	= new JTextArea(Constants.INPUT_FIELD_HEIGHT, Constants.INPUT_FIELD_WIDTH)
	private val descriptionScroll	= new JScrollPane(descriptionEditor, ScrollPaneConstants.VERTICAL_SCROLLBAR_AS_NEEDED, ScrollPaneConstants.HORIZONTAL_SCROLLBAR_AS_NEEDED)
	private val sourceEditor		= new JTextField(Constants.INPUT_FIELD_WIDTH)
	private val dateEditor			= new JTextField(Constants.INPUT_FIELD_WIDTH)
	private val authorEditor		= new JTextField(Constants.INPUT_FIELD_WIDTH)
	private val categoriesEditor	= new JTextField(Constants.INPUT_FIELD_WIDTH)
	private val licenseEditor		= new NotWideComboBox(licenseList.toArray[Object])
	
	// separators
	private val separator1	= new JPanel()
	private val separator2	= new JPanel()
	separator1.setPreferredSize(new Dimension(0,0))
	separator2.setPreferredSize(new Dimension(0,0))

	// setup
	UIUtil2.tabMovesFocus(descriptionEditor)
	descriptionEditor.setLineWrap(true)
	descriptionEditor.setWrapStyleWord(true)
	categoriesEditor.setToolTipText(Messages.text("common.categories.tooltip"))
	
	// license contains the full text as a tooltip
	private def updateLicenseTooltip() {
		licenseEditor.setToolTipText(licenseEditor.getSelectedItem.nullOption map {_.toString} getOrElse "")
	}
	licenseEditor.addActionListener(new ActionListener {
		def actionPerformed(ev:ActionEvent) { updateLicenseTooltip() }
	});
	updateLicenseTooltip()
	
	//------------------------------------------------------------------------------
	//## layout
	
	setBorder(Constants.PANEL_BORDER)
	setLayout(new GridBagLayout())
	
	// header label 
	add(commonLabel,		GBC.pos(1,0).size(1,1).weight(0,0).anchorWest().fillNone().insetsAll(0,0,4,0))

	// part 1
	
	add(userLabel,			GBC.pos(0,1).size(1,1).weight(0,0).anchorEast().fillNone().insetsAll(0,4,0,4))
	add(userEditor,			GBC.pos(1,1).size(1,1).weight(0,0).anchorWest().fillHorizontal().insetsAll(0,0,0,0))
	
	add(passwordLabel,		GBC.pos(0,2).size(1,1).weight(0,0).anchorEast().fillNone().insetsAll(0,4,0,4))
	add(passwordEditor,		GBC.pos(1,2).size(1,1).weight(0,0).anchorWest().fillHorizontal().insetsAll(0,0,0,0))

	add(wikiLabel,			GBC.pos(0,3).size(1,1).weight(0,0).anchorEast().fillNone().insetsAll(0,4,0,4))
	add(wikiEditor,			GBC.pos(1,3).size(1,1).weight(0,0).anchorWest().fillHorizontal().insetsAll(0,0,0,0))

	// separator 1
	add(separator1,			GBC.pos(0,4).size(2,1).weight(1,0).anchorCenter().fillHorizontal().insetsAll(2,0,2,0))
	
	// part 2
	
	add(descriptionLabel,	GBC.pos(0,5).size(1,1).weight(0,1).anchorNorthEast().fillNone().insetsAll(0,4,0,4))
	add(descriptionScroll,	GBC.pos(1,5).size(1,1).weight(0,1).anchorWest().fillBoth().insetsAll(0,0,0,0))

	add(sourceLabel,		GBC.pos(0,6).size(1,1).weight(0,0).anchorEast().fillNone().insetsAll(0,4,0,4))
	add(sourceEditor,		GBC.pos(1,6).size(1,1).weight(0,0).anchorWest().fillHorizontal().insetsAll(0,0,0,0))

	add(dateLabel,			GBC.pos(0,7).size(1,1).weight(0,0).anchorEast().fillNone().insetsAll(0,4,0,4))
	add(dateEditor,			GBC.pos(1,7).size(1,1).weight(0,0).anchorWest().fillHorizontal().insetsAll(0,0,0,0))

	add(authorLabel,		GBC.pos(0,8).size(1,1).weight(0,0).anchorEast().fillNone().insetsAll(0,4,0,4))
	add(authorEditor,		GBC.pos(1,8).size(1,1).weight(0,0).anchorWest().fillHorizontal().insetsAll(0,0,0,0))

	add(categoriesLabel,	GBC.pos(0,9).size(1,1).weight(0,0).anchorNorthEast().fillNone().insetsAll(0,4,0,4))
	add(categoriesEditor,	GBC.pos(1,9).size(1,1).weight(0,0).anchorWest().fillHorizontal().insetsAll(0,0,0,0))

	add(licenseLabel,		GBC.pos(0,10).size(1,1).weight(0,0).anchorEast().fillNone().insetsAll(0,4,0,4))
	add(licenseEditor,		GBC.pos(1,10).size(1,1).weight(0,0).anchorWest().fillHorizontal().insetsAll(0,0,0,0))

	// separator 2
	add(separator2,			GBC.pos(0,11).size(2,1).weight(1,0).anchorCenter().fillHorizontal().insetsAll(0,0,0,0))
	
	/** gets all data edit in this UI */
	def getData:CommonData = new CommonData(
			wikiEditor.getSelectedItem.asInstanceOf[WikiData],
			userEditor.getText.trim,
			new String(passwordEditor.getPassword),
			descriptionEditor.getText,
			dateEditor.getText,
			sourceEditor.getText,
			authorEditor.getText,
			licenseEditor.getSelectedItem.asInstanceOf[LicenseData],
			categoriesEditor.getText)
	
	//------------------------------------------------------------------------------
	//## Settings
	
	/** loads this UI's state from the properties */
	def loadSettings(settings:Settings) {
		userEditor.setText(				settings.get("userEditor.Text",				""))
		passwordEditor.setText(			settings.get("passwordEditor.Text",			""))
		descriptionEditor.setText(		settings.get("descriptionEditor.Text",		""))
		sourceEditor.setText(			settings.get("sourceEditor.Text",			""))
		dateEditor.setText(				settings.get("dateEditor.Text",				""))
		authorEditor.setText(			settings.get("authorEditor.Text",			""))
		categoriesEditor.setText(		settings.get("categoriesEditor.Text",		""))
		
		val wikiSel		= settings.get("wikiEditor.SelectedItem")
		val wikiData	= wikiList find { it => Some(it.api) == wikiSel } getOrElse null
		wikiEditor.setSelectedItem(wikiData)
		
		val licenseSel	= settings.get("licenseEditor.SelectedItem")
		val licenseData	= licenseList find { it => Some(it.template) == licenseSel } getOrElse null
		licenseEditor.setSelectedItem(licenseData)
		
		if (userEditor.getText.length == 0)	userEditor.requestFocusInWindow()
		else								passwordEditor.requestFocusInWindow()
	}
	
	/** stores this UI's state in the properties */
	def saveSettings(settings:Settings) {
		settings.set("userEditor.Text",				userEditor.getText)
		//settings.set("passwordEditor.Text",		passwordEditor.getText)
		settings.set("descriptionEditor.Text",		descriptionEditor.getText)
		settings.set("sourceEditor.Text",			sourceEditor.getText)
		settings.set("dateEditor.Text",				dateEditor.getText)
		settings.set("authorEditor.Text",			authorEditor.getText)
		settings.set("categoriesEditor.Text",		categoriesEditor.getText)
		
		val wikiData	= wikiEditor.getSelectedItem.asInstanceOf[WikiData]
		settings.set("wikiEditor.SelectedItem",		wikiData.api)
		
		val licenseData	= licenseEditor.getSelectedItem.asInstanceOf[LicenseData]
		settings.set("licenseEditor.SelectedItem",	licenseData.template)
	}
}

