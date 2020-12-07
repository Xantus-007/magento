<?php

require_once('vendor/autoload.php');

use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormError;

use Dbm\Model as DbmModel;

$app = new Silex\Application();

$app['langManager'] = new DbmModel\LanguageManager();
$app->register(new TwigServiceProvider(), array(
            'twig.options' => array('debug' => true)
        ))
		->register(new SessionServiceProvider)
        ->register(new UrlGeneratorServiceProvider)
        ->register(new FormServiceProvider())
        ->register(new DoctrineServiceProvider(), array(
            'db.options' => array(
                'driver' => 'pdo_mysql',
                'host' => 'localhost',
                'dbname' => 'monbento_kids',
                'user' => 'monbento_kids',
                'password' => 'MTlgUakO5BSG88G7Mc',
                'charset' => 'utf8',
            )
        ))
        ->register(new ValidatorServiceProvider())
        ->register(new TranslationServiceProvider(), array(
            'locale_fallbacks' => array('en')
        ))
        ->register(new Silex\Provider\SwiftmailerServiceProvider());

$app['translator.domains'] = array(
    'messages' => array(
        'en' => array(
            'L\'adresse e-mail existe déjà' => 'Email address already exists',
            'L\'adresse e-mail n\'est pas valide' => 'Email address not valid',
        ),
        'fr' => array(
            'L\'adresse e-mail existe déjà' => 'L\'adresse e-mail existe déjà',
            'L\'adresse e-mail n\'est pas valide' => 'L\'adresse e-mail n\'est pas valide',
        )
    )
);

$locale = $app['langManager']->getBrowserLocale();
list($lang, $country) = explode('_', $locale);
if($_GET['lang'] == 'fr' || $_GET['lang'] == 'en' || $app['session']->get('lang'))
{
    if(isset($_GET['lang']))
    {
        $lang = $_GET['lang'];
        $locale = $_GET['lang'];
        $app['session']->set('lang', $lang);
    }
    elseif($app['session']->get('lang'))
    {
        $lang = $app['session']->get('lang');
        $locale = $app['session']->get('lang');
    }
}

$app['twig']->addExtension(new Twig_Extension_Debug());
$app['debug'] = true;
$app['locale'] =  $locale;
$app['country'] = $country;
$app['lang'] = $lang;

$app->match('/', function(Request $request) use ($app) {
    $lang = $app['lang'];
    $errors = $app['validator']->validateValue($_POST['email'], new Assert\Email());

    if (count($errors) > 0 or empty($_POST['email'])) {
        echo $app['translator']->trans('L\'adresse e-mail n\'est pas valide');
    } else {
        $testSql = 'SELECT * FROM subscription WHERE email=:email';
        $exists = $app['db']->fetchAssoc($testSql, array('email' => $_POST['email']));

        if(!$exists)
        {
            $sql = 'INSERT INTO subscription(email, lang, ip, created_at) VALUES(:email, :lang, :ip, NOW())';
            $app['db']->executeUpdate($sql, array('email' => $_POST['email'], 'lang' => $app['langManager']->getDbLanguage($app), 'ip' => $_SERVER['REMOTE_ADDR']));

            echo 'success';
        }
        else
        {
            echo 'success';
        }
    }
    
    return '';
});

$app->run();
