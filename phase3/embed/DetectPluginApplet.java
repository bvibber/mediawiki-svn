import java.awt.*;


public class DetectPluginApplet extends java.applet.Applet
{
	public void init()
	{
		add(new Label("DetectPluginApplet"));
	}

	public String getJavaVersion()
	{
		return System.getProperty("java.version");
	}
} 