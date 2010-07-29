package commonist.ui

import java.awt.Dimension

import javax.swing.JComboBox

import commonist.data.LicenseData

// TODO make this a trait
class NotWideComboBox(data:Array[Object]) extends JComboBox(data) {
	override def getPreferredSize():Dimension = new Dimension(
			10,
			super.getPreferredSize.height)
}