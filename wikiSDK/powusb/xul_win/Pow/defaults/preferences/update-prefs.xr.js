// Always prompt by default.
pref("app.update.enabled", true);
pref("app.update.auto", false);
pref("app.update.silent", false);
pref("app.update.mode", 0);
pref("app.update.incompatible.mode", 0);

pref("app.update.url", "http://pow.rdmsoft.com/xulrunner/update/1/%CHANNEL%?v=%VERSION%&b=%BUILD_ID%&o=%BUILD_TARGET%");
pref("app.update.url.manual", "http://pow.rdmsoft.com/xulrunner/");
pref("app.update.url.details", "http://pow.rdmsoft.com/xulrunner/");

// Check every day, if download is refused ask again each day.
// If download accepted but install refused, nag after 3 hours.
pref("app.update.interval", 86400);
pref("app.update.nagTimer.download", 86400);
pref("app.update.nagTimer.restart", 7200);
pref("app.update.timer", 600000);

// Seems to be broken, and nothing in-tree uses it.
pref("app.update.showInstalledUI", false);
