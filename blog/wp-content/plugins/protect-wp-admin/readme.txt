=== Protect Your Admin ===
Contributors: wpexpertsin, india-web-developer
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZEMSYQUZRUK6A
Tags: rename default admin url, rename admin url, secure admin, change username, protect admin, change login page logo
Requires at least: 4.0
Tested up to: 5.2.4
Stable tag: 3.0.3

Hide your WP Admin URL using a secret term and secure your website against hackers!!

== Description ==

Hide your WP Admin URL by rename deafult wordpress admin URL (i.e /wp-admin or /wp-login.php)

If you run a WordPress website, you should absolutely use "protect-wp-admin" to secure it against hackers.

Protect WP-Admin fixes a glaring security hole in the WordPress community: the well-known problem of the admin panel URL.
Everyone knows where the admin panel, and this includes hackers as well.

Protect WP-Admin solve this problem by allowing administrator to customize their admin panel URL and blocking the default links.

Administrator will be able to change default login page url "sitename.com/wp-admin" to something like "sitename.com/custom-string", so after that guest user will be redirect to the homepage.

The plugin also comes with some access filters, allowing Administrator to restrict guest and registered users access to wp-admin, just in case you want some of your editors to log in the classic way.

= NOTE :Back up your database before beginning the activate plugin. =

It is extremely important to back up your database before beginning the activate plugin. If, for some reason, you find it necessary to restore your database from these backups. Plugin will not work for IIS SERVER.


 https://www.youtube.com/watch?v=D4j6LS0uKuY

 **[ Upgrade to Pro Version ](https://rgaddons.wordpress.com/protect-wp-admin-pro/)**


= Features =

 * Define custom wp-admin url(i.e http://yourdomain.com/myadmin)
 * Define custom Logo OR change default logo on login page
 * Define body background color on login page 
 * SEO friendly URL for "Register" page (i.e http://yourdomain.com/myadmin/register)
 * SEO friendly URL for "Lost Password" page (i.e http://yourdomain.com/myadmin/lostpassword)
 * Restrict guest users for access to wp-admin
 * Restrict registered non-admin users from wp-admin
 * Allow admin access to non-admin users by define comma seprate multiple ids

= Go Pro =

We have also released an add-on for Protect-WP-Admin which not only demonstrates the flexibility of Protect-WP-Admin, but also adds some important features

 * Login Attempt Counter
 * An option to define login page logo URL
 * An option to manage login page CSS from admin
 * An option to change username of any user from admin
 * An option to define custom redirect url for defalut wp-admin url
 * Faster support



 **[ Upgrade to Pro Version](https://rgaddons.wordpress.com/protect-wp-admin-pro/)**
 
 PRO FEATURES
 
 https://www.youtube.com/watch?v=Vbk8QX2HWic


== Installation ==
In most cases you can install automatically from WordPress.

However, if you install this manually, follow these steps:

 * Step 1. Upload "protect-wp-admin-pro" folder to the `/wp-content/plugins/` directory
 * Step 2. Activate the plugin through the Plugins menu in WordPress
 * Step 3. Go to Settings "Protect WP-Admin Pro" and configure the plugin settings.

== Frequently Asked Questions ==

* 1.) Nothing happen after enable and add the new wordpress admin url? 

   Don't worry, Just update the site permalink ("Settings" >> "Permalinks") and re-check,Now this time it will be work fine

* 2.) Not able to login into admin after enable plugin? 

 May be issue can come when you not give proper writable permission on htaccess file OR you have not update permalink settings to SEO friendly url from admin. You can access login page url with default wp-admin slug after disable my plugin, you can disable plugin through FTP by rename protect-wp-admin folder to any other one. 

* 3.) Am i not able to login after installation

Basicaly issues can come only in case when you will use default permalink settings. 
If your permalink will be update to any other option except default then it will be work fine. Anyway Dont' worry, manualy you can add code into your site .htaccess file.

<code>
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteRule ^newadmin/?$ /wp-login.php [QSA,L]
RewriteRule ^newadmin/register/?$ /wp-login.php?action=register [QSA,L]
RewriteRule ^newadmin/lostpassword/?$ /wp-login.php?action=lostpassword [QSA,L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress
</code>

Don not forgot to update the "newadmin" slug with your new admin slug (that you were added during update the plugin settings) :-)

4.) Is there any option for set number of login attempt?
* Yes, this option is available in pro addon not in free version. please browse https://rgaddons.wordpress.com/protect-wp-admin-pro/ this url for purchase to pro addon.

== Screenshots ==

1. screenshot-1.png

2. screenshot-2.png

3. screenshot-3.png

4. screenshot-4.png

5. screenshot-5.png



== Changelog == 
= 3.0.3 = 
 * Tested with new wordpress version 5.0.2
 * Fixed some minor issues
 
= 3.0.2 = 
 * Tested with new wordpress version 4.9.8
 * Fixed some minor issues
 
= 3.0.1 = 
 * Fixed admin access issues 

= 3.0 = 
 * Tested with new wordpress version 4.9.7
 * Optimized code of the plugin 
 
= 2.9 = 
 * Tested with new wordpress version 4.9.4
 * Fixed getimagesize() function issue for HTTPS urls 
 
= 2.8 = 
 * Tested with new wordpress version 4.8.1
 * Added upload image lighbox and color picker
 
= 2.7 = 
 * Tested with new wordpress version 4.8.1
 * Added upload image lighbox and color picker
 
= 2.6 = 
 * Tested with new wordpress version 4.8
 
= 2.5 = 
 * Fixed links issues on login, forget and register page for all language
 * Fixed access the wp login page using new admin slug even admin is already logged in

= 2.4 = 
 * Tested with new wordpress version 4.7
 * Fixed images logo image notice error issue.
 
= 2.3 = 
 * Tested with new wordpress version 4.6.1
 * Fixed images size logo issue
 * Modify code for redirect user to new admin url
= 2.2 = 
 * Tested with new wordpress version 4.5.3
 * Optmized plugin code
= 2.1 = 
 * Tested with new wordpress version 4.5.2
 
= 2.0 = 
 * Tested with new wordpress version 4.5 
 * Removed localhost permission related conditions.

= 1.9 = 
 * Fixed htaccess writable notice popup related issues on localhost 
 * Add an new confirmation alert before enable plugin 

= 1.8 = 
 * Fixed Login Failure issue
 * Released Pro Addon

= 1.7 = 
 * Fixed forget password email issue
 * Fixed forgot password link issue on login error page

= 1.6 = 
 *  Fixed wp-login.php issue for www url
 
= 1.5 = 
 * Fixed wp-login url issue
 * Fixed wp-admin url issue

= 1.4 = 
 * Fixed links issue on "Register", "Login" & "Lost Password" As Per New Admin Url
 * Fixed the "Register", "Login" & "Lost Password" Form Action URL As Per New Admin Url
 * Add validation to check SEO firendly url enable or not.
 * Add validation to check .htaccess file is writable or not.

= 1.3 = 
 * Added an option for define to admin login page logo
 * Added an option for define to wp-login page background-color
 * Fixed some minor css issues

= 1.2 = 
 * Added new option for allow admin access to non-admin users
 * Added condition for check permalink is updated or not
 * Fixed a minor issues (logout issues after add/update admin new url)
 
= 1.1 = 
 * Add new option for restrict registered users from wp-admin
 * Fixed permalink update issue after add/update admin new url. Now no need to update your permalink
 * Add option for redirect user to new admin url after update the new admin url

= 1.0 = 
 * First stable release
