<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_USER_NOTICE);
require_once('vendor/autoload.php');

use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Form\FormError;

use Dbm\Model as DbmModel;

//$country = new DbmModel\Country();
$app = new Silex\Application();
$app['langManager'] = new DbmModel\LanguageManager();
$app->register(new TwigServiceProvider(), array(
            'twig.path' => __DIR__ . '/views',
            'twig.options' => array('debug' => true)
        ))
        ->register(new SessionServiceProvider)
        ->register(new UrlGeneratorServiceProvider)
        ->register(new FormServiceProvider())
        ->register(new DoctrineServiceProvider(), array(
            'db.options' => array(
                'driver' => 'pdo_mysql',
                'host' => 'localhost',
                'dbname' => 'monbento_jadore',
                'user' => 'monbento_jadore',
                'password' => 'wMFQoH7u2mCXsZTa99',
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
            'DÉCOUVREZ BIENTÔT NOS ASTUCES POUR PROFITER DE VOTRE NOUVEAU COMPAGNON' => 'CHECK OUT OUR TIPS ON HOW TO TAKE FULL ADVANTAGE OF YOUR NEW LITTLE FRIEND',
            'À <span>100%</span>' => 'AT A <span>100%</span>',
            'Il ne vous reste plus qu\'à indiquer :' => 'All you need to do is enter:',
            'RECEVOIR BIENTÔT LES 3 ASTUCES' => 'AND YOU’LL SOON RECEIVE OUR THREE TIPS',
            'ÉCONOMISEZ JUSQU\'À 3 000€ EN UNE ANNÉE !' => 'SAVE UP TO €3 000 IN A SINGLE YEAR',
            'Et au moins jusqu\'à 500€ en appliquant une technique simple 1 fois par semaine seulement.' => 'And up to €500, at least, if you apply a simple technique just once a week.',
            'DISPOSEZ DE 6H DE TEMPS LIBRE SUPPLÉMENTAIRE CHAQUE SEMAINE !' => 'GAIN SIX HOURS OF FREE TIME EVERY WEEK!',
            'Soit plus de temps pour votre famille, vos amis et surtout pour vous. Un secret qu\'on vous enviera sans aucun doute !' => 'That’s time you can spend with your family, friends and above all, it’s time for yourself! A secret worth sharing!',
            'PROFITEZ DES BIENFAITS DU « MIEUX MANGER ».' => 'BENEFIT FROM THE ADVANTAGES OF « BETTER EATING »',
            '« Mangez où vous voulez » dixit le slogan monbento, mais aussi « mangez plus sainement » grâce à un volume adapté, et « plus sûr » en composant vous-même votre bento.' => '« Eat wherever you want », that’s the monbento motto, but so is « eat more healthily » thanks to appropriate quantities, and « more safely » because you prepare your bento yourself.',
            'LA SOCIÉTÉ MONBENTO ET SES PRODUITS ONT ÉTÉ RÉCOMPENSÉS' => 'THE MONBENTO COMPANY AND ITS PRODUCTS HAVE RECOGNISED',
            'PAR 6 PRIX NATIONAUX ET INTERNATIONAUX' => 'WITH SEVEN NATIONAL AND INTERNATIONAL AWARDS.',
            'Découvrez pourquoi monbento est si unique !' => 'Discover the keys to our success!',
            'Emilie est curieuse, et vous ?' => 'Curious?',
            'Il ne vous reste plus qu’à indiquer :' => 'All you need to do is enter:',
            'Find out what makes monbento so unique, through Emilie’s eyes.' => 'Find out what makes monbento so unique, through Emilie’s eyes.',
            'Votre prénom' => 'Your first name',
            'Votre e-mail' => 'Your email address',
            'L\'adresse e-mail existe déjà' => 'Email address already exists',
            'Vous avez été inscrit avec succès.' => 'You have been successfuly registered.',
            '04 73 23 72 72' => '+33 4 73 23 72 72',
            'Du Lundi au Vendredi - 9h>12h/14h>17h (prix d\'un appel local)' => 'From monday to friday - 9am>12am/2pm>5pm (France timezone)',
            'Téléchargez l\'appli mobile gratuite' => 'Download the free mobile apps',
            'Aller sur <span>monbento.com</span>' => 'Go to <span>monbento.com</span>',
            'Bonjour' => 'Hi',
            'Merci pour votre inscription sur la page J’adore et bienvenue dans la communauté monbento® !' => 'Thank you for registering to the J\'adore page.<br /> Welcome to the monbento® community!',
            'Vous allez prochainement recevoir les astuces qui vous permettront de profiter au mieux de votre nouveau compagnon !' => 'You will shortly receive some tips that will help you to make the best use of your new companion.',
            'A bientôt,' => 'See you soon,',
        ),
        'fr' => array(
            'DÉCOUVREZ BIENTÔT NOS ASTUCES POUR PROFITER DE VOTRE NOUVEAU COMPAGNON' => 'DÉCOUVREZ BIENTÔT NOS ASTUCES POUR PROFITER DE VOTRE NOUVEAU COMPAGNON',
            'À <span>100%</span>' => 'À <span>100%</span>',
            'Il ne vous reste plus qu\'à indiquer :' => 'Il ne vous reste plus qu\'à indiquer :',
            'RECEVOIR BIENTÔT LES 3 ASTUCES' => 'RECEVOIR BIENTÔT LES 3 ASTUCES',
            'ÉCONOMISEZ JUSQU\'À 3 000€ EN UNE ANNÉE !' => 'ÉCONOMISEZ JUSQU\'À 3 000€ EN UNE ANNÉE !',
            'Et au moins jusqu\'à 500€ en appliquant une technique simple 1 fois par semaine seulement.' => 'Et au moins jusqu\'à 500€ en appliquant une technique simple 1 fois par semaine seulement.',
            'DISPOSEZ DE 6H DE TEMPS LIBRE SUPPLÉMENTAIRE CHAQUE SEMAINE !' => 'DISPOSEZ DE 6H DE TEMPS LIBRE SUPPLÉMENTAIRE CHAQUE SEMAINE !',
            'Soit plus de temps pour votre famille, vos amis et surtout pour vous. Un secret qu\'on vous enviera sans aucun doute !' => 'Soit plus de temps pour votre famille, vos amis et surtout pour vous. Un secret qu\'on vous enviera sans aucun doute !',
            'PROFITEZ DES BIENFAITS DU « MIEUX MANGER ».' => 'PROFITEZ DES BIENFAITS DU « MIEUX MANGER ».',
            '« Mangez où vous voulez » dixit le slogan monbento, mais aussi « mangez plus sainement » grâce à un volume adapté, et « plus sûr » en composant vous-même votre bento.' => '« Mangez où vous voulez » dixit le slogan monbento, mais aussi « mangez plus sainement » grâce à un volume adapté, et « plus sûr » en composant vous-même votre bento.',
            'LA SOCIÉTÉ MONBENTO ET SES PRODUITS ONT ÉTÉ RÉCOMPENSÉS' => 'LA SOCIÉTÉ MONBENTO ET SES PRODUITS ONT ÉTÉ RÉCOMPENSÉS',
            'PAR 6 PRIX NATIONAUX ET INTERNATIONAUX' => 'PAR 6 PRIX NATIONAUX ET INTERNATIONAUX',
            'Découvrez pourquoi monbento est si unique !' => 'Découvrez pourquoi monbento est si unique !',
            'Emilie est curieuse, et vous ?' => 'Emilie est curieuse, et vous ?',
            'Il ne vous reste plus qu’à indiquer :' => 'Il ne vous reste plus qu’à indiquer :',
            'Find out what makes monbento so unique, through Emilie’s eyes.' => 'Find out what makes monbento so unique, through Emilie’s eyes.',
            'Votre prénom' => 'Votre prénom',
            'Votre e-mail' => 'Votre e-mail',
            'L\'adresse e-mail existe déjà' => 'L\'adresse e-mail existe déjà',
            'Vous avez été inscrit avec succès.' => 'Vous avez été inscrit avec succès.',
            '04 73 23 72 72' => '04 73 23 72 72',
            'Du Lundi au Vendredi - 9h>12h/14h>17h (prix d\'un appel local)' => 'Du Lundi au Vendredi - 9h>12h/14h>17h (prix d\'un appel local)',
            'Téléchargez l\'appli mobile gratuite' => 'Téléchargez l\'appli mobile gratuite',
            'Aller sur <span>monbento.com</span>' => 'Aller sur <span>monbento.com</span>',
            'Bonjour' => 'Bonjour',
            'Merci pour votre inscription sur la page J’adore et bienvenue dans la communauté monbento® !' => 'Merci pour votre inscription sur la page J’adore et bienvenue dans la communauté monbento® !',
            'Vous allez prochainement recevoir les astuces qui vous permettront de profiter au mieux de votre nouveau compagnon !' => 'Vous allez prochainement recevoir les astuces qui vous permettront de profiter au mieux de votre nouveau compagnon !',
            'A bientôt,' => 'A bientôt,',
        )
    ),
    'validators' => array(
        'fr' => array(
            'This value is not a valid email address.' => 'L\'adresse mail est invalide',
            'This value should not be blank.' => 'Le champ est vide',
            'This value is too long. It should have {{ limit }} character or less.|This value is too long. It should have {{ limit }} characters or less.' => 'La valeur est trop grande, vous devez saisir au plus {{limit}} caractères|La valeur est trop grande, vous devez saisir au plus {{limit}} caractères',
            'This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.' => 'La valeur est trop petite, vous devez saisir au moins {{limit}} caractères|La valeur est trop petite, vous devez saisir au moins {{limit}} caractères'
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
    $form = DbmModel\Form::getForm($app);
    $form2 = clone($form);
    $lang = $app['lang'];
    $messages = $app['session']->getFlashBag()->get('message');
    $form->handleRequest($request);
    $form2->handleRequest($request);

    if ($form->isValid() || $form2->isValid()) {
        $testSql = 'SELECT * FROM subscription WHERE email=:email';
        $data = $form->getData();
        $exists = $app['db']->fetchAssoc($testSql, array('email' => $data['email']));
        
        if(!$exists)
        {
            $sql = 'INSERT INTO subscription(name, email, lang, ip, created_at) VALUES(:name, :email, :lang, :ip, NOW())';
            $data['lang'] = $app['langManager']->getDbLanguage($app);
            $data['ip'] = $_SERVER['REMOTE_ADDR'];
            $app['db']->executeUpdate($sql, $data);
            $app['session']->getFlashBag()->add('message', 'Vous avez été inscrit avec succès.');
            
            $message = \Swift_Message::newInstance()
                ->setSubject('Votre inscription sur le site monbento')
                ->setFrom(array('noreply@monbento.com'))
                ->setTo(array($data['email']))
                ->setBody($app['twig']->render('mails/register.html.twig', array(
                'name' => $data['name']
            )), 'text/html');
            
            $app['mailer']->send($message);
            
            return $app->redirect($request->getBaseUrl().'/');
        }
        else
        {
            $error = $app['translator']->trans('L\'adresse e-mail existe déjà');
            $form->get('email')->addError(new FormError($error));
            $form2->get('email')->addError(new FormError($error));
        }
    }
    
    return $app['twig']->render('1column.html.twig', array(
        'form' => $form->createView(),
        'form2' => $form2->createView(),
        'messages' => $messages,
        'contactUrl' => $app['langManager']->getContactsUrlForlang($app)
    ));
});

/*
$app->get('/mail', function(Request $request) use($app){
    return $app['twig']->render('mails/register.html.twig', array('name' => 'Vincent'));
});
*/
$app->run();
