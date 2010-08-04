package net.psammead.commonist.ui;

import java.awt.Dimension;
import java.awt.GridBagLayout;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Vector;

import javax.swing.JComboBox;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JPasswordField;
import javax.swing.JScrollPane;
import javax.swing.JTextArea;
import javax.swing.JTextField;

import net.psammead.commonist.Constants;
import net.psammead.commonist.data.CommonData;
import net.psammead.commonist.data.LicenseData;
import net.psammead.commonist.util.Messages;
import net.psammead.commonist.util.Settings;
import net.psammead.util.ui.GridBagConstraints2;

/** an editor for Data common to all images */
public final class CommonUI extends NotWidePanel {
	// components
	private final JComboBox			wikiEditor;
	private final JTextField		userEditor;
	private final JPasswordField	passwordEditor;
	private final JTextArea			descriptionEditor;
	private final JScrollPane		descriptionScroll;
	private final JTextField		sourceEditor;
	private final JTextField		dateEditor;
	private final JTextField		authorEditor;
	private final JTextField		categoriesEditor;
	private final JComboBox			licenseEditor;
	
	// state
	private final List<LicenseData>		licenseList;
	private final Map<String,LicenseData>	licenseMap;
	
	
	/** UI for common data for all uploads */
	public CommonUI(List<String> wikiList, List<LicenseData> licenseList) {
		super(300);
		
		this.licenseList	= licenseList;
		licenseMap	= new HashMap<String,LicenseData>();
		for (Iterator<LicenseData> it=licenseList.iterator(); it.hasNext();) {
			final LicenseData	data	= it.next();
			licenseMap.put(data.template, data);
		}
		
		//------------------------------------------------------------------------------
		//## components
		
		// ui sugar
		final JLabel	commonLabel			= new JLabel(Messages.text("common.header"));

		// labels
		final JLabel	wikiLabel			= new JLabel(Messages.text("common.wiki"),			JLabel.RIGHT);
		final JLabel	userLabel			= new JLabel(Messages.text("common.user"),			JLabel.RIGHT);
		final JLabel	passwordLabel		= new JLabel(Messages.text("common.password"),		JLabel.RIGHT);
		final JLabel	descriptionLabel	= new JLabel(Messages.text("common.description"),	JLabel.RIGHT);
		final JLabel	sourceLabel			= new JLabel(Messages.text("common.source"),		JLabel.RIGHT);
		final JLabel	dateLabel			= new JLabel(Messages.text("common.date"),			JLabel.RIGHT);
		final JLabel	authorLabel			= new JLabel(Messages.text("common.author"),		JLabel.RIGHT);
		final JLabel	categoriesLabel		= new JLabel(Messages.text("common.categories"),	JLabel.RIGHT);
		final JLabel	licenseLabel		= new JLabel(Messages.text("common.license"),		JLabel.RIGHT);
		
		// editors
		wikiEditor			= new JComboBox(new Vector<String>(wikiList));
		userEditor			= new JTextField(Constants.INPUT_FIELD_WIDTH);
		passwordEditor		= new JPasswordField(Constants.INPUT_FIELD_WIDTH);
		descriptionEditor	= new JTextArea(Constants.INPUT_FIELD_HEIGHT, Constants.INPUT_FIELD_WIDTH);
		descriptionScroll	= new JScrollPane(descriptionEditor,JScrollPane.VERTICAL_SCROLLBAR_AS_NEEDED, JScrollPane.HORIZONTAL_SCROLLBAR_AS_NEEDED);
		sourceEditor		= new JTextField(Constants.INPUT_FIELD_WIDTH);
		dateEditor			= new JTextField(Constants.INPUT_FIELD_WIDTH);
		authorEditor		= new JTextField(Constants.INPUT_FIELD_WIDTH);
		categoriesEditor	= new JTextField(Constants.INPUT_FIELD_WIDTH);
		licenseEditor		= new NotWideComboBox(new Vector<LicenseData>(licenseList));
		
		// separators
		final JPanel	separator1	= new JPanel();
		final JPanel	separator2	= new JPanel();
		separator1.setPreferredSize(new Dimension(0,0));
		separator2.setPreferredSize(new Dimension(0,0));

		// setup
		descriptionEditor.setLineWrap(true);
		descriptionEditor.setWrapStyleWord(true);
		categoriesEditor.setToolTipText(Messages.text("common.categories.tooltip"));
		
		//------------------------------------------------------------------------------
		//## layout
		
		setBorder(Constants.PANEL_BORDER);
		setLayout(new GridBagLayout());
		
		// header label 
		add(commonLabel,		new GridBagConstraints2().pos(1,0).size(1,1).weight(0,0).anchorWest().fillNone().insets(0,0,4,0));

		// part 1
		
		add(userLabel,			new GridBagConstraints2().pos(0,1).size(1,1).weight(0,0).anchorEast().fillNone().insets(0,4,0,4));
		add(userEditor,			new GridBagConstraints2().pos(1,1).size(1,1).weight(0,0).anchorWest().fillHorizontal().insets(0,0,0,0));
		
		add(passwordLabel,		new GridBagConstraints2().pos(0,2).size(1,1).weight(0,0).anchorEast().fillNone().insets(0,4,0,4));
		add(passwordEditor,		new GridBagConstraints2().pos(1,2).size(1,1).weight(0,0).anchorWest().fillHorizontal().insets(0,0,0,0));

		add(wikiLabel,			new GridBagConstraints2().pos(0,3).size(1,1).weight(0,0).anchorEast().fillNone().insets(0,4,0,4));
		add(wikiEditor,			new GridBagConstraints2().pos(1,3).size(1,1).weight(0,0).anchorWest().fillHorizontal().insets(0,0,0,0));

		// separator 1
		add(separator1,			new GridBagConstraints2().pos(0,4).size(2,1).weight(1,0).anchorCenter().fillHorizontal().insets(0,0,0,0));
		
		// part 2
		
		add(descriptionLabel,	new GridBagConstraints2().pos(0,5).size(1,1).weight(0,1).anchorNorthEast().fillNone().insets(0,4,0,4));
		add(descriptionScroll,	new GridBagConstraints2().pos(1,5).size(1,1).weight(0,1).anchorWest().fillBoth().insets(0,0,0,0));

		add(sourceLabel,		new GridBagConstraints2().pos(0,6).size(1,1).weight(0,0).anchorEast().fillNone().insets(0,4,0,4));
		add(sourceEditor,		new GridBagConstraints2().pos(1,6).size(1,1).weight(0,0).anchorWest().fillHorizontal().insets(0,0,0,0));

		add(dateLabel,			new GridBagConstraints2().pos(0,7).size(1,1).weight(0,0).anchorEast().fillNone().insets(0,4,0,4));
		add(dateEditor,			new GridBagConstraints2().pos(1,7).size(1,1).weight(0,0).anchorWest().fillHorizontal().insets(0,0,0,0));

		add(authorLabel,		new GridBagConstraints2().pos(0,8).size(1,1).weight(0,0).anchorEast().fillNone().insets(0,4,0,4));
		add(authorEditor,		new GridBagConstraints2().pos(1,8).size(1,1).weight(0,0).anchorWest().fillHorizontal().insets(0,0,0,0));

		add(categoriesLabel,	new GridBagConstraints2().pos(0,9).size(1,1).weight(0,0).anchorNorthEast().fillNone().insets(0,4,0,4));
		add(categoriesEditor,	new GridBagConstraints2().pos(1,9).size(1,1).weight(0,0).anchorWest().fillHorizontal().insets(0,0,0,0));

		add(licenseLabel,		new GridBagConstraints2().pos(0,10).size(1,1).weight(0,0).anchorEast().fillNone().insets(0,4,0,4));
		add(licenseEditor,		new GridBagConstraints2().pos(1,10).size(1,1).weight(0,0).anchorWest().fillHorizontal().insets(0,0,0,0));

		// separator 2
		add(separator2,			new GridBagConstraints2().pos(0,11).size(2,1).weight(1,0).anchorCenter().fillHorizontal().insets(0,0,0,0));
	}
	
