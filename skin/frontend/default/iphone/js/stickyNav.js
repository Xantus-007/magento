/*!
 * Scripts
 *
 */
jQuery(document).ready(function($) {
    if ( !(/(iPad|iPhone|iPod).*Mobile.*Safari/.test(navigator.userAgent)) ) {
        $.smartbanner({
            title: 'Monbento', // What the title of the app should be in the banner (defaults to <title>)
            author: 'monbento', // What the author of the app should be in the banner (defaults to <meta name="author"> or hostname)
            price: Translator.translate('FREE'), // Price of the app
            inAppStore: Translator.translate('On the App Store'), // Text of price for iOS
            inGooglePlay: Translator.translate('In Google Play'), // Text of price for Android
            GooglePlayParams: null, // Aditional parameters for the market
            icon: 'http://www.monbento.com/apps/android/logo.webp', // The URL of the icon (defaults to <meta name="apple-touch-icon">)
            iconGloss: null, // Force gloss effect for iOS even for precomposed
            button: Translator.translate('VIEW'), // Text for the install button
            scale: 'auto', // Scale based on viewport size (set to 1 to disable)
            speedIn: 300, // Show animation speed of the banner
            speedOut: 400, // Close animation speed of the banner
            daysHidden: 0, // Duration to hide the banner after being closed (0 = always show banner)
            daysReminder: 0, // Duration to hide the banner after "VIEW" is clicked *separate from when the close button is clicked* (0 = always show banner)
            force: null, // Choose 'ios', 'android' or 'windows'. Don't do a browser check, just always show this banner
            layer: true,
            hideOnInstall: true, // Hide the banner after "VIEW" is clicked.
            iOSUniversalApp: true, // If the iOS App is a universal app for both iPad and iPhone, display Smart Banner to iPad users, too.      
            appendToSelector: 'body' //Append the banner to a specific selector
        })
    }
});