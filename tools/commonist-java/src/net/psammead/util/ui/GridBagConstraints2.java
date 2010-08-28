package net.psammead.util.ui;

import java.awt.GridBagConstraints;
import java.awt.Insets;

/** 
 * GridBagConstraints with a fluent interface.
 * if you don't modify this object's fields directly,
 * everything is done via cloning.
 */
public final class GridBagConstraints2 extends GridBagConstraints {
	//-------------------------------------------------------------------------
	//## object

	public static GridBagConstraints2 gc() { return new GridBagConstraints2(); }
	
	public GridBagConstraints2() {}
	
	public GridBagConstraints2(GridBagConstraints defaults) {
		this.gridx		= defaults.gridx;
		this.gridy		= defaults.gridy;
		this.gridwidth	= defaults.gridwidth;
		this.gridheight	= defaults.gridheight;
		this.weightx	= defaults.weightx;
		this.weighty	= defaults.weighty;
		this.anchor		= defaults.anchor;
		this.fill		= defaults.fill;
		this.insets		= defaults.insets;
		this.ipadx		= defaults.ipadx;
		this.ipady		= defaults.ipady;
	}
	
	//-------------------------------------------------------------------------
	//## gridx/gridy
	
	public GridBagConstraints2 pos(int x, int y) {
		return posX(x).posY(y);
	}
	
	public GridBagConstraints2 posRelative() {
		return posRelativeX().posRelativeY();
	}
	
	public GridBagConstraints2 posX(int x) {
		if (x < 0)	throw new IllegalArgumentException("expected x >= 0");
		return gridx(x);
	}
	
	public GridBagConstraints2 posY(int y) {
		if (y < 0)	throw new IllegalArgumentException("expected y >= 0");
		return gridy(y);
	}
	
	public GridBagConstraints2 posRelativeX() {
		return gridx(RELATIVE);
	}
	
	public GridBagConstraints2 posRelativeY() {
		return gridy(RELATIVE);
	}
	
	private GridBagConstraints2 gridx(int gridx) {
		GridBagConstraints2	c	= new GridBagConstraints2(this);
		c.gridx	= gridx;
		return c;
	}
	
	private GridBagConstraints2 gridy(int gridy) {
		GridBagConstraints2	c	= new GridBagConstraints2(this);
		c.gridy	= gridy;
		return c;
	}
	//-------------------------------------------------------------------------
	//## width/height
	
	public GridBagConstraints2 size(int x, int y) {
		return sizeX(x).sizeY(y);
	}
	
	public GridBagConstraints2 sizeRelative() {
		return sizeRelativeX().sizeRelativeY();
	}
	
	public GridBagConstraints2 sizeRemainder() {
		return sizeRemainderX().sizeRemainderY();
	}
	
	public GridBagConstraints2 sizeX(int x) {
		if (x < 0)	throw new IllegalArgumentException("expected x >= 0");
		return gridwidth(x);
	}
	
	public GridBagConstraints2 sizeY(int y) {
		if (y < 0)	throw new IllegalArgumentException("expected y >= 0");
		return gridheight(y);
	}
	
	public GridBagConstraints2 sizeRelativeX() {
		return gridwidth(RELATIVE);
	}
	
	public GridBagConstraints2 sizeRelativeY() {
		return gridheight(RELATIVE);
	}
	
	public GridBagConstraints2 sizeRemainderX() {
		return gridwidth(REMAINDER);
	}
	
	public GridBagConstraints2 sizeRemainderY() {
		return gridheight(REMAINDER);
	}
	
	private GridBagConstraints2 gridwidth(int gridwidth) {
		GridBagConstraints2	c	= new GridBagConstraints2(this);
		c.gridwidth	= gridwidth;
		return c;
	}
	
	private GridBagConstraints2 gridheight(int gridheight) {
		GridBagConstraints2	c	= new GridBagConstraints2(this);
		c.gridheight	= gridheight;
		return c;
	}
	
	//-------------------------------------------------------------------------
	//## weightx/weighty
	
	public GridBagConstraints2 weight(double x, double y) {
		return weightX(x).weightY(y);
	}
	
	public GridBagConstraints2 weightX(double x) {
		if (x < 0 || x > 1)	throw new IllegalArgumentException("expected 0 <= x <= 1");
		GridBagConstraints2	c	= new GridBagConstraints2(this);
		c.weightx	= x;
		return c;
	}
	
	public GridBagConstraints2 weightY(double y) {
		if (y < 0 || y > 1)	throw new IllegalArgumentException("expected 0 <= y <= 1");
		GridBagConstraints2	c	= new GridBagConstraints2(this);
		c.weighty	= y;
		return c;
	}
	
	//-------------------------------------------------------------------------
	//## ipadx/y
	
	public GridBagConstraints2 ipad(int x, int y) {
		return ipadX(x).ipadY(y);
	}
	
