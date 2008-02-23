/* Copyright (C) <2004> Wim Taymans <wim@fluendo.com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Library General Public
 * License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Library General Public License for more details.
 *
 * You should have received a copy of the GNU Library General Public
 * License along with this library; if not, write to the
 * Free Software Foundation, Inc., 59 Temple Place - Suite 330,
 * Boston, MA 02111-1307, USA.
 */

package com.fluendo.jst;

public abstract class Object
{
  protected String name;
  protected com.fluendo.jst.Object parent;
  protected int flags;

  public static final int OBJECT_FLAG_LAST = (1 << 4);

  public synchronized String getName () {
    return name;
  }
  public synchronized void setName (String newName) {
    name = newName;
  }
  public Object() {
    this("unnamed");
  }
  public Object(String name) {
    super();
    this.name = name;
    parent = null;
  }
  public synchronized boolean setParent (com.fluendo.jst.Object newParent) {
    if (parent != null)
      return false;

    parent = newParent;

    return true;
  }
  public synchronized com.fluendo.jst.Object getParent () {
    return parent;
  }
  public synchronized void unParent () {
    parent = null;
  }

  public synchronized void setFlag(int flag)
  {
    flags |= flag;
  }
  public synchronized void unsetFlag(int flag)
  {
    flags &= ~flag;
  }
  public synchronized boolean isFlagSet(int flag)
  {
    return (flags & flag) == flag;
  }

  public synchronized boolean setProperty(String name, java.lang.Object value)
  {
    return false;
  }
  public synchronized java.lang.Object getProperty(String name)
  {
    return null;
  }
}
