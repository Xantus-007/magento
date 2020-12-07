# RGPD - Cookie management & form consent
This WordPress plugin intent to manage cookies based on a JavaScript plugin called **tarteaucitron**. **tarteaucitron** has been modified to be compliant with **RGPD** 

## Installation
- Save the Google Analytics ID, already configured in a plugin like **Google Analytics for WordPress by MonsterInsights**
- Go in plugin management
- Activate **RGPD - Cookie management & form consent**
- Fill the field *Settings* > *General* > *Code Google Analytics UA* with the Google Analytics ID you saved earlier
- There are 2 steps to register a social network service **tarteaucitron**
--  In your `functions.php` file, define a function like this :
```
    add_action('wp_footer', 'registerTarteaucitronServices');
    function registerTarteaucitronServices() {
        echo '
        <script>
            (tarteaucitron.job = tarteaucitron.job || []).push("twitter");
        </script>';
    }
```
- Add the css class to your share button according to [tarteaucitron documentation](https://opt-out.ferank.eu/fr/install/)

If there is a form to subscribe to a newsletter based on **Mailjet** and you need to register all forms subscription in **Flamingo**, you must create the equivalent form in **Contact From 7** and fill the field *Settings* > *General* > *Identifiant formulaire CF7* with the form id you just created. 

## Design
All the dom is set by **tarteaucitron**, in `js/tartaucitron/tarteaucitron.js` mostly after the comment `// Step 3: prepare the html`

## Note
 - Enabling this plugin disallow **Google Analytics for WordPress by MonsterInsights** plugin in order to avoid automatic integration of Google Analytics scripts.
 - You should disable other plugin like **Cookie Notice**
 - Facebook, Twitter, Google+ and Google Analytics are already registered. If one of this services is not required by the website you have to remove the action `addTarteaucitronServices`.

## External references
- https://www.cnil.fr/fr/solutions-centralisees-de-recueil-de-consentement-aux-cookies-les-gestionnaires-de-tag
- https://opt-out.ferank.eu/fr/install/
