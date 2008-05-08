
package imgservserver;

import java.io.IOException;

public class Main {
	static final int PORT = 8765;
	
    public static void main(String[] args) {
		try {
			RequestListener listener = new RequestListener(PORT);
			listener.run();
		} catch (IOException e) {
			System.out.printf("% Error occurred in request loop: %s",
					e.getMessage());
		}
    }

}