	/** gets all data edit in this UI */
	public CommonData getData() {
		return new CommonData(
				(String)wikiEditor.getSelectedItem(),
				userEditor.getText(),
				new String(passwordEditor.getPassword()),
				descriptionEditor.getText(),
				sourceEditor.getText(),
				dateEditor.getText(),
				authorEditor.getText(),
				(LicenseData)licenseEditor.getSelectedItem(),
				categoriesEditor.getText());
	}
	
	//------------------------------------------------------------------------------
	//## Settings

	/** loads this UI's state from the properties */
	public void loadSettings(Settings settings) {
		wikiEditor.setSelectedItem(		settings.get("wikiEditor.SelectedItem",		"commons"));
		userEditor.setText(				settings.get("userEditor.Text",				""));
		passwordEditor.setText(			settings.get("passwordEditor.Text",			""));
		descriptionEditor.setText(		settings.get("descriptionEditor.Text",		""));
		sourceEditor.setText(			settings.get("sourceEditor.Text",			""));
		dateEditor.setText(				settings.get("dateEditor.Text",				""));
		authorEditor.setText(			settings.get("authorEditor.Text",			""));
		categoriesEditor.setText(		settings.get("categoriesEditor.Text",		""));
		
		final String		licenseTemplate	= settings.get("licenseEditor.SelectedItem", null);
		LicenseData	licenseData		= null;
		if (licenseTemplate != null) {
			licenseData	= licenseMap.get(licenseTemplate);
		}
		if (licenseData == null) {
			licenseData	= licenseList.get(0);
		}
		licenseEditor.setSelectedItem(licenseData);
		
		if (userEditor.getText().length() == 0)	userEditor.requestFocusInWindow();
		else									passwordEditor.requestFocusInWindow();
	}
	
	/** stores this UI's state in the properties */
	public void saveSettings(Settings settings) {
		settings.set("wikiEditor.SelectedItem",		(String)wikiEditor.getSelectedItem());
		settings.set("userEditor.Text",				userEditor.getText());
		//settings.set("passwordEditor.Text",		passwordEditor.getText());
		settings.set("descriptionEditor.Text",		descriptionEditor.getText());
		settings.set("sourceEditor.Text",			sourceEditor.getText());
		settings.set("dateEditor.Text",				dateEditor.getText());
		settings.set("authorEditor.Text",			authorEditor.getText());
		settings.set("categoriesEditor.Text",		categoriesEditor.getText());
		
		final LicenseData	licenseData	= (LicenseData)licenseEditor.getSelectedItem();
		settings.set("licenseEditor.SelectedItem",	licenseData.template);
	}
}

