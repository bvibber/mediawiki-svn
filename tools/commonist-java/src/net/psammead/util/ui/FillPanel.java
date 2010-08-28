package net.psammead.util.ui;

import java.awt.Dimension;
import java.awt.GridBagLayout;

import javax.swing.JPanel;

/** an empty JPanel to be used within a {@link GridBagLayout} to fill available space */
public final class FillPanel extends JPanel {
	public FillPanel() {
		setMinimumSize(new Dimension(0,0));
		setMaximumSize(new Dimension(Integer.MAX_VALUE,Integer.MAX_VALUE));
		setPreferredSize(new Dimension(0,0));
	}
}
