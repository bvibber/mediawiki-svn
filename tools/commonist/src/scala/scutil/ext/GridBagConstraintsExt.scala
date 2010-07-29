package scutil.ext

import java.awt.GridBagConstraints
import java.awt.Insets

object GridBagConstraintsExt {
	def GBC = new GridBagConstraints
	
	implicit def toGridBagConstraintsExt(c:GridBagConstraints):GridBagConstraintsExt = new GridBagConstraintsExt(c)
}
	
/** 
 * GridBagConstraints with a fluent interface.
 * if you don't modify this object's fields directly,
 * everything is done withput ever mutating anything.
 */
class GridBagConstraintsExt(delegate:GridBagConstraints) {
	import GridBagConstraintsExt.toGridBagConstraintsExt
	
	private val gc = copy(delegate)
	private def copy:GridBagConstraints = copy(gc)
	private def copy(c:GridBagConstraints):GridBagConstraints = c.clone.asInstanceOf[GridBagConstraints]
	private def copy(i:Insets):Insets                         = i.clone.asInstanceOf[Insets]
	
	//-------------------------------------------------------------------------
	//## gridx/gridy
	
	def pos(x:Int, y:Int)	= posX(x).posY(y)
	def pos(xy:(Int,Int))	= posX(xy._1).posY(xy._2)
	def posRelative()		= posRelativeX().posRelativeY()
 
	def posX(x:Int)			= gridx(x)
	def posY(y:Int)			= gridy(y)
	def posRelativeX()		= gridx(GridBagConstraints.RELATIVE)
	def posRelativeY()		= gridy(GridBagConstraints.RELATIVE)
	
	//-------------------------------------------------------------------------
	//## width/height
	
	def size(x:Int, y:Int)	= sizeX(x).sizeY(y)
	def size(xy:(Int,Int))	= sizeX(xy._1).sizeY(xy._2)
	def sizeRelative()		= sizeRelativeX().sizeRelativeY()
	def sizeRemainder()		= sizeRemainderX().sizeRemainderY()

	def sizeX(x:Int)		= gridwidth(x)
	def sizeY(y:Int)		= gridheight(y)
	def sizeRelativeX()		= gridwidth(GridBagConstraints.RELATIVE)
	def sizeRelativeY()		= gridheight(GridBagConstraints.RELATIVE)
	def sizeRemainderX()	= gridwidth(GridBagConstraints.REMAINDER)
	def sizeRemainderY()	= gridheight(GridBagConstraints.REMAINDER)
	
	//-------------------------------------------------------------------------
	//## weight
	
	def weight(x:Double, y:Double)	= weightX(x).weightY(y)
	def weight(xy:(Double,Double))	= weightX(xy._1).weightY(xy._2)
	def weightX(x:Double)			= weightx(x)
	def weightY(y:Double)			= weighty(y)
	
	//-------------------------------------------------------------------------
	//## ipad
	
	def ipad(x:Int, y:Int)	= ipadX(x).ipadY(y)
	def ipad(xy:(Int,Int))	= ipadX(xy._1).ipadY(xy._2)
	def ipadX(x:Int)		= ipadx(x)
	def ipadY(y:Int)		= ipady(y)
		
	//-------------------------------------------------------------------------
	//## insets
	
	// TODO use RichInsets, maybe?
	 
	def insetsAll(top:Int, left:Int, bottom:Int, right:Int)	= insetsTop(top).insetsLeft(left).insetsBottom(bottom).insetsRight(right)
	def insetsAll(all:(Int,Int,Int,Int))	= insetsTop(all._1).insetsLeft(all._2).insetsBottom(all._3).insetsRight(all._4)
 
	def insetsHorizontal(left:Int, right:Int)	= insetsLeft(left).insetsRight(right)
	def insetsHorizontal(horizontal:(Int,Int))	= insetsLeft(horizontal._1).insetsRight(horizontal._2)
	def insetsVertical(top:Int, bottom:Int)		= insetsTop(top).insetsBottom(bottom)
	def insetsVertical(vertical:(Int,Int))		= insetsTop(vertical._1).insetsBottom(vertical._2)
 
