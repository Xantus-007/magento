<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache


/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clés secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C’est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */
// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define( 'DB_NAME', 'b2c-blog' );

/** Utilisateur de la base de données MySQL. */
define( 'DB_USER', 'b2c-blog' );

/** Mot de passe de la base de données MySQL. */
define( 'DB_PASSWORD', 'eJdlJtINWrdXsmov' );

/** Adresse de l’hébergement MySQL. */
define( 'DB_HOST', 'localhost' );

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Type de collation de la base de données.
  * N’y touchez que si vous savez ce que vous faites.
  */
define('DB_COLLATE', '');

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         ':vWXs|/f^7u%[n6RY`Ge@=Qx%~!^d^!qiPM!=|AOg}hnZTohO[H&V<cd8@l`s$J3' );
define( 'SECURE_AUTH_KEY',  'Oc4A^>D:N8=fxG#T4v.$t:+oaHv%y-GXXJ/Md Cq~}pL(0&?dsS0tO,0y8W+d`S|' );
define( 'LOGGED_IN_KEY',    'i?mS]d#; `7O:%OFwKv#(@Fb+w{Mc0C):P6-+N$=HcYYZuki0s!y0(sXaqP4^w^<' );
define( 'NONCE_KEY',        'zc8wapHti<?+N`O ??#LE|~oT&D9||eVulTc92>IfaYYA4ZQFJH~~z=D?WOse(G`' );
define( 'AUTH_SALT',        'j:KXoDW?&rdJdbYW9@Z)4wI,?TUib3,&1V)O!@xnzSQ%)%dU#XP)@L%;&&M`~:!#' );
define( 'SECURE_AUTH_SALT', 'jP#g{_#=5Rmatw5vsa;MH>qCx)LZ!P>4YYp-z(UXfk}nUaCF]^mr^,o|%Bl+!]/8' );
define( 'LOGGED_IN_SALT',   'y<?TPY@8j(}<3xs;_ODa;@`igvX<rxvMjD9z/(Hx=]ct^+Dywsq7Y}z*c`w%E?%F' );
define( 'NONCE_SALT',       'YFV3PDl`bZux$glRZ|f]U4Dq%wPN&:`OW.<1y^/bZc`qEZM.BRB`t6yN+fnEbGJ}' );
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix = 'mb_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortemment recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* C’est tout, ne touchez pas à ce qui suit ! Bonne publication. */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
define('WP_CONTENT_DIR', __DIR__ . '/wp-content');
define('WP_CONTENT_URL', 'https://' . $_SERVER['SERVER_NAME'] . '/blog/wp-content');
define('WP_SITEURL', 'https://' . $_SERVER['SERVER_NAME'] . '/blog/wp');
define('WP_HOME', 'https://' . $_SERVER['SERVER_NAME'] . '/blog/');

require_once(__DIR__ . '/vendor/autoload.php');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');
