package net.psammead.commonist.ui;

import java.awt.Dimension;
import java.util.Vector;

import javax.swing.JComboBox;

import net.psammead.commonist.data.LicenseData;

class NotWideComboBox extends JComboBox {
	public NotWideComboBox(Vector<LicenseData> vector) {
		super(vector);
	}

	@Override
	public Dimension getPreferredSize() {
		return new Dimension(
				10,
				super.getPreferredSize().height);
	}
}