	public GridBagConstraints2 ipadX(int x) {
		if (x < 0)	throw new IllegalArgumentException("expected x >= 0");
		GridBagConstraints2	c	= new GridBagConstraints2(this);
		c.ipadx	= x;
		return c;
	}
	
	public GridBagConstraints2 ipadY(int y) {
		if (y < 0)	throw new IllegalArgumentException("expected y >= 0");
		GridBagConstraints2	c	= new GridBagConstraints2(this);
		c.ipady	= y;	
		return c;
	}

	//-------------------------------------------------------------------------
	//## insets
	
	public GridBagConstraints2 insets(int top, int left, int bottom, int right) {
		return insetsTop(top).insetsLeft(left).insetsBottom(bottom).insetsRight(right);
	}

	public GridBagConstraints2 insetsHorizontal(int left, int right) {
		return insetsLeft(left).insetsRight(right);
	}

	public GridBagConstraints2 insetsVertical(int top, int bottom) {
		return insetsTop(top).insetsBottom(bottom);
	}
	
	public GridBagConstraints2 insetsTop(int top) {
		if (top < 0)	throw new IllegalArgumentException("expected top >= 0");
		return insets(new Insets(top, this.insets.left, this.insets.bottom, this.insets.right));
	}
	
	public GridBagConstraints2 insetsLeft(int left) {
		if (left < 0)	throw new IllegalArgumentException("expected left >= 0");
		return insets(new Insets(this.insets.top, left, this.insets.bottom, this.insets.right));
	}
	
	public GridBagConstraints2 insetsBottom(int bottom) {
		if (bottom < 0)	throw new IllegalArgumentException("expected bottom >= 0");
		return insets(new Insets(this.insets.top, this.insets.left, bottom, this.insets.right));
	}
	
	public GridBagConstraints2 insetsRight(int right) {
		if (right < 0)	throw new IllegalArgumentException("expected top >= 0");
		return insets(new Insets(this.insets.top, this.insets.left, this.insets.bottom, right));
	}
	
	public GridBagConstraints2 insets(Insets insets) {
		//return insetsTop(insets.top).insetsLeft(insets.left).insetsBottom(insets.bottom).insetsRight(insets.right);
		GridBagConstraints2	c	= new GridBagConstraints2(this);
		c.insets	= (Insets)insets.clone();
		return c;
	}
	
	//-------------------------------------------------------------------------
	//## insets
	
	public GridBagConstraints2 anchorCenter() {
		return anchor(CENTER);
	}
	
	public GridBagConstraints2 anchorNorth() {
		return anchor(NORTH);
	}
	
	public GridBagConstraints2 anchorSouth() {
		return anchor(SOUTH);
	}
	
	public GridBagConstraints2 anchorEast() {
		return anchor(EAST);
	}
	
	public GridBagConstraints2 anchorWest() {
		return anchor(WEST);
	}
	
	public GridBagConstraints2 anchorNorthEast() {
		return anchor(NORTHEAST);
	}
	
	public GridBagConstraints2 anchorNorthWest() {
		return anchor(NORTHWEST);
	}
	
	public GridBagConstraints2 anchorSouthEast() {
		return anchor(SOUTHEAST);
	}
	
	public GridBagConstraints2 anchorSouthWest() {
		return anchor(SOUTHWEST);
	}
	
	public GridBagConstraints2 anchorPageStart() {
		return anchor(PAGE_START);
	}
	
	public GridBagConstraints2 anchorPageEnd() {
		return anchor(PAGE_END);
	}
	
	public GridBagConstraints2 anchorLineStart() {
		return anchor(LINE_START);
	}
	
	public GridBagConstraints2 anchorLineEnd() {
		return anchor(LINE_END);
	}
	
	public GridBagConstraints2 anchorFirstLineStart() {
		return anchor(FIRST_LINE_START);
	}
	
	public GridBagConstraints2 anchorFirstLineEnd() {
		return anchor(FIRST_LINE_END);
	}
	
	public GridBagConstraints2 anchorLastLineStart() {
		return anchor(LAST_LINE_START);
	}
	
	public GridBagConstraints2 anchorLastLineEnd() {
		return anchor(LAST_LINE_START);
	}
	
	private GridBagConstraints2 anchor(int anchor) {
		GridBagConstraints2	c	= new GridBagConstraints2(this);
		c.anchor	= anchor;
		return c;
	}
	
	//-------------------------------------------------------------------------
	//## fill
	
	public GridBagConstraints2 fillNone() {
		return fill(NONE);
	}

	public GridBagConstraints2 fillHorizontal() {
		return fill(HORIZONTAL);
	}

	public GridBagConstraints2 fillVertical() {
		return fill(VERTICAL);
	}
	
	public GridBagConstraints2 fillBoth() {
		return fill(BOTH);
	}
	
	private GridBagConstraints2 fill(int fill) {
		GridBagConstraints2	c	= new GridBagConstraints2(this);
		c.fill	= fill;
		return c;
	}
}
