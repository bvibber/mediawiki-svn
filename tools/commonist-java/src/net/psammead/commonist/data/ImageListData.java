package net.psammead.commonist.data;

import java.util.ArrayList;
import java.util.Collections;
import java.util.Iterator;
import java.util.List;

/** Data edited in an ImageListUI */
public final class ImageListData {
	private List<ImageData>	imageDatas;
	
	public ImageListData(List<ImageData> imageDatas) {
		this.imageDatas	= imageDatas;
	}
	
	public List<ImageData> getSelected() {
		final List<ImageData>	out	= new ArrayList<ImageData>();
		for (Iterator<ImageData> it=imageDatas.iterator(); it.hasNext();) {
			final ImageData	data	= it.next();
			if (data.upload)	out.add(data);
		}
		return Collections.unmodifiableList(out);
	}
}