	def insetsTop(top:Int)			= insets(new Insets(top, gc.insets.left, gc.insets.bottom, gc.insets.right))
	def insetsLeft(left:Int)		= insets(new Insets(gc.insets.top, left, gc.insets.bottom, gc.insets.right))
	def insetsBottom(bottom:Int)	= insets(new Insets(gc.insets.top, gc.insets.left, bottom, gc.insets.right))
	def insetsRight(right:Int)		= insets(new Insets(gc.insets.top, gc.insets.left, gc.insets.bottom, right))
		
	//-------------------------------------------------------------------------
	//## anchor
	
	 // BASELINE BASELINE_LEADING BASELINE_TRAILING 
	 // ABOVE_BASELINE ABOVE_BASELINE_LEADING ABOVE_BASELINE_TRAILING 
	 // BELOW_BASELINE BELOW_BASELINE_LEADING BELOW_BASELINE_TRAILING 
	 
	def anchorCenter()			= anchor(GridBagConstraints.CENTER)
	def anchorNorth() 			= anchor(GridBagConstraints.NORTH)
	def anchorSouth() 			= anchor(GridBagConstraints.SOUTH)
	def anchorEast()  			= anchor(GridBagConstraints.EAST)
	def anchorWest()			= anchor(GridBagConstraints.WEST)
	def anchorNorthEast()		= anchor(GridBagConstraints.NORTHEAST)
	def anchorNorthWest()		= anchor(GridBagConstraints.NORTHWEST)
	def anchorSouthEast()		= anchor(GridBagConstraints.SOUTHEAST)
	def anchorSouthWest()		= anchor(GridBagConstraints.SOUTHWEST)
	def anchorPageStart()		= anchor(GridBagConstraints.PAGE_START)
	def anchorPageEnd()			= anchor(GridBagConstraints.PAGE_END)
	def anchorLineStart()		= anchor(GridBagConstraints.LINE_START)
	def anchorLineEnd()			= anchor(GridBagConstraints.LINE_END)
	def anchorFirstLineStart()	= anchor(GridBagConstraints.FIRST_LINE_START)
	def anchorFirstLineEnd()	= anchor(GridBagConstraints.FIRST_LINE_END)
	def anchorLastLineStart()	= anchor(GridBagConstraints.LAST_LINE_START)
	def anchorLastLineEnd()		= anchor(GridBagConstraints.LAST_LINE_START)
	
	//-------------------------------------------------------------------------
	//## fill
	
	def fillNone()			= fill(GridBagConstraints.NONE)
	def fillHorizontal()	= fill(GridBagConstraints.HORIZONTAL)
	def fillVertical()		= fill(GridBagConstraints.VERTICAL)
	def fillBoth()			= fill(GridBagConstraints.BOTH)
 
	//-------------------------------------------------------------------------
	//## private implementation
		
	// TODO simplify using a closure
	
	private def gridx(gridx:Int):GridBagConstraints = {
		val c = copy; c.gridx = gridx; c
	}
	
	private def gridy(gridy:Int):GridBagConstraints = {
		val c = copy; c.gridy = gridy; c
	}
 
	private def gridwidth(gridwidth:Int):GridBagConstraints = {
		val c = copy; c.gridwidth = gridwidth; c
	}
	
	private def gridheight(gridheight:Int):GridBagConstraints = {
		val c = copy; c.gridheight  = gridheight; c
	}
 
	private def weightx(x:Double):GridBagConstraints = {
		val c = copy; c.weightx = x; c
	}
	
	private def weighty(y:Double):GridBagConstraints = {
		val c = copy; c.weighty = y; c
	}
 
	private def ipadx(x:Int):GridBagConstraints = {
		val c = copy; c.ipadx = x; c
	}
	
	private def ipady(y:Int):GridBagConstraints = {
		val c = copy; c.ipady = y ; c
	}
 
	private def insets(insets:Insets):GridBagConstraints = {
		val c = copy; c.insets  = copy(insets); c
	}
	
	private def anchor(anchor:Int):GridBagConstraints = {
		val c = copy; c.anchor  = anchor; c
	}
		
	private def fill(fill:Int):GridBagConstraints = {
		val c = copy; c.fill  = fill; c
	}
}
